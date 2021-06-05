<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable 
{
    use HasApiTokens ,HasFactory, Notifiable ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }
   public function debts()
   {
       return $this->hasMany(Debt::class);
   }

   
   public function incomes()
   {
       return $this->hasMany(income::class);
   }

   public function expenses()
   {
       return $this->hasMany(expense::class);
   }


   public function saving_goals()
   {
       return $this->hasMany(saving_goal::class);
   }
   
   public function getJWTIdentifier()
   {
     return $this->getKey();  
   }
   public function getJWTCustomClaims()
   {
       return[];
   }
   

}
