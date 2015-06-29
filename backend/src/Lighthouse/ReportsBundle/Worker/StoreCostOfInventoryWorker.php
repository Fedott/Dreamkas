<?php

namespace Lighthouse\ReportsBundle\Worker;

use Lighthouse\JobBundle\Document\Job\Job;
use Lighthouse\JobBundle\Worker\WorkerInterface;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryJob;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository;

/**
 * @DI\Service("lighthouse.reports.job.store_cost_of_inventory.worker")
 * @DI\Tag("job.worker")
 */
class StoreCostOfInventoryWorker implements WorkerInterface
{
    /**
     * @var StoreCostOfInventoryRepository
     */
    protected $storeCostOfInventoryRepository;

    /**
     * @DI\InjectParams({
     *      "storeCostOfInventoryRepository"
     *          = @DI\Inject("lighthouse.reports.document.cost_of_inventory.store.repository"),
     * })
     *
     * @param StoreCostOfInventoryRepository $storeCostOfInventoryRepository
     */
    public function __construct(StoreCostOfInventoryRepository $storeCostOfInventoryRepository)
    {
        $this->storeCostOfInventoryRepository = $storeCostOfInventoryRepository;
    }

    /**
     * @param Job $job
     * @return boolean
     */
    public function supports(Job $job)
    {
        return $job instanceof StoreCostOfInventoryJob;
    }

    /**
     * @param Job $job
     * @return mixed result of work
     */
    public function work(Job $job)
    {
        /** @var StoreCostOfInventoryJob $job */
        $this->storeCostOfInventoryRepository->recalculateStoreIsNeeded($job->storeId);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return 'store_cost_of_inventory';
    }
}
