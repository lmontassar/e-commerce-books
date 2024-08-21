<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Livres;
use App\Form\UserType;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Entity\CommandeRelation;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommandeRelationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(CommandeRepository $Crep, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $Commande = null;
        if($user != null){
            $Commande = $Crep->findPanier($user);
        } else {
            $Commande =$session->get('Commande');
        }
        $total = 0;
        $Commande_relation = [];
        if ($Commande != null) {
            foreach ($Commande->getCommandeRelations() as  $Com) {
                $total += $Com->getQte() * $Com->getLivre()->getPrix();
            }
            $Commande_relation = $Commande->getCommandeRelations();
        }
        return $this->render('panier/index.html.twig', [
            'panier' => $Commande_relation ,
            'total' => $total
        ]);
    }

    #[Route('/addtopanier/{id}', name: 'app_add_panier')]
    public function addPanier(CommandeRepository $rep,SessionInterface $session, EntityManagerInterface $em, Livres $livre)
    {
        $user = $this->getUser();
        $Commande = null;
        if($user != null){
            $Commande = $rep->findPanier($user);
        } else {
            $Commande = $session->get('Commande');
        }
        $Commande_relation = null;
        if ($Commande == null) {
            $Commande = (new Commande())
                ->setDateCommandeAt(new \DateTimeImmutable())
                ->setEtat(0);
            if($user !=null){
                $Commande->setClient($user);
                $em->persist($Commande);
            }
        } else {
            foreach($Commande as $CR){
                if($CR->getLivre() == $livre)
                    return new JsonResponse(['statuss' => false]);
            }
        }
        $Commande_relation = (new CommandeRelation())
            ->setQte(1)
            ->setLivre($livre)
            ->setCommande($Commande);
        if($user){
            $em->persist($Commande_relation);
            $em->flush();
        } else{
            $Commande->addCommandeRelation($Commande_relation);
            $session->set('Commande',$Commande);
        }
        return new JsonResponse(['statuss' => true]);
    }

    #[Route('/addtopanier/{id}/{Qte}', name: 'app_add_panier_qte')]
    public function addPanierWithQte(SessionInterface $session,int $Qte, CommandeRepository $rep, CommandeRelationRepository $repCr, EntityManagerInterface $em, Livres $livre)
    {
        $user = $this->getUser();
        $Commande = null;
        if($user != null){
            $Commande = $rep->findPanier($user);
        } else {
            $Commande = $session->get('Commande');
        }
        $Commande_relation = null;
        if ($Commande == null) {
            $Commande = (new Commande())
                ->setDateCommandeAt(new \DateTimeImmutable())
                ->setEtat(0);
            if($user !=null){
                $Commande->setClient($user);
                $em->persist($Commande);
            }
        } else {
            foreach($Commande as $CR){
                if($CR->getLivre() == $livre)
                    return new JsonResponse(['statuss' => false]);
            }
        }
        $Commande_relation = (new CommandeRelation())
            ->setQte($Qte)
            ->setLivre($livre)
            ->setCommande($Commande);
        if($user){
            $em->persist($Commande_relation);
            $em->flush();
        } else{
            $Commande->addCommandeRelation($Commande_relation);
            $session->set('Commande',$Commande);
        }
        return new JsonResponse(['statuss' => true]);

        // $user = $this->getUser();
        // $Commande = $rep->findPanier($user);
        // $Commande_relation = null;
        // if ($Commande == null) {
        //     $Commande = (new Commande())
        //         ->setDateCommandeAt(new \DateTimeImmutable())
        //         ->setEtat(0)
        //         ->setClient($user);
        //     $em->persist($Commande);
        // } else {
        //     $Commande_relation = $repCr->findOneBy(['Commande' => $Commande, 'Livre' => $livre]);
        //     if ($Commande_relation != null) {

        //         return new JsonResponse(['statuss' => false]);
        //     }
        // }
        // $Commande_relation = (new CommandeRelation())
        //     ->setQte($Qte)
        //     ->setLivre($livre)
        //     ->setCommande($Commande);
        // $em->persist($Commande_relation);
        // $em->flush();
        // return new JsonResponse(['statuss' => true]);
    }

    #[Route('/panier/remove/{id}', name: 'app_delete_panier')]
    public function remove(CommandeRepository $Crep, CommandeRelation $CR, CommandeRelationRepository $CRrep, EntityManagerInterface $Em): Response
    {
        $user = $this->getUser();
        $Commande = $Crep->findPanier($user);
        if ($Commande != null) {
            $res = $CRrep->findBy(['Commande' => $Commande, 'id' => $CR->getId()]);
            if ($res != null) {
                $Em->remove($CR);
                $Em->flush();
                return $this->redirectToRoute('app_panier');
            }
        }
        return $this->redirectToRoute('app_panier');
    }
    
    #[Route('/panier/update/{id}/{qte}', name: 'app_update_panier')]
    public function update(int $qte, CommandeRelation $Cr, EntityManagerInterface $em)
    {
        $Cr->setQte($qte);
        $em->flush();
        return new JsonResponse(['statuss' => true]);
    }

    #[Route('/panier/remove/session/{isbn}', name: 'app_delete_panier_session')]
    public function removeFromSession(SessionInterface $session,CommandeRepository $Crep,String $isbn, CommandeRelationRepository $CRrep, EntityManagerInterface $Em): Response
    {
        $Commande =$session->get('Commande');
        if ($Commande != null) {
            foreach($Commande->getCommandeRelations() as $CR){
                if($CR->getLivre()->getISBN() == $isbn){
                    $Commande->removeCommandeRelation($CR);
                    $session->set('Commande',$Commande);
                    return $this->redirectToRoute('app_panier');
                }
            }
        }
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/update/session/{isbn}/{qte}', name: 'app_update_panier_session')]
    public function update_session(SessionInterface $session ,int $qte, String $isbn)
    {
        $Commande =$session->get('Commande');
        if ($Commande != null) {
            foreach($Commande->getCommandeRelations() as $CR){
                if($CR->getLivre()->getISBN() == $isbn){
                    $Commande->removeCommandeRelation($CR);
                    $CR->setQte($qte);
                    $Commande->addCommandeRelation($CR);
                    $session->set('Commande',$Commande);
                    return $this->redirectToRoute('app_panier');
                }
            }
        }
        return new JsonResponse(['statuss' => true]);
    }

    #[Route('/panier/validateData', name: 'app_validate')]
    public function validate(CommandeRepository $Crep,Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser() ;
            $Commande = $Crep->findPanier($user);
            $Commande->setEtat(1)
            ->setIsPayed(false)
            ->setDateCommandeAt(new \DateTimeImmutable());
            foreach($Commande->getCommandeRelations() as $CR){
                $CR->getLivre()->setQte( $CR->getLivre()->getQte($CR) -  $CR->getQte());
                $entityManager->persist($CR->getLivre());
                $entityManager->persist($CR);
            } 
            $entityManager->persist($Commande);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Order finished Successfuly!'
            );
            return $this->redirectToRoute('app_commande', ['id'=> $Commande->getId()]);
        }
        return $this->render('Panier/Validate.html.twig', [
            'user' => $this->getUser(),
            'form' => $form,
        ]);
    }
}