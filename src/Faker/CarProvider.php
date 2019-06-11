<?php


namespace App\Faker;


use Faker\Provider\Base;

class CarProvider extends Base
{

    const CARBURANT = [

        'essence',
        'diesel',
        'hybrid',
        'electrique',
    ];

    const COLOR = [

        'blanc',
        'rouge',
        'noir',
        'bleu',
        'beige',
        'orange',
        'jaune',
        'violet',
        'gris',
        'rose',
    ];

    public function carCarburant()
    {

        // la function va retourner de facçon aléatoire un carburant de ma constante " CARBURANT "
        return self::randomElement(self::CARBURANT);

    }

    public function carColor()
    {
        // la function va retourner de facçon aléatoire une COULEUR de ma constante " COLOR "
        return self::randomElement(self::COLOR);

    }

    public function carPrice()
    {
        // retourne un nombre aléatoire entre 100 et 1000000
        return rand(100, 100000);

    }


}