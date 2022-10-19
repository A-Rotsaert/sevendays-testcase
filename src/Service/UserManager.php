<?php

declare(strict_types=1);

namespace App\Service;

use App\Security\EmailVerifier;
use App\Service\Hubspot\HubspotInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author <andy.rotsaert@live.be>
 */
final class UserManager
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $em
     * @param EmailVerifier $emailVerifier
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private FlashBagInterface $flashBag,
        private HubspotInterface $hubspot,
    ) {
    }

    /**
     * @param object $u
     * @param string|null $plainPassword
     * @return User|null
     */
    public function createUser(object $u, ?string $plainPassword = null): User|null
    {
        if (!$u instanceof User) {
            $user = new User();
            $user->setEmail($u->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $u->password));
            $user->setRoles($u->roles);
            $user->setIsVerified(true);
            $this->em->persist($user);
            $this->em->flush();
            $this->logger->info(
                sprintf(
                    'User with id %d was registered through console command, activation mail not sent, user automatically activated.',
                    $user->getId()
                )
            );
        } else {
            $user = $u;
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );
            $this->em->persist($user);
            $this->em->flush();
            $this->sendActivationMail($user);
            $this->logger->info(sprintf('User with id %d was registered, activation mail sent.', $user->getId()));
        }


        return ($this->em->contains($user)) ? $user : null;
    }

    /**
     * @param UserInterface $user
     */
    public function deleteUser(
        UserInterface $user,
    ) {
        $this->em->remove($user);

        $this->flashBag->add(
            'success',
            $this->translator->trans('app.delete.success', ['%type%' => $this->translator->trans('app.menu.profile')])
        );
        $hubspot = $this->hubspot->createFactory();
        $hubspot->deleteContact($user);

        $this->logger->info(sprintf('User with id %d has deleted his profile', $user->getId()));
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function sendActivationMail(User $user)
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('hello@child-advisor.be', $this->translator->trans('app.name')))
                ->to($user->getEmail())
                ->subject($this->translator->trans('app.confirmation_email.title'))
                ->htmlTemplate('email/compiled/confirmation_email.html.twig')
        );
    }

    /**
     * @param string $username
     *
     * @return array
     */
    public function findUserByUsername(string $username): array
    {
        return $this->em->getRepository(User::class)->findBy(['username' => $username]);
    }
}
