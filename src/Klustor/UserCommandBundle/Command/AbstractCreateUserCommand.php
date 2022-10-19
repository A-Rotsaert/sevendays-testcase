<?php

declare(strict_types=1);

namespace Klustor\UserCommandBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author <andy.rotsaert@live.be>
 */
abstract class AbstractCreateUserCommand extends Command
{
    /**
     * Command defaultName
     *
     * Overwrite this if you want to change the command (default: php bin/console app:create-user)
     * to something else.
     *
     * @var string
     */
    protected static $defaultName = 'klustor:create-user';
    /**
     * Supported roles
     *
     * Overwrite this if you want to change the supported roles.
     * Note. ROLE_SUPER_ADMIN only available through the option --super-Admin.
     *
     * @var array
     */
    protected array $supportedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
    /**
     * Supported fields
     *
     * Overwrite this to add your own fields (eg. username, password, email, ...).
     *
     * @var array
     */
    protected array $supportedFields;
    /**
     * @var array
     */
    private array $roles = [];
    /**
     * @var InputInterface
     */
    private InputInterface $input;
    /**
     * @var OutputInterface
     */
    private OutputInterface $output;
    /**
     * @var mixed
     */
    private mixed $helper;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var SymfonyStyle
     */
    private SymfonyStyle $io;
    /**
     * @var object
     */
    private object $user;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->user = new \stdClass();
        $this->loadConstraints();
        parent::__construct();
    }

    /**
     * @return mixed
     */
    abstract protected function loadConstraints();

    /**
     * configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
            ->addOption('super-admin', null, InputOption::VALUE_NONE, 'Create super admin');
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // if (!$output instanceof ConsoleOutputInterface) throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        if (empty($this->supportedRoles)) {
            throw new \LogicException('You must specify at least one role in $supportedRoles.');
        }
        if (in_array('ROLE_SUPER_ADMIN', $this->supportedRoles)) {
            throw new \LogicException(
                'You cannot add ROLE_SUPER_ADMIN to supportedRoles, use the option --super-Admin instead.'
            );
        }
        if (empty($this->supportedFields)) {
            throw new \LogicException('You must specify at least one field in $supportedFields.');
        }

        $this->input = $input;
        $this->output = $output;
        $this->helper = $this->getHelper('question');
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('User Creator');
        foreach ($this->supportedFields as $field => $value) {
            $this->validator = Validation::createValidator();
            $this->user->$field = $this->validateValues(
                $value['question'],
                $value['hide_input'],
                $value['constraints']
            );
        }
        ($input->getOption('super-admin')) ? $this->addRoles('ROLE_SUPER_ADMIN') : $this->addRoles();
        $this->user->roles = $this->roles;
        $this->io->info(
            'Creating user with email: ' . $this->user->email . ' and roles: ' . implode(', ', $this->roles)
        );
        $this->io->info(($this->commandCreateUser($this->user)) ? 'User created !' : 'Failed to create user!');

        return Command::SUCCESS;
    }

    /**
     * validateValues
     *
     * @param string $question
     * @param bool $hide_input
     * @param array $constraints
     * @return string
     */
    private function validateValues(string $question, bool $hide_input, array $constraints): string
    {
        $q = new Question($question);
        if ($hide_input) {
            $q->setHidden(true);
        }
        $value = $this->helper->ask($this->input, $this->output, $q);
        $violations = $this->validator->validate($value, $constraints);
        if (count($violations) !== 0) {
            foreach ($violations as $violation) {
                $this->io->error($violation->getMessage());
            }
            $this->validateValues($question, $hide_input, $constraints);
        }
        return $value;
    }

    /**
     * addRoles
     *
     * @param string|null $role Sets initial role eg. for a super-Admin creation.
     * @return void
     */
    private function addRoles(string $role = null): void
    {
        if (isset($role)) {
            $this->addAndRemove($role, false);
        }

        while (true) {
            $question = ($this->roles) ? new ConfirmationQuestion(
                'Add another role? (yes/no) ',
                false
            ) : new ConfirmationQuestion('Add a role? (yes/no) ', false);
            if ($this->helper->ask($this->input, $this->output, $question)) {
                $question = new ChoiceQuestion('Pick a role: ', $this->supportedRoles, 0);
                $question->setErrorMessage('Role %s is invalid.');
                $r = $this->helper->ask($this->input, $this->output, $question);
                $this->addAndRemove($r);
                if (empty($this->supportedRoles)) {
                    break;
                }
            } else {
                break;
            }
        }
    }

    /**
     * addAndRemove
     *
     * @param string $role Save $role to $this->roles, remove $role from $this->supportedRoles
     * @param bool $remove
     * @return void
     */
    private function addAndRemove(string $role, bool $remove = true): void
    {
        $this->roles[] = $role;
        if ($remove) {
            unset($this->supportedRoles[array_search($role, $this->supportedRoles)]);
        }
        $this->io->info('Assigned roles: [ ' . implode(', ', $this->roles) . ' ]');
    }

    /**
     * @param object $user
     * @return mixed
     */
    abstract protected function commandCreateUser(object $user): mixed;
}
