<?php

declare(strict_types=1);

namespace App\Controllers\Games;

class DivideController extends AbstractGameController
{
    protected function getOperation(): string
    {
        return 'divide';
    }

    protected function getOperationSymbol(): string
    {
        return '/';
    }

    protected function getLabel(): string
    {
        return 'Divisiones';
    }

    protected function calculate(int $first_operand, int $second_operand): int
    {
        return intdiv($first_operand, $second_operand);
    }
}