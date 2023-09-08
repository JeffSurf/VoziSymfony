<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\ApplicationRepository;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/avis', name: "app_avis_")]
class AvisController extends AbstractController
{
    #[Route('/app/{idApp}/new', name: "create", requirements: ["idApp" => "\d+"])]
    public function create(ApplicationRepository $applicationRepository,
                           EntityManagerInterface $em,
                           Request $request,
                           HubInterface $hub,
                           int $idApp,
                           int $idAvis = null): Response
    {

        $avis = new Avis();

        $application = $applicationRepository->find($idApp);

        if(!$application)
            throw $this->createNotFoundException("L'application n'existe pas");

        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $avis->setUtilisateur($this->getUser());
            $avis->setApplication($application);

            $em->persist($avis);
            $em->flush();

            $this->addFlash("success", "Votre avis a bien été posté");

            $update = new Update('http://vozi.com/user/'.$application->getUtilisateur()->getId(),
                json_encode([
                    "utilisateur" => $this->getUser()->getPseudo(),
                    "app" => $application->getNom()
                ]));

            $hub->publish($update);

            return $this->redirectToRoute("app_application_voir", ["id" => $idApp]);
        }

        return $this->render('avis/editAvis.html.twig', [
            "form" => $form,
            "application" => $application
        ]);
    }

    #[Route('/update/{idAvis}', name: "update", requirements: ["idAvis" => "\d+"])]
    public function update(AvisRepository $avisRepository, EntityManagerInterface $em, Request $request, int $idAvis): Response
    {
        $avis = $idAvis ? $avisRepository->find($idAvis) : new Avis();

        if(!$avis)
            throw $this->createNotFoundException("Cet avis n'existe pas");

        if($avis->getUtilisateur()->getId() !== $this->getUser()->getId())
            throw new AccessDeniedHttpException("Vous ne pouvez pas modifier l'avis d'un autre utilisateur");

        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($avis);
            $em->flush();

            $this->addFlash("success", "Votre avis a bien été modifié");
            return $this->redirectToRoute("app_application_voir", ["id" => $avis->getApplication()->getId()]);
        }

        return $this->render('avis/editAvis.html.twig', [
            "form" => $form,
        ]);
    }

    #[Route('delete/{idAvis}', name: "delete", requirements: ["idAvis" => "\d+"])]
    public function delete(AvisRepository $avisRepository, EntityManagerInterface $em, int $idAvis) : Response
    {
        $avis = $avisRepository->find($idAvis);

        if(!$avis)
            throw $this->createNotFoundException("Cet avis n'existe pas");

        $idApp = $avis->getApplication()->getId();

        $em->remove($avis);
        $em->flush();

        $this->addFlash("success", "l'avis a bien été supprimé");

        return $this->redirectToRoute("app_application_voir", ["id" => $avis->getApplication()->getId()]);
    }
}