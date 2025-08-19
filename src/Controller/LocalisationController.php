<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocalisationController extends AbstractController
{
    #[Route('/localisation', name: 'app_localisation')]
    public function index(): Response
    {
        $localisations = [
            [
                'nom' => 'Quasebal Amiens',
                'ville' => 'Amiens',
                'rue' => '12 Rue du Chardonnet',
                'codePostal' => '80000',
                'email' => 'quasebal.amiens@gmail.com',
                'telephone' => '2356892125',
                'image' => 'amiens.png'
            ],
            [
                'nom' => 'Quasebal Compiègne',
                'ville' => 'Compiègne',
                'rue' => '8 Avenue des Tilleuls',
                'codePostal' => '60200',
                'email' => 'quasebal.compiegne@gmail.com',
                'telephone' => '2345678910',
                'image' => 'compiegne.png'
            ],
            [
                'nom' => 'Quasebal Paris Centre',
                'ville' => 'Paris',
                'rue' => '27 Rue des Érables',
                'codePostal' => '75005',
                'email' => 'quasebal.paris.centre@gmail.com',
                'telephone' => '2312345678',
                'image' => 'paris.png'
            ],
            [
                'nom' => 'Quasebal Paris Sud',
                'ville' => 'Paris',
                'rue' => '45 Avenue du Midi',
                'codePostal' => '75014',
                'email' => 'quasebal.paris.sud@gmail.com',
                'telephone' => '2398765432',
                'image' => 'paris.png'
            ],
            [
                'nom' => 'Quasebal Lille',
                'ville' => 'Lille',
                'rue' => '19 Rue des Saules',
                'codePostal' => '59000',
                'email' => 'quasebal.lille@gmail.com',
                'telephone' => '2387654321',
                'image' => 'lille.png'
            ],
        ];

        return $this->render('localisation/index.html.twig', [
            'localisations' => $localisations
        ]);
    }
}