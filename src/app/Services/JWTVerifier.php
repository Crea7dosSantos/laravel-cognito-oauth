<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class JWTVerifier
{
    // Allows a 5 second tolerance on timing checks
    public static $leeway = 5; //5秒の時刻ズレの誤差を許容

    /**
     * decode jwt
     *
     * @param string $jwt
     * @return object|null
     */
    public function decode(string $jwt)
    {
        $tks = explode('.', $jwt);
        if (count($tks) !== 3) {
            return null;
        }

        [$headb64, $_, $_] = $tks;

        $jwks = $this->fetchJWKs();
        try {
            $kid = $this->getKid($headb64);
            $jwk = $this->getJWK($jwks, $kid);
            $alg = $this->getAlg($jwks, $kid);
            return JWT::decode($jwt, $jwk, [$alg]);
        } catch (\RuntimeException $exception) {
            Log::error('jwtのdecodeに失敗しました');
            Log::error($exception->getMessage());
            return null;
        }
    }

    /**
     * get kid from encoded header part
     *
     * @param string $headb64
     * @return string|RuntimeException
     */
    private function getKid(string $headb64)
    {
        $headb64 = json_decode(JWT::urlsafeB64Decode($headb64), true);
        if (array_key_exists('kid', $headb64)) {
            return $headb64['kid'];
        }
        throw new \RuntimeException();
    }

    /**
     * get jwk from kid
     *
     * @param array $jwks
     * @param string $kid
     * @return void
     */
    private function getJWK(array $jwks, string $kid)
    {
        $keys = JWK::parseKeySet($jwks);
        if (array_key_exists($kid, $keys)) {
            return $keys[$kid];
        }
        throw new \RuntimeException();
    }

    /**
     * get algorithm from kid of jwt header
     *
     * @param array $jwks
     * @param string $kid
     * @return void
     */
    private function getAlg(array $jwks, string $kid)
    {
        if (!array_key_exists('keys', $jwks)) {
            throw new \RuntimeException();
        }

        foreach ($jwks['keys'] as $key) {
            if ($key['kid'] === $kid && array_key_exists('alg', $key)) {
                return $key['alg'];
            }
        }
        throw new \RuntimeException();
    }

    /**
     * fetch jwk array
     *
     * @return array
     */
    private function fetchJWKs(): array
    {
        $user_pool_id = env('AWS_COGNITO_USER_POOL_ID');
        $region = env('AWS_DEFAULT_REGION');

        $response = Http::get("https://cognito-idp.$region.amazonaws.com/$user_pool_id/.well-known/jwks.json");
        // Log::debug($response->getBody()->getContents());
        return json_decode($response->getBody()->getContents(), true) ?: [];

        // return json_decode(file_get_contents(public_path('jwks.json')), true) ?: [];
    }
}
