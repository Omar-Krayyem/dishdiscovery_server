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
        return $this->hasMany(Like::class, 'recipe_id');
    }

    public function comments(){
        return $this->hasMany(Comment::class, 'recipe_id');
    }

    public function calendar(){
        return $this->hasMany(Calendar::class, 'recipe_id');
    }

    public function delete()
    {
        $this->calendar()->delete();

        return parent::delete();
    }
}
