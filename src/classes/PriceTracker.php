<?php

namespace Classes;

class PriceTracker
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all active subscriptions
     *
     * @return array
     */
    public function getSubscriptions(): array
    {
        $stmt = $this->db->query('SELECT * FROM subscriptions');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get price from url page
     *
     * @param string $url
     * @return array
     */
    public function fetchCurrentPrice(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200 || !$html) {
            curl_close($ch);
            return [];
        }

        curl_close($ch);
        preg_match('/"priceCurrency":"([A-Z]+)","price":([0-9]+)\}/', $html, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            return [
                'currency' => $matches[1],
                'price' => (float)$matches[2]
            ];
        }

        return [];
    }


    /**
     * Send mail for price change
     *
     * @param string $email
     * @param string $url
     * @param float $price
     * @return bool
     */
    public function notifyUser(string $email, string $url, float $price): bool
    {
        $to = $email;
        $subject = "Price Update Notification";
        $message = "The price for the listing at $url has changed to $price.";
        $headers = "From: no-reply@example.com\r\n" .
            "Content-Type: text/plain; charset=UTF-8";

        return mail($to, $subject, $message, $headers);
    }

    /**
     * Update price in db for subscribes
     *
     * @param int $subscriptionId
     * @param float $newPrice
     * @return bool
     */
    public function updatePrice(int $subscriptionId, float $newPrice): bool
    {
        $stmt = $this->db->prepare('
                UPDATE subscriptions SET last_price = :new_price WHERE id = :id
            ');
        return $stmt->execute([
            'new_price' => $newPrice,
            'id' => $subscriptionId,
        ]);
    }
}
