<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Service\MailjetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'contact')]
    public function index(
        Request $request,
        MailjetService $mailjet
    ): Response {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $mail_content = "
                Bonjour,
                <hr>
                Vous avez recu un message envoyé depuis le formulaire de contact.
                <hr>
                <strong>Nom : </strong>{$data['lastName']}<br>
                <strong>Prénom : </strong>{$data['firstName']}<br>
                <strong>Téléphone : </strong>{$data['phone']}<br>
                <strong>EMail : </strong>{$data['email']}<br>
                <strong>Message : </strong><br>
                <code>
                    {$data['content']}
                </code>
            ";
            $mailjet->send(
                'compte-essais@nubox.fr',
                'La Boutique Française',
                'Vous avez reçu un nouveau message',
                $mail_content
            );

            $this->addFlash(
                'success',
                'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render(
            'contact/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
