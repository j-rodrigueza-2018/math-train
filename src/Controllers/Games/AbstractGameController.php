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
        if (isset($_SESSION['game'][$this->getOperation()])) {
            unset($_SESSION['game'][$this->getOperation()]);
        }

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
        $operation = $this->getOperation();
        $query_params = $request->getQueryParams();

        $is_new_game_request = isset($query_params['digit_1']);
        $game = $_SESSION['game'][$operation] ?? null;

        if ($is_new_game_request || $game === null) {
            $game = $this->generateGame($query_params);
            $_SESSION['game'][$operation] = $game;
        }

        $question_index = intval($request->getAttribute('question_index') ?? 1);
        $total_count = $game['count'];
        if ($question_index > $total_count) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', "/games/{$operation}/play/{$total_count}");
        }

        $current_operation = $game['operations'][$question_index - 1];

        $view = Twig::fromRequest($request);
        return $view->render($response, 'games/play.html.twig', [
            'operation' => $operation,
            'operation_symbol' => $this->getOperationSymbol(),
            'label' => $this->getLabel(),
            'question_index' => $question_index,
            'total_count' => $total_count,
            'first_operand' => $current_operation['first_operand'],
            'second_operand' => $current_operation['second_operand'],
            'prev_index' => $question_index > 1 ? $question_index - 1 : null,
            'next_index' => $question_index < $total_count ? $question_index + 1 : null,
            'answer' => $game['answers'][$question_index - 1] ?? '',
        ]);
    }

    public function submit(Request $request, Response $response): Response
    {
        $query_params = $request->getQueryParams();

        $operation = $this->getOperation();
        if (!isset($_SESSION['game'][$operation])) {
            $response->getBody()->write('¡Partida no encontrada!');
            return $response->withStatus(400);
        }

        $question_index = intval($query_params['question_index'] ?? 1);
        $answer = $query_params['answer'] ?? null;
        if ($answer !== null) {
            $_SESSION['game'][$operation]['answers'][$question_index - 1] = intval($answer);
        }

        $next_action = $query_params['next_action'] ?? 'next';
        if ($next_action === 'prev' && $question_index > 1) {
            return $response
                ->withHeader('Location', "/games/{$operation}/play/" . ($question_index - 1))
                ->withStatus(302);
        }

        if ($next_action === 'next' && $question_index < $_SESSION['game'][$operation]['count']) {
            return $response
                ->withHeader('Location', "/games/{$operation}/play/" . ($question_index + 1))
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', "/games/{$operation}/results")
            ->withStatus(302);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function results(Request $request, Response $response): Response
    {
        $operation = $this->getOperation();
        $game_data = $_SESSION['game'][$operation] ?? null;
        if ($game_data === null) {
            $response->getBody()->write('¡Partida no encontrada!');
            return $response->withStatus(400);
        }

        $results = [];
        $correct_answers = 0;
        $wrong_answers = 0;
        $unanswered_questions = 0;

        foreach ($game_data['operations'] as $index => $operation_data) {
            $expected_value = $this->calculate($operation_data['first_operand'], $operation_data['second_operand']);
            $given_value = $game_data['answers'][$index] ?? null;
            $is_correct_answer = ($given_value !== null && $given_value === $expected_value);

            if ($given_value === null) {
                $unanswered_questions++;
            } elseif ($is_correct_answer) {
                $correct_answers++;
            } else {
                $wrong_answers++;
            }

            $results[] = [
                'first_operand' => $operation_data['first_operand'],
                'second_operand' => $operation_data['second_operand'],
                'given_value' => $given_value,
                'expected_value' => $expected_value,
                'is_correct_answer' => $is_correct_answer,
            ];
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'games/results.html.twig', [
            'operation' => $operation,
            'operation_symbol' => $this->getOperationSymbol(),
            'label' => $this->getLabel(),
            'results' => $results,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers,
            'unanswered_questions' => $unanswered_questions,
            'total_count' => $game_data['count'],
        ]);
    }

    /**
     * @throws RandomException
     */
    private function generateGame(array $query_params): array
    {
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

        return [
            'digit_1' => $digit_1,
            'digit_2' => $digit_2,
            'count' => $count,
            'operations' => $operations,
            'answers' => []
        ];
    }

    protected abstract function getOperation(): string;

    protected abstract function getOperationSymbol(): string;

    protected abstract function getLabel(): string;

    protected abstract function calculate(int $first_operand, int $second_operand): int;
}