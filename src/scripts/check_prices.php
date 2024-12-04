<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../classes/PriceTracker.php';

use Classes\PriceTracker;

$config = require __DIR__ . '/../../config/config.php';
$db = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
    $config['db']['user'],
    $config['db']['password']
);

$tracker = new PriceTracker($db);

$subscriptions = $tracker->getSubscriptions();

foreach ($subscriptions as $sub) {
    $currentPrice = $tracker->fetchCurrentPrice($sub['url']);
    if ($currentPrice !== $sub['last_price']) {
        $tracker->notifyUser($sub['email'], $sub['url'], $currentPrice);
        $tracker->updatePrice($sub['id'], $currentPrice);
    }
}
