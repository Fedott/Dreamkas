<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\UnitOfWork;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Document\AbstractMongoDBListener;
use Lighthouse\CoreBundle\Document\StockMovement\Invoice\Invoice;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProductRepository;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Document\TrialBalance\CostOfGoods\CostOfGoodsCalculateJob;
use Lighthouse\CoreBundle\Document\TrialBalance\CostOfGoods\CostOfGoodsCalculator;
use Lighthouse\JobBundle\Job\JobManager;

/**
 * @DI\DoctrineMongoDBListener(events={"onFlush", "postFlush"}, priority=128)
 */
class TrialBalanceListener extends AbstractMongoDBListener
{
    /**
     * @var TrialBalanceRepository
     */
    protected $trialBalanceRepository;

    /**
     * @var StoreProductRepository
     */
    protected $storeProductRepository;

    /**
     * @var CostOfGoodsCalculator
     */
    protected $costOfGoodsCalculator;

    /**
     * @var JobManager
     */
    protected $jobManager;

    /**
     * @var array|string[]
     */
    protected $scheduledRecalculateStoreProduct = array();

    /**
     * @DI\InjectParams({
     *      "trialBalanceRepository" = @DI\Inject("lighthouse.core.document.repository.trial_balance"),
     *      "storeProductRepository" = @DI\Inject("lighthouse.core.document.repository.store_product"),
     *      "costOfGoodsCalculator" = @DI\Inject("lighthouse.core.document.trial_balance.calculator"),
     *      "jobManager" = @DI\Inject("lighthouse.job.manager"),
     * })
     * @param TrialBalanceRepository $trialBalanceRepository
     * @param StoreProductRepository $storeProductRepository
     * @param CostOfGoodsCalculator $costOfGoodsCalculator
     * @param JobManager $jobManager
     */
    public function __construct(
        TrialBalanceRepository $trialBalanceRepository,
        StoreProductRepository $storeProductRepository,
        CostOfGoodsCalculator $costOfGoodsCalculator,
        JobManager $jobManager
    ) {
        $this->trialBalanceRepository = $trialBalanceRepository;
        $this->storeProductRepository = $storeProductRepository;
        $this->costOfGoodsCalculator = $costOfGoodsCalculator;
        $this->jobManager = $jobManager;
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        $this->createJobsForScheduledStoreProducts();
        $this->scheduledRecalculateStoreProduct = array();
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach ($uow->getScheduledDocumentInsertions() as $document) {
            if ($document instanceof StockMovementProduct) {
                $this->onStockMovementProductPersist($document, $dm);
            }
        }

        foreach ($uow->getScheduledDocumentUpdates() as $document) {
            if ($document instanceof StockMovementProduct) {
                $this->onStockMovementProductUpdate($document, $dm);
            }

            if ($document instanceof Invoice) {
                $this->processInvoiceOnAcceptanceDateUpdate($document, $dm, $uow);
            }
        }

        foreach ($uow->getScheduledDocumentDeletions() as $document) {
            if ($document instanceof StockMovementProduct) {
                $this->onStockMovementProductRemove($document, $dm);
            }
        }
    }

    /**
     * @param TrialBalance $trialBalance
     * @param DocumentManager $dm
     */
    protected function processSupportsRangeIndexRemove(TrialBalance $trialBalance, DocumentManager $dm)
    {
        $nextProcessedTrialBalance = $this->trialBalanceRepository->findOneNext(
            $trialBalance,
            $this->costOfGoodsCalculator->getStockMovementTypesByTrialBalance($trialBalance)
        );

        if (null != $nextProcessedTrialBalance) {
            $oldValue = $nextProcessedTrialBalance->processingStatus;
            $nextProcessedTrialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;

            $this->schedulePropertyChange(
                $dm->getUnitOfWork(),
                $nextProcessedTrialBalance,
                'processingStatus',
                $oldValue,
                $nextProcessedTrialBalance->processingStatus
            );
        }

        $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);
    }

    /**
     * @param TrialBalance $trialBalance
     * @param StoreProduct $storeProduct
     * @param DocumentManager $dm
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    protected function processSupportsRangeIndexUpdate(
        TrialBalance $trialBalance,
        StoreProduct $storeProduct,
        DocumentManager $dm
    ) {
        if ($storeProduct != $trialBalance->storeProduct) {
            $needProcessedTrialProduct = $this->trialBalanceRepository->findOnePrevious(
                $trialBalance,
                $this->costOfGoodsCalculator->getStockMovementTypesByTrialBalance($trialBalance)
            );
            if (null == $needProcessedTrialProduct) {
                $needProcessedTrialProduct = $this->trialBalanceRepository->findOneNext(
                    $trialBalance,
                    $this->costOfGoodsCalculator->getStockMovementTypesByTrialBalance($trialBalance)
                );
            }
            if (null != $needProcessedTrialProduct) {
                $needProcessedTrialProduct->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;
                $dm->persist($needProcessedTrialProduct);
                $this->computeChangeSet($dm, $needProcessedTrialProduct);

                $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);
            }
        }

        $trialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;
        $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);
    }

    /**
     * @param TrialBalance $trialBalance
     * @param StockMovementProduct $stockMovementProduct
     * @param StoreProduct $storeProduct
     */
    protected function populateTrialBalanceByStockMovementProduct(
        TrialBalance $trialBalance,
        StockMovementProduct $stockMovementProduct,
        StoreProduct $storeProduct
    ) {
        $trialBalance->price = $stockMovementProduct->price;
        $trialBalance->quantity = $stockMovementProduct->quantity;
        $trialBalance->inventory = $stockMovementProduct->quantity;
        $trialBalance->storeProduct = $storeProduct;
        $trialBalance->reason = $stockMovementProduct;
        $trialBalance->createdDate = clone $stockMovementProduct->date;
    }

    /**
     * @param StockMovementProduct $document
     * @param DocumentManager $dm
     */
    protected function onStockMovementProductPersist(StockMovementProduct $document, DocumentManager $dm)
    {
        $storeProduct = $this->storeProductRepository->findOrCreateByStockMovementProduct($document);
        $dm->persist($storeProduct);
        $this->computeChangeSet($dm, $storeProduct);

        $trialBalance = new TrialBalance();
        $this->populateTrialBalanceByStockMovementProduct($trialBalance, $document, $storeProduct);

        $dm->persist($trialBalance);
        $this->computeChangeSet($dm, $trialBalance);

        $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);
    }
    
    /**
     * @param StockMovementProduct $document
     * @param DocumentManager $dm
     */
    protected function onStockMovementProductUpdate(StockMovementProduct $document, DocumentManager $dm)
    {
        $trialBalance = $this->trialBalanceRepository->findOneByStockMovementProduct($document);

        // FIXME Something wrong here, TrialBalance should be found
        if (!$trialBalance) {
            return;
        }

        $storeProduct = $this->storeProductRepository->findOrCreateByStockMovementProduct($document);

        if ($this->costOfGoodsCalculator->supportsRangeIndex($document)) {
            $this->processSupportsRangeIndexUpdate($trialBalance, $storeProduct, $dm);
        }

        $this->populateTrialBalanceByStockMovementProduct($trialBalance, $document, $storeProduct);

        $dm->persist($storeProduct);
        $dm->persist($trialBalance);

        $this->computeChangeSet($dm, $trialBalance);

        $this->scheduleRecalculateStoreProduct($storeProduct);
    }

    /**
     * @param StockMovementProduct $document
     * @param DocumentManager $dm
     */
    protected function onStockMovementProductRemove(StockMovementProduct $document, DocumentManager $dm)
    {
        $trialBalance = $this->trialBalanceRepository->findOneByStockMovementProduct($document);

        if ($this->costOfGoodsCalculator->supportsRangeIndex($document)) {
            $this->processSupportsRangeIndexRemove($trialBalance, $dm);
        }

        $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);

        $dm->remove($trialBalance);
    }

    /**
     * @param Invoice $invoice
     * @param DocumentManager $dm
     * @param UnitOfWork $uow
     */
    protected function processInvoiceOnAcceptanceDateUpdate(Invoice $invoice, DocumentManager $dm, UnitOfWork $uow)
    {
        $changeSet = $uow->getDocumentChangeSet($invoice);
        if (!isset($changeSet['date'])) {
            return;
        }
        $oldAcceptanceDate = $changeSet['date'][0];
        $newAcceptanceDate = $changeSet['date'][1];

        $invoiceProducts = $invoice->products;
        $trialBalances = $this->trialBalanceRepository->findByStockMovementProducts($invoiceProducts);

        foreach ($invoiceProducts as $invoiceProduct) {
            $invoiceProduct->beforeSave();
            $this->computeChangeSet($dm, $invoiceProduct);
        }

        foreach ($trialBalances as $trialBalance) {
            if ($newAcceptanceDate > $oldAcceptanceDate) {
                $nextTrialBalance = $this->trialBalanceRepository->findOneNext(
                    $trialBalance,
                    $this->costOfGoodsCalculator->getStockMovementTypesByTrialBalance($trialBalance)
                );
                if (null != $nextTrialBalance) {
                    $nextTrialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;
                    $this->computeChangeSet($dm, $nextTrialBalance);
                }
            }

            $trialBalance->createdDate = clone $newAcceptanceDate;
            $trialBalance->processingStatus = TrialBalance::PROCESSING_STATUS_UNPROCESSED;
            $this->computeChangeSet($dm, $trialBalance);

            $this->scheduleRecalculateStoreProduct($trialBalance->storeProduct);
        }
    }

    /**
     * @param StoreProduct $storeProduct
     */
    protected function scheduleRecalculateStoreProduct(StoreProduct $storeProduct)
    {
        $this->scheduledRecalculateStoreProduct[] = $storeProduct->id;
    }

    protected function createJobsForScheduledStoreProducts()
    {
        foreach ($this->scheduledRecalculateStoreProduct as $storeProductId) {
            $job = new CostOfGoodsCalculateJob();
            $job->storeProductId = $storeProductId;
            $this->jobManager->addJob($job);
        }
    }
}
