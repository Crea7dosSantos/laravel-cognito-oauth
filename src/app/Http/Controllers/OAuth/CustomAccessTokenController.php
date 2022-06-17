<?php

namespace App\Http\Controllers\OAuth;

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

        return $this->issueToken($request);
    }
}
