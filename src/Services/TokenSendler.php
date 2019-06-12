<?php


namespace App\Services;

// Fonction pour envoyer l'email de confirmation
use App\Entity\Token;
use App\Entity\User;

class TokenSendler
{

    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;

    }

    // fonction pour envoyer un message de confirmation d'inscription a l'utilisateur
    public function sendToken (User $user, Token $token)
    {

        $message = (new \Swift_Message('Confirmez votre inscription'))
            ->setFrom('noreply@rent-car.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                  'email/registration.html.twig',
                    ['token' => $token->getValue()]
                ),
                'text/html'
            );
        $this->mailer->send($message);

    }

}