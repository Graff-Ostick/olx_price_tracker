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
            'email' => 'my.flud.info@gmail.com'
        ];

        $invalidData1 = [
            'url' => '',
            'email' => 'my.flud.info@gmail.com'
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
