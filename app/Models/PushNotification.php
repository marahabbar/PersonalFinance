<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{protected $fillable = [
    'title','body','type','date','user_id'
    ];
    use HasFactory;
}
