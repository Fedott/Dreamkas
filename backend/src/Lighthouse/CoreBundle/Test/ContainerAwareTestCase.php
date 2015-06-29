<?php

namespace Lighthouse\CoreBundle\Test;

use Lighthouse\CoreBundle\Document\Project\Project;
use Lighthouse\CoreBundle\MongoDB\DocumentManager;
use Lighthouse\CoreBundle\Test\Factory\UserFactory;
use LighthouseKernel;
use Karzer\Framework\TestCase\SymfonyWebTestCase;
use Lighthouse\JobBundle\Job\JobManager;
use Lighthouse\CoreBundle\Test\Factory\Factory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Lighthouse\CoreBundle\Test\Console\ApplicationTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;

class ContainerAwareTestCase extends SymfonyWebTestCase
{
    /**
     * Init app with debug
     * @var bool
     */
    protected static $appDebug = false;

    /**
     * @var Factory
     */
    private $factories = array();

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$appDebug = (boolean) getenv('SYMFONY_DEBUG') ?: false;
    }

    protected function tearDown()
    {
        static::shutdownKernel();
        $this->factories = array();
    }

    /**
     * @return LighthouseKernel
     */
    protected static function initKernel()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        return static::$kernel;
    }

    protected static function rebootKernel()
    {
        $kernel = static::getKernel();
        $kernel->shutdown();
        $kernel->boot();
    }

    protected static function shutdownKernel()
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }
    }

    /**
     * @return KernelInterface
     */
    protected static function getKernel()
    {
        if (null === static::$kernel) {
            static::initKernel();
        }
        static::$kernel->boot();
        return static::$kernel;
    }

    /**
     * @param array $options
     * @return LighthouseKernel
     */
    protected static function createKernel(array $options = array())
    {
        $options['debug'] = isset($options['debug']) ? $options['debug'] : static::$appDebug;
        return parent::createKernel($options);
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return static::getKernel()->getContainer();
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @param string $name
     * @return Factory
     */
    protected function factory($name = UserFactory::PROJECT_DEFAULT_NAME)
    {
        if (!isset($this->factories[$name])) {
            $this->factories[$name] = $this->createFactory($name);
        }
        return $this->factories[$name];
    }

    /**
     * @param string $projectName
     * @return Factory
     */
    protected function createFactory($projectName = UserFactory::PROJECT_DEFAULT_NAME)
    {
        return new Factory(static::createKernel()->boot()->getContainer(), $projectName);
    }

    protected function clearMongoDb()
    {
        $dm = $this->getDocumentManager();
        $dm->getSchemaManager()->dropAllProjectCollections();
        $dm->getSchemaManager()->dropProjectDatabases();
        $dm->getSchemaManager()->dropGlobalCollections();
        $dm->getSchemaManager()->createGlobalCollections();
        $dm->getSchemaManager()->ensureGlobalIndexes();
    }

    protected function clearJobs()
    {
        /* @var JobManager $jobManager */
        $jobManager = $this->getContainer()->get('lighthouse.job.manager');
        $jobManager->startWatchingTubes()->purgeTubes()->stopWatchingTubes();
    }

    protected function processedAllJobs()
    {
        /* @var JobManager $jobManager */
        $jobManager = $this->getContainer()->get('lighthouse.job.manager');
        $jobManager->startWatchingTubes();
        while (1) {
            $job = $jobManager->reserveJob(0);
            if (null == $job) {
                break;
            }

            $jobManager->processJob($job);
        }
        $jobManager->stopWatchingTubes();
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getFixtureFilePath($filePath)
    {
        return __DIR__ . '/../Tests/Fixtures/' . $filePath;
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getIntegrationFixtureFilePath($filePath)
    {
        return __DIR__ . '/../../IntegrationBundle/Tests/Fixtures/' . $filePath;
    }

    protected function markTestBroken()
    {
        $this->markTestSkipped('Broken');
    }

    /**
     * @param bool $catchExceptions
     * @param bool $reboot
     * @return Application
     */
    protected function createConsoleApplication($catchExceptions = true, $reboot = false)
    {
        if ($reboot) {
            static::shutdownKernel();
        }
        $application = new Application(static::getKernel());
        $application->setCatchExceptions($catchExceptions);
        $application->setAutoExit(false);

        return $application;
    }

    /**
     * @param bool $catchExceptions
     * @param bool $reboot
     * @return ApplicationTester
     */
    protected function createConsoleTester($catchExceptions = true, $reboot = false)
    {
        $application = $this->createConsoleApplication($catchExceptions, $reboot);
        $tester = new ApplicationTester($application);
        return $tester;
    }

    /**
     * @return Project
     */
    protected function authenticateProject()
    {
        $project = $this->factory()->user()->getProject();
        $this->getContainer()->get('project.context')->authenticate($project);
        return $project;
    }
}
