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
        $query = 'SELECT id from jobs WHERE job_title = :title OR link = :link';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':link', $url);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function jobTitleExists($title)
    {
        $query = 'SELECT job_title from jobs WHERE job_title LIKE :title';

        $title = '%'.$title.'%';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function jobIdExists($id):bool
    {
        $query = 'SELECT job_title from jobs WHERE id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getAllJobs():array
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

    public function getNewTwitterJobs($limit=10)
    {
        $query = 'SELECT * from jobs WHERE jobs.new = 1 AND twitter = 0 ORDER BY created_at DESC LIMIT :limit';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTwitterPostedJobs($limit = 10)
    {
        $query = 'SELECT posted.id as post_id, posted.job_id, posted.platform, posted.post_date, jobs.job_title, jobs.company, jobs.link as job_link, posted.link as tweet_link
        from posted LEFT JOIN jobs on posted.job_id = jobs.id WHERE platform = "twitter"  ORDER BY created_at DESC LIMIT :limit';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setJobPostedTwitter($jobId)
    {
        $query = 'UPDATE jobs SET twitter=1 WHERE id = :job_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':job_id', $jobId);
        return $stmt->execute();
    }

    public function postTwitterJob($link, $job_id,$platform = 'twitter')
    {
        $this->conn->beginTransaction();
        $query = 'INSERT INTO posted SET link=:link, platform=:platform, job_id=:job_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':job_id', $job_id);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':platform', $platform);

        if ($stmt->execute()){
            $this->setJobPostedTwitter($job_id);
            $this->conn->commit();
            return true;
        }
        $this->conn->rollBack();
        return false;
    }

    public function getJobWithId($job_id):array
    {
        $query = 'SELECT * FROM jobs WHERE id=:id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $job_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getJobWithTitle($job_title):array
    {
        $query = 'SELECT * FROM jobs WHERE job_title LIKE :job_title';
        $stmt = $this->conn->prepare($query);
        $job_title = '%'. $job_title .'%';
        $stmt->bindParam(':job_title', $job_title);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return PDO
     * */
    public function getDbConnection()
    {
        return $this->conn;
    }

}