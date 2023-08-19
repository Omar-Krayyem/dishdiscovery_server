<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\shoppingListsController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});


Route::group(['middleware' => ['jwt.auth']], function () {

    Route::get("/Home", [RecipesController::class, "getAll"]);

    Route::group(['prefix' => 'recipe'], function(){
        Route::get('/{recipe}', [RecipesController::class, "getById"]);
        Route::post('/store', [RecipesController::class, "store"]);
        Route::delete('/destroy/{id}', [RecipesController::class, "destroy"]);
    });

    Route::group(['prefix' => 'like'], function(){
        Route::get('/{id}', [LikesController::class, "isLiked"]);
        Route::get('/count/{id}', [LikesController::class, "countLikes"]);
        Route::post('/store', [LikesController::class, "store"]);
        Route::delete('/destroy/{id}', [LikesController::class, "destroy"]);
    });

    Route::group(['prefix' => 'comments'], function(){
        Route::get('/{id}', [CommentsController::class, "getComments"]);
        Route::post('/store', [CommentsController::class, "store"]);
    });

    Route::group(['prefix' => 'list'], function(){
        Route::get('/{id}', [shoppingListsController::class, "getAll"]);
        Route::post('/store', [shoppingListsController::class, "store"]);
        Route::delete('/destroy/{id}', [shoppingListsController::class, "destroy"]);
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});