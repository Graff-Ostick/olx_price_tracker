<?php
namespace Classes;

class Subscription {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addSubscription($url, $email) {
        $stmt = $this->db->prepare(
            'INSERT INTO subscriptions (url, email) VALUES (:url, :email)'
        );
        return $stmt->execute(['url' => $url, 'email' => $email]);
    }
}
