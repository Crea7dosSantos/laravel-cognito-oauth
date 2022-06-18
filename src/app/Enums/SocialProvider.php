<?php

namespace App\Enums;

enum SocialProvider: string
{
    case GOOGLE = 'google';
    case APPLE = 'apple';

    /**
     * return description social provider type
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::GOOGLE => 'Google',
            self::APPLE => 'Apple',
        };
    }

    /**
     * return description social provider type
     *
     * @return string
     */
    public function identityProvider(): string
    {
        return match ($this) {
            self::GOOGLE => 'Google',
            self::APPLE => 'SignInWithApple',
        };
    }
}
