<?php


namespace App\Controller;


use App\Entity\Car;
use App\Entity\Image;
use App\Entity\Keyword;
use App\Form\CarType;
use App\Repository\CarRepository;
use App\Services\ImageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /*-----------------------------------------------------------------------
         FONCTION POUR AFFICHER LES ARTICLES AJOUTER A LA PAGE D'ACCEUIL
    ------------------------------------------------------------------------*/

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/")
     */
    public function index(CarRepository $carRepository)
    {

        $cars = $carRepository->findAll();

        return $this->render('home/index.html.twig',
                [
                    'cars' => $cars
                ]
            );

    }

    /*-----------------------------------------------------------------------
                            FONCTION POUR AJOUTER
    ------------------------------------------------------------------------*/
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/add")
     */
    public function add(EntityManagerInterface $manager, Request $request, ImageHandler $handler) // l'entityManager qui va permettre d'injecter mes données dans la bdd
    {

        // je cree un objet de Cartype ( formulaire )
        $form = $this->createForm(CarType::class);

        // recupère les informations soumis par le User
        $form->handleRequest($request);

        // si le formulaire est soumis et tout les champs requis sont bien rempli, alors j'enregistre les données dans la bdd
        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                $path = $this->getParameter('kernel.project_dir'). '/public/images';

                /*
                 * Recupere les valeurs sous forme d'objet Car
                 */
                $car = $form->getData();

                // Recupere l'image
                /** @var Image $image */
                $image = $car->getImage();

                $image->setPath($path);
                $manager->persist($car);
                $manager->flush();

                // le message a afficher
                $this->addFlash(
                    'success',
                    'Une nouvelle voiture ç été ajoutée'
                );

                // si tout fonctionne, je redirige vers la page d'acceuil
                return $this->redirectToRoute('app_index_index');
            }
            else {
                $this->addFlash(
                    'error',
                    'Merci de vérifier que tous les champs sont bien renseignés'
                );
            }
        }

        return $this->render('home/add.html.twig',
                [
                    /* creation d'une vue de mon formulaire que je vais recuperer dans add.html.twig
                       pour ensuite l'afficher dans le navigateur
                    */
                    'form' => $form->createView(),
                ]
            );

    }

    /*-----------------------------------------------------------------------
                            FONCTION POUR MODIFIER
    ------------------------------------------------------------------------*/
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/edit/{id}")
     */
    public function edit(Car $car, EntityManagerInterface $manager, Request $request)
        /*
         * Je recupere l'id de mon article grace a l'objet Car
         * Et je le modifie grace a l'EntityManager
         */
    {

        /*
         * Recupère et envoie ma voiture recuperer grâce a l'id dans le formulaire
         */
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);

        if($form->isSubmitted()) {

            if ($form->isValid()) {

                $path = $this->getParameter('kernel.project_dir'). '/public/images';

                /** @var Image $image */
                $image = $form->getData()->getImage();

                $image->setPath($path);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'La voiture à bien été modifée'
                );

                return $this->redirectToRoute('app_index_show',
                        [
                            'id' => $car->getId()
                        ]
                    );
            }
            else {

                $this->addFlash(
                    'error',
                    'Merci de bien vouloir remplir tous les champs requis'
                );
            }
        }

        return $this->render('home/edit.html.twig',
                [
                    'car' => $car,
                    'form' => $form->createView(),
                ]
            );

    }


    /*-----------------------------------------------------------------------
                         FONCTION POUR SUPPRIMER UN ARTICLE
    ------------------------------------------------------------------------*/
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/delete/{id}")
     */
    public function delete(Car $car, EntityManagerInterface $manager)
        /*
         * Je recupere l'id de mon article grace a l'objet Car
         * Et je le modifie grace a l'EntityManager
         */
    {

        $manager->remove($car);
        $manager->flush();

        return $this->redirectToRoute('app_index_index');

    }


    /*-------------------------------------------------------------------
                    FONCTION AFFICHAGE ANNONCES
    ---------------------------------------------------------------------*/

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/show/{id}")
     */
    public function show(Car $car)
    {

        return $this->render('home/show.html.twig',
                [
                    'car' => $car
                ]
            );

    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/contact")
     */
    public function contact()
    {

        return $this->render('home/contact.html.twig');

    }


    /*--------------------------------------------------------------------
                    FONCTION POUR SUPPRIMER LES MOTS CLES
    ---------------------------------------------------------------------*/

    /**
     *
     * @Route("/delete/keyword/{id}", methods={"POST"}, condition="request.headers.get('X-Requested-With') matches '/XMLHttpRequest/i'")
     */
    /*
     * isXmlHttpRequest ( si c'est une requête ajax
     */
    public function deleteKeyword(Keyword $keyword, EntityManagerInterface $entityManager)
    {

        $entityManager->remove($keyword);
        $entityManager->flush();

        return new JsonResponse();

    }

}