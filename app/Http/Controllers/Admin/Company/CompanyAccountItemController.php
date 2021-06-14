<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountItemRequest;
use App\Http\Resources\AccountItemResource;
use App\Models\AccountItem;
use App\Models\Company;
use App\Services\AccountItemService;

class CompanyAccountItemController extends Controller
{
    protected $accountItemService;

    public function __construct(AccountItemService $accountItemService)
    {
        $this->accountItemService = $accountItemService;
    }

    public function index(Company $company, AccountItemRequest $request)
    {
        return $this->sendResponse([
            'account_items' => AccountItemResource::collection($this->accountItemService->getAccountItems($company, $request)),
        ]);
    }

    public function update(Company $company, AccountItemRequest $request)
    {
        $data = $request->validated();

        foreach ($data['account_items'] as $accountItem) {
            if (count ($accountItem) > 1) {
                AccountItem::where('id', $accountItem['id'])
                    ->update($accountItem);
            }
        }

        return $this->sendResponse([
            'message' => __('Account items updated successfully.'),
        ]);
    }
}
