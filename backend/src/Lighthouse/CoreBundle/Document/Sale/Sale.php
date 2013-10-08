<?php

namespace Lighthouse\CoreBundle\Document\Sale;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Lighthouse\CoreBundle\Document\Sale\Product\SaleProduct;
use Symfony\Component\Validator\Constraints as Assert;
use Lighthouse\CoreBundle\Validator\Constraints as LighthouseAssert;
use DateTime;

/**
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Sale\SaleRepository"
 * )
 *
 * @property int        $id
 * @property DateTime   $createdDate
 * @property SaleProduct[]  $product
 */
class Sale extends AbstractDocument
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
     *      targetDocument="Lighthouse\CoreBundle\Document\Sale\Product\SaleProduct",
     *      simple=true,
     *      cascade="persist"
     * )
     *
     * @Assert\NotBlank(message="lighthouse.validation.errors.sale.product_empty")
     * @Assert\Valid(traverse=true)
     * @var SaleProduct[]
     */
    protected $products = array();

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function prePersist()
    {
        if (empty($this->createdDate)) {
            $this->createdDate = new DateTime();
        }

        foreach ($this->products as $product) {
            $product->sale = $this;
        }
    }
}
