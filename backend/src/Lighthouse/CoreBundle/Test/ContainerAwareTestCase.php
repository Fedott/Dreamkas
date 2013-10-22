<?php

namespace Lighthouse\CoreBundle\Test;

use AppKernel;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Exception;

class ContainerAwareTestCase extends WebTestCase
{
    /**
     * Init app with debug
     * @var bool
     */
    protected static $appDebug = false;

    /**
     * @var bool
     */
    protected $isolated = false;

    public static function setUpBeforeClass()
    {
        self::$appDebug = (boolean) getenv('SYMFONY_DEBUG') ?: false;
    }

    /**
     * @return AppKernel
     */
    protected static function initKernel()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        return static::$kernel;
    }

    /**
     * @param array $options
     * @return AppKernel
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
        return static::initKernel()->getContainer();
    }

    protected function clearMongoDb()
    {
        /* @var DocumentManager $mongoDb */
        $mongoDb = $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
        $mongoDb->getSchemaManager()->dropCollections();
        $mongoDb->getSchemaManager()->createCollections();
        $mongoDb->getSchemaManager()->ensureIndexes();
    }

    protected function clearJobs()
    {
        /* @var \Lighthouse\CoreBundle\Job\JobManager $jobManager */
        $jobManager = $this->getContainer()->get('lighthouse.core.job.manager');
        $jobManager->startWatchingTubes()->purgeTubes()->stopWatchingTubes();
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
     * @param bool $inIsolation
     */
    public function setIsolated($inIsolation)
    {
        parent::setIsolated($inIsolation);
        $this->isolated = $inIsolation;
    }

    /**
     * @param Exception $e
     */
    protected function onNotSuccessfulTest(Exception $e)
    {
        if ($this->isolated) {
            $e = SerializableException::factory($e);
        }
        parent::onNotSuccessfulTest($e);
    }
}
