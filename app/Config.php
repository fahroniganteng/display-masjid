<?php

include "vendor/autoload.php";

use Dotenv\Dotenv;

class Config
{

    private function __construct(){}
    public static function load(string $configName=null): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__,$configName);
        $dotenv->load();
    }

    public static function getAppUrl(){
        return $_ENV['APP_URL'] ?? '';
    }
}


Config::load();