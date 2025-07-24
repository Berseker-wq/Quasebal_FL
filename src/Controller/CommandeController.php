<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\PanierService;
use Stripe\Stripe;
use Stripe\Charge;


class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
   public function index(Request $request, PanierService $panierService): Response
    {
       $produitsPanier = $panierService->getProduitsPanier();
        $total = $panierService->getTotal();


        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $adresse = $request->request->get('adresse');
            $email = $request->request->get('email');
            $telephone = $request->request->get('telephone');
            $modePaiement = $request->request->get('modePaiement');
            $accepterCgu = $request->request->get('accepter_cgu');

        // Vérification de la checkbox CGU
        if (!$accepterCgu) {
            $this->addFlash('error', 'Vous devez accepter les Conditions Générales d\'Utilisation pour valider votre commande.');
            return $this->render('panier/valider.html.twig', [
                'produitsPanier' => $produitsPanier,
                'total' => $total,
            ]);
        }

            $session = $request->getSession();
            $session->set('commande_data', compact('nom', 'adresse', 'email', 'telephone', 'modePaiement', 'produitsPanier', 'total'));

            if ($modePaiement === 'CB') {
                return $this->redirectToRoute('app_paiement_cb');
            } elseif ($modePaiement === 'PayPal') {
                return $this->redirectToRoute('app_paiement_paypal');
            } else {
                return $this->redirectToRoute('app_confirmation');
            }
        }

        return $this->render('panier/valider.html.twig', [
            'produitsPanier' => $produitsPanier,
            'total' => $total,
        ]);
    }

    #[Route('/paiement/cb', name: 'app_paiement_cb', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function paiementCb(Request $request): Response
    {
        $session = $request->getSession();
        $commandeData = $session->get('commande_data');

        if (!$commandeData) {
            return $this->redirectToRoute('app_commande');
        }

        return $this->render('commande/paiement_cb.html.twig', [
            'total' => $commandeData['total'],
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
             'produitsPanier' => $commandeData['produitsPanier'] ?? [],
        ]);
    }

    #[Route('/paiement/cb/api', name: 'app_paiement_cb_api', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function paiementCbApi(Request $request): JsonResponse
{
    $session = $request->getSession();
    $commandeData = $session->get('commande_data');

    if (!$commandeData) {
        return new JsonResponse(['success' => false, 'message' => 'Commande introuvable']);
    }

    $data = json_decode($request->getContent(), true);
    $token = $data['stripeToken'] ?? null;

    if (!$token) {
        return new JsonResponse(['success' => false, 'message' => 'Token manquant']);
    }

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    try {
        $charge = Charge::create([
            'amount' => intval($commandeData['total'] * 100),
            'currency' => 'eur',
            'source' => $token,
            'description' => 'Paiement CB - Commande Symfony',
        ]);

        // ✅ NE PAS envoyer d'e-mail ici
        // ✅ NE PAS supprimer la session ici

        return new JsonResponse([
            'success' => true,
            'redirect' => $this->generateUrl('app_confirmation')
        ]);
    } catch (\Exception $e) {
        return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
    }
}

    #[Route('/paiement/paypal', name: 'app_paiement_paypal', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function paiementPaypal(Request $request, MailerInterface $mailer): Response
    {
        $session = $request->getSession();
        $commandeData = $session->get('commande_data');

        if (!$commandeData) {
            return $this->redirectToRoute('app_commande');
        }

        if ($request->isMethod('POST')) {
            $this->envoyerEmailConfirmation($commandeData, $mailer);
            $session->remove('commande_data');
            return $this->redirectToRoute('app_confirmation');
        }

        return $this->render('commande/paiement_paypal.html.twig', [
            'commandeData' => $commandeData,
        ]);
    }

   #[Route('/commande/confirmation', name: 'app_confirmation', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
public function confirmation(Request $request, MailerInterface $mailer): Response
{
    $session = $request->getSession();
    $commandeData = $session->get('commande_data');

    if (!$commandeData) {
        return $this->redirectToRoute('app_commande');
    }
     // Enregistrement dans l'historique
    $historique = $session->get('historique_commandes', []);
    $historique[] = [
        'date' => (new \DateTime())->format('Y-m-d H:i:s'),
        'nom' => $commandeData['nom'],
        'adresse' => $commandeData['adresse'],
        'email' => $commandeData['email'],
        'telephone' => $commandeData['telephone'],
        'total' => $commandeData['total'],
        'produitsPanier' => $commandeData['produitsPanier'],
        'modePaiement' => $commandeData['modePaiement'],
    ];
    $session->set('historique_commandes', $historique);


    // Envoi de l'email de confirmation (une seule fois)
    $this->envoyerEmailConfirmation($commandeData, $mailer);

    // Affichage de la page avec les données
    $response = $this->render('commande/index.html.twig', [
        'nom' => $commandeData['nom'] ?? '',
        'adresse' => $commandeData['adresse'] ?? '',
        'email' => $commandeData['email'] ?? '',
        'telephone' => $commandeData['telephone'] ?? '',
        'total' => $commandeData['total'] ?? 0,
    ]);

    // Suppression des données en session
    $session->remove('commande_data');

    return $response;
}

private function envoyerEmailConfirmation(array $commandeData, MailerInterface $mailer): void
{
       $montant = floatval(str_replace(',', '.', $commandeData['total'] ?? 0));
    // Envoi de l'email de confirmation
    $email = (new Email())
        ->from('ton.email@tondomaine.com')
        ->to($commandeData['email'])
        ->subject('Confirmation de votre commande')
        ->text(sprintf(
            "Bonjour %s,\nMerci pour votre commande.\nAdresse: %s\nTéléphone: %s\nMontant : %.2f€\n\nCordialement,\nL'équipe",
            $commandeData['nom'],
            $commandeData['adresse'],
            $commandeData['telephone'],
            $commandeData['total']
        ));
        
    $mailer->send($email);
}
#[Route('/commande/historique', name: 'app_commande_historique', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
public function historique(Request $request): Response
{
    $session = $request->getSession();
    $historique = $session->get('historique_commandes', []);

    return $this->render('commande/historique.html.twig', [
        'historique' => $historique,
    ]);
}

}