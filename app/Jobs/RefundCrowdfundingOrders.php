<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use App\Services\OrderService;

class RefundCrowdfundingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $crowdfunding; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CrowdfundingProduct $crowdfunding)
    {
        $this->crowdfunding = $crowdfunding;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 如果众筹的状态不是失败则不执行退款，原则上不会发生，这里只是增加健壮性
        if ($this->crowdfunding->status !== CrowdfundingProduct::STATUS_FAIL) {
            return;
        }
        $orderService = app(OrderService::class);
        
        // 查询出所有参与了此众筹的订单
        Order::query()
             ->where('type',Order::TYPE_CROWDFUNDING)
             // 已支付的订单
             ->whereNotNull('paid_at')
             ->whereHas('items',function($query){
                $query->where('product_id',$this->crowdfunding->product_id);
             })
             ->get()
             ->each(function(Order $order) use ($orderService) {
                $orderService->refundOrder($order);
        });
    }
}
