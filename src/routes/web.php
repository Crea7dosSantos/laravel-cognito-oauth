<?php

use App\Http\Controllers\OAuth\CognitoController;
use App\Http\Controllers\OAuth\LoginController;
use App\Http\Controllers\OAuth\LogoutController;
use App\Http\Controllers\OAuth\PassportController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::controller(LoginController::class)->prefix('login')->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/', 'store')->name('login');
});
Route::controller(PassportController::class)->group(function () {
    Route::get('/redirect', 'redirect')->name('redirect');
    Route::get('/auth/callback', 'callback')->name('callback');
});
Route::controller(CognitoController::class)->prefix('cognito')->group(function () {
    Route::get('redirect/{social_provider}', 'redirect')->name('cognito.redirect');
    Route::get('callback', 'callback')->name('cognito.callback');
});

Route::middleware(['auth:web', 'update.expiration'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/logout', LogoutController::class)->name('logout');
});
