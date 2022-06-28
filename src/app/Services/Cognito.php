<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

final class Cognito
{
    private $library_version;
    private $region;
    private $access_key;
    private $secret_key;
    private $client_id;
    private $client_secret;
    private $user_pool_id;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $this->library_version = 'latest';
        $this->region = env('AWS_DEFAULT_REGION');
        $this->access_key = env('AWS_ACCESS_KEY_ID');
        $this->secret_key = env('AWS_SECRET_ACCESS_KEY');
        $this->client_id = env('AWS_COGNITO_CLIENT_ID');
        $this->client_secret = env('AWS_COGNITO_CLIENT_SECRET');
        $this->user_pool_id = env('AWS_COGNITO_USER_POOL_ID');
    }

    /**
     * sign up
     * link here https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_SignUp.html
     *
     * @param string $username
     * @param string $password
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function signUp(string $username, string $password): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this
                ->adminInstantiation()
                ->signUp([
                    'ClientId' => $this->client_id,
                    'Username' => $username,
                    'Password' => $password,
                    'SecretHash' => $this->cognitoSecretHash($username),
                ]);
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * confirm sign up
     * link here https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminConfirmSignUp.html
     *
     * @param string $username
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function confirmSignUp(string $username): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this
                ->adminInstantiation()
                ->adminConfirmSignUp([
                    'Username' => $username,
                    'UserPoolId' =>  $this->user_pool_id
                ]);
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * sign out
     * link here https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminUserGlobalSignOut.html
     *
     * @param string $username
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function signOut(string $username): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this->adminInstantiation()
                ->adminUserGlobalSignOut([
                    'Username' => $username,
                    'UserPoolId' =>  $this->user_pool_id
                ]);
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * sign in
     * link here https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminInitiateAuth.html
     *
     * @param string $cognito_username
     * @param string $password
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function auth(string $cognito_username, string $password): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this->adminInstantiation()
                ->adminInitiateAuth(
                    [
                        'AuthFlow' => 'ADMIN_USER_PASSWORD_AUTH',
                        'ClientId' => $this->client_id,
                        'UserPoolId' => $this->user_pool_id,
                        'AuthParameters' => [
                            'USERNAME' => $cognito_username,
                            'PASSWORD' => $password,
                            'SECRET_HASH' => $this->cognitoSecretHash($cognito_username),
                        ],
                    ]
                );
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * refresh token
     * link here https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminInitiateAuth.html
     *
     * @param string $cognito_username
     * @param string $refresh_token
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function refresh(string $cognito_username, string $refresh_token): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this->adminInstantiation()
                ->adminInitiateAuth(
                    [
                        'AuthFlow' => 'REFRESH_TOKEN_AUTH',
                        'ClientId' => $this->client_id,
                        'UserPoolId' => $this->user_pool_id,
                        'AuthParameters' => [
                            'REFRESH_TOKEN' => $refresh_token,
                            'SECRET_HASH' => $this->cognitoSecretHash($cognito_username),
                        ],
                    ]
                );
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * set password
     * https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminSetUserPassword.html
     *
     * @param string $cognito_username
     * @param string $password
     * @return \Aws\Result|CognitoIdentityProviderException
     */
    public function setPassword(string $cognito_username, string $password): \Aws\Result
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $response = $this->adminInstantiation()
                ->adminSetUserPassword([
                    'Password' => $password,
                    'Permanent' => true,
                    'UserPoolId' => $this->user_pool_id,
                    'Username' => $cognito_username
                ]);;
        } catch (CognitoIdentityProviderException $e) {
            Log::error($e->getAwsErrorCode());
            Log::error($e->getAwsErrorType());
            Log::error($e->getAwsErrorMessage());

            throw $e;
        }

        return $response;
    }

    /**
     * get CognitoIdentityProviderClient object
     *
     * @return CognitoIdentityProviderClient
     */
    private function adminInstantiation(): CognitoIdentityProviderClient
    {
        return new CognitoIdentityProviderClient([
            'version' => $this->library_version,
            'region' => $this->region,
            'credentials' => [
                'key' => $this->access_key,
                'secret' => $this->secret_key,
            ],
        ]);
    }

    /**
     * get secret hash
     *
     * @param string $username
     * @return string
     */
    private function cognitoSecretHash(string $username): string
    {
        $hash = hash_hmac('sha256', $username . $this->client_id, $this->client_secret, true);
        return base64_encode($hash);
    }
}
