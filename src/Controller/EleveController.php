<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eleves', name: 'app_eleve_')]
class EleveController extends AbstractController
{

    #[Route('', name: "lister")]
    public function lister(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('eleve/index.html.twig', [
            "eleves" => $utilisateurRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: "info", requirements: ["id" => "\d+"])]
    public function voirEleve(int $id, UtilisateurRepository $utilisateurRepository): Response
    {
        $eleve = $utilisateurRepository->find($id);

        if(!$eleve)
            throw $this->createNotFoundException("L'élève recherché n'existe pas");

        return $this->render("eleve/voirEleve.html.twig", [
            "eleve" => $eleve
        ]);
    }
}
