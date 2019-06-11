<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\Image;
use App\Faker\CarProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('model', TextType::class, [

            ])
            ->add('price', NumberType::class, [
            ])
            ->add('image', ImageType::class, ['label'=> false])

            ->add('keywords', CollectionType::class, [
                'label' => false,
                'entry_type' => KeywordType::class,
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->add('cities', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('color', ChoiceType::class,
                [
                    'choices' => array_combine(CarProvider::COLOR, CarProvider::COLOR),
                    'label' => false,
                ]
                )
            ->add('carburant', ChoiceType::class,
                [
                    'choices' => array_combine(CarProvider::CARBURANT, CarProvider::CARBURANT),
                    'label' => false,
                ]
                )

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }

}
