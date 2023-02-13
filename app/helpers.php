<?php

use Illuminate\Support\Facades\URL;

if (! function_exists('getCustomerConfig')) {
    function global_tenancy_asset($path)
    {
        return URL::to('/') . '/cl/' . tenant('id') . $path;
    }

    function global_hours_format($hour)
    {
        return strtok($hour,':').__("h ").substr($hour, strpos($hour, ":") + 1);
    }

    function global_hours_sum($get_hours)
    {
        $sum_minutes = 0;

      
        foreach($get_hours as $hour)
        {
            $explodedTime = array_map('intval',explode(':',$hour->total_hours));
            $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
        }
            

        if(strlen(floor($sum_minutes/60)) == 1){
            $hoursCheck = '0'.floor($sum_minutes/60);
        }
        else {
            $hoursCheck = floor($sum_minutes/60);
        }

        if(strlen(floor($sum_minutes % 60)) == 1){
            $minutesCheck = '0'.floor($sum_minutes % 60);
        }
        else {
            $minutesCheck = floor($sum_minutes % 60);
        }

        $sumTime = $hoursCheck. ':' .$minutesCheck;


        return global_hours_format($sumTime);
    }
}


