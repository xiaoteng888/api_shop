<?php

namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;
use App\Models\InstallmentItem;
use App\Models\Installment;
use Carbon\Carbon;

class CalculateInstallmentFine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:calculate-installment-fine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算分期付款逾期费';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        InstallmentItem::query()
                      ->with(['installment'])
                      ->whereHas('installment',function($query){
                        $query->where('status',Installment::STATUS_REPAYING);
                      })
                      ->where('due_date','<=',Carbon::now())
                      ->whereNull('paid_at')
                      ->chunkById(1000,function($items){
                        // 遍历查询出来的还款计划
                        foreach($items as $item){
                            //计算逾期天数
                            $overdueDays = Carbon::now()->diffInDays($item->due_date);
                            //计算本金+手续费
                            $base = big_number($item->base)->add($item->fee)->getValue();
                            //计算逾期费
                            $fine = big_number($base)->multiply($overdueDays)->multiply($item->installment->fine_rate)->divide(100)->getValue();
                            // 避免逾期费高于本金与手续费之和，使用 compareTo 方法来判断
                            // 如果 $fine 大于 $base，则 compareTo 会返回 1，相等返回 0，小于返回 -1
                            $fine = big_number($fine)->compareTo($base) === 1 ? $base : $fine;
                            $item->update(['fine' => $fine]);
                        }
                      });
    }
}
