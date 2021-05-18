<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\SendReviewRequest;
use Carbon\Carbon;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;

class OrdersController extends Controller
{
    public function store(OrderRequest $request,OrderService $orderService)
    {
    	$user = $request->user();
        $remark = $request->input('remark');
        $address = UserAddress::find($request->input('address_id'));
        $items = $request->input('items');
        return $orderService->store($user,$address,$remark,$items);
    }

    public function index(Request $request)
    {
    	$orders = Order::query()->with(['items.product','items.productSku'])->where('user_id',$request->user()->id)->orderBy('created_at','desc')->paginate();
    	return view('orders.index',compact('orders'));
    }

    public function show(Order $order,Request $request)
    {
    	$this->authorize('own',$order);
    	return view('orders.show',['order' => $order->load('items.product','items.productSku')]);
    }

    public function received(Order $order,Request $request)
    {
        //权限校验
        $this->authorize('own',$order);
        // 判断订单的发货状态是否为已发货
        if($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }
        // 更新发货状态为已收到
        $order->ship_status = Order::SHIP_STATUS_RECEIVED;
        $order->save();
        // 返回原页面
        return $order;
    }

    public function review(Order $order)
    {
        $this->authorize('own',$order);
        // 判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review',['order' => $order->load(['items.productSku','items.product'])]);
    }

    public function sendReview(Order $order,SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own',$order);
        // 判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        // 判断是否已经评价
        if($order->reviewed){
            throw new InvalidRequestException('该订单已经评价');
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
            //将订单标记为已评价
            $order->update(['reviewed' => true]);
        });
        event(new OrderReviewed($order)); 
        return redirect()->back();
    }

    public function applyRefund(Order $order,ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own',$order);
        // 判断订单是否已付款
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未付款'); 
        }
        //判断订单退款状态是否正确
        if($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已申请过退款，请勿重复提交');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ? : [];
        $extra['refund_reason'] = $request->input('reason');

        $order->update([
            'extra' => $extra,
            'refund_status' => Order::REFUND_STATUS_APPLIED,
        ]);

        return $order;
    }
}
