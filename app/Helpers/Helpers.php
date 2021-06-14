<?php

use App\Models\Config;
use Carbon\CarbonPeriod;

if (! function_exists('getFreeeAccessToken')) {
    function getFreeeAccessToken()
    {
        return Config::where('type', 'freee.api.access-token')
            ->pluck('value')
            ->first();
    }
}

if (! function_exists('isPaginate')) {
    function isPaginate($value)
    {
        return is_null($value) || $value != 0;
    }
}

if (! function_exists('getMonthsInPeriod')) {
    function getMonthsInPeriod($startDate, $endDate)
    {
        $dates = CarbonPeriod::create($startDate, '1 month', $endDate);

        $months = [];

        foreach ($dates as $date) {
            $months[] = $date->format('Y-n');
        }

        return $months;
    }
}

