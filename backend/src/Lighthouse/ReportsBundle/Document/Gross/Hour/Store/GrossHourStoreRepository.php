<?php

namespace Lighthouse\ReportsBundle\Document\Gross\Hour\Store;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;

class GrossHourStoreRepository extends DocumentRepository
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
     * @return ArrayIterator
     */
    public function recalculate()
    {
        $quantityPrecision = $this->numericFactory->createQuantity(0)->getPrecision();

        $ops = array(
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
                    'store' => true,
                    'hourDate' => '$createdDate.hourDate',
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'hourDate' => '$hourDate',
                        'store' => '$store'
                    ),
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
                    'store' => '$_id.store',
                    'hourDate' => '$_id.hourDate'
                )
            ),
            array(
                '$out' => $this->getDocumentCollection()->getName()
            )
        );

        return $this->trialBalanceRepository->aggregate($ops);
    }

    /**
     * @return Cursor|GrossHourStore[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('hourDate' => self::SORT_ASC, 'store' => self::SORT_ASC));
    }
}
