<?php

namespace Lighthouse\CoreBundle\Document\Purchase;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Lighthouse\CoreBundle\Document\PurchaseProduct\PurchaseProduct;
use Symfony\Component\Validator\Constraints as Assert;
use Lighthouse\CoreBundle\Validator\Constraints as LighthouseAssert;

/**
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Purchase\PurchaseRepository"
 * )
 *
 * @property int        $id
 * @property DiteTime   $createdDate
 * @property PurchaseProduct[]  $product
 */
class Purchase extends AbstractDocument
{
    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\PurchaseProduct\PurchaseProduct",
     *      simple=true,
     *      cascade="persist"
     * )
     *
     * @Assert\NotBlank(message="lighthouse.validation.errors.purchase.product_empty")
     * @Assert\Valid(traverse=true)
     * @var PurchaseProduct[]
     */
    protected $products = array();

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function prePersist()
    {
        if (empty($this->createdDate)) {
            $this->createdDate = new \DateTime();
        }

        foreach ($this->products as $product) {
            $product->purchase = $this;
        }
    }
}
