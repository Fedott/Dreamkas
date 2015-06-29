<?php

namespace Lighthouse\CoreBundle\Document\FirstStart;

use Doctrine\ODM\MongoDB\LockMode;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\StockMovement\ReceiptRepository;
use Lighthouse\CoreBundle\Document\Store\StoreRepository;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository;
use Lighthouse\ReportsBundle\Reports\GrossMarginSales\Receipt\ReceiptGrossMarginSalesManager;

/**
 * @method FirstStart find($id, $lockMode = LockMode::NONE, $lockVersion = null)
 * @method FirstStart findOneBy(array $criteria, array $sort = array(), array $hints = array())
 */
class FirstStartRepository extends DocumentRepository
{
    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var ReceiptRepository
     */
    protected $receiptRepository;

    /**
     * @var ReceiptGrossMarginSalesManager
     */
    protected $receiptReportManager;

    /**
     * @var StoreCostOfInventoryRepository
     */
    protected $storeCostOfInventoryRepository;

    /**
     * @param StoreRepository $storeRepository
     */
    public function setStoreRepository(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param ReceiptRepository $receiptRepository
     */
    public function setReceiptRepository(ReceiptRepository $receiptRepository)
    {
        $this->receiptRepository = $receiptRepository;
    }

    /**
     * @param ReceiptGrossMarginSalesManager $receiptReportManager
     */
    public function setReceiptReportManager(ReceiptGrossMarginSalesManager $receiptReportManager)
    {
        $this->receiptReportManager = $receiptReportManager;
    }

    /**
     * @param StoreCostOfInventoryRepository $storeCostOfInventoryRepository
     */
    public function setStoreCostOfInventoryRepository(StoreCostOfInventoryRepository $storeCostOfInventoryRepository)
    {
        $this->storeCostOfInventoryRepository = $storeCostOfInventoryRepository;
    }

    /**
     * @return FirstStart
     */
    public function findOrCreate()
    {
        $firstStart = $this->findOneBy(array());
        if (null === $firstStart) {
            $firstStart = $this->createNew();
            $this->save($firstStart);
        }
        return $firstStart;
    }

    /**
     * @param FirstStart $firstStart
     * @return FirstStart
     */
    public function populateFirstStart(FirstStart $firstStart)
    {
        $stores = $this->storeRepository->findAllActive();

        foreach ($stores as $store) {
            $storeFirstStart = new StoreFirstStart($store);

            $this->populateStoreFirstStartInventoryCostOfGoods($storeFirstStart);
            $this->populateStoreFirstStartSale($storeFirstStart);

            $firstStart->addStoreFirstStart($storeFirstStart);
        }

        return $firstStart;
    }

    /**
     * @param StoreFirstStart $storeFirstStart
     */
    public function populateStoreFirstStartInventoryCostOfGoods(StoreFirstStart $storeFirstStart)
    {
        $storeReport = $this->storeCostOfInventoryRepository->find($storeFirstStart->store->id);
        if ($storeReport) {
            $storeFirstStart->costOfInventory = $storeReport->costOfInventory;
        }
    }

    /**
     * @param StoreFirstStart $storeFirstStart
     */
    public function populateStoreFirstStartSale(StoreFirstStart $storeFirstStart)
    {
        $lastSale = $this->receiptRepository->findLastSaleByStore($storeFirstStart->store);

        if ($lastSale) {
            $storeFirstStart->sale = $this->receiptReportManager->getReceiptReport($lastSale);
        }
    }
}
