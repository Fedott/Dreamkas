<?php

namespace Lighthouse\CoreBundle\Document\Product\RecalcProductPrice;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Document\AbstractMongoDBListener;
use Lighthouse\CoreBundle\Document\Product\RecalcProductPrice\RecalcProductPriceFactory;
use Lighthouse\CoreBundle\Job\JobManager;
use Lighthouse\CoreBundle\Job\Worker\WorkerManager;
use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\Product\Version\ProductVersion;

/**
 * @DI\DoctrineMongoDBListener(events={"postUpdate"})
 */
class RecalcProductPriceListener extends AbstractMongoDBListener
{
    /**
     * @var RecalcProductPriceFactory
     */
    protected $factory;

    /**
     * @var \Lighthouse\CoreBundle\Job\JobManager
     */
    protected $jobManager;

    /**
     * @DI\InjectParams({
     *      "factory" = @DI\Inject("lighthouse.core.job.retail_product_price.factory"),
     *      "jobManager" = @DI\Inject("lighthouse.core.job.manager")
     * })
     * @param RecalcProductPriceFactory $factory
     * @param \Lighthouse\CoreBundle\Job\JobManager $jobManager
     */
    public function __construct(RecalcProductPriceFactory $factory, JobManager $jobManager)
    {
        $this->factory = $factory;
        $this->jobManager = $jobManager;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof Product && !$document instanceof ProductVersion) {
            $retailPriceMinDiff = $this->getChangeSetIntPropertyDiff($eventArgs, 'retailPriceMin');
            $retailPriceMaxDiff = $this->getChangeSetIntPropertyDiff($eventArgs, 'retailPriceMax');
            if (0 <> $retailPriceMinDiff || 0 <> $retailPriceMaxDiff) {
                $this->createRecalcProductPriceJob($document);
            }
        }
    }

    /**
     * @param Product $product
     */
    protected function createRecalcProductPriceJob(Product $product)
    {
        $job = $this->factory->createByProduct($product);
        $this->jobManager->addJob($job);
    }
}
