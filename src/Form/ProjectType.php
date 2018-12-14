<?php

namespace App\Form;

use App\Entity\Project;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * ProjectType constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('users', UsersInputType::class, [
                'label' => 'Employees',
                'required' => false,
            ])
            ->add('logo', FileType::class, [
                'required' => false,
                'data_class' => null
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $data = $event->getData();
            $owner = $this->userRepository->find($data->getOwnerId());
            $data->addUser($owner);
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Project::class]);
    }
}