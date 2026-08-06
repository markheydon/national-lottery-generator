<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Application;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::loadEnv(dirname(__DIR__));

$app = new Application();
$response = $app->handleRequest();
$response->send();
