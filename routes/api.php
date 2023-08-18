<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [\App\Http\Controllers\ApiAuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\ApiAuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function ()
{
    Route::post('/logout',[\App\Http\Controllers\ApiAuthController::class, 'logout']);

    Route::group(['middleware' => 'role:admin'], function ()
    {
        Route::apiResource('users', \App\Http\Controllers\UserController::class);

        Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
        Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class);

    });
});
