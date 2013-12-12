<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Query\Expr;
use Doctrine\ODM\MongoDB\Query\Query;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\Invoice\Product\InvoiceProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Document\Sale\Product\SaleProduct;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Date\DatePeriod;
use MongoId;
use MongoCode;
use MongoCursor;

class TrialBalanceRepository extends DocumentRepository
{
    const MAX_STORE_GROSS_SALES_REPORT_AGGREGATION = 150000;

    /**
     * @param $storeProductId
     * @return Cursor
     */
    public function findByStoreProduct($storeProductId)
    {
        return $this->findBy(array('storeProduct' => $storeProductId));
    }

    /**
     * @param Reasonable $reason
     * @return TrialBalance
     */
    public function findOneByReason(Reasonable $reason)
    {
        $criteria = array(
            'reason.$id' => new \MongoId($reason->getReasonId()),
            'reason.$ref' => $reason->getReasonType(),
        );
        return $this->findOneBy($criteria);
    }

    /**
     * @param Reasonable[] $reasons
     * @return TrialBalance[]|Cursor
     */
    public function findByReasons($reasons)
    {
        $reasonTypes = array();
        foreach ($reasons as $reason) {
            $reasonTypes[$reason->getReasonType()][] = new MongoId($reason->getReasonId());
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
            'createdDate' => self::SORT_DESC,
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
        $criteria['reason.$ref'] = InvoiceProduct::REASON_TYPE;
        // Ugly hack to force document refresh
        $hints = array(Query::HINT_REFRESH => true);
        $sort = array(
            'createdDate' => self::SORT_DESC,
            '_id' => self::SORT_DESC,
        );
        return $this->uow->getDocumentPersister($this->documentName)->load($criteria, null, $hints, 0, $sort);
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
            ->field('createdDate')->gt($datePeriod->getStartDate()->getMongoDate())
            ->field('createdDate')->lt($datePeriod->getEndDate()->getMongoDate())
            ->field('reason.$ref')->equals(InvoiceProduct::REASON_TYPE)
            ->map(
                new MongoCode(
                    "function() {
                        emit(
                            this.storeProduct,
                            {
                                totalPrice: this.totalPrice,
                                quantity: this.quantity
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
            ->finalize(
                new MongoCode(
                    "function(storeProductId, obj) {
                        if (obj.quantity > 0) {
                            obj.averagePrice = obj.totalPrice / obj.quantity;
                        } else {
                            obj.averagePrice = null;
                        }
                        return obj;
                    }"
                )
            )
            ->out(array('inline' => true));

        return $query->getQuery()->execute();
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
            ->field('createdDate')->gt($datePeriod->getStartDate()->getMongoDate())
            ->field('createdDate')->lt($datePeriod->getEndDate()->getMongoDate())
            ->field('reason.$ref')->equals(SaleProduct::REASON_TYPE)
            ->map(
                new MongoCode(
                    "function() {
                        emit(
                            this.storeProduct,
                            {
                                quantity: this.quantity
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
     * @return array
     */
    public function calculateGrossSales()
    {
        if ($this->isCollectionEmpty()) {
            return array();
        }

        $datePeriod = new DatePeriod("-10 day 00:00", "+1 day 23:59:59");

        $query = $this
            ->createQueryBuilder()
            ->field('createdDate')->gt($datePeriod->getStartDate()->getMongoDate())
            ->field('createdDate')->lt($datePeriod->getEndDate()->getMongoDate())
            ->field('reason.$ref')->equals(SaleProduct::REASON_TYPE)
            ->map(
                new MongoCode(
                    "function() {
                        var date = this.createdDate;
                        var createHour = date.getHours();
                        date.setHours(0, 0, 0, 0);
                        var key = {
                            store: this.store,
                            day: date
                        }
                        var grossSales = {};
                        for (var newHour = 0; newHour < 24; newHour++) {
                            grossSales[newHour] = {
                                runningSum: 0,
                                hourSum: 0
                            };
                        }

                        for (var hour in grossSales) {
                            if (hour >= createHour) {
                                grossSales[hour].runningSum += this.totalPrice;
                            }
                            if (hour == createHour) {
                                grossSales[hour].hourSum += this.totalPrice;
                            }
                        }
                        emit(
                            key,
                            grossSales
                        )
                    }"
                )
            )
            ->reduce(
                new MongoCode(
                    "function (key, grossSalesItems) {
                        var reducedGrossSales = {};
                        for (var newHour = 0; newHour < 24; newHour++) {
                            reducedGrossSales[newHour] = {
                                runningSum: 0,
                                hourSum: 0
                            };
                        }

                        for (var item in grossSalesItems) {
                            var grossSale = grossSalesItems[item];
                            for (var hour in grossSale) {
                                reducedGrossSales[hour].runningSum += grossSale[hour].runningSum;
                                reducedGrossSales[hour].hourSum += grossSale[hour].hourSum;
                            }
                        }

                        return reducedGrossSales;
                    }"
                )
            )
            ->out(array('inline' => true));

        return $query->getQuery()->execute();
    }

    /**
     * @param Store[] $stores
     * @param int $countProducts
     * @return array
     * @throws \Exception
     */
    public function calculateGrossSalesProduct($stores, $countProducts)
    {
        if ($this->isCollectionEmpty()) {
            return array();
        }

        $collection = $this->getDocumentManager()->getDocumentCollection($this->getClassName());

        $backupTimeout = MongoCursor::$timeout;
        MongoCursor::$timeout = -1;

        $requireDatePeriod = new DatePeriod("-8 day 00:00", "+1 day 23:59:59");

        $maxHoursStep = self::MAX_STORE_GROSS_SALES_REPORT_AGGREGATION / $countProducts;

        $result = array();

        foreach ($stores as $store) {
            $startDate = clone $requireDatePeriod->getStartDate();
            $endDate = clone $startDate;
            $endDate->modify("+{$maxHoursStep} hour");
            if ($endDate > $requireDatePeriod->getEndDate()) {
                $endDate = $requireDatePeriod->getEndDate();
            } else {
                $endDate->modify("+{$maxHoursStep} hour");
            }

            while (1) {
                $ops = array(
                    array(
                        '$match' => array(
                            'createdDate' => array(
                                '$gte' => $startDate->getMongoDate(),
                                '$lt' => $endDate->getMongoDate(),
                            ),
                            'reason.$ref' => SaleProduct::REASON_TYPE,
                            'store' => new MongoId($store->id),
                        ),
                    ),
                    array(
                        '$sort' => array(
                            'createdDate' => 1,
                        )
                    ),
                    array(
                        '$project' => array(
                            'storeProduct' => 1,
                            'totalPrice' => 1,
                            'year' => array('$year' => '$createdDate'),
                            'month' => array('$month' => '$createdDate'),
                            'day' => array('$dayOfMonth' => '$createdDate'),
                            'hour' => array('$hour' => '$createdDate'),
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
                        ),
                    ),
                );

                $stepResult = $collection->getMongoCollection()->aggregate($ops);
                if (1 == $stepResult['ok']) {
                    $result = array_merge($result, $stepResult['result']);

                    if ($endDate >= $requireDatePeriod->getEndDate()) {
                        break;
                    }

                    $startDate = clone $endDate;
                    $endDate->modify("+{$maxHoursStep} hour");
                    if ($endDate > $requireDatePeriod->getEndDate()) {
                        $endDate = $requireDatePeriod->getEndDate();
                    }
                } else {
                    throw new \Exception($stepResult['errmsg']);
                    break;
                }
            }


        }

        MongoCursor::$timeout = $backupTimeout;

        return $result;
    }
}
