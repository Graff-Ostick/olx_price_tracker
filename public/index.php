<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Repositories\SubscriptionRepository;
use Services\EmailService;
use Services\HttpClient;
use Services\PriceTrackerService;
use Services\Validator;

$log = new Logger('olx_price_tracker');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));

$config = require __DIR__ . '/../config/config.php';
$pdo = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
    $config['db']['user'],
    $config['db']['password']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$subscriptionRepository = new SubscriptionRepository($pdo);
$httpClient = new HttpClient($log);
$emailService = new EmailService($config['mail']['from'], $config['mail']['from_name']);
$priceTrackerService = new PriceTrackerService($subscriptionRepository, $httpClient, $emailService, $log);
$validator = new Validator();

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']  === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if (!$validator->validateSubscriptionData($data)) {
            throw new Exception('Invalid input data');
        }

        if ($subscriptionRepository->addSubscription($data['url'], $data['email'])) {
            $log->info("Subscription added: {$data['url']} for email: {$data['email']}");
            sendApiResponse('success', 'Subscription added successfully!');
        } else {
            throw new Exception('Failed to add subscription');
        }
    } else {
        throw new Exception('Unsupported content type');
    }
} else {
    $priceTrackerService->checkForPriceChanges();
}

function sendApiResponse(string $status, string $message, array $data = []): void
{
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
}
