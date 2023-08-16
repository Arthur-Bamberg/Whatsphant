<?php

class globals {
    private function __construct() {}

    public static function start() {
        self::defineGlobals();
        self::defineSecrets();
    }

    private static function defineGlobals() {
        $globals = json_decode(file_get_contents(__DIR__ . "/../../storage/globals.json"), true);
        
        foreach ($globals as $key => $value) {
            define($key, $value);
        }
    }

    private static function defineSecrets() {
        $secrets = json_decode(file_get_contents(__DIR__ . "/../../storage/secrets.json"), true);
        
        foreach ($secrets as $key => $value) {
            define($key, $value);
        }
    }
}

globals::start();