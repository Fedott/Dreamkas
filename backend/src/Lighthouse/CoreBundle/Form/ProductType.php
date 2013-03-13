<?php


namespace Lighthouse\CoreBundle\Form;

use Lighthouse\CoreBundle\Form\DataTransformer\PriceModelTransformer;
use Lighthouse\CoreBundle\Form\DataTransformer\PriceViewTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $purchasePrice = $builder
            ->create('purchasePrice', 'text')
            ->addViewTransformer(new PriceViewTransformer())
            ->addModelTransformer(new PriceModelTransformer());

        $builder
            ->add('name', 'text')
            ->add('units', 'text')
            ->add('vat', 'text')
            ->add($purchasePrice)
            ->add('barcode', 'text')
            ->add('sku', 'text')
            ->add('vendorCountry', 'text')
            ->add('vendor', 'text')
            ->add('info', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Lighthouse\CoreBundle\Document\Product',
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'product';
    }
}
