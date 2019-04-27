<?php
declare(strict_types=1);

namespace Auth\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class RequestSender
{
    public function send(ResponseInterface $response): void
    {
        $this->sendBasicHeader($response);
        $this->sendHeaders($response->getHeaders(), $response->getStatusCode());
        $this->sendContent($response->getBody());
    }

    private function sendBasicHeader(ResponseInterface $response): void
    {
        header(
            sprintf(
                "HTTP/%s %s %s",
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ),
            true,
            $response->getStatusCode()
        );
    }

    private function sendHeaders(array $headers, int $statusCode): void
    {
        if (headers_sent()) {
            return;
        }
        foreach ($headers as $header => $values) {
            foreach ($values as $value) {
                header(sprintf("%s: %s", $header, $value), false, $statusCode);
            }
        }
    }

    private function sendContent(StreamInterface $content): void
    {
        echo $content->__toString();
    }
}
