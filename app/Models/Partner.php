<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'long_name',
        'name_kana',
        'default_title',
        'phone',
        'contact_name',
        'email',
        'shortcut1',
        'shortcut2',
        'org_type',
        'country_code',
        'address_zipcode',
        'address_prefecture_id',
        'address_street_name1',
        'address_street_name2',
        'bank_name',
        'bank_name_kana',
        'bank_code',
        'bank_branch_name',
        'bank_branch_name_kana',
        'bank_branch_code',
        'bank_account_name',
        'bank_account_number',
        'bank_account_type',
    ];

    public $incrementing = false;
    public $timestamps = false;
}
