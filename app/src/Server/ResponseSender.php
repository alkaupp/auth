<?php

declare(strict_types=1);

namespace Auth\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ResponseSender
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent($this->response->getBody());
    }

    private function sendHeaders(): void
    {
        header(
            sprintf(
                'HTTP/%s %s %s',
                $this->response->getProtocolVersion(),
                $this->response->getStatusCode(),
                $this->response->getReasonPhrase()
            ),
            true,
            $this->response->getStatusCode()
        );

        if (headers_sent()) {
            return;
        }
        foreach ($this->response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $header, $value), false, $this->response->getStatusCode());
            }
        }
    }

    private function sendContent(StreamInterface $content): void
    {
        echo $content->__toString();
    }
}
