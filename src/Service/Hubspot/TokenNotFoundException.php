<?php

declare(strict_types=1);

namespace App\Service\Hubspot;

final class TokenNotFoundException extends \Exception
{
    protected $message = 'Please specify ACCESS_TOKEN in .env.';
}
