<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class CreateUser
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function __invoke(RequestInterface $request,ResponseInterface $response):ResponseInterface
    {
        $data = $request->getParsedBody();

        $username = (string) isset($data['username']) && !empty($data['username']) ? $data['username'] : '';
        $fullname = (string) isset($data['fullname']) && !empty($data['fullname']) ? $data['fullname'] : '';
        $password = (string) isset($data['password']) && !empty($data['password']) ? $data['password'] : '';

        if (!empty($username) && !empty($fullname) && !empty($password)){
            if ($this->user->isUsernameTaken($username)){
                $response->getBody()->write(json_encode([
                    "error"=> true,
                    "message" => "The username is already taken"
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(403);
            }

            if ($this->user->register($username, $fullname, $password)){
                $response->getBody()->write(json_encode([
                    "success"=> true,
                    "message" => "User was created successfully"
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201, 'Created');
            }

            $response->getBody()->write(json_encode([
                "error"=> true,
                "message" => "There was a problem saving the user."
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

//        if not all fields are provided
        $response->getBody()->write(json_encode([
            "error"=> true,
            "message" => "Provide all the fields."
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

}