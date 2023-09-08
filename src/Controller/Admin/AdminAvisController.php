<?php

namespace App\Controller\Admin;

use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/avis', name: "app_admin_avis_")]
class AdminAvisController extends AbstractController
{
    #[Route('', name: "lister")]
    public function lister(AvisRepository $avisRepository): Response
    {
        return $this->render('admin/avis/index.html.twig', [
            "avis" => $avisRepository->findAll()
        ]);
    }

    #[Route('/delete/{id}', name: "delete", requirements: ["id" => "\d+"])]
    public function delete(int $id, AvisRepository $avisRepository, EntityManagerInterface $em): Response
    {
        $avis = $avisRepository->find($id);

        if(!$avis)
        {
            $this->addFlash("danger", "L'avis n'existe pas");
            return $this->redirectToRoute("app_admin_avis_lister");
        }

        $em->remove($avis);
        $em->flush();

        $this->addFlash("success", "L'avis a bien été supprimé");


        return $this->redirectToRoute("app_admin_avis_lister");
    }
}