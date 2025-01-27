<?php

require_once "vendor/autoload.php";


\Simp\Environment\Environment::create("server_host", [
    "host" => "localhost",
    "port" => 3306,
    "database" => "database",
    "username" => "chance_",
    "password" => "12345",
    "prefix" => "chance_"
]);

dump(\Simp\Environment\Environment::load('server_host'));