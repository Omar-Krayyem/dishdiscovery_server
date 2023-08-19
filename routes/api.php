<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipesController;

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

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});