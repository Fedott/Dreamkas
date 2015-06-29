<?php

namespace Lighthouse\ReportsBundle\Document\CostOfInventory\Store;

use Lighthouse\JobBundle\Document\Job\Job;

/**
 * @property string $storeId
 */
class StoreCostOfInventoryJob extends Job
{
    /**
     * @var string
     */
    protected $storeId;

    /**
     * @return bool
     */
    public function isPersist()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getTubeData()
    {
        return array(
            'storeId' => $this->storeId,
        ) + parent::getTubeData();
    }

    /**
     * @param array $tubeData
     */
    public function setDataFromTube(array $tubeData)
    {
        parent::setDataFromTube($tubeData);

        $this->storeId = $tubeData['storeId'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "store_cost_of_inventory";
    }
}
