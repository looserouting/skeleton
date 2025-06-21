<?php
declare(strict_types=1);

use App\App;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../vendor/autoload.php';

// Initialize and run the application
$app = new App();
$app->run();
