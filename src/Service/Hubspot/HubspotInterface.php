<?php

declare(strict_types=1);

namespace App\Service\Hubspot;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
interface HubspotInterface
{
    public function createFactory(): HubspotInterface;

    public function searchContact(User $user);

    public function createContact(UserInterface $user);

    public function updateContact(UserInterface $user);

    public function deleteContact(UserInterface $user): bool;
}
