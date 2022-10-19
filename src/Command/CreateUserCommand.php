<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Klustor\UserCommandBundle\Command\AbstractCreateUserCommand;
use App\Service\UserManager;

/**
 * @author <andy.rotsaert@live.be>
 */
final class CreateUserCommand extends AbstractCreateUserCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:user:create';
    /**
     * @var UserManager
     */
    public UserManager $userManager;
    /**
     * @var string[]
     */
    protected array $supportedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
    /**
     * @var array
     */
    protected array $supportedFields;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * @param object $user
     * @return User|null
     */
    protected function commandCreateUser(object $user): ?User
    {
        return $this->userManager->createUser($user);
    }

    /**
     * load constraints
     */
    protected function loadConstraints()
    {
        $this->supportedFields = [
            'email' =>
                [
                    'question' => 'E-mail: ',
                    'hide_input' => false,
                    'constraints' => [
                        new Assert\Email(),
                    ],
                ],
            'password' => [
                'question' => 'Password: ',
                'hide_input' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Your password must be at least {{ limit }} characters long',
                    ]),
                    new Assert\NotCompromisedPassword()
                ]
            ]
        ];
    }
}
