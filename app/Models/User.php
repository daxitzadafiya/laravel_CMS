<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'last_name',
        'first_name',
        'last_name_kana',
        'first_name_kana',
        'email',
        'password',
        'position',
        'role',
        'company_id',
        'subscriber_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeOrderByName($query, $order = 'asc')
    {
        $query->orderBy('last_name_kana', $order)
            ->orderBy('first_name_kana', $order);
    }

    public function scopeOrderByCompany($query, $order = 'asc')
    {
        $query->orderBy(
            Company::select('display_name')
                ->whereColumn('users.company_id', 'companies.id'),
            $order
        );
    }

    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function userGroups()
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_users');
    }
}
