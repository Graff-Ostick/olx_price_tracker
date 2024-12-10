<?php

use PHPUnit\Framework\TestCase;
use Services\Validator;

class ValidatorTest extends TestCase
{
    public function testValidateSubscriptionData()
    {
        $validator = new Validator();

        $validData = [
            'url' => 'https://example.com',
            'email' => 'test@example.com'
        ];

        $invalidData1 = [
            'url' => '',
            'email' => 'test@example.com'
        ];

        $invalidData2 = [
            'url' => 'https://example.com',
            'email' => 'invalid-email'
        ];

        $this->assertTrue($validator->validateSubscriptionData($validData));
        $this->assertFalse($validator->validateSubscriptionData($invalidData1));
        $this->assertFalse($validator->validateSubscriptionData($invalidData2));
    }
}
