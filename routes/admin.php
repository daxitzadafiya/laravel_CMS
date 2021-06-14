<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Company\CompanyAccountItemController;
use App\Http\Controllers\Admin\Company\CompanyController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Company\CompanyFLRatioController;
use App\Http\Controllers\Admin\Company\CompanySalesGoalController;
use App\Http\Controllers\Admin\Company\GetAllCompanyController;
use App\Http\Controllers\Admin\Company\GetCompanyBusinessYearsController;
use App\Http\Controllers\Admin\Company\GetCompanyDealsController;
use App\Http\Controllers\Admin\Company\GetCompanyPerformanceController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Company\GetCompanyPerformanceSummaryController;
use App\Http\Controllers\Admin\Faq\GetFaqCategoriesController;
use App\Http\Controllers\Admin\Faq\FaqController;
use App\Http\Controllers\Admin\Notification\UploadNotificationImageController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\User\AdminUserController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Notification\NotificationCategoryController;
use App\Http\Controllers\Admin\Notification\NotificationTagController;
use App\Http\Controllers\Admin\Notification\NotificationTypeController;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Notification\NotificationLinkPostController;
use App\Http\Controllers\Admin\Company\GetCompanyBankBalancesController;
use App\Http\Controllers\Admin\User\UserGroupController;


Route::post('auth/login', [AuthController::class, 'store']);
Route::post('auth/forgot-password', ForgotPasswordController::class);
Route::post('auth/reset-password', ResetPasswordController::class);

Route::middleware(['auth:sanctum', 'role:A,SA'])->group(function () {
    Route::get('auth/user', [AuthController::class, 'show']);
    Route::post('auth/logout', [AuthController::class, 'destroy']);

    Route::prefix('companies')->group(function () {
        Route::get('all', GetAllCompanyController::class);
        Route::get('{company}/account-items', [CompanyAccountItemController::class, 'index']);
        Route::put('{company}/account-items', [CompanyAccountItemController::class, 'update']);
        Route::get('{company}/business-years', GetCompanyBusinessYearsController::class);
        Route::get('{company}/performance/summary', GetCompanyPerformanceSummaryController::class);
        Route::get('{company}/performance', GetCompanyPerformanceController::class);
        Route::get('{company}/deals', GetCompanyDealsController::class);
        Route::get('{company}/bank-balances', GetCompanyBankBalancesController::class);
        Route::get('{company}/sales-goals/show', [CompanySalesGoalController::class, 'show']);
        Route::apiResource('{company}/sales-goals', CompanySalesGoalController::class)->except('show');
        Route::apiResource('{company}/fl-ratios', CompanyFLRatioController::class)->except('show');
    });

    Route::prefix('faq')->group(function () {
        Route::get('categories', GetFaqCategoriesController::class);
        Route::apiResource('faqs', FaqController::class);
    });

    Route::apiResource('companies', CompanyController::class)->except(['store']);

    Route::apiResource('users', UserController::class);

    Route::apiResource('admins', AdminUserController::class);

    Route::get('stats', StatisticsController::class);

    Route::apiResource('user-group', UserGroupController::class);

    Route::prefix('notification')->group(function () {
        Route::apiResource('notifications', NotificationController::class);
        Route::apiResource('categories', NotificationCategoryController::class);
        Route::apiResource('tags', NotificationTagController::class);
        Route::apiResource('link-posts', NotificationLinkPostController::class);
        Route::get('types', [NotificationTypeController::class, 'index']);
        Route::post('images', UploadNotificationImageController::class);
    });
});
