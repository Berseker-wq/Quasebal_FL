<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\PlatRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

final class CatalogueController extends AbstractController
{
     /**
     * Page d'accueil du catalogue
     * - Affiche toutes les catégories
     * - Affiche uniquement les plats actifs (ex : menu du jour)
     * - Vérifie si une vidéo de présentation existe dans /public/asset/video/
     */
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
      /**
     * Page qui affiche tous les plats
     * - Si un mot-clé est fourni en paramètre GET (?q=...), on filtre
     * - Sinon, on affiche tous les plats
     */
    #[Route('/plats', name: 'app_catalogue_plats')]
    public function plats(Request $request, PlatRepository $platRepository): Response
    {
        $query = $request->query->get('q');
        
        if ($query) {
            $plats = $platRepository->searchByKeyword($query);
        } else {
            $plats = $platRepository->findAll();
        }

        return $this->render('catalogue/plats.html.twig', [
            'plats' => $plats,
            'searchQuery' => $query,
        ]);
    }
     /**
     * Page qui affiche les plats d'une catégorie spécifique
     * - L'ID de la catégorie est passé dans l'URL
     * - Exemple : /categorie/2
     */
    #[Route('/categorie/{id}', name: 'app_catalogue_categorie_plats')]
    public function platsParCategorie(Categorie $categorie, PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findBy(['categorie' => $categorie]);

        return $this->render('catalogue/categorie_plats.html.twig', [
            'plats' => $plats,
            'categorie' => $categorie,
        ]);
    }

     /**
     * Page qui liste toutes les catégories
     */
    #[Route('/categories', name: 'app_catalogue_categories')]
    public function categories(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('catalogue/categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
