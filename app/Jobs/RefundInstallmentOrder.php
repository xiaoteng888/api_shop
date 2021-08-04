<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\InstallmentItem;
use App\Models\Installment;
use App\Exceptions\InternalException;


class RefundInstallmentOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 如果商品订单支付方式不是分期付款、订单未支付、订单退款状态不是退款中，则不执行后面的逻辑
        if($this->order->payment_method !== 'installment' || !$this->order->paid_at || $this->order->refund_status !== Order::REFUND_STATUS_PROCESSING){
            return;
        }
        // 找不到对应的分期付款，原则上不可能出现这种情况，这里的判断只是增加代码健壮性
        if(!$installment = Installment::query()->where('order_id',$this->order->id)->first()){
            return;
        }
        // 遍历对应分期付款的所有还款计划
        foreach($installment->items as $item){
            //未支付或者已退款和退款中的跳过
            if(!$item->paid_at || in_array($item->refund_status,[InstallmentItem::REFUND_STATUS_PROCESSING,InstallmentItem::REFUND_STATUS_SUCCESS])){
                continue;
            }
            // 调用具体的退款逻辑，
            try {
                $this->refundInstallmentItem($item);
            } catch (\Exception $e) {
                \Log::warning('分期退款失败:'.$e->getMessage(),['installment_item_id' => $item->id,]);
                // 假如某个还款计划退款报错了，则暂时跳过，继续处理下一个还款计划的退款
                continue;
            }
        }
        $installment->refreshRefundStatus();
    }

    protected function refundInstallmentItem(InstallmentItem $item)
    {
        //退款流水号= 订单退款号+还款计划序号
        $refundNo = $this->order->refund_no.'_'.$item->sequence;
        // 根据还款计划的支付方式执行对应的退款逻辑
        switch ($item->method) {
            case 'wechat':
                // code...
                break;
            case 'alipay':
                // 调用支付宝支付实例的 refund 方法
                $res = app('alipay')->refund([
                    'trade_no' => $item->payment_no,// 使用支付宝交易号来退款
                    'refund_amount' => $item->base,
                    'out_request_no' => $refundNo,
                ]);
                // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
                if($res->sub_code){
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_FAILED,
                    ]);
                }else{
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                // 原则上不可能出现，这个只是为了代码健壮性
            throw new InternalException('未知订单支付方式:'.$item->method);
                break;
        }
    }
}
