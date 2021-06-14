<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->sendResponse([
            'companies' => $this->getCompanyStats(),
            'users' => $this->getUserStats(),
            'notifications' => $this->getNotificationStats(),
        ]);
    }

    protected function getCompanyStats()
    {
        $response = [
            'total' => 0,
        ];

        $companyStatsByType = Company::selectRaw('type, COUNT(id) as count')
            ->where('status', 1)
            ->groupBy('type')
            ->get();

        foreach (config('reddish.company.types') as $type) {
            $count = $companyStatsByType->where('type', $type['id'])
                ->pluck('count')
                ->first();

            $response[$type['id']] = $count ?? 0;
        }

        $companyStatsByStatus = Company::selectRaw('status, COUNT(id) as count')
            ->groupBy('status')
            ->get();

        foreach (config('reddish.company.statuses') as $status) {
            $count = $companyStatsByStatus->where('status', $status['id'])
                ->pluck('count')
                ->first();

            $response['total'] += $count;
            $response[strtolower($status['name'])] = $count ?? 0;
        }

        return $response;
    }

    protected function getUserStats()
    {
        return [
            'total' => User::count(),
        ];
    }

    protected function getNotificationStats()
    {
        return [
            'draft' => Notification::where('is_draft', 1)->count(),
        ];
    }
}
