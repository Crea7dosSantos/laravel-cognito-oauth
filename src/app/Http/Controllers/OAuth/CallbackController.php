<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;

final class CallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $state = $request->session()->pull('state');

        $codeVerifier = $request->session()->pull('code_verifier');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $client = Client::where('name', 'MPA')->first();

        $response = Http::asForm()->post('http://host.docker.internal:80/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $client->id,
            'redirect_uri' => 'http://localhost/auth/callback',
            'code_verifier' => $codeVerifier,
            'code' => $request->code,
        ]);

        return redirect()->route('home');
        // return $response->json();
    }
}
