<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@lokkal.be>
 */
final class PreFlushEventListener
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    /**
     * @param mixed $object
     * @return void
     */
    public function preFlush(mixed $object)
    {
        if ($this->tokenStorage->getToken()?->getUser()) {
            $object->setUpdatedBy($this->tokenStorage->getToken()->getUser());
        }
    }
}
