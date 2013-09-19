<?php

namespace Lighthouse\CoreBundle\Document\Invoice;

use Lighthouse\CoreBundle\Document\DocumentRepository;

class InvoiceRepository extends DocumentRepository
{
    /**
     * @param string $storeId
     * @return \Doctrine\ODM\MongoDB\Cursor
     */
    public function findByStore($storeId)
    {
        return $this->findBy(array('store' => $storeId));
    }

    /**
     * @param Invoice $invoice
     * @param int $itemsCountDiff
     * @param int $sumTotalDiff
     */
    public function updateTotals(Invoice $invoice, $itemsCountDiff, $sumTotalDiff)
    {
        $query = $this
            ->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals($invoice->id)
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
