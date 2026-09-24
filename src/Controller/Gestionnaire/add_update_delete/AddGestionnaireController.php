<?php

namespace App\Controller\Gestionnaire\add_update_delete;

use App\Entity\AllPassword;
use App\Repository\AllPasswordRepository;
use App\Repository\UserRepository;
use App\Repository\FolderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ImageFormatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class AddGestionnaireController extends AbstractController
{
    private UserRepository $ur;
    private AllPasswordRepository $apr;
    private EntityManagerInterface $em;
    private FolderRepository $fr;

    public function __construct(UserRepository $ur, AllPasswordRepository $apr, FolderRepository $fr, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->apr = $apr;
        $this->em = $em;
        $this->fr = $fr;
    }
    
    #[Route('/gestionnaire/mot-de-passe/nouveau', name: 'app_gestionnaire_add')]
    #[Route('/gestionnaire/mot-de-passe/{id}/nouveau', name: 'app_gestionnaire_add_mdp_folder')]
    public function addNewPassword(?int $id, Request $request, ImageFormatService $imageFormatService, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() && !$user->getMasterSalt()) {
             return $this->redirectToRoute('app_logout');
        }

        $folders = $this->fr->findBy(['user' => $user]);
        $findFolder = null;
      
        if (!empty($id)) {
            try {
                $findFolder = $this->fr->findOneBy([
                    'id' => $id
                ]);
            } catch (\Throwable $e) {
            }
        }
     
        if ($request->isMethod('POST')) {

            $url = $request->request->get('url');
            $identifier = $request->request->get('identifier');
            $site = $request->request->get('site');
            $password = $request->request->get('password');
            $idFolder = $request->request->get('folder');

            $key = base64_decode($request->getSession()->get('vault_key'));

            if (!$key) {
                throw new \Exception('Vault locked');
            }

            $nonce = random_bytes(12);

            $ciphertext = openssl_encrypt(
                $password,
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag
            );



            $findUser =  $this->ur->findOneBy(['email' => $user->getEmail()]);
            $addNewPasswordList = new AllPassword;

            $addNewPasswordList->setUrl($url);
            $addNewPasswordList->setSite($site);
            $addNewPasswordList->setIdentifier($identifier);
            $addNewPasswordList->setUser($findUser);


            $addNewPasswordList->setPassword(base64_encode($ciphertext));
            $addNewPasswordList->setNonce(base64_encode($nonce));
            $addNewPasswordList->setTag(base64_encode($tag));

            if (!empty($idFolder)) {
                try {
                    $findFolder = $this->fr->findOneBy([
                        'id' => $idFolder
                    ]);

                    if ($findFolder !== null) {
                        $addNewPasswordList->setFolder($findFolder);
                    }
                } catch (\Throwable $e) {
                }
            } else if (!empty($id)) {
                try {
                    $findFolder = $this->fr->findOneBy([
                        'id' => $id
                    ]);

                    if ($findFolder !== null) {
                        $addNewPasswordList->setFolder($findFolder);
                    }
                } catch (\Throwable $e) {
                }
            } else {
                // Aucun dossier associer
            }

     

            $this->em->persist($addNewPasswordList);
            $this->em->flush();
            if (!empty($id)) {
  
                return $this->redirectToRoute('app_gestionnaire_folder', [
                    'id' => $id
                ]);
            } else {
                return $this->redirectToRoute('app_gestionnaire');
            }
        }



        return $this->render('gestionnaire/add_update_delete/gestionnaire_add.html.twig', ["folders" => $folders, "folderUrl" => $findFolder, "id" => $id]);
    }
    
}
