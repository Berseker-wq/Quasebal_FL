<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PanierService;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('panier/index.html.twig', [
            'produitsPanier' => $panierService->getProduitsPanier(),
            'total' => $panierService->getTotal(),
        ]);
    }

    #[Route('/panier/ajout/{id}', name: 'app_panier_ajouter')]
    public function ajouter(int $id, PanierService $panierService): Response
    {
        if (!$panierService->produitExiste($id)) {
            $this->addFlash('error', 'Produit introuvable');
        } else {
            $panierService->add($id);
            $this->addFlash('success', 'Produit ajouté au panier');
        }

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id, PanierService $panierService): Response
    {
        $panierService->remove($id);
        $this->addFlash('success', 'Produit supprimé du panier');
        return $this->redirectToRoute('app_panier');
    }
#[Route('/panier/valider', name: 'app_panier_valider')]
public function valider(PanierService $panierService): Response
{
    if (empty($panierService->getProduitsPanier())) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('app_panier');
    }

    // Affiche la page de validation avant la commande
    return $this->render('panier/valider.html.twig', [
        'produitsPanier' => $panierService->getProduitsPanier(),
        'total' => $panierService->getTotal(),
    ]);
}

    #[Route('/panier/retirer/{id}', name: 'app_panier_retirer')]
    public function retirer(int $id, PanierService $panierService): Response
    {
        if (!$panierService->produitExiste($id)) {
            $this->addFlash('error', 'Produit introuvable');
        } else {
            $panierService->remove($id);
            $this->addFlash('success', 'Produit retiré du panier');
        }

        return $this->redirectToRoute('app_panier');
    }
}
