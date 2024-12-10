<?php
use PHPUnit\Framework\TestCase;
use Services\EmailService;

class EmailServiceTest extends TestCase
{
    public function testSendEmailSuccess()
    {

        $mailerMock = Mockery::mock('alias:mail');
        $mailerMock->shouldReceive('mail')
            ->once()
            ->with('test@example.com',
                'Test Subject',
                'Test Body',
                'From: Test Sender <no-reply@example.com>' .
                "\r\n" . 'Reply-To: no-reply@example.com' . "\r\n" . 'Content-Type: text/html; charset=UTF-8"')
            ->andReturn(true);

        $emailService = new EmailService('no-reply@example.com', 'Test Sender');
        $result = $emailService->sendEmail('test@example.com', 'Test Subject', 'Test Body');

        $this->assertEquals('Email sent successfully!', $result);
    }

    public function testSendEmailFailure()
    {
        $mailerMock = Mockery::mock('alias:mail');
        $mailerMock->shouldReceive('mail')
            ->once()
            ->with('invalid-email', 'Test Subject', 'Test Body', 'From: Test Sender <no-reply@example.com>' . "\r\n" . 'Reply-To: no-reply@example.com' . "\r\n" . 'Content-Type: text/html; charset=UTF-8"')
            ->andReturn(false);

        $emailService = new EmailService('no-reply@example.com', 'Test Sender');
        $result = $emailService->sendEmail('invalid-email', 'Test Subject', 'Test Body');

        $this->assertEquals('Error: Failed to send email.', $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
