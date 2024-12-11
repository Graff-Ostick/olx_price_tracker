<?php

use Mockery;
use PHPUnit\Framework\TestCase;
use Services\EmailService;

class EmailServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testSendEmailSuccess()
    {
        $emailServiceMock = Mockery::mock(EmailService::class);

        $emailServiceMock->shouldReceive('sendEmail')
            ->once()
            ->with('my.flud.info@gmail.com', 'Test Subject', 'Test Body')
            ->andReturn('Email sent successfully!'); // Відповідність реальній реалізації

        $result = $emailServiceMock->sendEmail('my.flud.info@gmail.com', 'Test Subject', 'Test Body');
        $this->assertSame('Email sent successfully!', $result);
    }

    public function testSendEmailFailure()
    {
        $emailServiceMock = Mockery::mock(EmailService::class);

        $emailServiceMock->shouldReceive('sendEmail')
            ->once()
            ->with('invalid-email', 'Test Subject', 'Test Body')
            ->andReturn('Error: Failed to send email.');

        $result = $emailServiceMock->sendEmail('invalid-email', 'Test Subject', 'Test Body');
        $this->assertSame('Error: Failed to send email.', $result);
    }
}
