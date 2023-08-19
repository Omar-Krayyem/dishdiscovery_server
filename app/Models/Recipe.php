<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function likes(){
        return $this->hasMany(User::class, 'recipe_id');
    }

    public function comments(){
        return $this->hasMany(User::class, 'recipe_id');
    }

    public function shoppingItems(){
        return $this->hasMany(User::class, 'recipe_id');
    }

    public function calender(){
        return $this->hasMany(User::class, 'recipe_id');
    }
}
