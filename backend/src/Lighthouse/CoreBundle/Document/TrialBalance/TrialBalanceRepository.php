<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Query\Expr;
use Doctrine\ODM\MongoDB\Query\Query;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\StockMovement\Invoice\InvoiceProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Date\DatePeriod;
use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;
use MongoCode;
use MongoId;

class TrialBalanceRepository extends DocumentRepository
{
    const MAX_STORE_GROSS_SALES_REPORT_AGGREGATION = 150000;

    /**
     * @param $storeProductId
     * @return Cursor|TrialBalance[]
     */
    public function findByStoreProduct($storeProductId)
    {
        return $this->findBy(
            array('storeProduct' => $storeProductId),
            array('createdDate.date' => self::SORT_ASC, '_id' => self::SORT_ASC)
        );
    }

    /**
     * @param string $storeProductId
     * @param string $reasonType
     * @return Cursor|TrialBalance[]
     */
    public function findByStoreProductIdAndReasonType($storeProductId, $reasonType)
    {
        $criteria = array(
            'storeProduct' => $storeProductId,
            'reason.$ref' => $reasonType
        );
        $sort = array(
            'createdDate.date' => self::SORT_ASC,
            '_id' => self::SORT_ASC
        );
        return $this->findBy($criteria, $sort);
    }

    /**
     * @param StockMovementProduct $reason
     * @return TrialBalance
     */
    public function findOneByStockMovementProduct(StockMovementProduct $reason)
    {
        return $this->findOneByReasonTypeReasonId($reason->id, $reason->getType());
    }

    /**
     * @param TrialBalance $trialBalance
     * @param array $reasonTypes
     * @return null|TrialBalance
     */
    public function findOneNext(TrialBalance $trialBalance, array $reasonTypes)
    {
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $trialBalance->storeProduct->id,
            'createdDate.date' => array('$gte' => $trialBalance->createdDate),
            '_id' => array('$ne' => $trialBalance->id),
        );
        $sort = array(
            'createdDate.date' => self::SORT_ASC,
            '_id' => self::SORT_ASC,
        );
        return $this->findOneBy($criteria, $sort);
    }

    /**
     * @param TrialBalance $trialBalance
     * @param array $reasonTypes
     * @return TrialBalance
     */
    public function findOnePrevious(TrialBalance $trialBalance, array $reasonTypes)
    {
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $trialBalance->storeProduct->id,
            'createdDate.date' => array('$lte' => $trialBalance->createdDate),
            '_id' => array('$ne' => $trialBalance->id),
        );
        $sort = array(
            'createdDate.date' => self::SORT_DESC,
            '_id' => self::SORT_DESC
        );
        return $this->findOneBy($criteria, $sort);
    }

    /**
     * @param TrialBalance $trialBalance
     * @param array $reasonTypes
     * @return TrialBalance
     */
    public function findOnePreviousDateByReasonsTypes(TrialBalance $trialBalance, array $reasonTypes)
    {
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $trialBalance->storeProduct->id,
            'createdDate.date' => array('$lt' => $trialBalance->createdDate),
        );
        $sort = array(
            'createdDate.date' => self::SORT_DESC,
            '_id' => self::SORT_DESC
        );
        return $this->findOneBy($criteria, $sort);
    }

    /**
     * @param StockMovementProduct[] $reasons
     * @return TrialBalance[]|Cursor
     */
    public function findByStockMovementProducts($reasons)
    {
        if (0 == count($reasons)) {
            return array();
        }

        $reasonTypes = array();
        foreach ($reasons as $reason) {
            $reasonTypes[$reason->getType()][] = new MongoId($reason->id);
        }

        $query = $this->createQueryBuilder()->find();

        foreach ($reasonTypes as $reasonType => $reasonIds) {
            /* @var Expr $reasonQuery */
            $reasonQuery = $query->expr();
            $reasonQuery->field('reason.$id')->in($reasonIds);
            $reasonQuery->field('reason.$ref')->equals($reasonType);

            $query->addOr($reasonQuery->getQuery());
        }

        /* @var Cursor $result */
        $cursor = $query->getQuery()->execute();

        return $cursor;
    }

    /**
     * @param StoreProduct $storeProduct
     * @param InvoiceProduct $invoiceProduct
     * @return TrialBalance
     */
    public function findOneByStoreProduct(StoreProduct $storeProduct, InvoiceProduct $invoiceProduct = null)
    {
        $criteria = array('storeProduct' => $storeProduct->id);
        if (null !== $invoiceProduct) {
            $criteria['reason.$id'] = array('$ne' => new MongoId($invoiceProduct->id));
            //$criteria['reason.$ref'] = array('$ne' => 'InvoiceProduct');
        }
        // Ugly hack to force document refresh
        $hints = array(Query::HINT_REFRESH => true);
        $sort = array(
            'createdDate.date' => self::SORT_DESC,
            '_id' => self::SORT_DESC,
        );
        return $this->uow->getDocumentPersister($this->documentName)->load($criteria, null, $hints, 0, $sort);
    }

    /**
     * @param StoreProduct $storeProduct
     * @return TrialBalance
     */
    public function findOneReasonInvoiceProductByProduct(StoreProduct $storeProduct)
    {
        $criteria = array('storeProduct' => $storeProduct->id);
        $criteria['reason.$ref'] = InvoiceProduct::TYPE;
        // Ugly hack to force document refresh
        $hints = array(Query::HINT_REFRESH => true);
        $sort = array(
            'createdDate.date' => self::SORT_DESC,
            '_id' => self::SORT_DESC,
        );
        return $this->uow->getDocumentPersister($this->documentName)->load($criteria, null, $hints, 0, $sort);
    }

    /**
     * @param string $reasonId
     * @param string $reasonType
     * @return null|TrialBalance
     */
    public function findOneByReasonTypeReasonId($reasonId, $reasonType)
    {
        $criteria = array(
            'reason.$id' => new MongoId($reasonId),
            'reason.$ref' => $reasonType,
        );
        return $this->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function calculateAveragePurchasePrice()
    {
        if ($this->isCollectionEmpty()) {
            return array();
        }

        $datePeriod = new DatePeriod("-30 day 00:00", "00:00");

        $query = $this
            ->createQueryBuilder()
            ->field('createdDate.date')->gte($datePeriod->getStartDate()->getMongoDate())
            ->field('createdDate.date')->lt($datePeriod->getEndDate()->getMongoDate())
            ->field('reason.$ref')->equals(InvoiceProduct::TYPE)
            ->sort(array('storeProduct' => 1))
            ->map(
                new MongoCode(
                    "function() {
                        emit(
                            this.storeProduct,
                            {
                                totalPrice: this.totalPrice,
                                quantity: this.quantity.count
                            }
                        )
                    }"
                )
            )
            ->reduce(
                new MongoCode(
                    "function(storeProductId, obj) {
                        var reducedObj = {totalPrice: 0, quantity: 0}
                        for (var item in obj) {
                            reducedObj.totalPrice += obj[item].totalPrice;
                            reducedObj.quantity += obj[item].quantity;
                        }
                        return reducedObj;
                    }"
                )
            )
//            ->finalize(
//                new MongoCode(
//                    "function(storeProductId, obj) {
//                        if (obj.quantity > 0) {
//                            obj.averagePrice = obj.totalPrice / obj.quantity;
//                        } else {
//                            obj.averagePrice = null;
//                        }
//                        return obj;
//                    }"
//                )
//            )
            ->out(array('inline' => true));

        return $query->getQuery()->execute();
    }

    /**
     * @return array
     */
    public function calculateDailyAverageSalesAggregate()
    {
        $datePeriod = new DatePeriod("-30 day 00:00", "00:00");
        $days = $datePeriod->diff()->days;
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getClassName());

        $ops = array(
            array(
                '$match' => array(
                    'createdDate.date' => array(
                        '$gte' => $datePeriod->getStartDate()->getMongoDate(),
                        '$lt' => $datePeriod->getEndDate()->getMongoDate(),
                    ),
                    'reason.$ref' => SaleProduct::TYPE
                ),
            ),
            array(
                '$sort' => array(
                    'storeProduct' => 1,
                ),
            ),
            array(
                '$group' => array(
                    '_id' => '$storeProduct',
                    'total' => array('$sum' => '$quantity.count')
                )
            ),
//            array(
//                '$project' => array(
//                    'value' => array(
//                        'dailyAverageSales' => array('$divide' => array('$total', $days))
//                    )
//                )
//            )
        );
        $result = $collection->getMongoCollection()->aggregate($ops);
        $results = array(
            'results' => $result['result'],
            'days' => $days
        );
        return $results;
    }

    /**
     * @return array
     */
    public function calculateDailyAverageSales()
    {
        if ($this->isCollectionEmpty()) {
            return array();
        }

        $datePeriod = new DatePeriod("-30 day 00:00", "00:00");
        $days = $datePeriod->diff()->days;
        $query = $this
            ->createQueryBuilder()
            ->field('createdDate.date')->gt($datePeriod->getStartDate()->getMongoDate())
            ->field('createdDate.date')->lt($datePeriod->getEndDate()->getMongoDate())
            ->field('reason.$ref')->equals(SaleProduct::TYPE)
            ->sort(array('storeProduct' => 1))
            ->map(
                new MongoCode(
                    "function() {
                        emit(
                            this.storeProduct,
                            {
                                quantity: this.quantity.count
                            }
                        )
                    }"
                )
            )
            ->reduce(
                new MongoCode(
                    "function (storeProductId, obj) {
                        var reducedObj = {quantity: 0}
                        for (var item in obj) {
                            reducedObj.quantity += obj[item].quantity;
                        }
                        return reducedObj;
                    }"
                )
            )
            ->finalize(
                new MongoCode(
                    sprintf(
                        "function(storeProductId, obj) {
                            if (obj.quantity > 0) {
                                obj.dailyAverageSales = obj.quantity / %d;
                            } else {
                                obj.dailyAverageSales = null;
                            }
                            return obj;
                        }",
                        $days
                    )
                )
            )
            ->out(array('inline' => true));

        return $query->getQuery()->execute();
    }

    /**
     * @return ArrayIterator
     */
    public function calculateGrossSales()
    {
        if ($this->isCollectionEmpty()) {
            return array();
        }

        $datePeriod = new DatePeriod("-8 day 00:00", "+1 day 23:59:59");

        $ops = array(
            array(
                '$match' => array(
                    'createdDate.date' => array(
                        '$gte' => $datePeriod->getStartDate()->getMongoDate(),
                        '$lt' => $datePeriod->getEndDate()->getMongoDate(),
                    ),
                    'reason.$ref' => SaleProduct::TYPE,
                ),
            ),
            array(
                '$sort' => array(
                    'createdDate.date' => self::SORT_ASC,
                )
            ),
            array(
                '$project' => array(
                    'store' => 1,
                    'totalPrice' => 1,
                    'year' => array('$year' => '$createdDate.date'),
                    'month' => array('$month' => '$createdDate.date'),
                    'day' => array('$dayOfMonth' => '$createdDate.date'),
                    'hour' => array('$hour' => '$createdDate.date'),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'store' => '$store',
                        'year' => '$year',
                        'month' => '$month',
                        'day' => '$day',
                        'hour' => '$hour',
                    ),
                    'hourSum' => array('$sum' => '$totalPrice'),
                ),
            ),
        );

        return $this->aggregate($ops);
    }

    /**
     * @param Store[] $stores
     * @param int $countProducts
     * @return array
     * @throws \Exception
     */
    public function calculateGrossSalesProduct($stores, $countProducts)
    {
        $results = array(
            'reports' => array(),
            'totalCount' => 0,
        );

        if ($this->isCollectionEmpty()) {
            return $results;
        }

        $requireDatePeriod = new DatePeriod("-8 day 00:00", "+1 day 23:59:59");

        $maxHoursStep = (int) (self::MAX_STORE_GROSS_SALES_REPORT_AGGREGATION / $countProducts);
        if ($maxHoursStep < 1) {
            $maxHoursStep = 1;
        }

        $totalDurationAggregateTime = 0.0;
        $totalMergeTime = 0.0;

        foreach ($stores as $store) {
            $startDate = clone $requireDatePeriod->getStartDate();
            $endDate = clone $startDate;
            $endDate->modify("+{$maxHoursStep} hour");
            if ($endDate > $requireDatePeriod->getEndDate()) {
                $endDate = $requireDatePeriod->getEndDate();
            }

            while (1) {
                $startAggregateTime = microtime(true);
                $stepResult = $this->grossSalesProductAggregate($startDate, $endDate, $store);
                $durationAggregateTime = microtime(true) - $startAggregateTime;

                $startMergeTime = microtime(true);
                if (0 !== count($stepResult)) {
                    $results['reports'][] = $stepResult;
                    $results['totalCount'] += count($stepResult);
                }
                $durationMergeTime = microtime(true) - $startMergeTime;

                $totalDurationAggregateTime += $durationAggregateTime;
                $totalMergeTime += $durationMergeTime;

                if ($endDate >= $requireDatePeriod->getEndDate()) {
                    break;
                }

                $startDate = clone $endDate;
                $endDate->modify("+{$maxHoursStep} hour");
                if ($endDate > $requireDatePeriod->getEndDate()) {
                    $endDate = $requireDatePeriod->getEndDate();
                }
            }
        }

        return $results;
    }

    /**
     * @param DateTimestamp $startDate
     * @param DateTimestamp $endDate
     * @param Store $store
     * @return array
     */
    protected function grossSalesProductAggregate(
        DateTimestamp $startDate,
        DateTimestamp $endDate,
        Store $store
    ) {
        $ops = array(
            array(
                '$match' => array(
                    'createdDate.date' => array(
                        '$gte' => $startDate->getMongoDate(),
                        '$lt' => $endDate->getMongoDate(),
                    ),
                    'reason.$ref' => SaleProduct::TYPE,
                    'store' => new MongoId($store->id),
                ),
            ),
            array(
                '$sort' => array(
                    'createdDate.date' => 1,
                )
            ),
            array(
                '$project' => array(
                    'storeProduct' => 1,
                    'totalPrice' => 1,
                    'quantity' => 1,
                    'year' => array('$year' => '$createdDate.date'),
                    'month' => array('$month' => '$createdDate.date'),
                    'day' => array('$dayOfMonth' => '$createdDate.date'),
                    'hour' => array('$hour' => '$createdDate.date'),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'storeProduct' => '$storeProduct',
                        'year' => '$year',
                        'month' => '$month',
                        'day' => '$day',
                        'hour' => '$hour',
                    ),
                    'hourSum' => array('$sum' => '$totalPrice'),
                    'hourQuantity' => array('$sum' => '$quantity.count'),
                ),
            ),
        );


        return $this->aggregate($ops);
    }

    /**
     * @param string|string[] $reasonTypes
     * @param string $storeProductId
     * @param Quantity $startIndex
     * @param Quantity $endIndex
     * @return Cursor|TrialBalance[]
     */
    public function findByIndexRange($reasonTypes, $storeProductId, Quantity $startIndex, Quantity $endIndex)
    {
        if (!is_array($reasonTypes)) {
            $reasonTypes = array($reasonTypes);
        }
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $storeProductId,
            'endIndex.count' => array('$gt' => $startIndex->getCount()),
            'startIndex.count' => array('$lt' => $endIndex->getCount()),
        );
        $sort = array(
            'createdDate.date' => self::SORT_ASC,
            '_id' => self::SORT_ASC,
        );
        return $this->findBy($criteria, $sort);
    }

    /**
     * @param string|string[] $reasonTypes
     * @param string $storeProductId
     * @param Quantity $startIndex
     * @param Quantity $endIndex
     * @return TrialBalance|null
     */
    public function findOneByIndexRange($reasonTypes, $storeProductId, Quantity $startIndex, Quantity $endIndex)
    {
        if (!is_array($reasonTypes)) {
            $reasonTypes = array($reasonTypes);
        }
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $storeProductId,
            'endIndex.count' => array('$gt' => $startIndex->getCount()),
            'startIndex.count' => array('$lt' => $endIndex->getCount()),
        );
        $sort = array(
            'createdDate.date' => self::SORT_ASC,
            '_id' => self::SORT_ASC,
        );
        return $this->findOneBy($criteria, $sort);
    }

    /**
     * @param string $reasonTypes
     * @param string $storeProductId
     * @param Quantity $startIndex
     * @return null|TrialBalance
     */
    public function findOneByPreviousEndIndex($reasonTypes, $storeProductId, Quantity $startIndex)
    {
        if (!is_array($reasonTypes)) {
            $reasonTypes = array($reasonTypes);
        }
        $criteria = array(
            'reason.$ref' => array('$in' => $reasonTypes),
            'storeProduct' => $storeProductId,
            'startIndex.count' => array('$lte' => $startIndex->getCount()),
            'endIndex.count' => array('$gt' => $startIndex->getCount()),
        );
        $sort = array(
            'createdDate.date' => self::SORT_ASC,
            '_id' => self::SORT_ASC,
        );
        return $this->findOneBy($criteria, $sort);
    }

    /**
     * @param string[] $reasonTypes
     * @return array
     */
    public function getUnprocessedTrialBalanceGroupStoreProduct(array $reasonTypes)
    {
        $ops = array(
            array(
                '$match' => array(
                    'processingStatus' => TrialBalance::PROCESSING_STATUS_UNPROCESSED,
                    'reason.$ref' => array('$in' => $reasonTypes),
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'storeProduct' => '$storeProduct',
                    )
                )
            )
        );

        return $this->aggregate($ops);
    }

    /**
     * @param string $storeProductId
     * @param array $reasonTypes
     * @return null|TrialBalance
     */
    public function findOneFirstUnprocessedByStoreProductIdReasonType($storeProductId, array $reasonTypes)
    {
        return $this->findOneBy(
            array(
                'storeProduct' => $storeProductId,
                'reason.$ref' => array('$in'=> $reasonTypes),
                'processingStatus' => TrialBalance::PROCESSING_STATUS_UNPROCESSED,
            ),
            array(
                'createdDate.date' => self::SORT_ASC,
                '_id' => self::SORT_ASC,
            )
        );
    }

    /**
     * @param TrialBalance $trialBalance
     * @param array $reasonTypes
     * @return Cursor|TrialBalance[]
     */
    public function findByStartTrialBalanceDateStoreProductReasonTypes(TrialBalance $trialBalance, array $reasonTypes)
    {
        return $this->findBy(
            array(
                'createdDate.date' => array('$gte' => $trialBalance->createdDate),
                'reason.$ref' => array('$in' => $reasonTypes),
                'storeProduct' => $trialBalance->storeProduct->id
            ),
            array(
                'createdDate.date' => self::SORT_ASC,
                '_id' => self::SORT_ASC,
            )
        );
    }
}
