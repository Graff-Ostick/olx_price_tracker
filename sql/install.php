<?php

$config = require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
        $config['db']['user'],
        $config['db']['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($pdo) {
        echo "Connect to db success.\n";
    } else {
        echo "Connect to db fail.\n";
        exit;
    }


    $createTableSQL = <<<SQL
        CREATE TABLE IF NOT EXISTS subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            last_price DECIMAL(10, 2) DEFAULT NULL,
            currency VARCHAR(255) DEFAULT 'UAH',
            is_verified TINYINT(1) DEFAULT 0,
            token VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (url, email)
        );
SQL;

    $pdo->exec($createTableSQL);
    echo "Table 'subscriptions' created or already exist.\n";

} catch (PDOException $e) {
    echo "Error, cant connect to db:\n" . $e->getMessage() . "\n";
    exit;
}
