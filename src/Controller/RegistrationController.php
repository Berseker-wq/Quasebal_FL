<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Utilise Annotation pour Route
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // IMPORTANT : Si tu utilises VichUploaderBundle, 
            // il faut forcer la mise à jour de l'entité pour détecter le fichier uploadé
            // Par exemple, si tu as un champ updatedAt, tu dois le mettre à jour ici
            // $user->setUpdatedAt(new \DateTime());

            $em->persist($user);
            $em->flush();

            // Envoi de l'email via template Twig
            $email = (new TemplatedEmail())
                ->from('ton.email@tondomaine.com')
                ->to($user->getEmail()) // Envoie à l'utilisateur inscrit (tu peux adapter)
                ->subject('Nouvelle inscription utilisateur')
                ->htmlTemplate('email/index.html.twig')
                ->context([
                    'user' => $user,
                ]);

            $mailer->send($email);

            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
