<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();
//
//// Configuration for Database
//define("DB_HOST", getenv('DB_HOST'));
//define("DB_USER", getenv('DB_USER'));
//define("DB_PASS", getenv('DB_PASS'));
//define("DB_NAME", getenv('DB_NAME'));

define("DB_HOST", 'localhost');
define("DB_USER", 'oyaacoke_james');
define("DB_PASS", '31*66D9o0');
define("DB_NAME", 'oyaacoke_coding_jobs');

echo getenv('DB_USER');