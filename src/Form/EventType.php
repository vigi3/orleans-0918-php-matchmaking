<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\FormatEvent;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('date', DateTimeType::class)
            ->add('formatEvent', EntityType::class, [
                'class' => FormatEvent::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.numberOfPlayers', 'ASC');
                }
            ])
            ->add('logoFile', FileType::class, [
                'required' => false
            ])
            ->add('roundMinutes', IntegerType::class)
            ->add('roundSeconds', IntegerType::class)
            ->add('pauseMinutes', IntegerType::class)
            ->add('pauseSeconds', IntegerType::class)

            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($user) {
                    $labelName = $user->getLastName()
                        . ' '
                        . $user->getFirstName()
                        . ' Email : '
                        . $user->getEmail();
                    return $labelName;
                },
                'by_reference' => false,
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'label_attr' => [
                    'class' => 'list-group-item list-group-item-action'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
