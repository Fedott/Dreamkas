<?php

namespace Lighthouse\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubCategoryType extends ClassifierNodeType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'category',
                'reference',
                array(
                    'class' => 'Lighthouse\\CoreBundle\\Document\\Classifier\\Category\\Category',
                    'invalid_message' => 'lighthouse.validation.errors.subCategory.category.does_not_exists'
                )
            );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    protected function getDataClass()
    {
        return 'Lighthouse\\CoreBundle\\Document\\Classifier\\SubCategory\\SubCategory';
    }
}
