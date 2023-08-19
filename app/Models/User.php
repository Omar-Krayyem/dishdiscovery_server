<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }

    public function recipes(){
        return $this->hasMany(User::class, 'user_id');
    }

    public function likes(){
        return $this->hasMany(User::class, 'user_id');
    }

    public function comments(){
        return $this->hasMany(User::class, 'user_id');
    }

    public function shoppingItems(){
        return $this->hasMany(User::class, 'user_id');
    }

    public function calender(){
        return $this->hasOne(User::class, 'user_id');
    }
}
