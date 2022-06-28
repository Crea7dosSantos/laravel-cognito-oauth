<?php

namespace App\Http\Controllers\OAuth;

use App\Enums\SocialProvider;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JWTVerifier;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class CognitoController extends Controller
{
    use RedirectsUsers;

    /**
     * return redirect response for cognito authorization endpoint
     *
     * @param Request $request
     * @param SocialProvider $social_provider
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function redirect(Request $request, SocialProvider $social_provider)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $request->session()->put('cognito_state', $state = Str::random(40));

        $request->session()->put(
            'cognito_code_verifier',
            $code_verifier = Str::random(128)
        );

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true)),
            '='
        ), '+/', '-_');

        $query = http_build_query([
            'client_id' => env('AWS_COGNITO_CLIENT_ID'),
            'identity_provider' => $social_provider->identityProvider(),
            'redirect_uri' => env('APP_URL') . env('AWS_COGNITO_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect('https://' . env('AWS_COGNITO_DOMAIN') . '/oauth2/authorize?' . $query);
    }

    /**
     * verify query parmeter and request cognito token endpoint
     *
     * @param Request $request
     * @param JWTVerifier $jwt_verifier
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function callback(Request $request, JWTVerifier $jwt_verifier): \Illuminate\Http\RedirectResponse
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $code_verifier = $request->session()->pull('cognito_code_verifier');
        $state = $request->session()->pull('cognito_state');

        if (strlen($state) <= 0 || $state !== $request->state) {
            Log::debug('指定されたstateは保存したstateと一致しませんでした');

            return redirect()
                ->route('login')
                ->withErrors(['リクエストで指定された値に不正が見つかりました']);
        }

        $response = Http::asForm()->post(
            'https://' . env('AWS_COGNITO_DOMAIN') . '/oauth2/token',
            [
                'grant_type' => 'authorization_code',
                'scope' => 'openid',
                'client_id' => env('AWS_COGNITO_CLIENT_ID'),
                'client_secret' => env('AWS_COGNITO_CLIENT_SECRET'),
                'code' => $request->code,
                'code_verifier' => $code_verifier,
                'redirect_uri' => env('APP_URL') . env('AWS_COGNITO_REDIRECT_URI'),
            ]
        );

        $jwt = $response['id_token'];
        $decoded = $jwt_verifier->decode($jwt);

        $condition_query = User::where('cognito_google_sub', $decoded->sub)->orWhere('cognito_apple_sub', $decoded->sub);

        if (!$condition_query->exists()) {
            Log::debug("sub: {$decoded->sub}のソーシャルアカウントの登録情報は見つかりませんでした");

            return redirect()
                ->route('login')
                ->withErrors(['システムに対象のソーシャルアカウントの登録情報はありませんでした']);
        }

        $user = $condition_query->first();
        Auth::loginUsingId($user->id);
        $user->updateExpiredAt();

        return $request->wantsJson()
            ? new JsonResponse([], Response::HTTP_NO_CONTENT)
            : redirect()->intended($this->redirectPath());
    }
}
