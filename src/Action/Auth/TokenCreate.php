<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

final class TokenCreate
{
    private $jwtAuth;

    public function __construct()
    {
        $this->jwtAuth = new JwtAuth();
    }

    public function __invoke(RequestInterface $request,ResponseInterface $response):ResponseInterface
    {
        $data = $request->getParsedBody();

        $username = (string) isset($data['username']) && !empty($data['username']) ? $data['username'] : '';
        $password = (string) isset($data['password']) && !empty($data['password']) ? $data['password'] : '';

        $this->jwtAuth->setUsername($username);

        $isValidLogin = $this->jwtAuth->verifyUser($username,$password);

        if ($isValidLogin){
            $this->jwtAuth->setUsername($username);
            $this->jwtAuth->setPassword($password);

            $user = $this->jwtAuth->getUser();

            $response->getBody()->write(json_encode([
                "access_token" => $this->jwtAuth->generateToken($user['username'])
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        $response->getBody()->write(json_encode([
            "error"=> true,
            "message" => "Wrong credentials"
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401, 'Unauthorised');
    }
}