<?php

declare(strict_types=1);

use App\Core\Application;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

Application::loadEnv(BASE_PATH . '/.env');

$app = new Application();
$app->run();
