<?php

declare(strict_types=1);

namespace App\Controllers\Games;

class SubstractController extends AbstractGameController
{
    protected function getOperation(): string
    {
        return 'substract';
    }

    protected function getOperationSymbol(): string
    {
        return '-';
    }

    protected function getLabel(): string
    {
        return 'Restas';
    }
}