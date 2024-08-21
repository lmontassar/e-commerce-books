<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Entity\Commande;
use App\Entity\CommandeRelation;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Types\DateImmutableType;
use App\Repository\CommandeRelationRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
    #[Route('/commande/All', name: 'app_commande_all')]
    public function All(CommandeRepository $CR): Response
    {   
        $LesCommande = $CR->findBy(['Client'=>$this->getUser()],['DateCommandeAt'=>'DESC']);
        $array = [];
        foreach($LesCommande as $Com){
            $total = 0;
            $Qte = 0 ;
            foreach( $Com->getCommandeRelations() as $art){
                $total += $art->getQte()*$art->getLivre()->getPrix();
                $Qte += $art->getQte();
            }
            $total *= 1.07;
            $arr = [
                "total" => $total,
                "Qte" => $Qte
            ];
            if($Com->getEtat() != 0)
            array_push($array , [ "com" => $Com , "arr" => $arr ]);
        }

            return $this->render('commande/commandeAll.html.twig', [
            'orders' => $array,

            ]);
    }

    #[Route('/commande/{id}', name: 'app_commande')]
    public function Afficher(CommandeRepository $CR ,int $id, CommandeRelationRepository $CRP): Response
    {
        try{           
            $user = $this->getUser();
            $C = $CR->find($id);
            if($C == null ) return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
            if($C->getClient()->getEmail() != $user->getUserIdentifier() || $C->getEtat() == 0)
                return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
            $total = 0;
            foreach ($C->getCommandeRelations() as  $Com) {
                $total += $Com->getQte() * $Com->getLivre()->getPrix();
            }

            return $this->render('commande/Commande.html.twig', [
                'controller_name' => 'CommandeController',
                'Commande' => $C,
                'Articles' => $C->getCommandeRelations(),
                'total' => $total
            ]);
        }catch(Exception $ex){
            return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
        }
    }

}