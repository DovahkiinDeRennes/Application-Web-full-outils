<?php

namespace App\Controller\Folder;

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

final class FolderController extends AbstractController
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

    #[Route('/gestionnaire/dossier/{id}/mes-mot-de-passes', name: 'app_gestionnaire_folder')]
    public function dossier($id, Request $request, ImageFormatService $imageFormatService): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();

        $key = base64_decode($session->get('vault_key'));

        $check = true;

        if (!$key) {
            $check = false;
            return $this->render('gestionnaire/gestionnaire.html.twig', [
                'check' => $check
            ]);
        }

        $query = $request->query->get('query');

        if ($query) {
            $datasUser = $this->apr->searchByQuery($query, $user);
        } else {
            $datasUser = $this->apr->findBy(['user' => $user]);
        }

        $folder = $this->fr->findOneBy(['id' => $id]);

        $passwords = $this->apr->findBy([
            'folder' => $folder,
            'user' => $user,
        ]);

        $folders = $this->fr->findAll();

        $folderWithoutId = [];

        foreach ($folders as $checkFolderId) {

            if ($checkFolderId->getId() != $id) {
                $folderWithoutId[] = $checkFolderId;
            }
        }






        return $this->render('folder/folder.html.twig', [
            'datas' => $passwords,
                'folders' => $folderWithoutId,
            'check' => $check,
            'id' => $id,
            'user' => $user
        ]);
    }
}
