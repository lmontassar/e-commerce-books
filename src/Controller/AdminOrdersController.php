<?php

namespace App\Controller;

use Exception;
use App\Repository\CommandeRepository;
use App\Repository\CommandeRelationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/Admin/orders')]
class AdminOrdersController extends AbstractController
{

    #[Route('/', name: 'app_admin_orders')]
    public function All(CommandeRepository $CR): Response
    {   
        $LesCommande = $CR->findBy([],["DateCommandeAt"=>"DESC"]);
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
        return $this->render('admin_orders/index.html.twig',[
            'orders' => $array,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_commande')]
    public function Afficher(CommandeRepository $CR ,int $id, CommandeRelationRepository $CRP): Response
    {
        try{           
            $C = $CR->find($id);
            if($C == null ) return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
            if( $C->getEtat() == 0 )
                return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
            $total = 0;
            foreach ($C->getCommandeRelations() as  $Com) {
                $total += $Com->getQte() * $Com->getLivre()->getPrix();
            }

            return $this->render('admin_orders/order.html.twig', [
                'Commande' => $C,
                'Articles' => $C->getCommandeRelations(),
                'total' => $total
            ]);
        }catch(Exception $ex){
            return $this->redirectToRoute('app_notfound', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/export/toExcel', name: 'app_admin_orders_export')]
    public function export(CommandeRepository $CR): Response
    {
        $orders = $CR->findBy([], ["DateCommandeAt" => "DESC"]);
        
        $response = new StreamedResponse(function() use ($orders) {
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile('php://output');

            $headerRow = WriterEntityFactory::createRowFromArray([
                'ID', 'ID_CLIENT', 'FULLNAME', 'DATE', 'QTE_TOTAL', 'SUB_TOTAL', 'ETAT', 'Payment'
            ]);
            $writer->addRow($headerRow);

            // Fill data
            foreach ($orders as $order) {
                $orderDetails = $order->getCommandeRelations();
                $qteTotal = array_reduce($orderDetails->toArray(), function($sum, $rel) { return $sum + $rel->getQte(); }, 0);
                $subTotal = array_reduce($orderDetails->toArray(), function($sum, $rel) { return $sum + $rel->getQte() * $rel->getLivre()->getPrix(); }, 0) * 1.07;

                $row = WriterEntityFactory::createRowFromArray([
                    $order->getId(),
                    $order->getClient()->getId(),
                    $order->getClient()->getNom() . ' ' . $order->getClient()->getPrenom(),
                    $order->getDateCommandeAt()->format('d M, Y'),
                    $qteTotal,
                    $subTotal,
                    $order->getEtat(),
                    $order->isIsPayed() ? 'Paid' : 'Not Paid'
                ]);
                $writer->addRow($row);
            }
            $writer->close();
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="orders.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
