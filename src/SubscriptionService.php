<?php

class SubscriptionService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function subscribe($url, $email)
    {
        $exists = $this->db->query("SELECT * FROM subscriptions WHERE ad_url = ? AND email = ?", [$url, $email]);

        if ($exists) {
            return ['status' => 'error', 'message' => 'Ви вже підписані на це оголошення'];
        }

        $this->db->execute("INSERT INTO subscriptions (ad_url, email, last_price) VALUES (?, ?, ?)", [$url, $email, null]);

        return ['status' => 'success', 'message' => 'Підписка успішно створена'];
    }
}
