<?php

namespace Lighthouse\ReportsBundle\Reports\CostOfInventory;

use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lighthouse.reports.cost_of_inventory.manager")
 */
class CostOfInventoryReportManager
{
    /**
     * @var StoreCostOfInventoryRepository
     */
    protected $storeCostOfInventoryRepository;

    /**
     * @DI\InjectParams({
     *      "storeCostOfInventoryRepository"
     *          = @DI\Inject("lighthouse.reports.document.cost_of_inventory.store.repository")
     * })
     * @param StoreCostOfInventoryRepository $storeCostOfInventoryRepository
     */
    public function __construct(StoreCostOfInventoryRepository $storeCostOfInventoryRepository)
    {
        $this->storeCostOfInventoryRepository = $storeCostOfInventoryRepository;
    }

    /**
     * @param OutputInterface $output
     * @param int $batch
     * @return int
     */
    public function recalculateStoreCostOfInventory(OutputInterface $output, $batch = 1000)
    {
        return $this->storeCostOfInventoryRepository->recalculate($output, $batch);
    }
}
