<?php

namespace App\Controller\Folder\add_update_delete;

use App\Entity\Folder;
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

final class AddFolderController extends AbstractController
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
    }

    #[Route('/gestionnaire/dossier/nouveau', name: 'app_gestionnaire_add_folder')]
    public function addNewFolder(Request $request, ImageFormatService $imageFormatService, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() && !$user->getMasterSalt()) {
            return $this->redirectToRoute('app_logout');
        }

        if ($request->isMethod('POST')) {

            $name = $request->request->get('name');
            $ref = $request->request->get('ref');

            $key = base64_decode($request->getSession()->get('vault_key'));

            if (!$key) {
                throw new \Exception('Vault locked');
            }

            $addNewFolder = new Folder;

            $addNewFolder->setName($name);
            $addNewFolder->setRef($ref);
            $addNewFolder->setUser($user);

            $this->em->persist($addNewFolder);
            $this->em->flush();
            return $this->redirectToRoute('app_gestionnaire');
        }



        return $this->render('folder/add_update_delete/folder_add.html.twig', []);
    }
}
