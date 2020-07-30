<?php
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtAuthMiddleware implements MiddlewareInterface
{
    private $jwtAuth;
    private $responseFactory;

    public function __construct()
    {
        $this->jwtAuth = new JwtAuth();
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';
        $response = $this->responseFactory->createResponse();


        if (!$token || !$this->jwtAuth->validateToken($token)){
            $response->getBody()->write(json_encode([
                "error"=>true,
                "message" => "provide a valid token"
            ]));

            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        return $handler->handle($request);

    }
}