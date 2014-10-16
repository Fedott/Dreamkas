<?php

namespace Lighthouse\ReportsBundle\Document\GrossMarginSales\Product;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Console\DotHelper;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\Types\Date\DatePeriod;
use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;
use Lighthouse\ReportsBundle\Document\GrossMarginSales\GrossMarginSalesFilter;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class GrossMarginSalesProductRepository extends DocumentRepository
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
     * @param GrossMarginSalesFilter $filter
     * @param SubCategory $catalogGroup
     * @return GrossMarginSalesProduct[]|Cursor
     */
    public function findByFilterCatalogGroup(GrossMarginSalesFilter $filter, SubCategory $catalogGroup)
    {
        $criteria = array(
            'subCategory' => $catalogGroup->id,
            'day' => array(
                '$gte' => $filter->dateFrom,
                '$lte' => $filter->dateTo,
            ),
        );

        if ($filter->store) {
            $criteria['store'] = $filter->store->id;
        }

        return $this->findBy($criteria);
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @param DateTime $day
     * @return GrossMarginSalesProduct
     */
    public function findByStoreIdProductIdAndDay($storeId, $productId, DateTime $day)
    {
        return $this->findOneBy(
            array(
                'store' => $storeId,
                'product' => $productId,
                'day' => $day,
            )
        );
    }

    /**
     * @param OutputInterface $output
     * @param int $batch
     * @return int
     */
    public function recalculate(OutputInterface $output = null, $batch = 5000)
    {
        $output = $output ?: new NullOutput();
        $dotHelper = new DotHelper($output);

        $requireDatePeriod = new DatePeriod("-8 day 00:00", "+1 day 23:59:59");

        $results = $this->aggregateProductByDay($requireDatePeriod->getStartDate(), $requireDatePeriod->getEndDate());
        $count = 0;

        $dotHelper->setTotalPositions(count($results));

        foreach ($results as $result) {
            $report = new GrossMarginSalesProduct();
            $report->day = DateTimestamp::createFromParts(
                $result['_id']['year'],
                $result['_id']['month'],
                $result['_id']['day']
            );
            $report->costOfGoods = $this->numericFactory->createMoneyFromCount($result['costOfGoodsSum']);
            $report->quantity = $this->numericFactory->createQuantityFromCount($result['quantitySum']);
            $report->grossSales = $this->numericFactory->createMoneyFromCount($result['grossSales']);
            $report->grossMargin = $this->numericFactory->createMoneyFromCount($result['grossMargin']);
            $report->product = $this->dm->getReference(Product::getClassName(), $result['_id']['product']);
            $report->subCategory = $this->dm->getReference(SubCategory::getClassName(), $result['_id']['subCategory']);
            $report->store = $this->dm->getReference(Store::getClassName(), $result['_id']['store']);

            $this->dm->persist($report);
            $count++;
            $dotHelper->write();

            if ($count % $batch == 0) {
                $this->dm->flush();
            }
        }

        $this->dm->flush();

        $dotHelper->end();

        return $count;
    }

    /**
     * @param DateTimestamp $startDate
     * @param DateTimestamp $endDate
     * @return ArrayIterator
     */
    protected function aggregateProductByDay(DateTimestamp $startDate, DateTimestamp $endDate)
    {
        $ops = array(
            array(
                '$match' => array(
                    'createdDate.date' => array(
                        '$gte' => $startDate->getMongoDate(),
                        '$lt' => $endDate->getMongoDate(),
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
                    'subCategory' => true,
                    'product' => true,
                    'year' => '$createdDate.year',
                    'month' => '$createdDate.month',
                    'day' => '$createdDate.day'
                ),
            ),
            array(
                '$group' => array(
                    '_id' => array(
                        'store' => '$store',
                        'product' => '$product',
                        'subCategory' => '$subCategory',
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
