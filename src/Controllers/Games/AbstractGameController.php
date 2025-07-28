<?php

declare(strict_types=1);

namespace App\Controllers\Games;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class AbstractGameController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function configure(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'games/configure.html.twig', [
            'operation' => $this->getOperation(),
            'label' => $this->getLabel(),
        ]);
    }

    protected abstract function getOperation(): string;

    protected abstract function getLabel(): string;
}