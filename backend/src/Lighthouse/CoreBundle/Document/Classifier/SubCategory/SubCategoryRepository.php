<?php

namespace Lighthouse\CoreBundle\Document\Classifier\SubCategory;

use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\DocumentRepository;

class SubCategoryRepository extends DocumentRepository
{
    /**
     * @param string $categoryId
     * @return int
     */
    public function countByCategory($categoryId)
    {
        $query = $this
            ->createQueryBuilder()
            ->field('category')->equals($categoryId)
            ->count()
            ->getQuery();
        $count = $query->execute();
        return $count;
    }

    /**
     * @param string $categoryId
     * @return Cursor
     */
    public function findByCategory($categoryId)
    {
        return $this->findBy(array('category' => $categoryId));
    }
}
