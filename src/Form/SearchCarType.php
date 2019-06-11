<?php


namespace App\Form;


use App\Entity\City;
use App\Faker\CarProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchCarType extends AbstractType
{

    const PRICE = [1000,2000,3000,4000,4500,5000,5500,6000,6500,7000,7500,8000,8500,9000,9500,10000,15000,18000,22500,30000,40000,50000,60000,70000,80000,90000,100000
        ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('color', ChoiceType::class,
                [
                   'choices' => array_combine(CarProvider::COLOR, CarProvider::COLOR)
                ]
                )
            ->add('carburant', ChoiceType::class,
                [
                   'choices' => array_combine(CarProvider::CARBURANT, CarProvider::CARBURANT)
                ]
                )
            ->add('city', EntityType::class,
                [
                    'class' => City::class,
                    'choice_label' => 'name',
                ]
                )
            ->add('minimumPrice', ChoiceType::class,
                [
                    'label' => 'Prix minimum',
                    'choices' => array_combine(self::PRICE,self::PRICE),
                ]
                )
            ->add('maximumPrice', ChoiceType::class,
                [
                    'label' => 'Prix maximum',
                    'choices' => array_combine(self::PRICE,self::PRICE),
                ]
                )
            ->add('recherche', SubmitType::class)
            ;
    }

}