<?php

// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    /**
     * Page de contact
     * - Permet à l'utilisateur d'envoyer un message
     * - Envoie un email à l'adresse spécifiée
     */
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $telephone = $request->request->get('telephone');
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            $emailMessage = (new Email())
                ->from('no-reply@example.com')
                ->to('ton.email@tondomaine.com') // Mets ici une adresse visible dans MailHog
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom : $nom\n" .
                    "Prénom : $prenom\n" .
                    "Téléphone : $telephone\n" .
                    "Email : $email\n" .
                    "Message :\n$message"
                );

            $mailer->send($emailMessage);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
        }

        return $this->render('contact/index.html.twig');
    }
}
