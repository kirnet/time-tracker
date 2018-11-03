<?php

namespace App\Admin;

use App\Entity\Project;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\BooleanType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class ModuleAdmin
 * @package App\Admin
 */
class UserAdmin extends AbstractAdmin
{
    /**

     */
    private $container;

    public function __construct(string $code, string $class, string $baseControllerName, $container = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isNew = !(bool) $this->request->get('id');
        $formMapper->add('email', TextType::class);
        $formMapper->add('login', TextType::class);
        $formMapper->add('register_at', DateTimeType::class);
        $formMapper->add('roles', CollectionType::class);
        $formMapper->add('password', PasswordType::class, [
            'required' => $isNew
        ]);
        $formMapper->add('is_banned', ChoiceType::class, [
            'choices' => [
                'yes' => true,
                'no' => false,
            ]
        ]);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
        $datagridMapper->add('login');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('email');
        $listMapper->addIdentifier('login');
        $listMapper->addIdentifier('register_at', 'datetime', [
            'pattern' => 'd.m.Y H:i:s',
            'locale' => $this->request->getLocale()
        ]);
        $listMapper->addIdentifier('roles');
    }

    /**
     * @param User $user
     */
    public function preUpdate($user)
    {
        /** @var UserPasswordEncoderInterface  $encoder */
        $encoder = $this->container->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
    }

    /**
     * @param User $user
     */
    public function prePersist($user)
    {
        $this->preUpdate($user);
    }
}