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
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/contrat')]
class AdminContratController extends AbstractController
{
    #[Route('/{userId}', name: 'app_admin_contrat_index', methods: ['GET'])]
    public function index(Request $request, int $userId, UserRepository $userRepository, ContratRepository $contratRepository): Response
    {
        $searchType = $request->query->get('type');

        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $contracts = $searchType
            ? $contratRepository->findByTypeAndUser($searchType, $user)
            : $contratRepository->findBy(['user' => $user]);

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

            return $this->redirectToRoute('app_admin_contrat_show', ['id' => $contrat->getId()]);
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
        $contrat = $contratRepository->find($id);
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé');
        }

        $user = $contrat->getUser();

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

            return $this->redirectToRoute('app_admin_contrat_show', ['id' => $contrat->getId()], Response::HTTP_SEE_OTHER);
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
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_contrat_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_admin_contrat_pdf', methods: ['GET'])]
    public function pdf(int $id, ContratRepository $contratRepository): Response
    {
        $contrat = $contratRepository->find($id);
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé');
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('admin_contrat/pdf.html.twig', [
            'contrat' => $contrat,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="contrat.pdf"',
        ]);
    }
}
