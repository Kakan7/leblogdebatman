<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name: 'main_')]
class MainController extends AbstractController
{
    //Controller de la page d'accueil
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    //Controller de la page d'accueil
    #[Route('/mon-profil/', name: 'profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }
}
