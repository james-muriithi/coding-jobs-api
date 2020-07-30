<?php
require_once __DIR__.'/../Libs/Database.php';

class User
{
    /**
     * @var PDO database connection
    */
    protected $conn;

    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $full_name;
    /**
     * @var string
     */
    private $password;

    /**
     * User constructor.
     * @param $conn PDO
     */
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * @param $username string
     * @param $fullname string
     * @param $password string
     * @return bool
     */
    public function register($username, $fullname, $password):bool
    {
        $query = 'INSERT INTO users SET full_name = :full_name, username=:username, password=:password';

        $stmt = $this->conn->prepare($query);

        $password = $this->hashPass($password);

        $stmt->bindParam(':full_name', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        return $stmt->execute();
    }

    /**
     * @param $username string
     * @param $password string
     * @return bool
     */
    public function verifyUser($username, $password):bool
    {
        return $this->isUsernameTaken($username) && $this->verifyPassword($password);
    }

    /**
     * @return array
     */
    public function getUser():array
    {
        $query = 'SELECT * FROM users WHERE username=:username';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param $pass string
     * @return string
     */
    private function hashPass($pass):string
    {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

    /**
     * @param $username string
     * @return bool
     */
    public function isUsernameTaken($username):bool
    {
        $query = 'SELECT id FROM users WHERE username =:username';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * @param $pass string
     * @return bool
    */
    public function verifyPassword($pass):bool
    {
        return password_verify($pass, $this->getDbPass());
    }

    /**
     * @return string
     */
    private function getDbPass():String
    {
        $query = 'SELECT password FROM users WHERE username = :username';

        //prepare the query
        $stmt = $this->conn->prepare($query);


        //bind the values
        $stmt->bindParam(':username', $this->username);

        // execute the query
        $stmt->execute();

        // return
        return strval($stmt->fetch(PDO::FETCH_ASSOC)['password']);

    }



    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->full_name;
    }

    /**
     * @param string $full_name
     */
    public function setFullName(string $full_name): void
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

}