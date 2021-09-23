<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Models\UserAddress;
use App\Models\CouponCode;
use App\Models\Order;
use App\Exceptions\CouponCodeUnavailableException;
use App\Http\Queries\OrderQuery;
use App\Http\Requests\Api\SendReviewRequest;
use Carbon\Carbon;
use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Api\ApplyRequest;
use App\Http\Requests\Api\CrowdFundingOrderRequest;
use App\Models\ProductSku;

class OrdersController extends Controller
{
    public function store(OrderRequest $request,OrderService $orderService)
    {
        $user = $request->user();
        $remark = $request->input('remark');
        $address = UserAddress::find($request->input('address_id'));
        $items = $request->input('items');
        $coupon = null;
        // 如果用户提交了优惠码
        if($code = $request->input('coupon_code')){
            $coupon = CouponCode::where('code',$code)->first();
            if(!$coupon){
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }
        $order = $orderService->store($user,$address,$remark,$items,$coupon);
        $order->load('user:name,id,email_verified_at');
        return new OrderResource($order);
    }

    public function index(Request $request,OrderQuery $query)
    {
        $orders = $query->where('user_id',$request->user()->id)->paginate(15);
        return OrderResource::collection($orders);
    }

    public function show(Order $order,OrderQuery $query)
    {
        $this->authorize('own',$order); 
        $order = $query->find($order->id);
        return new OrderResource($order);
    }
    //确认收货
    public function received(Order $order,Request $request)
    {
        $this->authorize('own',$order);
        //物流状态不是已发货
        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('物流状态不正确');
        }
        //更新物流状态为已收货
        $order->ship_status = Order::SHIP_STATUS_RECEIVED;
        $order->save();
        return new OrderResource($order);
    }
    //商品评价页面
    public function review(Order $order,OrderQuery $query)
    {
        $this->authorize('own',$order);
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付不能评价');
        }
        $order = $query->find($order->id);
        return new OrderResource($order);
    }

    //提交商品评价
    public function sendReview(Order $order,SendReviewRequest $request)
    {
        $this->authorize('own',$order);
        //没支付不能评价
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付不能评价');
        }
        //订单已经评价
        if($order->reviewed){
            throw new InvalidRequestException('订单已经评价过');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function() use($order,$reviews){
            // 遍历用户提交的数据
            foreach($reviews as $v){
                $orderItem = $order->items()->find($v['id']);
                $orderItem->update([
                    'rating' => $v['rating'],
                    'review' => $v['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            //将订单标记为以评价
            $order->update([
                'reviewed' => true,
            ]);
        });
        event(new OrderReviewed($order));
        return response('评价成功',200);
    }

    //申请退款
    public function applyRefund(Order $order,ApplyRequest $request)
    {
        $this->authorize('own',$order);
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单没有支付');
        }
        // 众筹订单不允许申请退款
        if($order->status === Order::TYPE_CROWDFUNDING){
            throw new InvalidRequestException('众筹订单不允许申请退款');
        }
        //已经申请过退款
        if($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('已经申请过退款');
        }
        $extra = $order->extra ? : [];
        $extra['refund_reason'] = $request->input('reason');
        $order->update([
            'extra' => $extra,
            'refund_status' => Order::REFUND_STATUS_APPLIED,
        ]);
        return new OrderResource($order);
    }

    //众筹商品下单请求
    public function crowdfunding(CrowdFundingOrderRequest $request,OrderService $orderService)
    {
        $user = $request->user();
        $sku = ProductSku::find($request->input('sku_id'));
        $amount = $request->input('amount');
        $address = UserAddress::find($request->input('address_id'));
        $order = $orderService->crowdfunding($user,$address,$sku,$amount);
        return new OrderResource($order);
    }
}
