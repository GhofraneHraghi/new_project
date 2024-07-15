<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher
    , Security $security, EntityManagerInterface $entityManager// pour pouvoir accéder à mes entités
    ): Response
    { // crée un nouvelle user
        $user = new User();
        // crée le formulaire correspondant
        $form = $this->createForm(RegistrationFormType::class, $user);
        //gére le formulaire
        $form->handleRequest($request);
        // si le formulaire est bon 
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password( gére l'inscrit)
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Ensure roles are assigned
            $user->setRoles(['ROLE_USER']);

            // Ensure the city field is correctly set (if applicable)
            // $user->setCity($form->get('city')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            // authentifier l'user
            return $security->login($user, Authenticator::class, 'main');
        }
         // si le formulaire n'a pas été encore nevoyé je l'envoi
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}


