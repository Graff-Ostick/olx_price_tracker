<?php

namespace Services;

use Monolog\Logger;

class HttpClient
{
    /**
     * @var Logger
     */
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @return string
     * @throws \RuntimeException
     */
    public function get(string $url): string
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Тайм-аут у секундах

        $response = $this->curlExecWrapper($ch);

        if (curl_errno($ch)) {
            $error_message = curl_error($ch);
            curl_close($ch);
            $this->logger->error("CURL request failed for URL: $url. Error: $error_message");
            throw new \RuntimeException("CURL request failed: $error_message");
        }

        curl_close($ch);

        if ($response === false) {
            $this->logger->error("CURL request returned false for URL: $url");
            throw new \RuntimeException('CURL request returned false, unable to get response.');
        }

        return $response;
    }

    protected function curlExecWrapper($ch)
    {
        return curl_exec($ch);
    }
}
