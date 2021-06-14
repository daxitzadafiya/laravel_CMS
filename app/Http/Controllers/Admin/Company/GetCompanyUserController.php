<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Company;

class GetCompanyUserController extends Controller
{
    public function __invoke(Company $company)
    {
        return $this->sendResponse([
            'users' => UserResource::collection($company->users()->get()),
        ]);
    }
}
