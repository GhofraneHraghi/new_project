<?php
namespace App\Controller;

use App\Entity\Contrat;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

#[Route('/contrat')]
class AdminContratController extends AbstractController
{
    #[Route('/{userId}', name: 'app_admin_contrat_index', methods: ['GET'])]
    public function index(Request $request,int $userId, UserRepository $userRepository, ContratRepository $contratRepository): Response
    {      // Récupérer le paramètre de recherche
        $search = $request->query->get('search');
            // Chercher les contrats en fonction de l'email, ou tous si aucun paramètre n'est donné
    $contrats = $contratRepository->findByUserEmail($search);
        // Trouver l'utilisateur par son identifiant
        $user = $userRepository->find($userId);
        
        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Récupérer les contrats de l'utilisateur
        $contracts = $contratRepository->findBy(['user' => $user]);

        // Rendre la vue avec les contrats et l'utilisateur
        return $this->render('admin_contrat/index.html.twig', [
            'user' => $user,
            'contrats' => $contracts,
        ]);
    }

    #[Route('/new/{email}', name: 'app_admin_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, string $email): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $contrat = new Contrat();
        $contrat->setUser($user);
        $form = $this->createForm(ContratType::class, $contrat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contrat);
            $em->flush();

            return $this->redirectToRoute('app_admin_contrat_show', ['email' => $user->getEmail()]);
        }

        return $this->render('admin_contrat/new.html.twig', [
            'contrat' => $contrat,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_admin_contrat_show', methods: ['GET'])]
    public function show(int $id, ContratRepository $contratRepository): Response
    {
        // Trouver le contrat par son identifiant
        $contrat = $contratRepository->find($id);
        
        // Vérifier si le contrat existe
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé');
        }
        
        // Récupérer l'utilisateur associé au contrat
        $user = $contrat->getUser();
    
        // Rendre la vue avec le contrat et l'utilisateur
        return $this->render('admin_contrat/show.html.twig', [
            'contrat' => $contrat,
            'user' => $user,
        ]);
    }
    
    #[Route('/edit/{id}', name: 'app_admin_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $user = $contrat->getUser();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_contrat_show', ['email' => $user->getEmail()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
    //public function someAction()
//{
    // Récupérez les valeurs nécessaires depuis la base de données ou autre logique
   // $someUserId = 1;
    //$someUserEmail = 'user@example.com';
    //$someContratId = 1;

    //return $this->render('some_template.html.twig', [
      //  'someUserId' => $someUserId,
        //'someUserEmail' => $someUserEmail,
        //'someContratId' => $someContratId,
    //]);
//}

public function test (){
    return true;
}

}
