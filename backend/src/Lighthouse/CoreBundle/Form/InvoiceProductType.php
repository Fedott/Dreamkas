<?php

namespace Lighthouse\CoreBundle\Form;

use Lighthouse\CoreBundle\Document\Invoice\Product\InvoiceProduct;
use Lighthouse\CoreBundle\Document\Product\Version\ProductVersion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvoiceProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array('mapped' => false))
            ->add(
                'product',
                'reference',
                array(
                    'class' => ProductVersion::getClassName(),
                    'invalid_message' => 'lighthouse.validation.errors.invoice_product.product.does_not_exists'
                )
            )
            ->add('priceEntered', 'money')
            ->add('quantity', 'quantity');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => InvoiceProduct::getClassName(),
                'csrf_protection' => false
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
