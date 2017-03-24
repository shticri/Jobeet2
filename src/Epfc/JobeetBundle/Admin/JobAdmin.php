<?php

namespace Epfc\JobeetBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
//use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Epfc\JobeetBundle\Entity\Job;

class JobAdmin extends AbstractAdmin
{
    // setup the defaut sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );
 
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category')
            //->add('type', 'choice', array('choices' => Job::getTypes(), 'expanded' => true))
            ->add('type', ChoiceType::class, [
                'choices' => Job::getTypes()
            ])
            ->add('company')
            //->add('file', 'file', array('label' => 'Company logo', 'required' => false))
            ->add('file', FileType::class, array('label' => 'Logo de l\'entreprise(jpg, png)'))
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('howToApply')
            ->add('isPublic')
            ->add('email')
            ->add('isActivated');
    }
 
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category')
            ->add('company')
            ->add('position')
            ->add('description')
            ->add('isActivated')
            ->add('isPublic')
            ->add('email')
            ->add('expiresAt');
    }
 
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('company')
            ->add('position')
            ->add('location')
            ->add('url')
            ->add('isActivated')
            ->add('email')
            ->add('category')
            ->add('expiresAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }
 
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('category')
            ->add('type')
            ->add('company')
            ->add('webPath', 'string', array('template' => 'JobAdmin/list_image.html.twig'))
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('howToApply')
            ->add('isPublic')
            ->add('isActivated')
            ->add('token')
            ->add('email')
            ->add('expiresAt');
    }
    
    public function getBatchActions()
    {
    // retrieve the default (currently only the delete action) actions
    $actions = parent::getBatchActions();

    // check user permissions
    if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')){
            $actions['extend'] = array(
                    'label'            => 'Extend',
                    'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
            );

            $actions['deleteNeverActivated'] = array(
                    'label'            => 'Delete never activated jobs',
                    'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
            );
    }

    return $actions;
    }
}