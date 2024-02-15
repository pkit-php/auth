<?php
use Pkit\Auth\Session;
use Pkit\Middlewares\Auth;

test('session login', function ($session) {
    Session::config(1);
    $time = (new DateTime())->format('Y-m-d H:i:s');
    Session::login($session);
    expect(Session::getCreated())->toEqual($time);
    expect(Session::getSession())->toEqual($session);
    Session::logout();
    expect(Session::getSession())->toBeNull();
})->with([
            "",
            [1, 2, 3],
            ["a" => "b", "c" => "d"],
            new stdClass(),
            new Auth()
        ]);

test('session middleware login not expires', function ($session) {
    Session::config(1);
    Session::login($session);
    sleep(1);
    expect((new Auth())->__invoke($session, fn($arg) => $arg, "session"))->toEqual($session);
    Session::logout();
})->with([""]);

test('session middleware login expires', function ($session) {
    Session::config(1);
    Session::login($session);
    sleep(2);
    expect(fn() => (new Auth())->__invoke($session, fn($arg) => $arg, "session"))->toThrow(Exception::class);
    Session::logout();
})->with([""]);

