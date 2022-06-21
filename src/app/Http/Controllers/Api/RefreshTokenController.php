<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RefreshToken\InvokeRequest;
use App\Models\User;
use Carbon\Carbon;
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

    public function __invoke(InvokeRequest $request): JsonResponse
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $bearer_token = $request->access_token;
        $jti = Configuration::forUnsecuredSigner()->parser()->parse($bearer_token)->claims()->get('jti');
        $decoded = $this->token_repository->find($jti);
        if (is_null($decoded)) {
            return new JsonResponse(['message' => '指定されたパラメータに不正な為、更新トークンを利用できません'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($decoded->user_id);

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

        Log::debug($response);
        if ($response->failed()) {
            return new JsonResponse(['message' => $response->json('message')], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse($response->json(), Response::HTTP_OK);
    }
}
