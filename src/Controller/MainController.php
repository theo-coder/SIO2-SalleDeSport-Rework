<?php

namespace App\Controller;

use App\Form\NewsletterType;
use App\Form\UserInfosType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\OfferRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $articleRepository, OfferRepository $offerRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'offres' => $offerRepository->findAll()
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
     * @Route("/offer/{id}", name="offer")
     */
    public function offer($id, OfferRepository $offerRepository): Response
    {
        return $this->render('offer/offer.html.twig', [
            'offer' => $offerRepository->findOneBy(['id' => $id])
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

    /**
     * @Route("/offers", name="offers")
     */
    public function offers(OfferRepository $offerRepository)
    {
        return $this->render('offer/offers.html.twig', [
            'offres' => $offerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/newsletter", name="newsletter")
     */
    public function newsletter(Request $request, MailerInterface $mailerInterface, UserRepository $userRepository)
    {
        $form = $this->createForm(NewsletterType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $users = $userRepository->findBy(['isNewsletter' => true]);

            $message = (new Email())
                    ->from('salledesport@posty.fr')
                    ->subject('Salle de sport Newsletter')
                    ->text($formData['message'],'text/plain');

            foreach($users as $user) {
                $message->addTo($user->getEmail());
            }

            $mailerInterface->send($message);
            $this->addFlash('info', 'Your message has been sent');
            return $this->redirectToRoute('newsletter');
        }



        return $this->render('newsletter/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
