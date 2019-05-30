<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/")
     */
    public function index()
    {

        return $this->render('base.html.twig');

    }

}