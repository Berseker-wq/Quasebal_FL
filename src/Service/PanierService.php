<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    private SessionInterface $session;

    private array $produits = [
        15 => ['nom' => 'Vin Blanc', 'prix' => 4.15, 'image' => 'images/produit_a.jpg'],
        13 => ['nom' => 'Pizza Margaritha', 'prix' => 12, 'image' => 'images/produit_b.jpg'],
        14 => ['nom' => 'Sangria', 'prix' => 5.65,  'image' => 'uploads/images/plats/sangria-maison-1752652711.jpg'],
        11 => ['nom' => 'Pizza Vege', 'prix' => 14.5,  'image' => 'images/produit_d.jpg'],
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
}
