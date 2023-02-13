<?php

namespace App\Console\Commands;

use Log;
use App\Models\Tenant;
use Illuminate\Console\Command;
use App\Events\Alerts\AlertEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant\CustomerServices;
use Stancl\Tenancy\Controllers\TenantAssetsController;

class AlertsEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:cron';

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
       
        tenancy()->runForMultiple(null, function (Tenant $tenant) {
            $customersServices = CustomerServices::where('end_date','>=',date('Y-m-d'))->with('service')->with('customer')->with('customerLocation')->get();
            foreach ($customersServices as $customer)
            {
                $date_subtracted = date('Y-m-d', strtotime('-'.$customer->alert.' day', strtotime($customer->end_date)));
                if(date('Y-m-d') == $date_subtracted)
                {
                    event(new AlertEvent($customer));
                }
            }
        });
        \Log::info("Cron is working fine!");
        
        //return Command::SUCCESS;
    }
}
