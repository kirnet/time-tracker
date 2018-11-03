<?php

namespace App\Admin;

use App\Entity\Project;
use App\Entity\Timer;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Class ModuleAdmin
 * @package App\Admin
 */
class TimerAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'login'
        ]);
        $formMapper->add('project', EntityType::class, [
            'class' => Project::class,
            'choice_label' => 'name'
        ]);

        $formMapper->add('state', ChoiceType::class, [
            'choices' => [
                Timer::STATE_NEW => Timer::STATE_NEW,
                Timer::STATE_PAUSED => Timer::STATE_PAUSED,
                Timer::STATE_RUNING => Timer::STATE_RUNING,
                Timer::STATE_STOPPED => Timer::STATE_STOPPED
            ]
        ]);
        $formMapper->add('created_at', DateTimeType::class);
        $formMapper->add('name', TextType::class);
        $formMapper->add('time', IntegerType::class);


    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('project.name');
        $listMapper->addIdentifier('user.email');
        $listMapper->addIdentifier('state');
        $listMapper->addIdentifier('timer_start');
        $listMapper->addIdentifier('time');
        $listMapper->addIdentifier('created_at', 'datetime',[
            'pattern' => 'dd MMM y G',
            'locale' => 'fr',
        ]);
    }
}