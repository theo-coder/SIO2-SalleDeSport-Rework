<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Contact1Type;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contact")
 */
class ContactAdminController extends AbstractController
{
    /**
     * @Route("/", name="contact_admin_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact_admin/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="contact_admin_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
        return $this->render('contact_admin/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}", name="contact_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_admin_index');
    }
}
