<?php

namespace Lighthouse\CoreBundle\Form;

use Lighthouse\CoreBundle\Document\File\File;
use Lighthouse\CoreBundle\Document\Supplier\Supplier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupplierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('phone', 'text')
            ->add('fax', 'text')
            ->add('email', 'text')
            ->add('contactPerson', 'text')
            ->add(
                'agreement',
                'reference',
                array(
                    'class' => File::getClassName(),
                    'invalid_message' => 'lighthouse.validation.errors.supplier.file.does_not_exist'
                )
            )
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array(LegalDetailsType::getClassName(), 'setTypeForm'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Supplier::getClassName(),
                'csrf_protection' => false,
                'cascade_validation' => true
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}
