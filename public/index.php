<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

try {
    $twig = Twig::create(__DIR__ . '/../templates', [
        'cache' => false,
    ]);

    $app->add(TwigMiddleware::create($app, $twig));
} catch (\Twig\Error\LoaderError $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}

$app->get('/', [\App\Controllers\HomeController::class, 'index']);

$app->group('/games', function (\Slim\Routing\RouteCollectorProxy $group) {
    // Sumas
    $group->get('/sum', [\App\Controllers\Games\SumController::class, 'configure']);
    $group->get('/sum/play/{question_index}', [\App\Controllers\Games\SumController::class, 'play']);
    $group->get('/sum/submit', [\App\Controllers\Games\SumController::class, 'submit']);
    $group->get('/sum/results', [\App\Controllers\Games\SumController::class, 'results']);

    // Restas
    $group->get('/subtract', [\App\Controllers\Games\SubtractController::class, 'configure']);
    $group->get('/subtract/play/{question_index}', [\App\Controllers\Games\SubtractController::class, 'play']);
    $group->get('/subtract/submit', [\App\Controllers\Games\SubtractController::class, 'submit']);
    $group->get('/subtract/results', [\App\Controllers\Games\SubtractController::class, 'results']);

    // Multiplicaciones
    $group->get('/multiply', [\App\Controllers\Games\MultiplyController::class, 'configure']);
    $group->get('/multiply/play/{question_index}', [\App\Controllers\Games\MultiplyController::class, 'play']);
    $group->get('/multiply/submit', [\App\Controllers\Games\MultiplyController::class, 'submit']);
    $group->get('/multiply/results', [\App\Controllers\Games\MultiplyController::class, 'results']);

    // Divisiones
    $group->get('/divide', [\App\Controllers\Games\DivideController::class, 'configure']);
    $group->get('/divide/play/{question_index}', [\App\Controllers\Games\DivideController::class, 'play']);
    $group->get('/divide/submit', [\App\Controllers\Games\DivideController::class, 'submit']);
    $group->get('/divide/results', [\App\Controllers\Games\DivideController::class, 'results']);
});

$app->run();
