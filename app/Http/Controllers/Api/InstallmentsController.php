<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Queries\InstallmentQuery;
use App\Http\Resources\InstallmentResource;
use App\Models\Installment;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class InstallmentsController extends Controller
{
    public function index(Request $request,InstallmentQuery $query)
    {
        $installments = $query->where('user_id',$request->user()->id)->paginate(10);
        return InstallmentResource::collection($installments);
    }

    public function show($installment_id,InstallmentQuery $query)
    {
        $installment = $query->find($installment_id);
        $this->authorize('own',$installment);
        // 下一个未完成还款的还款计划
        $installment['nextItem'] = $installment->items()->where('paid_at',null)->first();
        return new InstallmentResource($installment);
    }

    public function payByAlipay(Installment $installment)
    {
        if($installment->order->closed){
            throw new InvalidRequestException('该订单已关闭');
        }
        if($installment->status === Installment::STATUS_FINISHED){
            throw new InvalidRequestException('该分期订单已经结清');
        }
        // 获取当前分期付款最近的一个未支付的还款计划
        if(!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()){
            throw new InvalidRequestException('该分期订单已经结清');
        }
        return app('alipay')->app([
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject'      => '支付 Laravel Shop 的分期订单：'.$installment->no,
            // 这里的 notify_url 和 return_url 可以覆盖掉在 AppServiceProvider 设置的回调地址
            'notify_url'   => ngrok_url('api.v1.installments.alipay.notify'), // todo
            'return_url'   => route('api.v1.installments.alipay.return'), // todo, 
        ]);
    }

    public function alipayReturn()
    {
        try{
            $data = app('alipay')->verify();
        }catch(\Exception $e){
            return response('数据不正确');
        }
        reutrn app('alipay')->success();
    }

    public function alipayNotify()
    {
        // 校验支付宝回调参数是否正确
        $data = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        if(!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])){
            return app('alipay')->success();
        }
        // 拉起支付时使用的支付订单号是由分期流水号 + 还款计划编号组成的
        // 因此可以通过支付订单号来还原出这笔还款是哪个分期付款的哪个还款计划
        list($no,$sequence) = explode('_',$data->out_trade_no);
        // 根据分期流水号查询对应的分期记录，原则上不会找不到，这里的判断只是增强代码健壮性
        if(!$installment = Installment::where('no',$no)->first()){
            return response('订单不存在',403);
        }
        // 根据还款顺序编号查询对应的还款计划，原则上不会找不到，这里的判断只是增强代码健壮性
        if(!$item = $installment->items()->where('sequence',$sequence)->first()){
            return response('还款计划不存在',403);
        }
        //如果订单已经支付告诉支付此订单完成，不再执行后面逻辑
        if($item->paid_at){
            return response('此订单已经支付',403);
        }
        // 使用事务，保证数据一致性
        \DB::transaction(function() use($data,$no,$installment,$item){
            // 更新对应的还款计划
            $item->update([
                'paid_at' => Carbon::now(),
                'method'  => 'alipay',
                'payment_no' => $data->trade_no,
            ]);
            // 如果这是第一笔还款
            if($item->sequence == 0){
                //修改分期付款状态
                $installment->update([
                    'status' => Installment::STATUS_REPAYING,
                ]);
                //将分期付款对应的商品订单状态改为已支付
                $installment->order->update([
                    'paid_at' => Carbon::now(),
                    'payment_method' => 'installment',
                    'payment_no' => $no,
                ]);
                // 触发商品订单已支付的事件
                event(new OrderPaid($installment->order));
            }
            //如果这是最后一期还款
            if($item->sequence == $installment->count -1){
                // 将分期付款状态改为已结清
                $installment->update([
                    'status' => Installment::STATUS_FINISHED,
                ]);
            }
        });
        return app('alipay')->success();
    }
}
