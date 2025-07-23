<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PlatRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

final class CatalogueController extends AbstractController
{
  #[Route('/', name: 'app_catalogue')]
public function index(CategorieRepository $categorieRepository, PlatRepository $platRepository): Response
{
    $categories = $categorieRepository->findAll();

    // On récupère les plats actifs (menu du jour)
    $plats = $platRepository->findBy(['active' => true]);

  // 1. Répertoire public
    $publicDirPath = $this->getParameter('kernel.project_dir') . '/public';

    // 2. Chemin relatif du fichier vidéo
    $videoRelativePath = 'asset/video/Cuisine_gastro.mp4';

    // 3. Chemin absolu complet
    $videoFullPath = $publicDirPath . '/' . $videoRelativePath;

    // 4. Vérification d’existence
    $filesystem = new Filesystem();
    $videoExists = $filesystem->exists($videoFullPath);


    return $this->render('catalogue/index.html.twig', [
        'categories' => $categories,
        'plats' => $plats,
        'videoExists' => $videoExists,
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
