<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailerInterface): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $contactFormData = $form->getData();
            $contact = new Contact();
            
            $contact
                ->setEmail($contactFormData['email'])
                ->setContent($contactFormData['message'])
                ->setName($contactFormData['fullName']);

            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('salledesport@posty.fr')
                ->subject('You got mail')
                ->text('Sender : '.$contactFormData['fullName'].\PHP_EOL.
                    $contactFormData['message'],
                    'text/plain');
            $mailerInterface->send($message);

            $entityManager->persist($contact);
            $entityManager->flush();


            $this->addFlash('info', 'Your message has been sent');

            

            return $this->redirectToRoute('contact');
        }



        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    
    }
}
