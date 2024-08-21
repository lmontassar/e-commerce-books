<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Component\Mime\Email;
use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/Home')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client')]
    public function index(LivresRepository $rep, CategoriesRepository $CR,PaginatorInterface $paginator,Request $request): Response
    {
        $data = $rep->findAll();
        $livres = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );
        $categories = $CR->findAll();
        return $this->render('client/index.html.twig', [
            'livres' => $livres,
            "categories" => $categories,
            "categorie"=> null
        ]);
    }
    #[Route('/search', name: 'app_client_reset')]
    public function xxx()
    {
        return $this->redirectToRoute("app_client");
    }
    #[Route('/search/{text}', name: 'app_client_search')]
    public function search(string $text ,LivresRepository $rep,CategoriesRepository $CR, PaginatorInterface $paginator,Request $request): Response
    {
        $data = $rep->findBySearch($text);
        $livres = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );
        $categories = $CR->findAll();
        return $this->render('client/index.html.twig', [
            'livres' => $livres,
            "categories" => $categories,
            "categorie"=> null
        ]);
    }

    

    #[Route('/{id}', name:'app_client_cat')]
    public function FiltreCat(Categories $Cat, LivresRepository $rep,CategoriesRepository $CR, PaginatorInterface $paginator,Request $request): Response
    {
        $data = $rep->findBy( ["categorie" => $Cat]);
        $livres = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );
        $categories = $CR->findAll();
        return $this->render('client/index.html.twig', [
            'livres' => $livres,
            "categories" => $categories,
            "categorie"=> $Cat
        ]);
    }
    #[Route('/sendmail', name: 'app_mailtest')]
    public function mail(MailerInterface $mailer )
    {
        $email = (new Email())
        ->from('lounissi.montassar@hotmail.com')
        ->to('montassar9000@gmail.com')
        ->subject('Test Email')
        ->text('This is a test email sent from Symfony.');
        $mailer->send($email);
    }
    #[Route('/Livre/{id}', name: 'app_client_livre')]
    public function Livre(int $id,LivresRepository $rep): Response
    {
        $livre = $rep->find($id);

        if($livre == null){
            return $this->redirectToRoute('app_notfound');
        }
        $livres = $rep->findBy(['categorie' => $livre->getCategorie()], ['id' => 'ASC'], 4);
        //$livres = $rep->findOneBySomeField($livre);
        return $this->render('client/Livre.html.twig', [
            'livre' => $livre,
            'livres' => $livres
        ]);
    }
    #[Route('/{min}/{max}', name: 'app_client_filtre_price')]
    public function filtrePrice( int $min ,int $max,LivresRepository $rep,CategoriesRepository $CR, PaginatorInterface $paginator,Request $request): Response
    {

        $data = $rep->findInRange($min,$max);
        $livres = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            8
        );
        $categories = $CR->findAll();
        return $this->render('client/index.html.twig', [
            'livres' => $livres,
            "categories" => $categories,
            "categorie"=> null
        ]);
    }
    
}
