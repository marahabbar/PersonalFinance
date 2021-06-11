<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'max_amount',
        'type',
        'user_id'
    ];
    
    public function incomes()
    {
        return $this->hasMany(income::class);
    }
 
    public function expenses()
    {
        return $this->hasMany(expense::class);
    }

   
        
      
 
}
