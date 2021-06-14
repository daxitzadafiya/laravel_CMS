<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category_id',
        'order',
        'question',
        'answer',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
