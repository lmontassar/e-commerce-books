<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/Client',)]
class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function validate(CommandeRepository $Crep,Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Profile updated successfuly!'
            );
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
            'form' => $form,
        ]);
    }
}
