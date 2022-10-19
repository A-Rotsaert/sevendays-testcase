<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserFinalizedEvent extends Event
{
    public const NAME = 'user.finalized';
    protected User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
