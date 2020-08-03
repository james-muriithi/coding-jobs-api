<?php
require_once __DIR__.'/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();
//

//// Configuration for Database
define("DB_HOST", $_ENV['DB_HOST']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("DB_NAME", $_ENV['DB_NAME']);

//define("DB_HOST", 'localhost');
//define("DB_USER", 'root');
//define("DB_PASS", '');
//define("DB_NAME", 'coding_jobs');