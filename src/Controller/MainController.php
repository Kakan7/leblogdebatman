<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\EditPhotoFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name: 'main_')]
class MainController extends AbstractController
{
    //Controller de la page d'accueil
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $articleRepo = $doctrine->getRepository(Article::class);
        $articles = $articleRepo->findBy(
            [],
            ['publicationDate'=>'DESC'],
            $this->getParameter('app.article.last_article_number_on_home')
        );


        return $this->render('main/home.html.twig',[
            'articles'=>$articles,
        ]);
    }

    //Controller de la page d'accueil
    #[Route('/mon-profil/', name: 'profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }

    #[Route( '/editer-photo/', name : 'edit_photo')]
    #[IsGranted('ROLE_USER')]
    public function editPhoto(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EditPhotoFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){

            $photo = $form->get('photo')->getData();

            if(
                $this->getUser()->getPhoto() != null &&
                file_exists($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() ))
            {
                unlink($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto());
            }

            do{

                $newFileName = md5( random_bytes(100) ) . '.' . $photo->guessExtension();

                dump($newFileName);

            }while(file_exists($this->getParameter('app.user.photo.directory') . $newFileName ) );

            $this->getUser()->setPhoto($newFileName);

            $em = $doctrine->getManager();
            $em->flush();

            $photo->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName,
            );

            $this->addFlash('success','Photo de profil modifiée ave succés');
            return $this->redirectToRoute('main_profil');

        }

        return $this->render('main/edit_photo.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

}
