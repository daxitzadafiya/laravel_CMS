<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountItem extends Model
{
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'shortcut',
        'shortcut_num',
        'account_category_id',
        'corresponding_income_id',
        'corresponding_expense_id',
        'walletable_id',
        'type',
        'subtype',
        'available',
    ];

    public $incrementing = false;
    public $timestamps = false;

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeFood($query)
    {
        return $query->where('subtype', 'F');
    }

    public function scopeLabor($query)
    {
        return $query->where('subtype', 'L');
    }

    public function scopeFoodAndLabor($query)
    {
        return $query->where('subtype', 'F')
            ->orWhere('subtype', 'L');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function manualJournals()
    {
        return $this->hasMany(ManualJournal::class);
    }
}
