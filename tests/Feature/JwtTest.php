<?php
use Pkit\Auth\Jwt;
use Pkit\Middlewares\Auth;

test('encode and decode jwt', function ($payload, $alg, $tokenSecret) {
    Jwt::config($tokenSecret, 0, $alg);

    $token = Jwt::tokenize($payload);
    expect(Jwt::validate($token))->toBeTrue();
    $localPayload = Jwt::getPayload($token);
    expect($localPayload)->toEqual($payload);
})->with([
    [[]],
    [[1, 2, 3]],
    [["email"=> "email@email.com"]],
])->with(["HS256", "HS384", "HS512"])
->with(["", "abc", "123", "@a1#b2\$c3"]);

test("invalid tokens", function ($token) {
    expect(Jwt::validate($token))->toBeFalse();
})->with([
    [""],
    ["a"],
    ["a.as"],
    ["a.as.asd"],
    ["a.as.asd.asdf"],
    ["eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.WzEsMiwzXQ.hkiTNZG7paZiNKvPcrey84amy3icAutFh2Aaply49Wa"]
]);


test('jwt middleware login not expires', function ($payload) {
    Jwt::config("", 1, "HS256");

    $token = Jwt::tokenize($payload);
    sleep(1);
    $request = json_decode('{"headers":[]}');
    $request->headers = ["Authorization" => Jwt::createBearer($token)];
    expect((new Auth())->__invoke($request, fn($arg) => $arg, "jwt"))->toEqual($request);
})->with([[["a" => "b"]]]);

test('jwt middleware login expires', function ($payload) {
    Jwt::config("", 1, "HS256");

    $token = Jwt::tokenize($payload);
    sleep(2);
    $request = json_decode('{"headers":[]}');
    $request->headers = ["Authorization" => Jwt::createBearer($token)];
    expect(fn() => (new Auth())->__invoke($request, fn($arg) => $arg, "jwt"))->toThrow(Exception::class);
})->with([[["a" => "b"]]]);
