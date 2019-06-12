<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\TokenRepository;
use App\Security\LoginFormAuthentificatorAuthenticator;
use App\Services\TokenSendler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Recupere les informations,pre remplie par l'utilisateur en cas d'erreur
        $error = $authenticationUtils->getLastAuthenticationError();
        // recupere le nom d'utilisateur entrer dans le formulaire
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/registration")
     */
    public function registrationForm(
        Request $request,
        EntityManagerInterface $manager,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthentificatorAuthenticator $loginFormAuthentificatorAuthenticator,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenSendler $tokenSendler
    )
    {
        // creation du nouveau utilisateur
        $user = new User();

        // creation du formulaire de registration
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                // encryptage du mot de passe de l'utilisateur
                $passwordEncoded = $passwordEncoder->encodePassword($user, $user->getPassword());

                // recuperation du nouveau mot de passe hasher
                $user->setPassword($passwordEncoded);

                // je set le role par défaut des nouveaux utilisateur
                $user->setRoles(['ROLE_ADMIN']);

                $token = new Token($user);
                $manager->persist($token);
                $manager->flush();

                $tokenSendler->sendToken($user,$token);

                $this->addFlash(
                  'success',
                  'Un email vous à été envoyé, veuillez cliquer sur le lien présent dans l\'email pour confirmer votre inscription'
                );

                // si l'utilisateur est inscrit,je le redirige vers la page d'acceuil
                return $this->redirectToRoute('app_index_index');
            }
        }

        return $this->render('security/registration.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
    }


    /**
     *
     * @Route("/confirmation/{value}")
     */
    public function validateToken(
        Token $token,
        TokenRepository $tokenRepository,
        EntityManagerInterface $manager,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthentificatorAuthenticator $loginFormAuthentificatorAuthenticator,
        Request $request)
    {

        // recuperation de l'utilisateur
        $user = $token->getUser();

        // Si l'utilisateur a déjà confirmer son inscription
        if ($user->getEnable()) {

            // message a afficher
            $this->addFlash(
                'success',
                'Ce token est déjà validé !'
            );

            // je redirige l'utilisateur vers la page d'acceuil
            return $this->redirectToRoute('app_index_index');
        }

        if ($token->isValid()) {

            $user->setEnable(true);

            $manager->flush($user);

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthentificatorAuthenticator,
                'main'
            );

        }

        // si le délait de confirmation est dépassé, je supprime le token et l'utilisateur
        $manager->remove($token);

        // message a afficher si l'utilisateur est supprimer
        $this->addFlash(
            'error',
            'Le token est expiré, Inscrivez vous à nouveau'
        );

        // je redirige vers la page d'inscription
        return $this->redirectToRoute('app_security_registrationform');

    }
}
