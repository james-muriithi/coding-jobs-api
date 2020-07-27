<?php
require_once __DIR__.'/../Libs/Database.php';

class Job
{
    /**
     * @var PDO
     * */
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * @var string $title 
     * @var string $company 
     * @var string $location 
     * @var string $salary 
     * @var string $summary 
     * @var string $post_date 
     * @var string $link 
     * @var string $full_text 
     * @return bool
     * */
    public function saveJob($title='', $company='', $location ='', $salary='',$summary='', $post_date='', $link='', $full_text='')
    {
        $query = 'INSERT INTO jobs SET
                        job_title =:title,
                        company=:company,
                        location=:location,
                        salary=:salary,
                        summary=:summary,
                        post_date=:post_date,
                        link=:link,
                        full_text=:full_text';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':salary', $salary);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':post_date', $post_date);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':full_text', $full_text);

        return $stmt->execute();
    }

    /**
     * @return bool
     * */
    public function jobExists($title, $url)
    {
        $query = 'SELECT id from jobs WHERE job_title LIKE :title OR link = :link';

        $stmt = $this->conn->prepare($query);
        $title = '%'.$title.'%';
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':link', $url);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getAllJobs()
    {
        $query = 'SELECT * from jobs';

        $stmt = $this->conn->query($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewJobs($limit=10): array
    {
        $query = 'SELECT * from jobs WHERE jobs.new = 1 ORDER BY created_at DESC LIMIT :limit';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return PDO
     * */
    public function getDbConnection()
    {
        return $this->conn;
    }

}