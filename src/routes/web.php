<?php

use App\Http\Controllers\OAuth\CallbackController;
use App\Http\Controllers\OAuth\LoginController;
use App\Http\Controllers\OAuth\LogoutController;
use App\Http\Controllers\OAuth\RedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
});

Route::get('set-session', function (Request $request) {
    $request->session()->put('hoge', 'sessionが入るよ');
});
Route::get('get-session', function (Request $request) {
    $hoge = $request->session()->get('hoge');
    Log::debug($hoge);
    echo $hoge;
});

Route::controller(LoginController::class)->prefix('login')->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/', 'store')->name('login');
});
Route::post('/logout', LogoutController::class)->name('logout');
Route::get('/redirect', RedirectController::class)->name('redirect');
Route::get('/auth/callback', CallbackController::class)->name('callback');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
