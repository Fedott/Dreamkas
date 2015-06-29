<?php

namespace Lighthouse\CoreBundle\Document\StockMovement;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Mapping;
use Lighthouse\CoreBundle\Document\StockMovement\Returne\Returne;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\Sale;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleFilter;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Exception\RuntimeException;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @method Receipt find($id, $lockMode = LockMode::NONE, $lockVersion = null)
 * @method Receipt findOneBy(array $criteria, array $sort = array(), array $hints = array())
 */
class ReceiptRepository extends StockMovementRepository
{
    /**
     * @var StockMovementProductRepository
     */
    protected $saleProductRepository;

    /**
     * @param StockMovementProductRepository $saleRepository
     */
    public function setSaleProductRepository(StockMovementProductRepository $saleRepository)
    {
        $this->saleProductRepository = $saleRepository;
    }

    /**
     * @param string $type
     * @return Returne|Sale
     */
    public function createNewByType($type)
    {
        switch ($type) {
            case Sale::TYPE:
                $receipt = new Sale();
                break;
            case Returne::TYPE:
                $receipt = new Returne();
                break;
            default:
                throw new RuntimeException(sprintf("Invalid receipt type given: '%s'", $type));
        }

        $receipt->sumTotal = $this->numericFactory->createMoney();
        return $receipt;
    }

    /**
     * @param string $hash
     */
    public function rollbackByHash($hash)
    {
        $receipt = $this->findOneBy(array('hash' => $hash));
        $this->dm->remove($receipt);
        $this->dm->flush();
    }

    /**
     * @param array $criteria
     * @return Cursor|Receipt[]
     */
    public function findReceiptBy(array $criteria)
    {
        $queryBuilder = $this->dm->createQueryBuilder(Receipt::getClassName());
        foreach ($criteria as $field => $value) {
            $queryBuilder->field($field)->equals($value);
        }

        $result = $queryBuilder->getQuery()->execute();
        return $result;
    }

    /**
     * @param SaleFilter $filter
     * @return Sale[]|Cursor
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findSalesByFilter(SaleFilter $filter)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('type')->equals(Sale::TYPE);
        $qb->field('store')->equals($filter->store->id);
        if (isset($filter->dateFrom)) {
            $qb->field('date')->gte($filter->dateFrom);
        }
        if (isset($filter->dateTo)) {
            $qb->field('date')->lte($filter->dateTo);
        }
        if (isset($filter->product)) {
            $saleIds = $this->saleProductRepository->findParentIdsByStoreAndProductAndDates(
                $filter->store->id,
                $filter->product->id,
                $filter->dateFrom,
                $filter->dateTo
            );
            $qb->field('id')->in($saleIds);
        }

        $qb->sort('date', self::SORT_DESC);

        return $qb->getQuery()->execute();
    }

    /**
     * @param Store $store
     * @return Sale
     */
    public function findLastSaleByStore(Store $store)
    {
        return $this->findOneBy(
            array(
                'type' => Sale::TYPE,
                'store' => $store->id,
            ),
            array(
                'date' => self::SORT_DESC,
            )
        );
    }
}
