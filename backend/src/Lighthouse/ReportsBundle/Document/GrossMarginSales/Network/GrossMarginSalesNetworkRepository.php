<?php

namespace Lighthouse\ReportsBundle\Document\GrossMarginSales\Network;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSales;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSalesFilter;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSalesRepository;
use DateTime;

class GrossMarginSalesNetworkRepository extends GrossMarginSalesRepository
{
    /**
     * @param GrossMarginSalesFilter $filter
     * @return Cursor|GrossMarginSalesNetwork[]
     */
    public function findByFilter(GrossMarginSalesFilter $filter)
    {
        $criteria = array(
            'day' => array(
                '$gte' => $filter->dateFrom,
                '$lt' => $filter->dateTo,
            )
        );
        return $this->findBy($criteria);
    }

    /**
     * @param DateTime $day
     * @return GrossMarginSalesNetwork
     */
    public function findByDay(DateTime $day)
    {
        return $this->findOneBy(
            array(
                'day' => $day,
            )
        );
    }

    /**
     * @param array $result
     * @return GrossMarginSales
     */
    protected function createReport(array $result)
    {
        return new GrossMarginSalesNetwork();
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
                    'year' => '$createdDate.year',
                    'month' => '$createdDate.month',
                    'day' => '$createdDate.day'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
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
