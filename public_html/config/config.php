<?php

define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');
define('DB_PASS', '123456');
define('DB_NAME', 'web_whey_center');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}