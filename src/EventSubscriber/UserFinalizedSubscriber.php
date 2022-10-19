<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\User\UserFinalizedEvent;
use App\Service\Hubspot\HubspotInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class UserFinalizedSubscriber implements EventSubscriberInterface
{
    public function __construct(private HubspotInterface $hubspot, private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            UserFinalizedEvent::NAME => 'onUserFinalized'
        ];
    }

    public function onUserFinalized(UserFinalizedEvent $event)
    {
        $hubspot = $this->hubspot->createFactory();
        $user = $event->getUser();
        $result = $hubspot->searchContact($user);
        if ($result->total === 0) {
            $result = $hubspot->createContact($user);
            $this->logger->info(sprintf('User with id %d was created as a hubspot contact', $result->id));

            return;
        }

        $hubspot->updateContact($user);
        $this->logger->info(sprintf('User with id %d was updated in a hubspot contact', $result->id));
    }
}
