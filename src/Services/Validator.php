<?php

namespace Services;

class Validator
{
    public function validateSubscriptionData(array $data): bool
    {
        if (empty($data['url']) || empty($data['email'])) {
            return false;
        }

        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
