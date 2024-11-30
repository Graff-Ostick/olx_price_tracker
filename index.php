<?php


require_once 'src/Database.php';
require_once 'src/SubscriptionService.php';

$db = new Database('sqlite:../database/price_tracker.sqlite');
$subscriptionService = new SubscriptionService($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['ad_url'];
    $email = $_POST['email'];

    $response = $subscriptionService->subscribe($url, $email);
    echo json_encode($response);
}
