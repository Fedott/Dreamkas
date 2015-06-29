<?php

namespace Lighthouse\CoreBundle\Document\FirstStart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serialize;

/**
 * @property string $id
 * @property bool   $complete
 * @property StoreFirstStart[] $stores
 *
 * @MongoDB\Document(repositoryClass="Lighthouse\CoreBundle\Document\FirstStart\FirstStartRepository")
 */
class FirstStart extends AbstractDocument
{
    /**
     * @MongoDB\Id
     * @Serialize\Exclude
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\Boolean
     * @var bool
     */
    protected $complete = false;

    /**
     * @var StoreFirstStart[]|Collection
     */
    protected $stores;

    /**
     * @param StoreFirstStart $storeFirstStart
     */
    public function addStoreFirstStart(StoreFirstStart $storeFirstStart)
    {
        if (null === $this->stores) {
            $this->stores = new ArrayCollection();
        }
        $this->stores->add($storeFirstStart);
    }
}
