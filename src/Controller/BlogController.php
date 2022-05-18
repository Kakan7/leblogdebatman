<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    /**
     * Controller pour créer un article
     * Accés réserve aux admin
     *
    */
    #[Route('/nouvelle_publication/', name: 'new_publication')]
    #[IsGranted('ROLE_ADMIN')]
    public function newPublication(Request $request, ManagerRegistry $doctrine): Response
    {

    $article = new Article();

    $form = $this->createForm(NewArticleFormType::class, $article);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

        $article
        ->setPublicationDate(new \DateTime() )
            ->setAuthor( $this->getUser() )
        ;
        $em = $doctrine->getManager();
        $em->persist($article);
        $em->flush();

        $this->addFlash('success','Article publié avec succès !');

        return $this->redirectToRoute('blog_publication_view',[
            'id'=>$article->getId(),
            'slug'=>$article->getSlug(),
        ]);
    }




        return $this->render('blog/new_publication.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

/*
 *
 * controller de la page view article
 *
 */
    #[Route('/publication{id}/{slug}/', name:'publication_view')]
    #[ParamConverter('article', options:['mapping'=>['id'=>'id','slug'=>'slug']])]
    public function publicationView(Article $article): Response
    {
    dump($article);
    return $this->render('blog/publication_view.html.twig',[
        'article'=>$article,
    ]);
}




}
