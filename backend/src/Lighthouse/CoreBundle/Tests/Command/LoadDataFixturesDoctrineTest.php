<?php

namespace Lighthouse\CoreBundle\Tests\Command;

use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;
use Lighthouse\CoreBundle\Test\TestOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\HttpKernel\KernelInterface;
use PHPUnit_Framework_Error_Notice;

class LoadDataFixturesDoctrineTest extends ContainerAwareTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->clearMongoDb();
    }

    /**
     * @param array $args
     * @return TestOutput
     */
    protected function runConsole(array $args)
    {
        /* @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $application = new Application($kernel);

        array_unshift($args, 'app/console');

        $input = new ArgvInput($args);
        $input->setInteractive(false);
        $output = new TestOutput();
        $application->doRun($input, $output);

        return $output;
    }

    public function testDefaultFixtures()
    {
        $output = $this->runConsole(array('doctrine:mongodb:fixtures:load'));

        $expected = '  > purging database
  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadUserData
  > loading [30] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadStoresData
  > loading [40] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadCatalogData
';

        $this->assertEquals($expected, $output->getDisplay());
    }

    public function testKeskoFixtures()
    {
        // :TODO: doctrine command produces notice so will suppress it in this test
        $errorNoticeBackup = PHPUnit_Framework_Error_Notice::$enabled;
        PHPUnit_Framework_Error_Notice::$enabled = false;
        $errorReporting = error_reporting();
        error_reporting($errorReporting ^ E_NOTICE);

        $fixturesPath = $this->getContainer()->getParameter('kernel.root_dir');
        $fixturesPath.= '/../src/Lighthouse/CoreBundle/DataFixtures/Kesko';
        $output = $this->runConsole(
            array(
                'doctrine:mongodb:fixtures:load',
                '--fixtures=' . $fixturesPath
            )
        );

        $expected = '  > purging database
  > loading [10] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadApiClientData
  > loading [20] Lighthouse\\CoreBundle\\DataFixtures\\MongoDB\\LoadUserData
  > loading Lighthouse\\CoreBundle\\DataFixtures\\Kesko\\KeskoLoadStoresData
';
        error_reporting($errorReporting);
        PHPUnit_Framework_Error_Notice::$enabled = $errorNoticeBackup;
        $this->assertEquals($expected, $output->getDisplay());
    }
}
