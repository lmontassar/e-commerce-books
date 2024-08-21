<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends AbstractController
{
    // #[Route('/Admin/categorie', name: 'admin_categorie')]
    // public function index(CategoriesRepository $rep): Response
    // {
    //     $cats = $rep->findAll();
    //     return $this->render('categorie/index.html.twig', [
    //         'les_cats' => $cats,
    //     ]);
    // }

    // #[Route('/Admin/categorie/create', name: 'admin_categorie_create')]
    // public function create(EntityManagerInterface $em, Request $request): Response
    // {   // Affichage du l'Objet formulaire 
    //     $categorie = new Categories;
    //     $form = $this->createForm(CategorieType::class,$categorie);
    //     //traitement des données
    //     $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isValid()){
    //         $em->persist($categorie);
    //         $em->flush();
    //         return $this->redirectToRoute('admin_categorie');
    //     }

    //     return $this->render('categorie/create.html.twig', [
    //         'f' => $form,
    //     ]);
    // }

    // #[Route('/Admin/categorie/update/{id}', name: 'admin_categorie_update')]
    // public function update(Categories $categorie , EntityManagerInterface $em, Request $request): Response
    // {   
    //     // Affichage du l'Objet formulaire 
    //     $form = $this->createForm(CategorieType::class,$categorie);
    //     //traitement des données
    //     $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isValid()){
    //         $em->persist($categorie);
    //         $em->flush();
    //         return $this->redirectToRoute('admin_categorie');
    //     }
    //     return $this->render('categorie/update.html.twig', [
    //         'f' => $form,
    //     ]);
    // }
}
