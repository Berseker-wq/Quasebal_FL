<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    private SessionInterface $session;

    private array $produits = [
        25 => ['nom' => 'Sushi X12', 'prix' => 18.70, 'image' => 'images/sushi.png'],
        26 => ['nom' => 'Gyoza X3', 'prix' => 5.53, 'image' => 'images/produit_b.jpg'],
        27 => ['nom' => 'Vin Blanc', 'prix' => 5.10, 'image' => 'images/produit_c.jpg'],
        28 => ['nom' => 'Tajine', 'prix' => 12.75, 'image' => 'images/produit_d.jpg'],
        29 => ['nom' => 'Millefeuille de champignions duxelles', 'prix' => 12.75, 'image' => 'images/produit_e.jpg'],
        30 => ['nom' => 'Chevreuil en sauce sur armure blanche', 'prix' => 25.50, 'image' => 'images/produit_f.jpg'],
        31 => ['nom' => 'Maki X3', 'prix' => 6, 'image' => 'images/produit_g.jpg'],
        33 => ['nom' => 'Omurice', 'prix' => 11.50, 'image' => 'images/produit_h.jpg'],
        34 => ['nom' => 'Bière Blonde', 'prix' => 4.25, 'image' => 'images/produit_i.jpg'],
        35 => ['nom' => 'Bière Brune', 'prix' => 4.68, 'image' => 'images/produit_j.jpg'],
        36 => ['nom' => 'Cocktail caiprinha ', 'prix' => 8.50, 'image' => 'images/produit_k.jpg'],
    ];

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function produitExiste(int $id): bool
    {
        return isset($this->produits[$id]);
    }

    public function add(int $id): void
    {
        $panier = $this->session->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $this->session->set('panier', $panier);
    }

    public function remove(int $id): void
    {
        $panier = $this->session->get('panier', []);
        if (isset($panier[$id])) {
            unset($panier[$id]);
            $this->session->set('panier', $panier);
        }
    }

    public function getProduitsPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $produitsPanier = [];

        foreach ($panier as $id => $quantite) {
            if (!isset($this->produits[$id])) continue;

            $produit = $this->produits[$id];
            $produitsPanier[] = [
                'id' => $id,
                'nom' => $produit['nom'],
                'prix' => $produit['prix'],
                'image' => $produit['image'],
                'quantite' => $quantite,
                'totalProduit' => $produit['prix'] * $quantite,
            ];
        }

        return $produitsPanier;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getProduitsPanier() as $produit) {
            $total += $produit['totalProduit'];
        }
        return $total;
    }

    public function decrease(int $id): void
{
    $panier = $this->session->get('panier', []);

    if (!empty($panier[$id])) {
        if ($panier[$id] > 1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]); // Supprime si la quantité tombe à 0
        }
    }

    $this->session->set('panier', $panier);
}
// Liste des codes promo possibles
private array $codesPromo = [
    'WELCOME10' => 10, // 10% de réduction
    'FREESHIP' => 40,   // Seulement Bastien, Elisa, Safae, Lylou , Louca... aura acces a ce code les autres debrouillez vous 
    'ANAIS2002' =>47, // Seulement Anais
];

// Appliquer un code promo
public function appliquerCodePromo(string $code): bool
{
    if (!isset($this->codesPromo[$code])) {
        return false;
    }

    $this->session->set('code_promo', $code);
    return true;
}


// Supprimer le code promo
public function retirerCodePromo(): void
{
    $this->session->remove('code_promo');
}

// Récupérer la valeur de la réduction
public function getReduction(): float
{
    $code = $this->session->get('code_promo');
    $reduction = 0;

    if ($code && isset($this->codesPromo[$code])) {
        $reduction = ($this->getTotal() * $this->codesPromo[$code]) / 100;
    }

    return $reduction;
}

// Total après réduction
public function getTotalAvecReduction(): float
{
    return max(0, $this->getTotal() - $this->getReduction());
}

}
