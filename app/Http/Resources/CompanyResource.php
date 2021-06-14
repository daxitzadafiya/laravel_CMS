<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subscription' => new CompanySubscriptionResource($this->active_subscription),
            'type' => [
                'id' => $this->type,
                'name' => ucwords($this->type),
            ],
            'freee_name' => $this->name,
            'display_name' => $this->display_name,
            'contact_name' => $this->contact_name,
            'postcode' => $this->postcode,
            'prefecture' => new PrefectureResource($this->whenLoaded('prefecture')),
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'registration_date' => ! empty($this->registration_date)
                ? $this->registration_date->format('Y-m-d')
                : null,
            'head_count' => new HeadCountResource($this->whenLoaded('headCount')),
            'business_year_start' => $this->business_year_start_month && $this->business_year_start_day
                ? [
                    'month' => $this->business_year_start_month,
                    'day' => $this->business_year_start_day,
                  ]
                : null,
            'current_business_year' => $this->current_business_year
                ? [
                    'term' => $this->current_business_year['term'],
                    'start_date' => $this->current_business_year['start_date']->format('Y-m-d'),
                    'end_date' => $this->current_business_year['end_date']->format('Y-m-d'),
                  ]
                : null,
            'connected_date' => ! empty($this->connected_at)
                ? $this->connected_at->format('Y-m-d')
                : null,
            'current_month_logins' => $this->current_month_logins,
            'previous_month_logins' => $this->previous_month_logins,
            'users_count' => $this->when(! is_null($this->users_count), $this->users_count),
            'last_login_date' => ! empty($this->last_login_at)
                ? $this->last_login_at->format('Y-m-d')
                : null,
            'last_updated_date' => ! empty($this->updated_at)
                ? $this->updated_at->format('Y-m-d')
                : null,
            'status' => config('reddish.company.statuses')[$this->status]
                ?? config('reddish.company.statuses')[0],
        ];
    }
}
