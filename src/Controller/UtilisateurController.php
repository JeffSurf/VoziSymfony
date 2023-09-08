<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Utilisateur;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'app_utilisateur_')]
#[IsGranted("ROLE_USER")]
class UtilisateurController extends AbstractController
{
    #[Route('/apps', name: 'apps')]
    public function getApps(): Response
    {
        return $this->render('utilisateur/listApps.html.twig');
    }

    #[Route('/apps/add', name: 'apps_add')]
    #[Route('/apps/update/{id}', name: 'apps_update')]
    public function editApp(Request $request, ApplicationRepository $applicationRepository, EntityManagerInterface $em, FileUploader $fileUploader, int $id = null): Response
    {
        $application = $id ? $applicationRepository->find($id) : new Application();

        if(!$application)
            throw $this->createNotFoundException("L'application n'existe pas");

        if($id)
            if($application->getUtilisateur()->getId() !== $this->getUser()->getId())
                throw new AccessDeniedHttpException("Vous ne pouvez pas modifier l'application d'une autre personne");

        $form = $this->createForm(ApplicationType::class, $application);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Ajouter l'utilisateur
            $application->setUtilisateur($this->getUser());

            /**
             * @var UploadedFile $imageFile
             */
            $imageFile = $form->get('image')->getData();

            if($imageFile)
            {
                $imageFilename = $fileUploader->upload($imageFile);

                //Supprimer l'ancienne image
                $fileUploader->delete($application->getImage());

                $application->setImage($imageFilename);
            }


            $em->persist($application);
            $em->flush();

            $this->addFlash("success", "L'application a bien été " . ($id ? "modifiée" : "ajoutée"));
            return $this->redirectToRoute("app_utilisateur_apps");
        }

        return $this->render("utilisateur/editApp.html.twig", [
            "form" => $form,
            "current_application" => $application
        ]);
    }

    #[Route("/apps/delete/{id}", name: "delete")]
    public function delete(int $id, ApplicationRepository $applicationRepository, EntityManagerInterface $em, FileUploader $fileUploader)
    {
        $appli = $applicationRepository->find($id);
        if($appli)
        {
            if($appli->getUtilisateur()->getId() !== $this->getUser()->getId())
                throw new AccessDeniedHttpException("Vous ne pouvez pas supprimer l'application d'une autre personne");

            $fileUploader->delete($appli->getImage());
            $em->remove($appli);
            $em->flush();
            $this->addFlash("success", "L'application a bien été supprimée");
        }

        return $this->redirectToRoute("app_utilisateur_apps");
    }
}
