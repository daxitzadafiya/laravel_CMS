<?php

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Auth\ForgotPasswordController;
use App\Http\Controllers\User\Auth\ResetPasswordController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\ContactUsController;
use App\Http\Controllers\User\FLRatioController;
use App\Http\Controllers\User\GetFaqsController;
use App\Http\Controllers\User\GetWalletablesController;
use App\Http\Controllers\User\SalesGoalController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PushrSubscriberController;
use App\Http\Controllers\User\Report\DailyDealReportController;
use App\Http\Controllers\User\Report\FLRatioReportController;
use App\Http\Controllers\User\Report\GoalReportController;
use App\Http\Controllers\User\Report\MonthlyDealReportController;
use App\Http\Controllers\User\Report\TopPageReportController;
use App\Http\Controllers\User\Report\WalletTransactionReportController;
use App\Http\Controllers\User\UserPreferenceController;
use App\Http\Controllers\User\UserProfileController;

Route::post('auth/login', [AuthController::class, 'store']);
Route::post('auth/forgot-password', ForgotPasswordController::class);
Route::post('auth/reset-password', ResetPasswordController::class);
Route::get('push-notification/{user_id}/{sid}', PushrSubscriberController::class);

Route::middleware(['auth:sanctum', 'role:U'])->group(function () {
    Route::get('auth/user', [AuthController::class, 'show']);
    Route::post('auth/logout', [AuthController::class, 'destroy']);

    Route::prefix('report')->group(function () {
        Route::get('top', TopPageReportController::class);
        Route::get('goals', GoalReportController::class);
        Route::get('fl-ratio', FLRatioReportController::class);
        Route::get('monthly-deals', MonthlyDealReportController::class);
        Route::get('daily-deals', DailyDealReportController::class);
        Route::get('bank-transactions/{walletable}', WalletTransactionReportController::class);
    });

    Route::get('profile', [UserProfileController::class, 'show']);
    Route::post('profile', [UserProfileController::class, 'update']);

    Route::put('change-password', ChangePasswordController::class);

    Route::get('bank-accounts', GetWalletablesController::class);
    Route::get('goals', [SalesGoalController::class, 'index']);
    Route::put('goals', [SalesGoalController::class, 'update']);

    Route::get('fl-ratios', [FLRatioController::class, 'index']);
    Route::put('fl-ratios', [FLRatioController::class, 'update']);

    Route::get('preferences', [UserPreferenceController::class, 'index']);
    Route::put('preferences', [UserPreferenceController::class, 'update']);

    Route::get('faqs', GetFaqsController::class);

    Route::Post('contact', [ContactUsController::class, 'store']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);

    Route::get('notifications-link-post', [NotificationController::class, 'getNotificationLinkPost']);

    Route::post('read-notification', [NotificationController::class, 'readNotification']);

    Route::get('unread-notification', [NotificationController::class, 'unreadNotificationCount']);

    Route::post('click-notification-link-post', [NotificationController::class, 'clicksNotificationLinkPost']);
});
