<?php

namespace App\Form;

use App\Entity\Period;
use App\Entity\Project;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class PeriodType extends AbstractType
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
            ->add('time_start', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('alert_time', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
//                'format' => 'yyyy-MM-dd HH:mm',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Period::class]);
    }
}