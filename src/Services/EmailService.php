<?php

namespace Services;

class EmailService
{
    private $from;
    private $fromName;

    public function __construct($from, $fromName = 'Sender')
    {
        $this->from = $from;
        $this->fromName = $fromName;
    }

    public function sendEmail($to, $subject, $body)
    {
        $headers = 'From: ' . $this->fromName . ' <' . $this->from . '>' . "\r\n" .
            'Reply-To: ' . $this->from . "\r\n" .
            'Content-Type: text/html; charset=UTF-8';

        if (mail($to, $subject, $body, $headers)) {
            return 'Email sent successfully!';
        } else {
            return 'Error: Failed to send email.';
        }
    }
}