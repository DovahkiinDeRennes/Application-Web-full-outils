<?php

namespace App\Controller\Gestionnaire;

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

final class GestionnaireController extends AbstractController
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

    #[Route('/gestionnaire/mes-mots-de-passes', name: 'app_gestionnaire')]
    public function list(Request $request, ImageFormatService $imageFormatService): Response
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
                'check' => $check,
                'user' => $user   
            ]);
        }

        $query = $request->query->get('query');

        if ($query) {
            $datasUser = $this->apr->searchByQuery($query, $user);
        } else {
            $datasUser = $this->apr->findBy(['user' => $user]);
        }

        $decryptedPasswords = [];
        foreach ($datasUser as $entry) {
            $ciphertext = base64_decode($entry->getPassword());
            $nonce = base64_decode($entry->getNonce());
            $tag = base64_decode($entry->getTag());

            $plaintext = openssl_decrypt(
                $ciphertext,
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag
            );

            if ($plaintext === false) {
                throw new \Exception('Erreur de déchiffrement');
            }

            $decryptedPasswords[] = [
                'uuid' => $entry->getUuid(),
                'site' => $entry->getSite(),
                'url' => $entry->getUrl(),
                'identifier' => $entry->getIdentifier(),
                'password' => $plaintext,
                'folder' => $entry->getFolder()
            ];
        }
        $folders = [];


        $folders = $this->fr->findBy(['user' => $user]);

        $hasPasswordWithoutFolder = false;

        foreach ($decryptedPasswords as $data) {
            if ($data['folder'] === null) {
                $hasPasswordWithoutFolder = true;
                break;
            }
        }

        return $this->render('gestionnaire/gestionnaire.html.twig', [
            'datas' => $decryptedPasswords,
            'folders' => $folders,
            'user' => $user,
            'check' => $check,
            'masterKeyHash' => $user->getMasterKeyHash(),
            'hasPasswordWithoutFolder' => $hasPasswordWithoutFolder,
         
        ]);
    }
}
