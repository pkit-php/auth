<?php

namespace Pkit\Auth\Jwt;


class JwtEnv
{
    public static function config(string $key = "HS256", $expire = 0, $alg = 'HS256')
    {
        putenv("JWT_KEY=$key");
        putenv("JWT_EXPIRES=$expire");
        putenv("JWT_ALG=$alg");

    }

    public static function getAlg(): string
    {
        return getenv("JWT_ALG") ?:'HS256';
    }

    public static function getExpire(): int
    {
        return (int) getenv("JWT_EXPIRES") ?: 0;
    }

    public static function getKey(): string
    {
        return getenv("JWT_KEY") ?:"";
    }
}
