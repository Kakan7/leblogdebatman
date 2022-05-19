<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function newPublication(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

    $article = new Article();

    $form = $this->createForm(NewArticleFormType::class, $article);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

        $article
        ->setPublicationDate(new \DateTime() )
            ->setAuthor( $this->getUser() )
            ->setSlug($slugger->slug($article->getTitle() ) ->lower())
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
    /*
     *
     * controller de la page listArticle
     */
    #[Route('/publication/liste/', name: 'publication_list')]
    public function publicationList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page',1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('blog/publication_list.html.twig', [
            'articles'=> $articles,
        ]);
    }


}
