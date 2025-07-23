<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    private SessionInterface $session;

    private array $produits = [
        25 => ['nom' => 'Sushi', 'prix' => 11, 'image' => 'images/produit_a.jpg'],
        26 => ['nom' => 'Gyoza', 'prix' => 9, 'image' => 'images/produit_b.jpg'],
        27 => ['nom' => 'Vin Blanc', 'prix' => 2.30, 'image' => 'images/produit_c.jpg'],
        28 => ['nom' => 'Tajine', 'prix' => 13, 'image' => 'images/produit_d.jpg'],
        29 => ['nom' => 'Millefeuille de champignions duxelles', 'prix' => 22.75, 'image' => 'images/produit_e.jpg'],
        30 => ['nom' => 'Chevreuil en sauce sur armure blanche', 'prix' => 32, 'image' => 'images/produit_f.jpg'],
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

}
