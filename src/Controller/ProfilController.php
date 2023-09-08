<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil', name: 'app_profil_')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('', name: 'voir')]
    public function voir(): Response
    {
        return $this->render('profil/voirProfil.html.twig');
    }

    #[Route('/edit', name: 'editer')]
    public function editer(Request $request, EntityManagerInterface $em, FileUploader $fileUploader)
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilFormType::class, $user);

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

                $fileUploader->delete($user->getImage());

                $user->setImage($imageFilename);
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("app_profil_voir");
        }

        return $this->render('profil/editProfil.html.twig', [
            "profilForm" => $form
        ]);
    }
}
