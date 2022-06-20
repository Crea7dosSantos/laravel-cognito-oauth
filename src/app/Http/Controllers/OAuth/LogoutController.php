<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class LogoutController extends Controller
{
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $request->user()->revokeTokens();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (isset($request->logout_uri)) {
            return redirect($request->logout_uri);
        }

        return redirect()->route('welcome');
    }
}
