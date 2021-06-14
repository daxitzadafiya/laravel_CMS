<?php

namespace App\Console\Commands;

use App\Models\AccountItem;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Walletable;
use App\Models\WalletTxn;
use Illuminate\Console\Command;

class DemoCompanySetup extends Command
{

    protected $signature = 'demo-company-setup';
    protected $description = 'Setup demo company';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $baseCompanyId = 2718046;
        $demoCompanyId = 1;
        $keyPrefix = '1000001';

        $walletables = Walletable::where('company_id', $baseCompanyId)->get();

        foreach ($walletables as $walletable) {
            $newWalletable = clone $walletable;
            $newWalletable->id = $keyPrefix . $newWalletable->id;
            $newWalletable->company_id = $demoCompanyId;
            Walletable::updateOrCreate(
                ['id' => $newWalletable->id],
                $newWalletable->toArray()
            );
        }

        $accountItems = AccountItem::where('company_id', $baseCompanyId)->get();

        foreach ($accountItems as $accountItem) {
            $newAccountItem = clone $accountItem;
            $newAccountItem->id = $keyPrefix . $newAccountItem->id;
            $newAccountItem->company_id = $demoCompanyId;
            $newAccountItem->corresponding_income_id = $keyPrefix . $newAccountItem->corresponding_income_id;
            $newAccountItem->corresponding_exepnse_id = $keyPrefix . $newAccountItem->corresponding_expense_id;
            $newAccountItem->walletable_id = !is_null($newAccountItem->walletable_id) ? $keyPrefix . $newAccountItem->walletable_id : null;
            AccountItem::updateOrCreate(
                ['id' => $newAccountItem->id],
                $newAccountItem->toArray()
            );
        }

        $deals = Deal::where('company_id', $baseCompanyId)
            ->orderBy('issue_date', 'desc')
            ->limit(1000)
            ->get();

        foreach ($deals as $deal) {
            $newDeal = clone $deal;
            $newDeal->id = $keyPrefix . $newDeal->id;
            $newDeal->company_id = $demoCompanyId;
            $newDeal->account_item_id = $keyPrefix . $newDeal->account_item_id;
            Deal::updateOrCreate(
                ['id' => $newDeal->id],
                $newDeal->toArray()
            );
        }

        $walletTxns = WalletTxn::where('company_id', $baseCompanyId)
            ->orderBy('date', 'desc')
            ->limit(1000)
            ->get();

        foreach ($walletTxns as $walletTxn) {
            $newWalletTxn = clone $walletTxn;
            $newWalletTxn->id = $keyPrefix . $newWalletTxn->id;
            $newWalletTxn->company_id = $demoCompanyId;
            $newWalletTxn->account_item_id = $keyPrefix . $newWalletTxn->account_item_id;
            $newWalletTxn->walletable_id = $keyPrefix . $newWalletTxn->walletable_id;
            WalletTxn::updateOrCreate(
                ['id' => $newWalletTxn->id],
                $newWalletTxn->toArray()
            );
        }

        $company = Company::find($baseCompanyId);

        Company::where('id', $demoCompanyId)->update([
            'deals_updated_date' => $company->deals_updated_date,
            'txns_updated_date' => $company->txns_updated_date,
        ]);
    }
}
