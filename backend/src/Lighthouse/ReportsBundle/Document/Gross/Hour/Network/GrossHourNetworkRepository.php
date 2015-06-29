<?php

namespace Lighthouse\ReportsBundle\Document\Gross\Hour\Network;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\MongoDB\CommandCursor;
use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;

class GrossHourNetworkRepository extends DocumentRepository
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
     * @param TrialBalanceRepository $trialBalanceRepository
     */
    public function setTrialBalanceRepository(TrialBalanceRepository $trialBalanceRepository)
    {
        $this->trialBalanceRepository = $trialBalanceRepository;
    }

    /**
     * @param NumericFactory $numericFactory
     */
    public function setNumericFactory(NumericFactory $numericFactory)
    {
        $this->numericFactory = $numericFactory;
    }

    /**
     * @param DateTimestamp $dateFrom
     * @param bool          $out
     * @return array        pipeline ops
     */
    protected function getAggregatePipeline(DateTimestamp $dateFrom = null, $out = false)
    {
        $quantityPrecision = $this->numericFactory->createQuantity(0)->getPrecision();
        $pipeline = array(
            array(
                '$match' => array(
                    'reason.$ref' => SaleProduct::TYPE,
                ),
            ),
            array(
                '$sort' => array(
                    'createdDate.hourDate' => self::SORT_ASC,
                )
            ),
            array(
                '$project' => array(
                    'totalPrice' => true,
                    'costOfGoods' => true,
                    'quantity' => true,
                    'hourDate' => '$createdDate.hourDate',
                ),
            ),
            array(
                '$group' => array(
                    '_id' => '$hourDate',
                    'grossSales' => array(
                        '$sum' => '$totalPrice'
                    ),
                    'costOfGoods' => array(
                        '$sum' => '$costOfGoods'
                    ),
                    'quantity' => array(
                        '$sum' => '$quantity.count'
                    ),
                    'grossMargin' => array(
                        '$sum' => array(
                            '$subtract' => array('$totalPrice', '$costOfGoods')
                        ),
                    )
                ),
            ),
            array(
                '$project' => array(
                    'grossSales' => true,
                    'costOfGoods' => true,
                    'grossMargin' => true,
                    'quantity' => array(
                        'count' => '$quantity',
                        'precision' => array('$literal' => $quantityPrecision),
                    ),
                    'hourDate' => '$_id'
                )
            ),
        );

        if ($dateFrom) {
            $pipeline[0]['$match']['createdDate.hourDate'] = array(
                '$gte' => $dateFrom->setMinutes(0)->setSeconds(0)->getMongoDate(),
            );
        }

        if ($out) {
            $pipeline[] = array(
                '$out' => $this->getDocumentCollection()->getName()
            );
        } else {
            $pipeline[] = array(
                '$sort' => array(
                    'hourDate' => self::SORT_ASC,
                )
            );
        }

        return $pipeline;
    }

    /**
     * @param DateTimestamp $dateFrom
     * @return ArrayIterator
     */
    public function recalculateAll(DateTimestamp $dateFrom = null)
    {
        $pipeline = $this->getAggregatePipeline($dateFrom, true);
        return $this->trialBalanceRepository->aggregate($pipeline);
    }

    /**
     * @param DateTimestamp $dateFrom
     * @return CommandCursor|GrossHourNetwork[]
     */
    public function recalculateCursor(DateTimestamp $dateFrom = null)
    {
        $pipeline = $this->getAggregatePipeline($dateFrom);
        return $this->trialBalanceRepository->aggregateCursor($pipeline, $this->class);
    }

    /**
     * @return Cursor|GrossHourNetwork[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('hourDate' => self::SORT_ASC));
    }
}
