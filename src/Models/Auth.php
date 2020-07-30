<?php
use Firebase\JWT\JWT;
require_once __DIR__.'/User.php';

class JwtAuth extends User
{
    /**
     * @var string
     */
    private $secret;

    /**
     * Auth constructor.
     * @param $secret string
     */
    public function __construct($secret='__my_secret__')
    {
        parent::__construct();
        $this->secret = $secret;
    }

    public function generateToken($username, $expiry=24):string
    {
        $now = time();
        $data = [
            'uid' => $username,
            'iat' => $now
//            'exp' => $now + $expiry * (60 * 60)
        ];
        return JWT::encode($data, $this->secret);
    }

    public function validateToken($token)
    {
        try {
            $payload = JWT::decode($token, $this->secret, array('HS256'));
            if (!$payload){
                return false;
            }

            return $payload;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }
}