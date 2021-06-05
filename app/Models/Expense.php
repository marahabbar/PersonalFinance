<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'monthly',
        'user_id',
        'category_id',
        'date',
    ];
    //protected $appends=['Type'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(category::class);
    }
    // public function getTypeAttribute()
    // {
    //     return "e";
    // }
}
