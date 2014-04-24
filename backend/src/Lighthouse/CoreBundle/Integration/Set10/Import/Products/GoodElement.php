<?php

namespace Lighthouse\CoreBundle\Integration\Set10\Import\Products;

use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Integration\Set10\SimpleXMLElement;

class GoodElement extends SimpleXMLElement
{
    const PRODUCT_PIECE_ENTITY = 'ProductPieceEntity';
    const PRODUCT_WEIGHT_ENTITY = 'ProductWeightEntity';
    const PRODUCT_SPIRITS_ENTITY = 'ProductSpiritsEntity';

    /**
     * @return string
     */
    public function getGoodName()
    {
        return (string) $this->name;
    }

    /**
     * @return int
     */
    public function getVat()
    {
        return (int) $this->vat;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return (string) $this['marking-of-the-good'];
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        foreach ($this->{'bar-code'} as $barCode) {
            if ('true' == (string) $barCode->{'default-code'}) {
                return (string) $barCode['code'];
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return (string) $this->manufacturer->name;
    }

    /**
     * @return string
     */
    public function getUnits()
    {
        $measureType = (string) $this->{'measure-type'}->name;
        switch (true) {
            case preg_match('/^кг\.?$/ui', $measureType):
                return Product::UNITS_KG;
            case preg_match('/^шт\.?$/ui', $measureType):
                return Product::UNITS_UNIT;
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        foreach ($this->{'price-entry'} as $price) {
            if ('1' == $price->number) {
                return (string) $price['price'];
            }
        }
        return null;
    }

    /**
     * @return array id => name
     */
    public function getGroups()
    {
        $groups = array();
        if (isset($this->group)) {
            array_unshift(
                $groups,
                array(
                    'id' => (string) $this->group['id'],
                    'name' => (string) $this->group->name,
                )
            );
            $parentGroup = $this->group;
            while (isset($parentGroup->{'parent-group'})) {
                $parentGroup = $parentGroup->{'parent-group'};
                array_unshift(
                    $groups,
                    array(
                        'id' => (string) $parentGroup['id'],
                        'name' => (string) $parentGroup->name,
                    )
                );
            }
        }
        return $groups;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return (string) $this->{'product-type'};
    }

    /**
     * @param string $key
     * @return string
     */
    public function getPluginProperty($key)
    {
        foreach ($this->{'plugin-property'} as $pluginProperty) {
            if ((string) $pluginProperty['key'] == $key) {
                return (string) $pluginProperty['value'];
            }
        }
        return null;
    }
}
