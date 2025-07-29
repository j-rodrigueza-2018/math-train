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
}