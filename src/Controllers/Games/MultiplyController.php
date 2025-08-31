<?php

declare(strict_types=1);

namespace App\Controllers\Games;

class MultiplyController extends AbstractGameController
{
    protected function getOperation(): string
    {
        return 'multiply';
    }

    protected function getOperationSymbol(): string
    {
        return '·';
    }

    protected function getLabel(): string
    {
        return 'Multiplicaciones';
    }

    protected function calculate(int $first_operand, int $second_operand): int
    {
        return $first_operand * $second_operand;
    }
}