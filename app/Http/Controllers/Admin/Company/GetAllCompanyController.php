<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllCompanyResource;
use App\Models\Company;

class GetAllCompanyController extends Controller
{
    public function __invoke()
    {
        $companies = Company::with('prefecture', 'headCount')
            ->select('id', 'display_name', 'postcode', 'prefecture_id', 'city', 'address', 'phone', 'type', 'status', 'connected_at', 'registration_date')
            ->orderBy('id')
            ->get();

        return $this->sendResponse([
            'companies' => AllCompanyResource::collection($companies),
        ]);
    }
}
