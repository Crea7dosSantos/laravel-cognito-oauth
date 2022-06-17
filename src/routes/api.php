<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

Route::post('set-session', function (Request $request) {
    $request->session()->put('hoge', 'apiからsessionが入るよ');
});
Route::get('get-session', function (Request $request) {
    $hoge = $request->session()->get('hoge');
    Log::debug($hoge);
    echo $hoge;
});

Route::middleware(['auth:api', 'write.expiration'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});
