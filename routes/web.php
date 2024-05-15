<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
});

Route::get('/users', [UserController::class, 'list']);

Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects', "index");
});
