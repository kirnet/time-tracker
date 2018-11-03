<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class ModuleAdmin
 * @package App\Admin
 */
class ProjectAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        $formMapper->add('description', TextType::class);
        $formMapper->add('created_at', DateTimeType::class);
        $formMapper->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'email',
            //'attr' => ['value' => $user->getId()]
        ]);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('description');
//        $datagridMapper->add('created_at', DateTimeType::class);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('description');
        $listMapper->addIdentifier('user.email');
//        $listMapper->addIdentifier('created_at', DateTimeType::class);
    }
}