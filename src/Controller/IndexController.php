<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $random_number = rand(0,100);

        $numbers = [
            'first' => 1,
            'second' => 2,
            'third' => 3,
        ];


        return $this->render('index/index.html.twig', [
            'random_number' => $random_number, 'numbers' => $numbers
        ]);
    }
}
