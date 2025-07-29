<?php

declare(strict_types=1);

namespace App\Controllers\Games;

class SumController extends AbstractGameController
{
    protected function getOperation(): string
    {
        return 'sum';
    }

    protected function getOperationSymbol(): string
    {
        return '+';
    }

    protected function getLabel(): string
    {
        return 'Sumas';
    }
}