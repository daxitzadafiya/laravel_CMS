<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
    ];

    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(AccountCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AccountCategory::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function allParents()
    {
        return $this->belongsTo(AccountCategory::class, 'parent_id', 'id')->with('allParents');
    }

}
