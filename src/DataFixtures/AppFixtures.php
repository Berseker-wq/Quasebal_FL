<?php
namespace App\DataFixtures;
 
use App\Entity\Categorie;
use App\Entity\Plat;
use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Detail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
 
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
 
    public function load(ObjectManager $manager): void
    {
        // Créer les catégories
        $categories = [];
        foreach (['Pizza', 'Plat Japonais', 'Plat Marrocain', 'Tacos', 'Work', 'Pâtes'] as $libelle) {
            $categorie = new Categorie();
            $categorie->setLibelle($libelle);
            $categorie->setImage('image_' . strtolower($libelle) . '.jpg');
            $categorie->setActive(true);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }
 
        // Créer 5 plats
        $plats = [];
        for ($i = 1; $i <= 5; $i++) {
            $plat = new Plat();
            $plat->setLibelle("Plat $i");
            $plat->setDescription("Délicieux plat $i");
            $plat->setImage('image_' . strtolower($libelle) . '.jpg');
            $plat->setPrix(mt_rand(8, 20));
            $plat->setActive(true);
            $plat->setCategorie($categories[array_rand($categories)]);
            $manager->persist($plat);
            $plats [] = $plat;
        }
 
        // Créer 3 users
        $users = [];
        // user ADMIN
        $admin = new user();
        $admin->setEmail("admin@example.com");
        $admin->setNom("Admin");
        $admin->setPrenom("Super");
        $admin->setAdresse("1 rue de l'Admin");
        $admin->setCp("80000");
        $admin->setVille("Amiens");
        $admin->setTelephone("0600000000");
        $admin->setRoles(['ROLE_ADMIN']);
 
        $password = $this->hasher->hashPassword($admin, "adminpass");
        $admin->setPassword($password);
 
        $manager->persist($admin);
        $users[] = $admin;
        
        for ($i = 1; $i <= 2; $i++) {
            $user = new user();
            $user->setEmail("user$i@example.com");
            $user->setNom("Nom$i");
            $user->setPrenom("Prenom$i");
            $user->setAdresse("Adresse $i");
            $user->setCp("8000$i");
            $user->setVille("Amiens");
            $user->setTelephone("060000000$i");
            $user->setRoles(['ROLE_USER']);
 
            $password = $this->hasher->hashPassword($user, "password");
            $user->setPassword($password);
 
            $manager->persist($user);
            $users[] = $user;
        }
 
// Créer 3 commandes avec détails
foreach (range(1, 3) as $i) {
    $commande = new Commande();
    $commande->setDateCommande(new \DateTime());
    $commande->setEtat("2");
    $commande->setuser($users[array_rand($users)]);
 
    $total = 0;
 
    // Ajouter entre 1 et 3 détails
    $nbDetails = rand(1, 3);
    for ($j = 0; $j < $nbDetails; $j++) {
        $detail = new Detail();
 
        $plat = $plats[array_rand($plats)];
        $quantite = rand(1, 3);
 
        $detail->setCommande($commande);
        $detail->setPlat($plat);
        $detail->setQuantite($quantite);
 
        $manager->persist($detail);
 
        $total += $quantite * $plat->getPrix();
    }
 
    $commande->setTotal($total);
    $manager->persist($commande);
}
 
 
        $manager->flush();
    }
}