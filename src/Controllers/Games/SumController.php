<?php

declare(strict_types=1);

namespace App\Controllers\Games;

class SumController extends AbstractGameController
{
    protected function getOperation(): string
    {
        return 'sum';
    }

    public function getOperationSymbol(): string
    {
        return '+';
    }

    public function getLabel(): string
    {
        return 'Sumas';
    }

    protected function calculate(int $first_operand, int $second_operand): int
    {
        return $first_operand + $second_operand;
    }
}