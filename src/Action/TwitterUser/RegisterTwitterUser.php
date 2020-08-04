<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class RegisterTwitterUser
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $this->user = new TwitterUser();
    }

    public function __invoke(RequestInterface $request,ResponseInterface $response):ResponseInterface
    {
        $data = $request->getParsedBody();

        $screen_name = (string) isset($data['screen_name']) && !empty($data['screen_name']) ? $data['screen_name'] : '';
        $name = (string) isset($data['name']) && !empty($data['name']) ? $data['name'] : '';
        $user_id_str = (string) isset($data['user_id_str']) && !empty($data['user_id_str']) ? $data['user_id_str'] : '';
        $email = (string) isset($data['email']) && !empty($data['email']) ? $data['email'] : '';

        if (!empty($screen_name) && !empty($name) && !empty($user_id_str)){
            if (!$this->user->userExists($screen_name)){
                if ($this->user->register($screen_name, $name, $user_id_str, $email)){
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
            }else{
                $response->getBody()->write(json_encode([
                    "error"=> true,
                    "message" => "The user already exists"
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(403);
            }
        }else{
            //if not all fields are provided
            $response->getBody()->write(json_encode([
                "error"=> true,
                "message" => "Provide all the fields."
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}