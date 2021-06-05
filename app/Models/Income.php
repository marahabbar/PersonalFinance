<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'amount',
        'saving_amount',
        'monthly',
        'user_id',
        'category_id',
        'date',
    ];
   // protected $appends=['type'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // public function getTypeAttribute()
    // {
    //     return "i";
    // }
}
