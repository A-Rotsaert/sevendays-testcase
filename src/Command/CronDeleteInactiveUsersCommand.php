<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'cron:delete:inactive-users',
    description: 'Command for cronjob to delete inactive users',
)]
/**
 * @author <andy.rotsaert@live.be>
 */
final class CronDeleteInactiveUsersCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $name = null)
    {
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTimeImmutable();

        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->lte('createdAt', $today->sub(new \DateInterval('P2D'))));

        $inactive_users = $this->entityManager->getRepository(User::class)->matching($criteria);
        $io->note(sprintf('Found %d inactive users', $inactive_users->count()));
        foreach ($inactive_users as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
        $io->success('All inactive users have been deleted.');

        return Command::SUCCESS;
    }
}
