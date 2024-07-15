<?php
// fait tout l'auth

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class Authenticator extends AbstractLoginFormAuthenticator
{
    // nous permettre d'avoir un certain nombre d'infomations importer directement à l'intérieur de notre authenticator
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    // pour faire des générations d'url
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    // passport permet de gérer l'authentification des utilisateurs
    public function authenticate(Request $request): Passport
    {   //récupére l'email d'user
        $email = $request->getPayload()->getString('email');
        // dans la session on insère le dernier utilisateur qui a été taper
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
        // retourne un passport 
        return new Passport(
            new UserBadge($email),//permet d'aller chercher l'user
            new PasswordCredentials($request->getPayload()->getString('password')),//va récupérer le mdp qui a été diractement tapper
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]// permet de vérifier que notre formulaire vient bien de notre site
        );
    }
    // si l'auth fonctionne on va entrer dans cette méthode la
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {// chemin de retour si jamais vous avez envie que votre user revient sur la page d'ou il s'est connecté
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
         // permet de rediriger l'user vers une page particuliere(exception )
        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_main'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }
// permettre d'avoir l'url par rapport à la routte qui s'appelle app_login
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}