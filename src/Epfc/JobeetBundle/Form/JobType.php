<?php

namespace Epfc\JobeetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Epfc\JobeetBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('type')->add('company')->add('logo')->add('url')->add('position')->add('location')->add('description')->add('howToApply')->add('token')->add('isPublic')->add('isActivated')->add('email')->add('expiresAt')->add('createdAt')->add('updatedAt')->add('categoryId')->add('category');
        $builder->add('category');
        $builder->add('type', ChoiceType::class, [
            'choices' => Job::getTypes()
        ]);
        $builder->add('company');
        //$builder->add('logo', null, ['label' => 'Company logo']);
        $builder->add('file', FileType::class, array('label' => 'Logo de l\'entreprise(jpg, png)'));
        $builder->add('url');
        $builder->add('position');
        $builder->add('location');
        $builder->add('description');
        $builder->add('hoToApply', null, array('label' => 'How to apply?'));
        $builder->add('token', HiddenType::class);
        $builder->add('isPublic', null, array('label' => 'Is public?'));
        $builder->add('email');
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
        return 'epfc_jobeetbundle_job';
    }

    public function getName()
    {
      return 'job';
    }
    
}
