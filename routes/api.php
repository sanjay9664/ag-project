<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SiteController;
use App\Http\Controllers\Backend\Auth\LoginController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login/submit', [LoginController::class, 'Apilogin']);
Route::post('/logout', [LoginController::class, 'Apilogout']);
Route::post('/store-device-events', [SiteController::class, 'apiStoreDevice']);
Route::put('/update-device-events/{deviceId}', [SiteController::class, 'apiUpdateDevice']);
Route::get('/device-status', [DashboardController::class, 'apiFetchDeviceStatus']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/sites', [SiteController::class, 'apiSites']);
    // Route::get('/dashboard-data', [DashboardController::class, 'apiIndex'])->name('dashboard');


});