<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }


    public function methodCalledOnKernelResponse(ResponseEvent $responseEvent)
    {

        $maintenance = false;

        if($maintenance) {

            $content = $this->twig->render('maintenance/maintenance.html.twig');

            $response = new Response($content);

            return $responseEvent->setResponse($response);
        }

        return $responseEvent->getResponse()->getContent();

    }

    public static function getSubscribedEvents()
    {
        // retourne en parametre l'Event a écouter, et en valeur la méthode qui sera appeler
        return [
            KernelEvents::RESPONSE => ['methodCalledOnKernelResponse', 255]
        ];
    }


}