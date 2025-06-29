<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Backend\AdminsController;
use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\SiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::get('/clear-cache', function () {
    Artisan::call('optimize');

    return response()->json([
        'message' => 'Application optimized successfully.',
        'output' => Artisan::output(),
    ]);
});

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/save-dashboard-data', [DashboardController::class, 'savedashboarddata'])->name('savedashboarddata');
    Route::resource('roles', RolesController::class);
    Route::resource('admins', AdminsController::class);
    Route::resource('sites', SiteController::class);
    Route::get('/admin-sites', [SiteController::class, 'AdminSites'])->name('admin.sites');
    Route::get('/site-data/{slug}', [SiteController::class, 'fetchLatestData'])->name('site.fetchData');

    // For Mobile Application
    Route::get('/notification-list', [SiteController::class, 'apiFetchDevice'])->name('notification.list');
    Route::get('/notification-create', [SiteController::class, 'NotificationCreate'])->name('notification.create');
    Route::get('/edit-notification-form/{deviceId}', [SiteController::class, 'NotificationEdit'])->name('notification.edit');
    Route::post('/store-device-events', [SiteController::class, 'apiStoreDevice']);
    Route::put('/update-device-events/{deviceId}', [SiteController::class, 'apiUpdateDevice']);

    // Login Routes.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/submit', [LoginController::class, 'login'])->name('login.submit');

    // Logout Routes.
    Route::post('/logout/submit', [LoginController::class, 'logout'])->name('logout.submit');

    // Forget Password Routes.
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/reset/submit', [ForgotPasswordController::class, 'reset'])->name('password.update');
    
    // Fetch DG Status
    Route::post('/site/statuses', [SiteController::class, 'fetchStatuses'])->name('site.statuses');

    Route::delete('/delete-device-events/{deviceId}', [SiteController::class, 'apiDeleteDevice']);
})->middleware('auth:admin');