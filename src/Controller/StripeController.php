<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use DateTimeImmutable;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommandeRelationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/Client/Pay', name: 'app_stripe')]
    public function index(CommandeRepository $Crep, CommandeRelationRepository $CRrep): Response
    {
        $user = $this->getUser() ;
        $Commande = $Crep->findPanier($user);
        $Commande_relation = [];
        $total = 0 ;
        if($Commande != null){
            $Commande_relation = $CRrep->findBy(['Commande' => $Commande]) ;
            foreach ($Commande_relation as  $Com) {
                $total += $Com->getQte() * $Com->getLivre()->getPrix();
            }
        }
        if($total == 0){
            return $this->redirectToRoute('app_panier');
        }
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'total' => $total*1.07
        ]);
    }
 
 
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request ,CommandeRepository $Crep, CommandeRelationRepository $CRrep,EntityManagerInterface $Em)
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $user = $this->getUser() ;
        $Commande = $Crep->findPanier($user);
        $Commande_relation = [];
        $total = 0 ;
        if($Commande != null){
            $Commande_relation = $CRrep->findBy(['Commande' => $Commande]) ;
            foreach ($Commande_relation as  $Com) {
                $total += $Com->getQte() * $Com->getLivre()->getPrix();
            }
        }
        Charge::create ([
                "amount" => (int) ($total * 100 * 1.07),
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        $Commande->setEtat(2)
            ->setIsPayed(true)
            ->setDateCommandeAt(new DateTimeImmutable());
        foreach($Commande->getCommandeRelations() as $CR){
            $CR->getLivre()->setQte( $CR->getLivre()->getQte($CR) -  $CR->getQte());
            $Em->persist($CR->getLivre());
            $Em->persist($CR);
        } 
        $Em->persist($Commande);
        $Em->flush($Commande);

        return $this->redirectToRoute('app_commande', ['id'=> $Commande->getId()]);
    }
}