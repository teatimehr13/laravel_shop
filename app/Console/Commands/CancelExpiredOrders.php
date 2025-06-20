<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:cancel-expired-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    protected $signature = 'orders:cancel-expired';
    protected $description = '自動取消超過 4 小時未付款的訂單';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deadline = Carbon::now('Asia/Taipei')->subHours(4);
        // Log::info(Order::where('order_status', 0));
        // $test = Order::where('order_status', 0)->get();
        // Log::info($test->toArray());     
        // Log::info($deadline);   

        $count = Order::where('order_status', 0)          // 1 = 待付款
            ->where('created_at', '<', $deadline)
            ->update(['order_status' => 6]);              // 6 = 已取消

        $this->info("已取消 {$count} 筆逾期訂單");
    }
}
