<?php

declare(strict_types=1);

namespace App\Controller\Enum;

enum FormAction
{
    case ADD;
    case EDIT;

    public function getAction(): string
    {
        return match ($this) {
            self::ADD => 'add',
            self::EDIT => 'edit',
        };
    }
}
