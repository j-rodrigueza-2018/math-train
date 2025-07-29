<?php

declare(strict_types=1);

namespace App\Controllers\Games;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Random\RandomException;
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

    /**
     * @throws SyntaxError
     * @throws RandomException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function play(Request $request, Response $response): Response
    {
        $query_params = $request->getQueryParams();

        $digit_1 = max(1, min(5, intval($query_params['digit_1'] ?? 1)));
        $digit_2 = max(1, min(5, intval($query_params['digit_2'] ?? 1)));
        $count = max(1, min(20, intval($query_params['count'] ?? 1)));

        $operations = [];
        for ($i = 0; $i < $count; $i++) {
            $first_operand = random_int(intval(pow(10, $digit_1 - 1)), intval(pow(10, $digit_1) - 1));
            $second_operand = random_int(intval(pow(10, $digit_2 - 1)), intval(pow(10, $digit_2) - 1));
            $operations[] = [
                'first_operand' => $first_operand,
                'second_operand' => $second_operand,
            ];
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'games/play.html.twig', [
            'operation' => $this->getOperation(),
            'operation_symbol' => $this->getOperationSymbol(),
            'label' => $this->getLabel(),
            'operations' => $operations,
        ]);
    }

    protected abstract function getOperation(): string;

    protected abstract function getOperationSymbol(): string;

    protected abstract function getLabel(): string;
}