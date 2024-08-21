<?php

namespace App\Controller;

use App\Repository\CommandeRelationRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/Admin/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/orders', name: 'app_dashboard')]
    public function orders(CommandeRepository $CR): Response
    {
        $now = new \DateTime();
        $now->modify('+1 days');
        $sdate = new \DateTime();
        $sdate->modify('-12 months');
        $month = (int)$sdate->format('m') + 1;
        $year = (int)$sdate->format('Y') ;
        $statistic = $CR->CountOrdersLast12Months($now,$sdate);
        $j = 0;
        $list = [];
        for( $i=0 ; $i<12 ; $i++ ){
            $t = false;
            foreach($statistic as $val){
                if($val["month"] == $month){
                    $list[] = $val;
                    $t = true;
                }
            }

            if( $t == false ) {
                array_push($list,[
                    "month" => $month,
                    "orderCount" => 0,
                    "year" => $year,
                ]);
            }

            if(($month+1) <= 12) {
                $month = $month +1;
            }
            else{
                $month =  1;
                $year  += 1 ;
            }
        }
        return $this->render('dashboard/orders.html.twig', [
            'list' => $list,
        ]);
    }
    #[Route('/books', name: 'app_dashboard_books')]
    public function books(CommandeRelationRepository $CR): Response
    {
        $Commands = $CR->GetLivreseles();

        return $this->render('dashboard/books.html.twig', [
            'list' => $Commands,
            'count' => count($Commands )
        ]);
    }

    #[Route('/categorie', name: 'app_dashboard_categorie')]
    public function categorie(CommandeRelationRepository $CRP): Response
    {
        $Cat = $CRP->GetQtePerCategorie();
        return $this->render('dashboard/categorie.html.twig', [
            'cat' => $Cat
        ]);
    }

    #[Route('/money', name: 'app_dashboard_money')]
    public function money(CommandeRepository $commandeRepository): Response
    {
        $commands = $commandeRepository->findAll();
        
        $totalmonth = 0;
        $totalyear = 0;
        $monthlyRevenue = [];
        $topProducts = [];
        
        $currentMonth = (new \DateTimeImmutable())->format('m');
        $currentYear = (new \DateTimeImmutable())->format('Y');

        foreach ($commands as $command) {
            if ($command->isIsPayed()) {
                $commandMonth = $command->getDateCommandeAt()->format('m');
                $commandYear = $command->getDateCommandeAt()->format('Y');
                $commandeRelations = $command->getCommandeRelations();
                foreach ($commandeRelations as $relation) {
                    $amount = $relation->getLivre()->getPrix() * $relation->getQte() * 1.07;
                    $totalyear += $amount;
                    $monthKey = $commandYear . '-' . $commandMonth;
                    if (!isset($monthlyRevenue[$monthKey])) {
                        $monthlyRevenue[$monthKey] = 0;
                    }
                    $monthlyRevenue[$monthKey] += $amount;
                    if ($commandMonth == $currentMonth && $commandYear == $currentYear) {
                        $totalmonth += $amount;
                    }
                    $productName = $relation->getLivre()->getTitre();
                    if (!isset($topProducts[$productName])) {
                        $topProducts[$productName] = 0;
                    }
                    $topProducts[$productName] += $relation->getQte();
                }
            }
        }
        
        $monthData = [['Month', 'Revenue']];
        foreach ($monthlyRevenue as $month => $revenue) {
            $monthData[] = [$month, $revenue];
        }
        usort($monthData, function($a, $b) {
            if ($a[0] === 'Month') return -1;
            return strtotime($a[0]) - strtotime($b[0]);
        });

        arsort($topProducts);
        $topProducts = array_slice($topProducts, 0, 5, true);

        $top_books = [['Product', 'Sales']];
        foreach ($topProducts as $product => $sales) {
            $top_books[] = [$product, $sales];
        }

        return $this->render('dashboard/money.html.twig', [
            'totalmonth' => $totalmonth,
            'totalyear' => $totalyear,
            'monthData' => json_encode($monthData),
            'top_books' => json_encode($top_books)
        ]);
    }
}
