<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletableResource;
use App\Models\Walletable;

class GetWalletablesController extends Controller
{
    public function __invoke()
    {
        $walletables = Walletable::where('company_id', auth()->user()->company_id)
            ->where('type', 'bank_account')
            ->where('last_balance', '>', 0)
            ->get();

        return $this->sendResponse([
            'bank_accounts' => WalletableResource::collection($walletables),
        ]);
    }
}
