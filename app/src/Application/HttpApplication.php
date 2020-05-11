<?php

declare(strict_types=1);

namespace Auth\Application;

use Auth\Server\ResponseSender;
use InvalidArgumentException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Throwable;

class HttpApplication
{
    private Router $router;
    private ServerRequestCreatorInterface $requestCreator;

    public function __construct(Router $router, ServerRequestCreatorInterface $requestCreator)
    {
        $this->router = $router;
        $this->requestCreator = $requestCreator;
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch($this->requestCreator->fromGlobals());
        } catch (NotFoundException $e) {
            $response = $this->createErrorResponse(404, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $response = $this->createErrorResponse(400, $e->getMessage());
        } catch (Throwable $e) {
            $response = $this->createErrorResponse(500, $e->getMessage());
        }
        (new ResponseSender($response))->send();
    }

    private function createErrorResponse(int $statusCode, string $errorMessage): Response
    {
        return new Response(
            $statusCode,
            ['Content-Type' => 'application/json'],
            json_encode(['status' => $statusCode, 'error' => $errorMessage], JSON_THROW_ON_ERROR, 512)
        );
    }
}
