<?php

namespace App\Controller\Admin;

use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/application', name: "app_admin_application_")]
class AdminApplicationController extends AbstractController
{
    #[Route('', name: "lister")]
    public function lister(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('admin/application/index.html.twig', [
            'applications' => $applicationRepository->findAll()
        ]);
    }

    #[Route("/edit/{id}", name: "editer")]
    public function editer(int $id, ApplicationRepository $applicationRepository, EntityManagerInterface $em, FileUploader $fileUploader, Request $request): Response
    {
        $application = $applicationRepository->find($id);

        if(!$application)
            throw $this->createNotFoundException("L'application recherchée n'existe pas");

        $form = $this->createForm(ApplicationType::class, $application);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
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

            $this->addFlash("success", "L'application a bien été modifiée");
            return $this->redirectToRoute("app_admin_application_lister");

        }

        return $this->render('admin/application/editApp.html.twig', [
            'form' => $form,
            'current_application' => $application
        ]);
    }

    #[Route("/delete/{id}", name: "supprimer")]
    public function supprimer(int $id, ApplicationRepository $applicationRepository, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $appli = $applicationRepository->find($id);

        if($appli)
        {
            $fileUploader->delete($appli->getImage());
            $em->remove($appli);
            $em->flush();
            $this->addFlash("success", "L'application a bien été supprimée");
        }

        return $this->redirectToRoute("app_admin_application_lister");
    }
}