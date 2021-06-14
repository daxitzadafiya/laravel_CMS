<?php

use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Route;

Route::prefix('master')->group(function () {
    Route::get('all', [MasterController::class, 'index']);
    Route::get('prefectures', [MasterController::class, 'prefectures']);
    Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']);
    Route::get('company-types', [MasterController::class, 'companyTypes']);
    Route::get('company-statuses', [MasterController::class, 'companyStatuses']);
    Route::get('head-counts', [MasterController::class, 'headCounts']);
    Route::get('accountitem-categories', [MasterController::class, 'accountItemCategories']);
});
