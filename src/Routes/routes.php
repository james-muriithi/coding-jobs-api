<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once __DIR__.'/../Models/Job.php';


$app->group('/', function () use ($app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'hello there']));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    $app->get('/new', function (Request $request, Response $response){
        $data = $request->getQueryParams();

        $limit = isset($data['limit']) ? $data['limit'] : '';
        $job = new Job();

        $newJobs = empty($limit) ? $job->getNewJobs() : $job->getNewJobs($limit);

        $response->getBody()->write(json_encode($newJobs));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    $app->get('/all-jobs', function (Request $request, Response $response){
        $job = new Job();

        $response->getBody()->write(json_encode($job->getAllJobs()));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

//    new job post
    $app->post('/new', function (Request $request, Response $response){
        $data = $request->getParsedBody();


        $job_title = isset($data['title']) && !empty($data['title']) ? $data['title'] : '';
        $company = isset($data['company']) && !empty($data['company']) ? $data['company'] : '';
        $salary = isset($data['salary']) && !empty($data['salary']) ? $data['salary'] : '';
        $full_text = isset($data['full_text']) && !empty($data['full_text']) ? $data['full_text'] : '';
        $location = isset($data['location']) && !empty($data['location']) ? $data['location'] : '';
        $post_date = isset($data['post_date']) && !empty($data['post_date']) ? $data['post_date'] : '';


        if (isset($data['link'], $data['summary'])){
            $summary = $data['summary'];
            $link = $data['link'];
            $job = new Job();

            if (!$job->jobExists($job_title, $link)){
                try {
                    $conn = $job->getDbConnection();
                    $conn->beginTransaction();
                    $saved = $job->saveJob($job_title, $company, $location, $salary, $summary, $post_date, $link, $full_text);
                    if ($saved){
                        $conn->commit();
                        $response->getBody()->write(json_encode([
                            "success"=>true,
                            "message" => "Your Job was saved successfully"
                        ]));
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(201);
                    }
                    $response->getBody()->write(json_encode([
                        "error"=>true,
                        "message" => "There was an error saving the job"
                    ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(500);
                }catch (PDOException $exception){

                }
            }else{
                $response->getBody()->write(json_encode([
                    "error"=>true,
                    "message" => "The job already exist"
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

        }else{
            $response->getBody()->write(json_encode([
                "error"=>true,
                "message" => "please provide all the job details"
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

    });
});