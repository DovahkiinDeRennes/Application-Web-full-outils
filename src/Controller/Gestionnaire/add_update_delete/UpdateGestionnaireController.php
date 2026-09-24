<?php

namespace App\Controller\Gestionnaire\add_update_delete;

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

final class UpdateGestionnaireController extends AbstractController
{
    private UserRepository $ur;
    private AllPasswordRepository $apr;
    private EntityManagerInterface $em;
    private FolderRepository $fr;

    public function __construct(UserRepository $ur, AllPasswordRepository $apr, EntityManagerInterface $em, FolderRepository $fr,)
    {
        $this->ur = $ur;
        $this->apr = $apr;
        $this->em = $em;
        $this->fr = $fr;
    }
    #[Route('/gestionnaire/mot-de-passe/{uuid}/modifier', name: 'app_gestionnaire_update', methods: ['GET', 'POST'])]
    public function updateNewPassword(
        $uuid,
        Request $request,
        ImageFormatService $imageFormatService
    ): Response {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() || !$user->getMasterSalt()) {
             return $this->redirectToRoute('app_logout');
        }

        $updatePassword = $this->apr->findOneBy([
            'uuid' => $uuid,
            'user' => $user
        ]);

        if (!$updatePassword) {
            throw $this->createAccessDeniedException(
                "Ce mot de passe ne vous appartient pas."
            );
        }
        $folders = $this->fr->findBy(['user' => $user]);

        $id = $request->query->get('id');
         
        if (!empty($id)) {
            try {
                $findFolder = $this->fr->findOneBy([
                    'id' => $id
                ]);
            } catch (\Throwable $e) {
            }
        }
     
        if ($request->isMethod('POST')) {

            if (!$this->isCsrfTokenValid('update' . $uuid, $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }

            $key = base64_decode($request->getSession()->get('vault_key'));

            if (!$key) {
                throw new \Exception('Vault locked');
            }

            $nonce = random_bytes(12);

            $ciphertext = openssl_encrypt(
                $request->request->get('password'),
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag
            );

            if ($ciphertext === false) {
                throw new \Exception('Encryption failed');
            }

            $updatePassword->setUrl($request->request->get('url'));
            $updatePassword->setSite($request->request->get('site'));
            $updatePassword->setIdentifier($request->request->get('identifier'));
            $updatePassword->setPassword(base64_encode($ciphertext));
            $updatePassword->setNonce(base64_encode($nonce));
            $updatePassword->setTag(base64_encode($tag));
            $idFolder = $request->request->get('folder');

            if (!empty($idFolder)) {
                try {
                    $findFolder = $this->fr->findOneBy([
                        'id' => $idFolder
                    ]);

                    if ($findFolder !== null) {
                        $updatePassword->setFolder($findFolder);
                    }
                } catch (\Throwable $e) {
                }
            }

            $this->em->flush();
 
             if (!empty($id)) {
  
                return $this->redirectToRoute('app_gestionnaire_folder', [
                    'id' => $id
                ]);
            } else {
                return $this->redirectToRoute('app_gestionnaire');
            }
        }

        return $this->render(
            'gestionnaire/add_update_delete/gestionnaire_update.html.twig',
            [
                'passwordEntry' => $updatePassword,
                'folders' => $folders,
                'id' => $id
            ]
        );
    }
}
