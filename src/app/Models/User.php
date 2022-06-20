<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cognito_username',
        'cognito_sub',
        'cognito_google_sub',
        'cognito_apple_sub',
        'name',
        'email',
        'expired_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function oauthAcessToken(): HasMany
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    /**
     * revoke user token
     *
     * @return void
     */
    public function revokeTokens(): void
    {
        $this->tokens
            ->each(function ($token, $key) {
                $this->revokeAccessAndRefreshTokens($token->id);
            });
    }

    /**
     * revoke token and refresh token
     *
     * @param [type] $tokenId
     * @return void
     */
    private function revokeAccessAndRefreshTokens($tokenId): void
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }

    /**
     * update expired time
     *
     * @return void
     */
    public function updateExpiredAt(): void
    {
        $now_at = Carbon::now();
        $this->expired_at = $now_at->addMinutes(3);
        $this->save();
    }
}
