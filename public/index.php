<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

//if (ENV === 'dev'){
    $app->getRouteCollector()->setBasePath(dirname($_SERVER['SCRIPT_NAME'], 2));
//}

// Parse json, form data and xml
$app->addBodyParsingMiddleware();
// Add Routing Middleware
$app->addRoutingMiddleware();

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
});

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Register routes
require __DIR__ . '/../src/Routes/routes.php';

$app->run();