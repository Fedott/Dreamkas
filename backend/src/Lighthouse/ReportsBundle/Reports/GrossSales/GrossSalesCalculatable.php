<?php

namespace Lighthouse\ReportsBundle\Reports\GrossSales;

use MongoId;

interface GrossSalesCalculatable
{
    /**
     * @param array $ids
     * @param string|\MongoId $storeId
     * @return array
     */
    public function calculateGrossSalesByIds(array $ids, $storeId);
}
