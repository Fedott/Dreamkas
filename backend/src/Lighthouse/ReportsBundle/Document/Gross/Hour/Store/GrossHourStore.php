<?php

namespace Lighthouse\ReportsBundle\Document\Gross\Hour\Store;

use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\ReportsBundle\Document\Gross\Hour\HourGross;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property Store $store
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\ReportsBundle\Document\Gross\Hour\Store\GrossHourStoreRepository",
 *      collection="Gross.Hour.Store"
 * )
 * @MongoDB\Index(keys={"hourDate"="asc", "store"="asc"})
 */
class GrossHourStore extends HourGross
{
    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Store\Store",
     *     simple=true
     * )
     * @var Store
     */
    protected $store;
}
