<?php

namespace App\Controller\Admin;

use App\Repository\UtilisateurRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/utilisateur', name: 'app_admin_utilisateur_')]
class AdminUtilisateurController extends AbstractController
{
    #[Route('', name: 'lister')]
    public function lister(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/utilisateur/index.html.twig', [
            "utilisateurs" => $utilisateurRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function supprimer(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em, FileUploader $fileUploader)
    {
        $user = $utilisateurRepository->find($id);
        if($user)
        {
            $fileUploader->delete($user->getImage());

            $em->remove($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été supprimé");
        }

        return $this->redirectToRoute('app_admin_utilisateur_lister');
    }
}
