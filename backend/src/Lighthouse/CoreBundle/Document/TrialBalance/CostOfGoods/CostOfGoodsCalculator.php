<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance\CostOfGoods;

use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Console\DotHelper;
use Lighthouse\CoreBundle\Document\StockMovement\Invoice\InvoiceProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProductRepository;
use Lighthouse\CoreBundle\Document\StockMovement\Returne\ReturnProduct;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\StockMovement\StockIn\StockInProduct;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Document\StockMovement\SupplierReturn\SupplierReturnProduct;
use Lighthouse\CoreBundle\Document\StockMovement\WriteOff\WriteOffProduct;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalance;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;
use Lighthouse\JobBundle\Job\JobManager;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryJob;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service("lighthouse.core.document.trial_balance.calculator")
 */
class CostOfGoodsCalculator
{
    /**
     * @var TrialBalanceRepository
     */
    protected $trialBalanceRepository;

    /**
     * @var NumericFactory
     */
    protected $numericFactory;

    /**
     * @var StoreProductRepository
     */
    protected $storeProductRepository;

    /**
     * @var StoreCostOfInventoryRepository
     */
    protected $storeCostOfInventoryRepository;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $increaseAmountTypes = array(
        InvoiceProduct::TYPE,
        StockInProduct::TYPE,
        ReturnProduct::TYPE,
    );

    /**
     * @var array
     */
    protected $decreaseAmountTypes = array(
        SaleProduct::TYPE,
        SupplierReturnProduct::TYPE,
        WriteOffProduct::TYPE,
    );

    /**
     * @DI\InjectParams({
     *      "trialBalanceRepository" = @DI\Inject("lighthouse.core.document.repository.trial_balance"),
     *      "numericFactory" = @DI\Inject("lighthouse.core.types.numeric.factory"),
     *      "storeProductRepository" = @DI\Inject("lighthouse.core.document.repository.store_product"),
     *      "storeCostOfInventoryRepository"
     *          = @DI\Inject("lighthouse.reports.document.cost_of_inventory.store.repository"),
     *      "container" = @DI\Inject("service_container"),
     * })
     * @param TrialBalanceRepository $trialBalanceRepository
     * @param NumericFactory $numericFactory
     * @param StoreProductRepository $storeProductRepository
     * @param StoreCostOfInventoryRepository $storeCostOfInventoryRepository
     * @param ContainerInterface $container
     */
    public function __construct(
        TrialBalanceRepository $trialBalanceRepository,
        NumericFactory $numericFactory,
        StoreProductRepository $storeProductRepository,
        StoreCostOfInventoryRepository $storeCostOfInventoryRepository,
        ContainerInterface $container
    ) {
        $this->trialBalanceRepository = $trialBalanceRepository;
        $this->numericFactory = $numericFactory;
        $this->storeProductRepository = $storeProductRepository;
        $this->storeCostOfInventoryRepository = $storeCostOfInventoryRepository;
        $this->container = $container;
    }

    /**
     * @return JobManager
     */
    protected function getJobManager()
    {
        return $this->container->get('lighthouse.job.manager');
    }

    /**
     * @param TrialBalance $trialBalance
     * @return Money
     */
    public function calculateByTrialBalance(TrialBalance $trialBalance)
    {
        return $this->calculateByIndexRange(
            $trialBalance->storeProduct->id,
            $trialBalance->startIndex,
            $trialBalance->endIndex
        );
    }

    /**
     * @param string $storeProductId
     * @param Quantity $startIndex
     * @param Quantity $endIndex
     * @return Money
     */
    public function calculateByIndexRange($storeProductId, Quantity $startIndex, Quantity $endIndex)
    {
        $increaseAmountProductTrialBalances = $this->trialBalanceRepository->findByIndexRange(
            $this->getIncreaseAmountTypes(),
            $storeProductId,
            $startIndex,
            $endIndex
        );
        $index = $startIndex;
        $currentEndIndex = null;
        $totalCostOfGoods = $this->numericFactory->createMoney(0);
        foreach ($increaseAmountProductTrialBalances as $increaseAmountProductTrialBalance) {
            if ($endIndex->toNumber() > $increaseAmountProductTrialBalance->endIndex->toNumber()) {
                $currentEndIndex = $increaseAmountProductTrialBalance->endIndex;
            } else {
                $currentEndIndex = $endIndex;
            }
            $indexQuantity = $currentEndIndex->sub($index);
            $costOfGoodsDonorPrice = $this->getCostOfGoodsDonorPriceByTrialBalance($increaseAmountProductTrialBalance);
            $costOfGoods = $costOfGoodsDonorPrice->mul($indexQuantity);
            $totalCostOfGoods = $totalCostOfGoods->add($costOfGoods);
            $index = $index->add($indexQuantity);

            $increaseAmountProductTrialBalance->inventory = $increaseAmountProductTrialBalance->quantity->sub(
                $indexQuantity
            );
            $this->trialBalanceRepository->getDocumentManager()->persist($increaseAmountProductTrialBalance);
        }

        if ($index->toNumber() < $endIndex->toNumber()) {
            $storeProduct = $this->storeProductRepository->find($storeProductId);
            $purchasePrice = $storeProduct->lastPurchasePrice?:$storeProduct->product->purchasePrice;
            $purchasePrice = $purchasePrice?:$this->numericFactory->createQuantity(0);
            $indexQuantity = $endIndex->sub($index);
            $costOfGoods = $purchasePrice->mul($indexQuantity);
            $totalCostOfGoods = $totalCostOfGoods->add($costOfGoods);
        }

        return $totalCostOfGoods;
    }

    /**
     * @param TrialBalance $trialBalance
     * @return Money
     */
    protected function getCostOfGoodsDonorPriceByTrialBalance(TrialBalance $trialBalance)
    {
        if ($trialBalance->reason instanceof ReturnProduct) {
            $saleProductTrialBalance = $this->trialBalanceRepository->findOneByStockMovementProduct(
                $trialBalance->reason->saleProduct
            );
            if (null !== $saleProductTrialBalance->costOfGoods) {
                $price = $saleProductTrialBalance->costOfGoods->div($saleProductTrialBalance->quantity);
                return $price;
            } else {
                return $this->numericFactory->createMoney(0);
            }
        } else {
            return $trialBalance->price;
        }
    }

    /**
     * @param OutputInterface|null $output
     * @return integer
     */
    public function calculateUnprocessed(OutputInterface $output = null)
    {
        $output = ($output) ?: new NullOutput();
        $dotHelper = new DotHelper($output);

        $results = $this->trialBalanceRepository->getUnprocessedTrialBalanceGroupStoreProduct(
            $this->getSupportRangeIndex()
        );

        $resultCount = count($results);
        $dotHelper->setTotalPositions($resultCount);

        foreach ($results as $result) {
            $this->calculateByStoreProductId($result['_id']['storeProduct']);

            $dotHelper->write();
        }

        $dotHelper->end();

        return $resultCount;
    }

    /**
     * @param string $storeProductId
     */
    public function calculateByStoreProductId($storeProductId)
    {
        $this->calculateByStoreProductReasonTypes($storeProductId, $this->getIncreaseAmountTypes());
        $this->calculateByStoreProductReasonTypes($storeProductId, $this->getDecreaseAmountTypes());
    }

    /**
     * @param TrialBalance $trialBalance
     */
    public function scheduleRecalculateCostOfInventoryByTrialBalance(TrialBalance $trialBalance)
    {
        $this->storeCostOfInventoryRepository->markRecalculateNeedByStore($trialBalance->store);

        $job = new StoreCostOfInventoryJob();
        $job->storeId = $trialBalance->store->id;

        $this->getJobManager()->addJob($job);
    }

    /**
     *
     * @param string $storeProductId
     * @param array $reasonTypes
     */
    public function calculateByStoreProductReasonTypes($storeProductId, array $reasonTypes)
    {
        $trialBalance = $this->trialBalanceRepository->findOneFirstUnprocessedByStoreProductIdReasonType(
            $storeProductId,
            $reasonTypes
        );

        if (null != $trialBalance) {
            $this->calculateAndFixRangeIndexesByTrialBalance($trialBalance, $reasonTypes);

            $this->scheduleRecalculateCostOfInventoryByTrialBalance($trialBalance);
        }
    }

    /**
     * @return array
     */
    public function getIncreaseAmountTypes()
    {
        return $this->increaseAmountTypes;
    }

    /**
     * @return array
     */
    public function getDecreaseAmountTypes()
    {
        return $this->decreaseAmountTypes;
    }

    /**
     * @return array
     */
    public function getSupportRangeIndex()
    {
        return array_merge($this->getIncreaseAmountTypes(), $this->getDecreaseAmountTypes());
    }

    /**
     * @param TrialBalance $trialBalance
     * @return bool
     */
    public function isIncreaseAmountTypeByTrialBalance(TrialBalance $trialBalance)
    {
        return $this->isIncreaseAmountType($trialBalance->reason);
    }

    /**
     * @param StockMovementProduct $stockMovementProduct
     * @return bool
     */
    public function isIncreaseAmountType(StockMovementProduct $stockMovementProduct)
    {
        return in_array(
            $stockMovementProduct->getType(),
            $this->getIncreaseAmountTypes()
        );
    }

    /**
     * @param TrialBalance $trialBalance
     * @return bool
     */
    public function isDecreaseAmountTypeByTrialBalance(TrialBalance $trialBalance)
    {
        return $this->isDecreaseAmountType($trialBalance->reason);
    }

    /**
     * @param StockMovementProduct $stockMovementProduct
     * @return bool
     */
    public function isDecreaseAmountType(StockMovementProduct $stockMovementProduct)
    {
        return in_array(
            $stockMovementProduct->getType(),
            $this->getDecreaseAmountTypes()
        );
    }

    /**
     * @param TrialBalance $trialBalance
     * @return bool
     */
    public function supportsRangeIndexByTrialBalance(TrialBalance $trialBalance)
    {
        return $this->supportsRangeIndex($trialBalance->reason);
    }

    /**
     * @param StockMovementProduct $stockMovementProduct
     * @return bool
     */
    public function supportsRangeIndex(StockMovementProduct $stockMovementProduct)
    {
        return in_array(
            $stockMovementProduct->getType(),
            $this->getSupportRangeIndex()
        );
    }

    /**
     * @param TrialBalance $trialBalance
     * @return array
     */
    public function getStockMovementTypesByTrialBalance(TrialBalance $trialBalance)
    {
        return $this->getStockMovementTypes($trialBalance->reason);
    }

    /**
     * @param StockMovementProduct $stockMovementProduct
     * @return array
     */
    public function getStockMovementTypes(StockMovementProduct $stockMovementProduct)
    {
        if ($this->isDecreaseAmountType($stockMovementProduct)) {
            return $this->getDecreaseAmountTypes();
        } else {
            return $this->getIncreaseAmountTypes();
        }
    }

    /**
     * @param TrialBalance $trialBalance
     * @param array $reasonTypes
     * @param int $batch
     */
    protected function calculateAndFixRangeIndexesByTrialBalance(
        TrialBalance $trialBalance,
        array $reasonTypes,
        $batch = 1000
    ) {
        if ($this->supportsRangeIndexByTrialBalance($trialBalance)) {
            $allNeedRecalculateTrialBalance = $this
                ->trialBalanceRepository
                ->findByStartTrialBalanceDateStoreProductReasonTypes($trialBalance, $reasonTypes);
            $count = 0;
            $dm = $this->trialBalanceRepository->getDocumentManager();

            $previousTrialBalance = $this->trialBalanceRepository->findOnePreviousDateByReasonsTypes(
                $trialBalance,
                $reasonTypes
            );
            if (null !== $previousTrialBalance) {
                $previousQuantity = $previousTrialBalance->endIndex;
            } else {
                $previousQuantity = $this->numericFactory->createQuantity(0);
            }

            if ($trialBalance->reason->increaseAmount()) {
                $referenceTrialBalance = $this->trialBalanceRepository->findOneByPreviousEndIndex(
                    $this->getDecreaseAmountTypes(),
                    $trialBalance->storeProduct->id,
                    $previousQuantity
                );
                if (null != $referenceTrialBalance) {
                    $referenceTrialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;
                    $dm->persist($referenceTrialBalance);
                }
            }

            foreach ($allNeedRecalculateTrialBalance as $nextTrialBalance) {
                $nextTrialBalance->startIndex = $previousQuantity;
                $nextTrialBalance->endIndex = $nextTrialBalance->startIndex->add($nextTrialBalance->quantity);

                $this->calculateCostOfGoodsIfNeeded($nextTrialBalance);

                $nextTrialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_OK;
                $previousQuantity = $nextTrialBalance->endIndex;
                $dm->persist($nextTrialBalance);
                $count++;
                if (1 == $batch / $count) {
                    $count = 0;
                    $dm->flush();
                    $dm->clear();
                }
            }

            $dm->flush();
            $dm->clear();
        }
    }

    /**
     * @param TrialBalance $trialBalance
     */
    protected function calculateCostOfGoodsIfNeeded(TrialBalance $trialBalance)
    {
        if ($this->isDecreaseAmountTypeByTrialBalance($trialBalance)) {
            $trialBalance->costOfGoods = $this->calculateByTrialBalance($trialBalance);
        }
    }
}
