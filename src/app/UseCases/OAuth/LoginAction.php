<?php

namespace App\UseCases\OAuth;

use App\Exceptions\Admin\OperatorNotExistedException;
use App\Exceptions\NotExistsUserException;
use App\Models\User;
use App\Services\Cognito;
use App\Services\JWTVerifier;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class LoginAction
{
    private $service;
    private $jwt_verifier;

    public function __construct(Cognito $service, JWTVerifier $jwt_verifier)
    {
        $this->service = $service;
        $this->jwt_verifier = $jwt_verifier;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void|CognitoIdentityProviderException|OperatorNotExistedException|NotExistsUserException
     */
    public function __invoke(Request $request): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user = User::where('email', $request->email)->first();

        try {
            $response = $this->service->auth($user->cognito_username, $request->password);
        } catch (CognitoIdentityProviderException $e) {
            throw $e;
        }

        $jwt = $response->toArray()['AuthenticationResult']['IdToken'];
        $decoded = $this->jwt_verifier->decode($jwt);

        if (!User::where('cognito_sub', $decoded->sub)->exists()) {
            throw new NotExistsUserException('入力されたログイン情報に一致するユーザーが見つかりません');
        }

        Log::debug('認証に成功しました');

        Auth::loginUsingId($user->id);
        $user->updateExpiredAt();
    }
}
