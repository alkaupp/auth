<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\Repository\ApplicationRepository;
use Auth\Service\Authorize;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizeAction
{
    /** @var ApplicationRepository */
    private $appRepository;

    public function __construct(ApplicationRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $authorize = new Authorize($this->appRepository);
        $body = json_decode((string) $request->getBody(), true);
        $appName = $body['appName'];
        $siteUrl = $body['siteUrl'];
        $secretKey = $body['secretKey'];
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($authorize($appName, $siteUrl, $secretKey))
        );
    }
}