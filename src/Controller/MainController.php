<?php

namespace App\Controller;

use App\Form\UserInfosType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'articles' => $articleRepository->findAll()
        ]);
    }

    /**
     * @Route("/me", name="me")
     */
    public function me(): Response
    {
        return $this->render('user/infos.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/me/edit", name="edit_me")
     */
    public function editMe(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->createForm(UserInfosType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            $password && $this->getUser()->setPassword(
                $userPasswordHasherInterface->hashPassword($this->getUser(), $password)
            );
            
            $this->getDoctrine()->getManager()->flush();

            return $password ? 
                $this->redirectToRoute('app_logout') :
                $this->redirectToRoute('me');

        }
        return $this->render('user/update.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id, ArticleRepository $articleRepository): Response
    {
        return $this->render('article/article.html.twig', [
            'article' => $articleRepository->findOneBy(['id' => $id])
        ]);
    }

    
    /**
     * @Route("/articles", name="articles")
     */
    public function articles(CategoryRepository $categoryRepository)
    {
        return $this->render('article/articles.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
