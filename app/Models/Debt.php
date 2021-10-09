<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'rewind_amount',
        'creditor',
        'debtor',
        'debt_date',
        'due_date',
        'user_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
