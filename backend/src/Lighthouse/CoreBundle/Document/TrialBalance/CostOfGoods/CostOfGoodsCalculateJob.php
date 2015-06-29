<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance\CostOfGoods;

use Lighthouse\JobBundle\Document\Job\Job;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property string $storeProductId
 */
class CostOfGoodsCalculateJob extends Job
{
    const TYPE = 'cost_of_goods_calculate';

    /**
     * @MongoDB\String
     * @var string
     */
    protected $storeProductId;

    /**
     * @return string
     */
    public function getType()
    {
        return CostOfGoodsCalculateJob::TYPE;
    }

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
            'storeProductId' => $this->storeProductId,
        ) + parent::getTubeData();
    }

    /**
     * @param array $tubeData
     */
    public function setDataFromTube(array $tubeData)
    {
        parent::setDataFromTube($tubeData);

        $this->storeProductId = $tubeData['storeProductId'];
    }
}
