<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

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

    // Restas
    $group->get('/substract', [\App\Controllers\Games\SubstractController::class, 'configure']);

    // Multiplicaciones
    $group->get('/multiply', [\App\Controllers\Games\MultiplyController::class, 'configure']);

    // Divisiones
    $group->get('/divide', [\App\Controllers\Games\DivideController::class, 'configure']);
});

$app->run();
