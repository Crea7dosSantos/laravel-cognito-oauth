<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Client;

final class PassportController extends Controller
{
    /**
     * return redirect response for authorization endpoint
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function redirect(Request $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $request->session()->put('state', $state = Str::random(40));

        $request->session()->put(
            'code_verifier',
            $code_verifier = Str::random(128)
        );

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true)),
            '='
        ), '+/', '-_');

        $client = Client::where('name', 'MPA')->first();

        $query = http_build_query([
            'client_id' => $client->id,
            'redirect_uri' => env('APP_URL') . '/auth/callback',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect(env('APP_URL') . '/oauth/authorize?' . $query);
    }

    /**
     * verify query parmeter and request token endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|InvalidArgumentException
     */
    protected function callback(Request $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $state = $request->session()->pull('state');
        $code_verifier = $request->session()->pull('code_verifier');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class,
            'リクエストに付与された認可コードで不正な値が使用されています'
        );

        $client = Client::where('name', 'MPA')->first();

        Http::asForm()->post('http://host.docker.internal:80/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $client->id,
            'redirect_uri' => env('APP_URL') . '/auth/callback',
            'code_verifier' => $code_verifier,
            'code' => $request->code,
        ]);

        return redirect()->route('home');
    }
}
