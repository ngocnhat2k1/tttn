<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MomoCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $momo = DB::table("momo")
            // ->where('status', "=", 0)
            ->where("status", "=", 0);

        for ($i = 0; $i < sizeof($momo->get()); $i++) {
            $order = DB::table("orders")
                ->where("id", "=", $momo->get()[$i]->order_id)
                ->update(['status' => -1]);
        }

        $momo->update([
            'status' => -1,
            'pay_url' => null
        ]);
    }
}
