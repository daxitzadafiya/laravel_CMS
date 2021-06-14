<?php

namespace App\Events;

use App\Models\Company;
use Illuminate\Foundation\Events\Dispatchable;

class CompanyConnected
{
    use Dispatchable;

    public $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }
}
