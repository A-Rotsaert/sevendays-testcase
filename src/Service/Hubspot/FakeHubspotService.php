<?php

declare(strict_types=1);

namespace App\Service\Hubspot;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[When(env: 'dev')]
#[When(env: 'test')]
/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class FakeHubspotService implements HubspotInterface
{
    private ParameterBagInterface $parameterBag;
    private string $mockfolder;


    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->mockfolder = sprintf('%s/FakeResponse', __DIR__);
    }

    /**
     * @throws TokenNotFoundException
     */
    public function createFactory(): HubspotInterface
    {
        if (empty($this->parameterBag->get('app.hubspot_api_token'))) {
            throw new TokenNotFoundException();
        }

        return $this;
    }

    public function searchContact(UserInterface $user): object
    {
        $result = json_decode(
            file_get_contents(sprintf('%s/searchContact.json', $this->mockfolder))
        );


        return $this->randomlyAddUserDetails($result, $user);
    }

    public function createContact(UserInterface $user)
    {
        $date = new \DateTime();
        $data = json_decode(file_get_contents(sprintf('%s/createContact.json', $this->mockfolder)));
        $data->id = $user->getId();
        $data->createdAt = $date->format('Y-m-d\TH:i:s.000\Z');
        $data->updatedAt = $date->format('Y-m-d\TH:i:s.000\Z');
        $data->properties->firstname = $user->getName();
        $data->properties->email = $user->getEmail();
        $data->properties->createdate = $date->format('Y-m-d\TH:i:s.000\Z');
        $data->properties->lastmodifieddate = $date->format('Y-m-d\TH:i:s.000\Z');

        return $data;
    }

    public function updateContact(UserInterface $user)
    {
        $this->createContact($user);
    }

    public function deleteContact(UserInterface $user): bool
    {
        return (rand() % 2 == 0);
    }

    private function randomlyAddUserDetails(object $data, UserInterface $user): object
    {
        if (rand() % 2 == 0) {
            $data->results[0] = $this->createContact($user);
        }

        return $data;
    }
}
