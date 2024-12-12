<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'default_db',
        'user' => $_ENV['DB_USER'] ?? 'default_user',
        'pass' => $_ENV['DB_PASS'] ?? 'default_pass',
    ],
    'mail' => [
        'from' => $_ENV['MAIL_FROM'] ?? 'example@example.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Olx Price Tracker',
    ]
];
