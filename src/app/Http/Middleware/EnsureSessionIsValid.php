<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureSessionIsValid
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

        $user = $request->user();

        $expired_at = new Carbon($user->expired_at);
        Log::debug("Session lifetimeが有効な時刻: $expired_at");

        if ($expired_at->isPast()) {
            Log::debug("Sessionの有効時間を過ぎているので、ログアウトしてトークンを全て無効化します");

            $this->logout($user);

            return redirect()->route('redirect');
        }

        return $next($request);
    }

    private function logout(User $user)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user->oauthAcessToken()->delete();
    }
}
