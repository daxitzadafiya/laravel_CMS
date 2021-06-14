<?php

namespace App\Console\Commands\Freee;

use App\Models\Company;
use App\Models\HeadCount;
use App\Models\Industry;
use App\Models\Prefecture;
use App\Services\FreeeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freee:sync-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync companies from freee api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FreeeService $freeeService)
    {
        try {
            $response = $freeeService->apiResponse('/companies', 'companies');

            foreach ($response['companies'] as $company) {
                $cpCompany = Company::find(trim($company['id']));

                if ($cpCompany) {
                    continue;
                }

                $cpCompany = Company::create([
                    'id' => trim($company['id']),
                    'name' => trim($company['display_name']),
                    'display_name' => trim($company['display_name']),
                    'role' => trim($company['role']),
                ]);

                if (! in_array($company['role'], ['admin', 'bookkeeping'])) {
                    continue;
                }

                $companyDetails = $freeeService->apiResponse('/companies/' . trim($company['id']), 'company');

                $prefecture = Prefecture::find(trim($companyDetails['company']['prefecture_code']));

                $industry = Industry::where('code', trim($companyDetails['company']['industry_code']))
                    ->whereHas('parent', function ($query) use($companyDetails) {
                        $query->where('code', trim($companyDetails['company']['industry_class']));
                    })->first();

                $headCount = HeadCount::find(trim($companyDetails['company']['head_count']));

                $cpCompany->name = trim($companyDetails['company']['display_name']);
                $cpCompany->display_name = trim($companyDetails['company']['display_name']);
                $cpCompany->contact_name = trim($companyDetails['company']['contact_name']);
                $cpCompany->postcode = trim($companyDetails['company']['zipcode']);
                $cpCompany->prefecture_id = $prefecture->id ?? null;
                $cpCompany->city = trim($companyDetails['company']['street_name1']);
                $cpCompany->address = trim($companyDetails['company']['street_name2']);
                $cpCompany->phone = trim($companyDetails['company']['phone1']);
                $cpCompany->role = trim($companyDetails['company']['role']);
                $cpCompany->industry_id = $industry->id ?? null;
                $cpCompany->head_count_id = $headCount->id ?? null;
                $cpCompany->save();

                sleep(10);
            }

            $this->comment('Synced companies!');
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'sync-companies exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
