<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PlatRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
  #[Route('/', name: 'app_catalogue')]
public function index(CategorieRepository $categorieRepository, PlatRepository $platRepository): Response
{
    $categories = $categorieRepository->findAll();

    // On récupère les plats actifs (menu du jour)
    $plats = $platRepository->findBy(['active' => true]);

    return $this->render('catalogue/index.html.twig', [
        'categories' => $categories,
        'plats' => $plats,
    ]);
}

    #[Route('/plats', name: 'app_catalogue_plats')]
    public function plats(PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findAll();
        
        return $this->render('catalogue/plats.html.twig', [
            'plats' => $plats,
        ]);
    }

    #[Route('/categorie/{id}', name: 'app_catalogue_categorie_plats')]
    public function platsParCategorie(Categorie $categorie, PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findBy(['categorie' => $categorie]);

        return $this->render('catalogue/categorie_plats.html.twig', [
            'plats' => $plats,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categories', name: 'app_catalogue_categories')]
    public function categories(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('catalogue/categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
