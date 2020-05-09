<?php

declare(strict_types=1);

namespace Auth\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class FrontpageAction
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(301, ['Location' => 'https://github.com/alkaupp/auth']);
    }
}
