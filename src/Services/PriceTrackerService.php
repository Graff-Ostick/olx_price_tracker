<?php

namespace Services;

use Monolog\Logger;
use Repositories\SubscriptionRepository;

class PriceTrackerService {
    private SubscriptionRepository $repository;
    private HttpClient $httpClient;
    private EmailService $emailService;
    private Logger $logger;

    public function __construct(
        SubscriptionRepository $repository,
        HttpClient $httpClient,
        EmailService $emailService,
        Logger $logger
    ) {
        $this->repository = $repository;
        $this->httpClient = $httpClient;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @return array
     */
    public function fetchCurrentPrice(string $url): array {
        $html = $this->httpClient->get($url);

        preg_match('/"priceCurrency":"([A-Z]+)","price":([0-9.]+)/', $html, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            return ['price' => (float)$matches[2], 'currency' => $matches[1]];
        }

        throw new \RuntimeException('Price not found on the page.');
    }

    /**
     * @return void
     */
    public function checkForPriceChanges(): void {
        $subscriptions = $this->repository->getSubscriptions();

        foreach ($subscriptions as $subscription) {
            try {
                $currentPriceData = $this->fetchCurrentPrice($subscription['url']);
                $lastPrice = $subscription['lastPrice'];

                if ($lastPrice !== $currentPriceData['price']) {
                    $this->repository->updatePriceAndCurrency(
                        $subscription['id'],
                        $currentPriceData['price'],
                        $currentPriceData['currency']
                    );

                    $this->sendPriceChangeNotification(
                        $subscription['email'],
                        $subscription['url'],
                        $lastPrice,
                        $currentPriceData['price'],
                        $currentPriceData['currency']
                    );
                }
            } catch (\Exception $e) {
                 $this->logger->error("Error updating subscription ID {$subscription['id']}: " . $e->getMessage());
            }
        }
    }

    /**
     * @param string $email
     * @param string $url
     * @param float $oldPrice
     * @param float $newPrice
     * @param string $currency
     * @return void
     */
    private function sendPriceChangeNotification(string $email, string $url, float $oldPrice, float $newPrice, string $currency): void {
        $subject = "Price change notification for your subscription!";
        $body = "
            <h1>Price Change Alert</h1>
            <p>The price for the listing <a href=\"$url\">$url</a> has changed.</p>
            <p>Old price: $oldPrice $currency</p>
            <p>New price: $newPrice $currency</p>
            <p>Stay tuned for more updates!</p>
        ";

        $this->emailService->sendEmail($email, $subject, $body);
    }
}
