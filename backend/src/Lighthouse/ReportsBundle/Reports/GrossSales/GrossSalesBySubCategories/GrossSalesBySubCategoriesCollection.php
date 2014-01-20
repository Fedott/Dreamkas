<?php

namespace Lighthouse\ReportsBundle\Reports\GrossSales\GrossSalesBySubCategories;

use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\ReportsBundle\Reports\GrossSales\GrossSalesByClassifierNodeCollection;

class GrossSalesBySubCategoriesCollection extends GrossSalesByClassifierNodeCollection
{
    /**
     * @param SubCategory|AbstractNode $subCategory
     * @param array $dates
     * @return GrossSalesBySubCategories
     */
    public function createReport(AbstractNode $subCategory, array $dates)
    {
        return new GrossSalesBySubCategories($subCategory, $dates);
    }
}
