<?php

namespace Lighthouse\ReportsBundle\Document\GrossMarginSales\Store;

use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use Lighthouse\CoreBundle\Util\Iterator\ArrayIterator;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSales;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSalesFilter;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSalesRepository;

class GrossMarginSalesStoreRepository extends GrossMarginSalesRepository
{
    /**
     * @param GrossMarginSalesFilter $filter
     * @return Cursor|GrossMarginSalesStore[]
     */
    public function findByFilter(GrossMarginSalesFilter $filter)
    {
        $criteria = array(
            'day' => array(
                '$gte' => $filter->dateFrom,
                '$lte' => $filter->dateTo,
            )
        );

        return $this->findBy($criteria);
    }

    /**
     * @param array $result
     * @return GrossMarginSales
     */
    protected function createReport(array $result)
    {
        $report = new GrossMarginSalesStore();
        $report->store = $this->dm->getReference(Store::getClassName(), $result['_id']['store']);

        $this->setReportValues($report, $result);

        return $report;
    }

    /**
     * @param DateTimestamp $dateFrom
     * @param DateTimestamp $dateTo
     * @return ArrayIterator
     */
    protected function aggregateByDays(DateTimestamp $dateFrom, DateTimestamp $dateTo)
    {
        $ops = array(
            array(
                '$match' => array(
                    'createdDate.date' => array(
                        '$gte' => $dateFrom->getMongoDate(),
                        '$lt' => $dateTo->getMongoDate(),
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
                    'totalPrice' => true,
                    'costOfGoods' => true,
                    'quantity' => true,
                    'store' => true,
                    'year' => '$createdDate.year',
                    'month' => '$createdDate.month',
                    'day' => '$createdDate.day'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'store' => '$store',
                        'year' => '$year',
                        'month' => '$month',
                        'day' => '$day',
                    ),
                    'grossSales' => array(
                        '$sum' => '$totalPrice'
                    ),
                    'costOfGoodsSum' => array(
                        '$sum' => '$costOfGoods'
                    ),
                    'quantitySum' => array(
                        '$sum' => '$quantity.count'
                    ),
                    'grossMargin' => array(
                        '$sum' => array(
                            '$subtract' => array('$totalPrice', '$costOfGoods')
                        ),
                    )
                ),
            ),
        );

        return $this->trialBalanceRepository->aggregate($ops);
    }
}
