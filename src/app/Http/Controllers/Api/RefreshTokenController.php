<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RefreshToken\InvokeRequest;
use App\Models\User;
use Carbon\Carbon;
use Defuse\Crypto\Crypto;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Configuration;
use Illuminate\Support\Facades\Log;


final class RefreshTokenController extends Controller
{
    private $token_repository;

    public function __construct()
    {
        $this->token_repository = app('Laravel\Passport\TokenRepository');
    }

    /**
     * 更新トークンを用いてトークンの更新を行います
     *
     * @param InvokeRequest $request
     * @return JsonResponse
     */
    public function __invoke(InvokeRequest $request): JsonResponse
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $decoded_refresh_token = $this->decodeRefreshToken($request->refresh_token);
        $access_token = $this->token_repository->find($decoded_refresh_token['access_token_id']);

        if (is_null($access_token)) {
            return new JsonResponse(['message' => '指定されたパラメータに不正な為、更新トークンを利用できません'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($access_token->user_id);

        $expired_at = new Carbon($user->expired_at);
        Log::debug("Session lifetimeが有効な時刻: $expired_at");

        if ($expired_at->isPast()) {
            return new JsonResponse(['message' => '最終リクエストから時間が経っていて更新トークンを利用できません'], Response::HTTP_UNAUTHORIZED);
        }

        $response = Http::asForm()->post('http://host.docker.internal:80/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $request->client_id,
            'client_secret' => env('MIX_SPA_CLIENT_SECRET_ID'),
            'refresh_token' => $request->refresh_token,
            'scope' => ''
        ]);

        if ($response->failed()) {
            return new JsonResponse(['message' => $response->json('message')], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse($response->json(), Response::HTTP_OK);
    }

    /**
     * 更新トークンをデコードした結果を返します
     *
     * @param string $refresh_token
     * @return void
     */
    public function decodeRefreshToken(string $refresh_token)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $enc_key = base64_decode(substr(env('APP_KEY'), 7));

        try {
            $crypto = Crypto::decryptWithPassword($refresh_token, $enc_key);
        } catch (Exception $exception) {
            return $exception;
        }

        return json_decode($crypto, true);
    }
}
