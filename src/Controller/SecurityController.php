<?php

namespace App\Controller;
use App\EventListener\LoginListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private SessionInterface $session;
    private EntityManagerInterface $Em;
    #[Route(path: '/', name: 'app_r')]
    public function toLogin()
    {
        if($this->getUser() == null)
            return $this->redirectToRoute('app_client');
        if($this->IsGranted("ROLE_ADMIN") )
            return $this->redirectToRoute('app_dashboard');
        if($this->IsGranted("ROLE_USER") )
            return $this->redirectToRoute('app_client');
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(EntityManagerInterface $Em, SessionInterface $session,AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // if ($this->getUser()) {
        //     $Commande = $session->get('Commande');
        //     $Commande->setClient($this->getUser());
        //     dd($Commande);
        //     foreach($Commande->getCommandeRelations() as $Cr){
        //         $Em->persist($Cr);
        //     }
        //     $Em->persist($Commande);
        //     $Em->flush();
        // }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
