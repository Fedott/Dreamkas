<?php

namespace Lighthouse\CoreBundle\Tests\Command;

use Lighthouse\CoreBundle\Command\LoadDataFixturesDoctrine;
use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;
use Lighthouse\CoreBundle\Test\TestOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class LoadDataFixturesDoctrineTest extends ContainerAwareTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->clearMongoDb();
    }

    /**
     * @return LoadDataFixturesDoctrine
     */
    protected function getCommand()
    {
        $container = $this->getContainer();
        /* @var KernelInterface $kernel */
        $kernel = $container->get('kernel');
        $application = new Application($kernel);
        /* @var LoadDataFixturesDoctrine $command */
        $command = $container->get('lighthouse.core.command.load_data_fixtures_doctrine');
        $command->setContainer($container);
        $command->setApplication($application);
        return $command;
    }

    /**
     * @param array $input
     * @param array $options
     * @return CommandTester
     */
    protected function execute(array $input = array(), array $options = array())
    {
        $command = $this->getCommand();
        $commandTester = new CommandTester($command);
        $input += array('command' => 'doctrine:fixtures:load');
        $options += array('interactive' => false);
        $commandTester->execute($input, $options);
        return $commandTester;
    }

    public function testExecute()
    {
        $commandTester = $this->execute();
        $display = $commandTester->getDisplay();
        $expected = '  > purging database
  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadUserData
  > loading [30] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadStoresData
  > loading [40] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadCatalogData
';

        $this->assertEquals($expected, $display);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Could not find any fixtures to load in:
     */
    public function testFixturesEmptyFolder()
    {
        $fixturesPath = $this->getFixtureFilePath('');
        $commandTester = $this->execute(array('--fixtures' => $fixturesPath));
        $display = $commandTester->getDisplay();
        $expected = '';
        $this->assertEquals($expected, $display);
    }

    public function testInInteractiveModePurgeYes()
    {
        $command = $this->getCommand();

        /* @var $dialogHelperMock DialogHelper|\PHPUnit_Framework_MockObject_MockObject */
        $dialogHelperMock = $this->getMock(
            'Symfony\\Component\\Console\\Helper\\DialogHelper',
            array('askConfirmation')
        );
        $dialogHelperMock
            ->expects($this->once())
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        $command->getHelperSet()->set($dialogHelperMock);

        $commandTester = new CommandTester($command);
        $input = array('command' => 'doctrine:fixtures:load');
        $options = array('interactive' => true);
        $commandTester->execute($input, $options);

        $display = $commandTester->getDisplay();
        $expected = '  > purging database
  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadUserData
  > loading [30] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadStoresData
  > loading [40] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadCatalogData
';

        $this->assertEquals($expected, $display);
    }

    public function testInInteractiveModePurgeNo()
    {
        $command = $this->getCommand();

        /* @var $dialogHelperMock DialogHelper|\PHPUnit_Framework_MockObject_MockObject */
        $dialogHelperMock = $this->getMock(
            'Symfony\\Component\\Console\\Helper\\DialogHelper',
            array('askConfirmation')
        );
        $dialogHelperMock
            ->expects($this->once())
            ->method('askConfirmation')
            ->will($this->returnValue(false));

        $command->getHelperSet()->set($dialogHelperMock);

        $commandTester = new CommandTester($command);
        $input = array('command' => 'doctrine:fixtures:load');
        $options = array('interactive' => true);
        $commandTester->execute($input, $options);

        $display = $commandTester->getDisplay();
        $this->assertEquals('', $display);
    }

    public function testAppendInInteractiveMode()
    {
        $command = $this->getCommand();

        /* @var $dialogHelperMock DialogHelper|\PHPUnit_Framework_MockObject_MockObject */
        $dialogHelperMock = $this->getMock(
            'Symfony\\Component\\Console\\Helper\\DialogHelper',
            array('askConfirmation')
        );
        $dialogHelperMock
            ->expects($this->never())
            ->method('askConfirmation')
            ->will($this->returnValue(false));

        $command->getHelperSet()->set($dialogHelperMock);

        $commandTester = new CommandTester($command);
        $input = array('command' => 'doctrine:fixtures:load', '--append' => true);
        $options = array('interactive' => true);
        $commandTester->execute($input, $options);

        $display = $commandTester->getDisplay();
        $expected = '  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadUserData
  > loading [30] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadStoresData
  > loading [40] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadCatalogData
';

        $this->assertEquals($expected, $display);
        $this->assertNotContains('> purging database', $display);
    }

    public function testExecuteThroughApplication()
    {
        /* @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $application = new Application($kernel);

        $input = new ArgvInput(
            array(
                'app/console',
                'doctrine:fixtures:load'
            )
        );
        $input->setInteractive(false);
        $output = new TestOutput();
        $application->doRun($input, $output);

        $expected = '  > purging database
  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadUserData
  > loading [30] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadStoresData
  > loading [40] Lighthouse\\CoreBundle\\DataFixtures\\ODM\\LoadCatalogData
';

        $this->assertEquals($expected, $output->getDisplay());
    }
}
