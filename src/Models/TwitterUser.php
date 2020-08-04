<?php


class TwitterUser extends User
{
    /**
     * @var string
     */
    private $screen_name;
    /**
     *
     */
    /**
     * @var string
     */
    private $user_id_str;


    public function register($screen_name, $name, $id_str, $email = '', $phone_number=''): bool
    {
        $query = 'INSERT INTO twitter_users SET name = :name, screen_name=:screen_name, user_id_str=:id_str, email=:email, phone_number=:phone_number';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':screen_name', $screen_name);
        $stmt->bindParam(':id_str', $id_str);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);

        return $stmt->execute();
    }

    public function updateUser($name, $subscribed, $preference, $email = '', $phone_number=''):bool
    {
        $query = 'UPDATE twitter_users SET name = :name, subscribed=:subscribed, preference=:pref, email=:email, phone_number=:phone_number';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':pref', $preference);
        $stmt->bindParam(':subscribed', $subscribed);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);

        return $stmt->execute();
    }

    public function userExists($screen_name='')
    {
        if (!empty($screen_name)){
            $this->setScreenName($screen_name);
        }

        $query = 'SELECT id FROM twitter_users WHERE screen_name =:screen_name';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':screen_name', $this->screen_name);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getUser(): array
    {
        $query = 'SELECT * FROM twitter_users WHERE screen_name=:screen_name';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':screen_name', $this->screen_name);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @return string
     */
    public function getScreenName(): string
    {
        return $this->screen_name;
    }

    /**
     * @param string $screen_name
     */
    public function setScreenName(string $screen_name): void
    {
        $this->screen_name = $screen_name;
    }

    /**
     * @return string
     */
    public function getUserIdStr(): string
    {
        return $this->user_id_str;
    }

    /**
     * @param string $user_id_str
     */
    public function setUserIdStr(string $user_id_str): void
    {
        $this->user_id_str = $user_id_str;
    }
}