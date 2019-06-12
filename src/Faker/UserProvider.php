<?php


namespace App\Faker;


use App\Entity\User;
use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProvider extends Base
{

    private $passwordEncoder;

    public function __construct(Generator $generator, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($generator);

        $this->passwordEncoder = $passwordEncoder;
    }

    // la méthode qui recupere le mot de passe rentrer par l'utilisateur
    public function encodePassword($password)
    {
        // je recupère le mdp rentrer par l'user, et je hash avec l'encodePassword, qui est defini dans UserPasswordEncoderInterface
        return $this->passwordEncoder->encodePassword((new User()), $password);

    }


}