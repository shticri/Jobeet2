<?php

namespace Epfc\JobeetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categoryId')->add('type')->add('company')->add('logo')->add('url')->add('position')->add('location')->add('description')->add('howToApply')->add('token')->add('isPublic')->add('isActivated')->add('email')->add('expiresAt')->add('createdAt')->add('updatedAt')->add('category');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Epfc\JobeetBundle\Entity\Job'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jobeetbundle_job';
    }


}
