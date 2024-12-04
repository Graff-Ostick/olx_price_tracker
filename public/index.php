<?php

require_once '../config/config.php';
require_once '../src/classes/Subscription.php';

use Classes\Subscription;

$config = require '../config/config.php';
$db = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
    $config['db']['user'],
    $config['db']['password']
);

$subscription = new Subscription($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $url = $data['url'];
    $email = $data['email'];

    if ($subscription->addSubscription($url, $email)) {
        echo json_encode(['status' => 'success', 'message' => 'Subscribed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Subscription failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
