<?php
declare(strict_types=1);

namespace Auth\Application;

use Auth\Server\RequestSender;
use Exception;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;

class HttpApplication
{
    /** @var Router */
    private $router;

    /** @var ServerRequestCreatorInterface */
    private $requestCreator;

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
                ["Content-Type" => "application/json"],
                json_encode(["status" => 404, "error" => $e->getMessage()])
            );
        } catch (Exception $e) {
            $response = new Response(
                500,
                ["Content-Type" => "application/json"],
                json_encode(["status" => 500, "error" => $e->getMessage()])
            );
        }
        (new RequestSender())->send($response);
    }
}