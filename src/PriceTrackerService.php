<?php

class PriceTrackerService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function checkPrices()
    {
        $client = new Client();
        $subscriptions = $this->db->query("SELECT * FROM subscriptions");

        foreach ($subscriptions as $sub) {
            // Парсинг OLX
            $crawler = $client->request('GET', $sub['ad_url']);
            $priceText = $crawler->filter('.price')->text();
            $price = preg_replace('/[^\d]/', '', $priceText);

            if ($price !== $sub['last_price']) {
                // Ціна змінилась
                $this->db->execute("UPDATE subscriptions SET last_price = ? WHERE id = ?", [$price, $sub['id']]);
                $this->notifyUser($sub['email'], $sub['ad_url'], $price);
            }
        }
    }

    private function notifyUser($email, $url, $price)
    {
        $subject = "Ціна змінилася!";
        $message = "Ціна оголошення за посиланням $url змінилася на $price.";
        mail($email, $subject, $message);
    }
}
