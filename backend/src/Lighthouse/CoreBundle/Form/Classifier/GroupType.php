<?php

namespace Lighthouse\CoreBundle\Form\Classifier;

use Lighthouse\CoreBundle\Document\Classifier\Group\Group;
use Lighthouse\CoreBundle\Form\Classifier\ClassifierNodeType;

class GroupType extends ClassifierNodeType
{
    /**
     * @return string
     */
    protected function getDataClass()
    {
        return Group::getClassName();
    }
}
