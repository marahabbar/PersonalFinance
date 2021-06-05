<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'user_id',
        
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
