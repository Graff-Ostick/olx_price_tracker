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

try {
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
        $config['db']['user'],
        $config['db']['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $log->critical('Database connection failed: ' . $e->getMessage());
    sendApiResponse('error', 'Database connection failed. Please try again later.');
    exit;
}

$subscriptionRepository = new SubscriptionRepository($pdo);
$httpClient = new HttpClient($log);
$emailService = new EmailService($config['mail']['from'], $config['mail']['from_name']);
$priceTrackerService = new PriceTrackerService($subscriptionRepository, $httpClient, $emailService, $log);
$validator = new Validator();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handlePostRequest($validator, $subscriptionRepository, $emailService, $log);
    } elseif (isset($_GET['token'])) {
        handleTokenVerification($subscriptionRepository, $priceTrackerService, $log);
    } else {
        $priceTrackerService->checkForPriceChanges();
    }
} catch (Exception $e) {
    $log->error('Unhandled exception: ' . $e->getMessage());
    sendApiResponse('error', $e->getMessage());
}

/**
 * Handle POST requests to add a subscription
 */
function handlePostRequest(Validator $validator, SubscriptionRepository $repository, EmailService $emailService, Logger $log): void
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (strpos($contentType, 'application/json') === false) {
        throw new Exception('Unsupported content type. Expected application/json.');
    }

    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload.');
    }

    if (!$validator->validateSubscriptionData($data)) {
        throw new Exception('Invalid input data.');
    }

    $token = bin2hex(random_bytes(16));

    if ($repository->addSubscription($data['url'], $data['email'], $token)) {
        $emailService->sendVerificationEmail($data['email'], $token);
        $log->info("Subscription added: {$data['url']} for email: {$data['email']}");
        sendApiResponse('success', 'Subscription added successfully!');
    } else {
        throw new Exception('Failed to add subscription.');
    }
}

/**
 * Handle token verification for email confirmation
 */
function handleTokenVerification(SubscriptionRepository $repository, PriceTrackerService $service, Logger $log): void
{
    $token = $_GET['token'];

    $subscription = $repository->getSubscriptionByToken($token);

    if ($subscription) {
        $repository->setVerifiedById($subscription['id']);
        $priceCurrency = $service->fetchCurrentPrice($subscription['url']);
        $repository->updatePriceAndCurrency($subscription['id'], $priceCurrency['price'], $priceCurrency['currency']);
        sendApiResponse('success', 'Your email has been confirmed for this subscription!');
        $log->info("Email confirmed: {$subscription['email']} for list: {$subscription['url']}");
    } else {
        throw new Exception('Invalid token.');
    }
}

/**
 * Send API response in JSON format
 */
function sendApiResponse(string $status, string $message, array $data = []): void
{
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
}