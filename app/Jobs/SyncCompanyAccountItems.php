<?php

namespace App\Jobs;

use App\Models\AccountCategory;
use App\Models\AccountItem;
use App\Models\Company;
use App\Models\Walletable;
use App\Services\FreeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCompanyAccountItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company;

    public $timeout = 0;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function handle(FreeeService $freeeService)
    {
        try {
            $response = $freeeService->apiResponse('/account_items?company_id=' .  $this->company->id, 'account_items');

            foreach ($response['account_items'] as $accountItem) {
                $categoryId = $this->getAccountCategoryId($accountItem['categories'] ?? []);

                $walletable = Walletable::find(trim($accountItem['walletable_id']));

                AccountItem::updateOrCreate([
                    'id' => trim($accountItem['id']),
                    'company_id' => $this->company->id,
                ], [
                    'name' => trim($accountItem['name']),
                    'shortcut' => trim($accountItem['shortcut']),
                    'shortcut_num' => trim($accountItem['shortcut_num']),
                    'account_category_id' => $categoryId,
                    'corresponding_income_id' => trim($accountItem['corresponding_income_id']),
                    'corresponding_expense_id' => trim($accountItem['corresponding_expense_id']),
                    'walletable_id' => $walletable->id ?? null,
                    'available' => is_numeric(trim($accountItem['available']))
                        ? trim($accountItem['available'])
                        : 0,
                ]);
            }

            $response = null;
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'SyncCompanyAccountItems exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }

    protected function getAccountCategoryId($categories = [])
    {
        if (count($categories) == 0) {
            return null;
        }

        $accountCategories = AccountCategory::whereIn('name', $categories)->get();
        $parentId = null;

        foreach ($categories as $key => $value) {
            $accountCategory = $accountCategories->where('name', trim($value))->first();

            if ($accountCategory) {
                $parentId = $accountCategory->id;
                continue;
            }

            $newCategory = AccountCategory::create([
                'parent_id' => $parentId,
                'name' => trim($value),
            ]);

            $parentId = $newCategory->id;
        }

        return $parentId;
    }
}
