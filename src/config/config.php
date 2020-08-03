<?php
//// Configuration for Database
if ($_ENV['ENV'] === 'prod'){
    define("DB_HOST", $_ENV['DB_HOST']);
    define("DB_USER", $_ENV['DB_USER']);
    define("DB_PASS", $_ENV['DB_PASS']);
    define("DB_NAME", $_ENV['DB_NAME']);
}else{
    define("DB_HOST", 'localhost');
    define("DB_USER", 'root');
    define("DB_PASS", '');
    define("DB_NAME", 'coding_jobs');
}