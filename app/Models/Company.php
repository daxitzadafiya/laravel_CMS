<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'id',
        'name',
        'display_name',
        'role',
        'contact_name',
        'postcode',
        'prefecture_id',
        'city',
        'address',
        'phone',
        'email',
        'type',
        'business_year_start_month',
        'business_year_start_day',
        'registration_date',
        'head_count_id',
        'status',
        'connected_at',
    ];

    protected $dates = [
        'registration_date',
        'connected_at',
        'last_login_at',
        'deals_updated_date',
        'txns_updated_date',
    ];

    public $incrementing = false;

    public function scopeConnected($query)
    {
        return $query->where('status', 1);
    }

    public function getCurrentBusinessYearAttribute()
    {

        if (! $this->business_year_start_month || ! $this->business_year_start_day || ! $this->registration_date) {
            return null;
        }

        $today = Carbon::now();
        $startDate = Carbon::create(date('Y'), $this->business_year_start_month, $this->business_year_start_day);
        $firstTermStartDate = Carbon::create($this->registration_date->format('Y'), $this->business_year_start_month, $this->business_year_start_day);

        if ($startDate->gt($today)) {
            $startDate->subYear(1);
        }

        if ($firstTermStartDate->gt($this->registration_date))
        {
            $firstTermStartDate->subYear(1);
        }

        return [
            'term' => $firstTermStartDate->diffInYears($startDate) + 1,
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addYear(1)->subDay(1),
        ];
    }

    public function getDealsUpdatedDateAttribute($value)
    {
        if (! $value) {
            return $this->connected_at;
        }

        return Carbon::parse($value);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'company_subscriptions')
            ->select([
                'subscription_plans.id',
                'subscription_plans.name',
                'company_subscriptions.status',
                'company_subscriptions.created_at',
            ]);
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscriptions->where('status', 'active')->first();
    }

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class)
            ->select([
                'id',
                'name',
            ]);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class)
            ->select([
                'id',
                'name',
            ]);
    }

    public function headCount()
    {
        return $this->belongsTo(HeadCount::class)
            ->select([
                'id',
                'name',
            ]);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeOrderBySubscriptionPlan($query, $order = 'asc')
    {
        $query->orderBy(
            CompanySubscription::select('subscription_plan_id')
                ->whereColumn('company_subscriptions.company_id', 'companies.id')
                ->where('status', 'active')
                ->take(1),
            $order
        );
    }

    public function scopeOrderByHeadCount($query, $order = 'asc')
    {
        $query->orderBy(
            HeadCount::select('name')
                ->whereColumn('companies.head_count_id', 'head_counts.id'),
            $order
        );
    }

    public function accountItems()
    {
        return $this->hasMany(AccountItem::class)
            ->select('id', 'company_id', 'name', 'type', 'subtype');
    }

    public function salesGoals()
    {
        return $this->hasMany(SalesGoal::class);
    }

    public function flRatios()
    {
        return $this->hasMany(FlRatio::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function manualJournals()
    {
        return $this->hasMany(ManualJournal::class);
    }

    public function walletables()
    {
        return $this->hasMany(Walletable::class);
    }

    public function bankAccounts()
    {
        return $this->walletables->where('type', 'bank_account');
    }
}
