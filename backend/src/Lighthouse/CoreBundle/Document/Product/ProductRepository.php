<?php

namespace Lighthouse\CoreBundle\Document\Product;

use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\Classifier\ParentableRepository;
use Lighthouse\CoreBundle\Document\DocumentRepository;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\CoreBundle\Types\Numeric\Decimal;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use MongoId;
use MongoRegex;

class ProductRepository extends DocumentRepository implements ParentableRepository
{
    /**
     * @param ProductFilter $filter
     * @return Cursor|Product[]|array
     */
    public function search(ProductFilter $filter)
    {
        $criteria = array('$or' => array());
        foreach ($filter->getPropertiesWithQuery() as $property => $query) {
            $and = array();
            $words = preg_split('/\s+/u', $query);
            foreach ($words as $word) {
                if (mb_strlen($word, 'UTF-8') > 2) {
                    $and[] = array($property => new MongoRegex('/' . preg_quote($word, '/') . '/i'));
                }
            }
            if (!empty($and)) {
                $criteria['$or'][] = (1 == count($and)) ? reset($and) : array('$and' => $and);
            }
        }

        if ($filter->isPropertiesRequired() && empty($criteria['$or'])) {
            return array();
        } elseif (empty($criteria['$or'])) {
            $criteria = array();
        }

        if ($filter->hasSubCategory()) {
            $criteriaSubCategory = array(
                'subCategory' => new MongoId($filter->getSubCategory())
            );
            if (!empty($criteria)) {
                $criteria = array(
                    '$and' => array(
                        $criteria,
                        $criteriaSubCategory,
                    ),
                );
            } else {
                $criteria = $criteriaSubCategory;
            }
        }

        if ($filter->isPurchasePriceNotEmpty()) {
            $criteria = array(
                '$and' => array(
                    $criteria,
                    array('purchasePrice' => array('$ne' => null, '$exists' => true))
                )
            );
        }

        if (isset($criteria['$and'])) {
            $criteria['$and'][] = array('deletedAt' => null);
        } else {
            $criteria['deletedAt'] = null;
        }

        return $this->findBy($criteria, null, 100);
    }

    /**
     * @param SubCategory $subCategory
     * @return Cursor|Product[]
     */
    public function findBySubCategory(SubCategory $subCategory)
    {
        return $this->findBy(array('subCategory' => $subCategory->id, 'deletedAt' => null));
    }

    /**
     * @param string $parentId
     * @return MongoId[]
     */
    public function findIdsByParent($parentId)
    {
        $qb = $this->createQueryBuilder()
            ->hydrate(false)
            ->select('_id')
            ->field('subCategory')->equals($parentId);
        $result = $qb->getQuery()->execute();
        $ids = array();
        foreach ($result as $row) {
            $ids[] = $row['_id'];
        }
        return $ids;
    }

    /**
     * @param string $sku
     * @return Product
     */
    public function findOneBySku($sku)
    {
        return $this->findOneBy(array('sku' => $sku));
    }

    /**
     * @param string $parentId
     * @return int
     */
    public function countByParent($parentId)
    {
        $query = $this
            ->createQueryBuilder()
                ->field('subCategory')->equals($parentId)
                ->field('deletedAt')->equals(null)
            ->count()
            ->getQuery();
        $count = $query->execute();
        return $count;
    }

    /**
     * @param string|array $barcodes
     * @return Cursor|Product[]
     */
    public function findByBarcodes($barcodes)
    {
        $barcodes = (array) $barcodes;
        $criteria = array(
            '$or' => array(
                array('barcode' => array('$in' => $barcodes)),
                array('barcodes.barcode' => array('$in' => $barcodes))
            )
        );
        return $this->findBy($criteria);
    }

    /**
     * @param Product $product
     */
    public function updateRetails(Product $product)
    {
        if (!Decimal::checkIsNull($product->sellingPrice)) {
            $product->retailPricePreference = Product::RETAIL_PRICE_PREFERENCE_PRICE;
            $product->retailPriceMin = clone $product->sellingPrice;
            $product->retailPriceMax = clone $product->sellingPrice;
        }

        switch ($product->retailPricePreference) {
            case Product::RETAIL_PRICE_PREFERENCE_PRICE:
                $product->retailMarkupMin = $this->calcMarkup($product->retailPriceMin, $product->purchasePrice);
                $product->retailMarkupMax = $this->calcMarkup($product->retailPriceMax, $product->purchasePrice);
                break;
            case Product::RETAIL_PRICE_PREFERENCE_MARKUP:
            default:
                $product->retailPriceMin = $this->calcRetailPrice($product->retailMarkupMin, $product->purchasePrice);
                $product->retailPriceMax = $this->calcRetailPrice($product->retailMarkupMax, $product->purchasePrice);
                $product->retailPricePreference = Product::RETAIL_PRICE_PREFERENCE_MARKUP;
                break;
        }
    }

    /**
     * @param Money $retailPrice
     * @param Money $purchasePrice
     * @return float|null
     */
    protected function calcMarkup(Money $retailPrice = null, Money $purchasePrice = null)
    {
        $roundedMarkup = null;
        if (!Decimal::checkIsNull($retailPrice)
            && !Decimal::checkIsNull($purchasePrice)
            && $purchasePrice->toNumber() > 0
        ) {
            $markup = (($retailPrice->getCount() / $purchasePrice->getCount()) * 100) - 100;
            $roundedMarkup = Decimal::createFromNumeric($markup, 2)->toNumber();
        }
        return $roundedMarkup;
    }

    /**
     * @param float $retailMarkup
     * @param Money $purchasePrice
     * @return Money
     */
    protected function calcRetailPrice($retailMarkup, Money $purchasePrice = null)
    {
        if (null !== $retailMarkup && '' !== $retailMarkup && !Decimal::checkIsNull($purchasePrice)) {
            $percent = 1 + ($retailMarkup / 100);
            $retailPrice = $purchasePrice->mul($percent);
        } else {
            $retailPrice = new Money();
        }
        return $retailPrice;
    }

    /**
     * @return Cursor|Product[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('id' => self::SORT_ASC));
    }

    /**
     * @return Cursor|Product[]
     */
    public function findAllActive()
    {
        return $this->findBy(
            array('deletedAt' => null),
            array('id' => self::SORT_ASC)
        );
    }
}
