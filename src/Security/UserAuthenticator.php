<?php

namespace App\Security;

use App\Entity\Commande;
use Doctrine\ORM\EntityManager;
use App\Entity\CommandeRelation;
use App\Repository\CommandeRepository;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $urlGenerator;
    private $security;
    private $Em;
    private $lr;
    private $CRP;

    public function __construct(LivresRepository $lr, CommandeRepository $CRP, UrlGeneratorInterface $urlGenerator, Security $security, EntityManagerInterface $Em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->Em = $Em;
        $this->lr = $lr;
        $this->CRP = $CRP;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($user = $this->security->getUser()) { // if user is logged
            $Commande = $request->getSession()->get('Commande'); // get orders from session 
            if ($Commande != null) { // There is an orders ? 
                $Commande->setClient($user);                // set the user to order
                $CR = $Commande->getCommandeRelations();    // get the orderDetails
                $Comm = $this->CRP->findPanier($user);      // get the cart from DB

                if ($Comm == null)   // if the user have no cart with his ID 
                {
                    // create a new cart that's mean create Commande with status(ETat) = 0
                    $Comm = new Commande();
                    $Comm->setClient($user)
                        ->setDateCommandeAt(new \DateTimeImmutable())
                        ->setEtat(0);
                    $this->Em->persist($Comm);
                    foreach ($CR as $Cr) {
                        $livre = $this->lr->findOneBy(["id" => $Cr->getLivre()->getId()]);
                        $c = new CommandeRelation();
                        $c->setLivre($livre)
                            ->setQte($Cr->getQte())
                            ->setCommande($Comm);
                        $this->Em->persist($c);
                    }
                    $this->Em->flush();
                } else { // the user have already a cart with his ID
                    $this->Em->persist($Comm);
                    foreach ($CR as $Cr) { // check all the orders 
                        $t = true;
                        foreach ($Comm->getCommandeRelations() as $cc) {
                            if ($Cr->getLivre()->getId() == $cc->getLivre()->getId()) //if the session article same with an user cart article 
                            {
                                // just change the QTE that's all 
                                $livre = $this->lr->findOneBy(["id" => $Cr->getLivre()->getId()]);
                                $cc->setLivre($livre)
                                    ->setQte($Cr->getQte() + $cc->getQte() )
                                    ->setCommande($Comm);
                                $this->Em->persist($cc);
                                $t = false;
                            }
                        }
                        if ($t == true) {
                            $livre = $this->lr->findOneBy(["id" => $Cr->getLivre()->getId()]);
                            $c = new CommandeRelation();
                            $c->setLivre($livre)
                                ->setQte($Cr->getQte())
                                ->setCommande($Comm);
                            $this->Em->persist($c);
                        }
                    }
                $this->Em->flush();
                }
            }
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
        } else {
            return new RedirectResponse($this->urlGenerator->generate('app_client'));
        }
        throw new \Exception('TODO: provide a valid redirect inside ' . __FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
