<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Bootstrap;

Bootstrap::loadEnv(dirname(__DIR__));

putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';
