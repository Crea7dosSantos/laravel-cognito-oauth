<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WriteExpirationTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $before_expired_at = $request->session()->get('expired_at');
        Log::debug("更新前の最終有効期限: {$before_expired_at}");

        $now_at = Carbon::now();
        $request->session()->put('expired_at', $now_at->addMinutes(30));
        $expired_at = $request->session()->get('expired_at');

        Log::debug("保存した最終有効期限: {$expired_at}");

        return $next($request);
    }
}
