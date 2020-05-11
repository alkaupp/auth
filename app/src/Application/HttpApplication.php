<?php

declare(strict_types=1);

namespace Auth\Application;

use Auth\Server\RequestSender;
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
            $response = new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode(['status' => 404, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR, 512)
            );
        } catch (InvalidArgumentException $e) {
            $response = new Response(
                400,
                ['Content-Type' => 'application/json'],
                json_encode(['status' => 400, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR, 512)
            );
        } catch (Throwable $e) {
            $response = new Response(
                500,
                ['Content-Type' => 'application/json'],
                json_encode(['status' => 500, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR, 512)
            );
        }
        (new RequestSender())->send($response);
    }
}
