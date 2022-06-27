<?php

namespace App\Http\Controllers\OAuth;

use App\Models\User;
use Carbon\Carbon;
use Defuse\Crypto\Crypto;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

final class CustomAccessTokenController extends AccessTokenController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function issueUserToken(ServerRequestInterface $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $parsed_body = $request->getparsedbody();
        if ($parsed_body['grant_type'] === 'refresh_token') {
            Log::debug('リフレッシュトークンなので、検証します');

            $token_repository = app('Laravel\Passport\TokenRepository');
            $decoded_refresh_token = $this->decodeRefreshToken($parsed_body['refresh_token']);
            $access_token = $token_repository->find($decoded_refresh_token['access_token_id']);

            if (is_null($access_token)) {
                return new JsonResponse(['message' => '指定されたパラメータに不正な為、更新トークンを利用できません'], Response::HTTP_UNAUTHORIZED);
            }

            $user = User::find($access_token->user_id);

            $expired_at = new Carbon($user->expired_at);
            Log::debug("Session lifetimeが有効な時刻: $expired_at");

            if ($expired_at->isPast()) {
                return new JsonResponse(['message' => '最終リクエストから時間が経っていて更新トークンを利用できません'], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $this->issueToken($request);
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
