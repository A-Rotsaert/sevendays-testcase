<?php

declare(strict_types=1);

namespace App\Klustor\UserCommandBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author <andy.rotsaert@live.be>
 */
final class AbstractCreateUserCommandTest extends KernelTestCase
{
    /**
     * test create user command
     *
     * creates a user, and checks output if it was successful.
     */
    public function testExecute()
    {
        $kernel = AbstractCreateUserCommandTest::createKernel();
        $application = new Application($kernel);

        $command = $application->find('klustor:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['user-test', 'dALxmMRb4WpetDtW', 'yes', 1, 'no']);
        $commandTester->execute();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User created !', $output);
    }
}
