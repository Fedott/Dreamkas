<?php

namespace Lighthouse\CoreBundle\Document\StockMovement\StockIn;

use Lighthouse\CoreBundle\Request\ParamConverter\Filter\FilterInterface;

class StockInFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return bool
     */
    public function hasNumber()
    {
        return null !== $this->number;
    }

    /**
     * @param array $data
     * @return null
     */
    public function populate(array $data)
    {
        if (isset($data['number'])) {
            $this->setNumber($data['number']);
        }
    }
}
