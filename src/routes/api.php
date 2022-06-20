<?php

use App\Http\Controllers\Api\RefreshTokenController;
use Illuminate\Http\Request;
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

Route::post('/token/refresh', RefreshTokenController::class);

Route::middleware(['auth:api', 'update.expiration'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json($user);
    });
});
