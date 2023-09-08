<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/apps", name: "app_application_")]
class ApplicationController extends AbstractController
{
    #[Route('', name: 'lister')]
    public function lister(ApplicationRepository $applicationRepository): Response
    {
        $apps = $applicationRepository->findAll();

        return $this->render('application/index.html.twig', [
            "applications" => $apps
        ]);
    }

    #[Route('/{id}', name: 'voir')]
    public function voir(int $id, ApplicationRepository $applicationRepository): Response
    {
        $app = $applicationRepository->find($id);

        if(!$app)
            throw $this->createNotFoundException("L'application recherchée n'existe pas");

        return $this->render("application/voirApp.html.twig", [
            "application" => $app
        ]);
    }
}
