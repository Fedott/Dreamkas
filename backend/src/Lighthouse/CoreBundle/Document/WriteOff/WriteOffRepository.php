<?php

namespace Lighthouse\CoreBundle\Document\WriteOff;

use Lighthouse\CoreBundle\Document\DocumentRepository;
use Doctrine\MongoDB\Cursor;

class WriteOffRepository extends DocumentRepository
{
    /**
     * @param $storeId
     * @param WriteOffsFilter $filter
     * @return Cursor
     */
    public function findByStore($storeId, WriteOffsFilter $filter)
    {
        $criteria = array('store' => $storeId);
        $sort = array('date' => self::SORT_DESC);

        if ($filter->hasNumber()) {
            $criteria['number'] = $filter->getNumber();
        }

        $cursor = $this->findBy($criteria, $sort);
        return $cursor;
    }

    /**
     * @param WriteOff $writeOff
     * @param int $itemsCountDiff
     * @param int $sumTotalDiff
     */
    public function updateTotals(WriteOff $writeOff, $itemsCountDiff, $sumTotalDiff)
    {
        $query = $this
            ->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals($writeOff->id)
            ->returnNew();

        if ($itemsCountDiff <> 0) {
            $query->field('itemsCount')->inc($itemsCountDiff);
        }

        if ($sumTotalDiff <> 0) {
            $query->field('sumTotal')->inc($sumTotalDiff);
        }

        $query->getQuery()->execute();
    }
}
