<?php

namespace App\Controller\Folder\add_update_delete;

use App\Entity\AllPassword;
use App\Repository\AllPasswordRepository;
use App\Repository\FolderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ImageFormatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class DeleteFolderController extends AbstractController
{
    private UserRepository $ur;
    private AllPasswordRepository $apr;
    private EntityManagerInterface $em;
    private FolderRepository $fr;

    public function __construct(FolderRepository $fr, UserRepository $ur, AllPasswordRepository $apr, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->apr = $apr;
        $this->em = $em;
        $this->fr = $fr;
    }

    #[Route('/gestionnaire/dossier/{id}/supprimer', name: 'app_gestionnaire_delete_folder', methods: ['POST'])]
    public function deleteFolder($id, Request $request, ImageFormatService $imageFormatService, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() && !$user->getMasterSalt()) {
             return $this->redirectToRoute('app_logout');
        }

        $deleteFolder = $this->fr->findOneBy([
            'id' => $id,
        ]);

        $findPasswords = $this->apr->findBy(['folder' => $deleteFolder, 'user' => $user]);

        if (!$deleteFolder) {
            throw new \Exception("Ce dossier n'existe pas");
        }

        if ($request->isMethod('POST')) {

            $key = base64_decode($request->getSession()->get('vault_key'));

            if (!$key) {
                throw new \Exception('Vault locked');
            }

            foreach ($findPasswords as $password) {
                if ($password->getUser() == $user) {
                    // $this->em->remove($password);
                    $password->setFolder(null);
                } else {
                    throw new \Exception("Ce dossier ne correspond pas à l'utilisateur connecter ou bien le dossier n'existe pas");
                }
            }
            $this->em->flush();
            $this->em->remove($deleteFolder);
            $this->em->flush();
            return $this->redirectToRoute('app_gestionnaire');
        }



        return $this->redirectToRoute('app_gestionnaire');
    }
}
