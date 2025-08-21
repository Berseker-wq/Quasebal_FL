<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PanierService;


class PanierController extends AbstractController
{
    /**
     * Page du panier
     * - Affiche les produits du panier
     * - Affiche le total et la réduction si applicable
     */
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('panier/index.html.twig', [
            'produitsPanier' => $panierService->getProduitsPanier(),
            'total' => $panierService->getTotal(),
            'reduction' => $panierService->getReduction(),           // Ajouté
        'totalAvecReduction' => $panierService->getTotalAvecReduction(),  // Ajouté
        ]);
    }
    /**
     * Ajoute un produit au panier
     * - Vérifie si le produit existe
     * - Ajoute le produit au panier
     */

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
    /**
     * Page de validation du panier
     * - Affiche les produits du panier
     * - Permet de valider la commande
     */
#[Route('/panier/valider', name: 'app_panier_valider')]
public function valider(PanierService $panierService): Response
{
    if (empty($panierService->getProduitsPanier())) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('app_panier');
    }

    // Affiche la page de validation avant la commande + CODE PROMO 
    return $this->render('panier/valider.html.twig', [
        'produitsPanier' => $panierService->getProduitsPanier(),
        'total' => $panierService->getTotal(),
        'reduction' => $panierService->getReduction(),
        'totalAvecReduction' => $panierService->getTotalAvecReduction(),
    ]);
}

// Suppression d'une quantité d'un article 
   #[Route('/panier/retirer/{id}', name: 'app_panier_retirer')]
public function retirer(int $id, PanierService $panierService): Response
{
    if (!$panierService->produitExiste($id)) {
        $this->addFlash('error', 'Produit introuvable');
    } else {
        $panierService->decrease($id);
        $this->addFlash('success', 'Quantité diminuée');
    }

    return $this->redirectToRoute('app_panier');
}
    /**
     * Applique un code promo
     * - Vérifie si le code est valide
     * - Applique la réduction si le code est correct
     */
#[Route('/panier/code', name: 'panier_code', methods: ['POST'])]
public function appliquerCode(Request $request, PanierService $panierService): Response
{
    $code = $request->request->get('code');
    if ($panierService->appliquerCodePromo($code)) {
        $this->addFlash('success', 'Code promo appliqué !');
    } else {
        $this->addFlash('error', 'Code promo invalide.');
    }

    return $this->redirectToRoute('app_panier');  // La méthode index() renverra toutes les données nécessaires
}



}
