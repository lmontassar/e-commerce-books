<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Admin', name: 'app_')]
class LivresController extends AbstractController
{
    #[Route('/livres', name: 'livres')]
    public function index(LivresRepository $rep): Response
    {
        $livres = $rep->findAll();
        //$livres = $rep->findGreaterThan(120);
        return $this->render('livres/index.html.twig', [
            'livres' => $livres
        ]);
    }
    #[Route('/livre/show/{id}',name:'livre')]
    public function show(Livres $livre){
        return $this->render('livres/show.html.twig', [
            'livre' => $livre
        ]);
    }
    // #[Route('/livres/create',name:'create_livre')]
    // public function create(EntityManagerInterface $em ){
        
    //     $livre = new Livres();
    //     $livre
    //     ->setImage('https://picsum.photos/300')
    //     ->setTitre('Titre du livre 5')
    //     ->setISBN('1245')
    //     ->setPrix(200)
    //     ->setEditeur('editeur 14')
    //     ->setEditedAt( new \DateTimeImmutable('01-01-2024') )
    //     ->setSlug('titre-du-livre-5')
    //     ->setResume('skldfjhglskdfjhgsldkrfgjhsdfglkjhsdfglkjh') ;
    //     $em->persist($livre);
    //     $em->flush();
    //     dd($livre);
    // }
    #[Route('/livres/delete/{id}',name:'delete_livre')]
    public function delete(EntityManagerInterface $em , Livres $livre){
        $em->remove($livre);
        $em->flush();
        return $this->redirectToRoute('app_livres');
    }

    #[Route('/livres/edit/{id}', name: 'livres_edit')]
    public function edit(Livres  $livre,EntityManagerInterface $em,  Request $request): Response
    {   // Affichage du l'Objet formulaire 
        $form = $this->createForm(LivreType::class,$livre);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livres/update.html.twig', [
            'f' => $form,
        ]);
    }
    #[Route('/livres/edit', name: 'livres_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {   // Affichage du l'Objet formulaire 
        $livre = new Livres;
        $form = $this->createForm(LivreType::class,$livre);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('app_livres');
        }

        return $this->render('livres/create.html.twig', [
            'f' => $form,
        ]);
    }

}
