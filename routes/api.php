<?php

use App\Http\Controllers;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * User routes
 */
Route::group(['middleware' => ['auth']], function () {

    /*
    * Admin routes
    */
    Route::group(['middleware' => ['admin']], function () {
        // User management
        Route::get('/users', [Controllers\UserController::class, 'getUsers']);
        Route::post('/user', [Controllers\UserController::class, 'postUser']);
        Route::delete('/user/{id}', [Controllers\UserController::class, 'deleteUser']);
    });

    /**
     * User routes
     */
    Route::group(['middleware' => ['user']], function () {
        // Languages
        Route::get('/languages', [Controllers\LanguageController::class, 'getLanguages']);        
    });
});

/*
 * Public routes
 */
Route::post('/login', [Controllers\AuthController::class, 'login']);