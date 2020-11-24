<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LatestPhotosController extends AbstractController{

    /**
     * @Route("/latest", name="latest_photos")
     */
    public function index(){

        $entityManager = $this->getDoctrine()->getManager();
        $latestPhotosPublic = $entityManager->getRepository(Photo::class)->findBy(['is_public'=>true]);

        return $this->render('latest_photos/index.html.twig',['latestPhotosPublic'=>$latestPhotosPublic]);

    }
}

