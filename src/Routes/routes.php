<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__.'/../Models/Job.php';
require_once __DIR__.'/../Models/Auth.php';
require_once __DIR__.'/../Models/TwitterUser.php';
require_once  __DIR__.'/../Action/Auth/TokenCreate.php';
require_once  __DIR__.'/../Action/User/CreateUser.php';
require_once __DIR__.'/../Action/TwitterUser/RegisterTwitterUser.php';
require_once  __DIR__.'/../Middleware/JwtAuthMiddleware.php';



$app->group('/', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'hello there']));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    $app->group('/twitter-user', function (RouteCollectorProxy $group){
        $group->post('/register', RegisterTwitterUser::class);
    });

    $app->group('/oauth', function (RouteCollectorProxy $group){
        $group->post('/generate', TokenCreate::class);
    });

    $app->group('/user', function (RouteCollectorProxy $group){
        $group->post('/register', CreateUser::class);
    });


    $app->get('/new', function (Request $request, Response $response){
        $data = $request->getQueryParams();

        $limit = isset($data['limit']) ? $data['limit'] : 10;
        $page = (isset($data['page']) && (int)$data['page'] > 0) ? $data['page'] : 1;

        $platform = isset($data['platform']) ? $data['platform'] : '';
        $job = new Job();

        $newJobs = $job->getNewJobs($page, $limit);
        if (!empty($platform) && $platform === 'twitter'){
            $newJobs =  $job->getNewTwitterJobs($page, $limit);
        }

        $response->getBody()->write(json_encode($newJobs));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    //get job
    $app->get('/job', function (Request $request,Response $response){
        $data = $request->getQueryParams();

        $title = isset($data['title']) ? $data['title'] : '';
        $job_id = isset($data['id']) ? $data['id'] : '';
        $job = new Job();
        if (!empty($job_id)){
            if ($job->jobIdExists($job_id)){
                $response->getBody()->write(json_encode($job->getJobWithId($job_id)));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $response->getBody()->write(json_encode(['error'=>true, 'message'=> 'No job was found with that id']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        }elseif (!empty($title)){
            if ($job->jobTitleExists($title)){
                $response->getBody()->write(json_encode($job->getJobWithTitle($title)));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $response->getBody()->write(json_encode(['error'=>true, 'message'=> 'No job was found with that title']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        }
    });

    $app->get('/posted',function (Request $request, Response $response){
        $data = $request->getQueryParams();

        $limit = isset($data['limit']) ? $data['limit'] : '';
        $job = new Job();

        $postedJobs = empty($limit) ? $job->getTwitterPostedJobs() : $job->getTwitterPostedJobs($limit);

        $response->getBody()->write(json_encode($postedJobs));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    });

//    mark job as posted
    $app->post('/posted',function (Request $request, Response $response){
        $data = $request->getParsedBody();

        $job_id = isset($data['job_id']) && !empty($data['job_id']) ? $data['job_id'] : '';
        $post_link = isset($data['link']) && !empty($data['link']) ? $data['link'] : '';
        $platform = isset($data['platform']) && !empty($data['platform']) ? $data['platform'] : '';

        if (!empty($job_id) && !empty($post_link) && !empty($platform)){
            $job = new Job();
            if ($job->jobIdExists($job_id)){
                if ($platform === 'twitter'){
                    if ($job->postTwitterJob($post_link,$job_id)){
                        $response->getBody()->write(json_encode([
                            "success"=>true,
                            "message" => "Your post was saved successfully"
                        ]));
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(201);
                    }else{
                        $response->getBody()->write(json_encode([
                            "error"=>true,
                            "message" => "There was an error saving the job post"
                        ]));
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
                    }
                }else{
                    $response->getBody()->write(json_encode([
                        "error"=>true,
                        "message" => "Not supported yet"
                    ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                }
            }else{
                $response->getBody()->write(json_encode([
                    "error"=>true,
                    "message" => "The job id does not exist"
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        }else{
            $response->getBody()->write(json_encode([
                "error"=>true,
                "message" => "Please provide all the details"
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

    })->add(JwtAuthMiddleware::class);

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

    })->add(JwtAuthMiddleware::class);
});