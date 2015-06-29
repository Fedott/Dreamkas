<?php

namespace Lighthouse\CoreBundle\Form;

use Lighthouse\CoreBundle\Document\FirstStart\FirstStart;
use Symfony\Component\Form\FormBuilderInterface;

class FirstStartType extends DocumentType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('complete', 'checkbox');
    }

    /**
     * @return string
     */
    protected function getDataClass()
    {
        return FirstStart::getClassName();
    }
}
