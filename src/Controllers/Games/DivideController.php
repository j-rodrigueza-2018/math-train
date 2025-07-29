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
}