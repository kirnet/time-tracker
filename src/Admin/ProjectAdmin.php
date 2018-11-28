<?php

namespace App\Admin;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ModuleAdmin
 * @package App\Admin
 */
class ProjectAdmin extends AbstractAdmin
{
    /** @var UserRepository  */
    private $userRepository;

    /**
     * ProjectAdmin constructor.
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param UserRepository $userRepository
     */
    public function __construct(string $code, string $class, string $baseControllerName, UserRepository $userRepository)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->userRepository = $userRepository;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        $formMapper->add('description', TextType::class, [
            'required' => false
        ]);
        $formMapper->add('created_at', DateTimeType::class);
//        $formMapper->add('owner_id', EntityType::class, [
//            'class' => User::class,
//            'choice_label' => 'email'
//        ]);
        $formMapper->add('users', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'email',
            'multiple' => true,
            'label' => 'Employees',

        ]);
        $formBuilder = $formMapper->getFormBuilder();
        $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $user = $this->getConfigurationPool()
                ->getContainer()
                ->get('security.token_storage')
                ->getToken()
                ->getUser()
            ;
            $data = $event->getData();

            if ($data->getId() === null) {
                $data->setOwnerId($user->getId());
                $data->addUser($user);
            }
            $event->setData($data);
        });
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
        $locale = $this->getRequest()->getLocale();
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('description');
        $listMapper->add('ownerId');
        $listMapper->addIdentifier('users', EntityType::class, [
            'class'    => User::class,
            //'choice_label' => 'login'
        ]);
        $listMapper->addIdentifier('created_at', 'datetime',[
            'pattern' => 'dd MMM y G',
            'locale' => $locale,
            //'timezone' => 'Europe/Paris',
        ]);
    }
}