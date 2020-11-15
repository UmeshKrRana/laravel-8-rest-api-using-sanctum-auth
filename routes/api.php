<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// user controller routes
Route::post("register", [UserController::class, "register"]);

Route::post("login", [UserController::class, "login"]);

// sanctum auth middleware routes

Route::middleware('auth:api')->group(function() {

    Route::get("user", [UserController::class, "user"]);

    Route::resource('tasks', TaskController::class);

});