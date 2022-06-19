<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/*
 * User routes
 */
Route::group(['middleware' => ['auth']], function () {

    /*
    * Admin routes
    */
    Route::group(['middleware' => ['admin']], function () {
        // Users
        Route::get('/users', function (Request $request) {
            return User::getUsers($request);
        });
        Route::post('/users', function (Request $request) {
            return User::createUser($request);
        });

        // Languages
        Route::get('/languages', function (Request $request) {
            return Language::getLanguages($request);
        });
    });
});

/*
 * Public routes
 */