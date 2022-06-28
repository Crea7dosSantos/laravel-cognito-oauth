<?php

namespace App\Http\Controllers\OAuth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\TokenRepository;
use Psr\Http\Message\ServerRequestInterface;

final class CustomAuthorizationController extends AuthorizationController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $psrRequest
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Laravel\Passport\TokenRepository  $tokens
     * @return \Illuminate\Http\Response
     */
    public function authorize(
        ServerRequestInterface $psrRequest,
        Request $request,
        ClientRepository $clients,
        TokenRepository $tokens
    ) {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        if ($request->user()) {
            Log::debug('認証が済んでいます');
            $expired_at = new Carbon($request->user()->expired_at);
            Log::debug("有効期限: $expired_at");

            if ($expired_at->isPast()) {
                Log::debug("Sessionの有効時間を過ぎているので、ログアウトしてトークンを全て無効化します");

                $request->user()->revokeTokens();

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }
        }

        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });

        $scopes = $this->parseScopes($authRequest);

        $token = $tokens->findValidToken(
            $user = $request->user(),
            $client = $clients->find($authRequest->getClient()->getIdentifier())
        );

        if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) ||
            $client->skipsAuthorization()
        ) {
            return $this->approveRequest($authRequest, $user);
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return $this->response->view('passport::authorize', [
            'client' => $client,
            'user' => $user,
            'scopes' => $scopes,
            'request' => $request,
            'authToken' => $authToken,
        ]);
    }
}
