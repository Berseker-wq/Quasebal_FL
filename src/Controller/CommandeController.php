<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
     #[Route('/commande', name: 'app_commande', methods: ['POST'])]
    public function index(Request $request): Response
    {
        // Exemple de récupération des données du formulaire
        $nom = $request->request->get('nom');
        $adresse = $request->request->get('adresse');
        $email = $request->request->get('email');
        $telephone = $request->request->get('telephone');

        // TODO : enregistrer la commande en base de données, envoyer un email, etc.

        return $this->render('commande/index.html.twig', [
            'nom' => $nom,
            'adresse' => $adresse,
            'email' => $email,
            'telephone' => $telephone,
        ]);
    }
}
