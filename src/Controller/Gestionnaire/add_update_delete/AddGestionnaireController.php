<?php

namespace App\Controller\Gestionnaire\add_update_delete;

use App\Entity\AllPassword;
use App\Repository\AllPasswordRepository;
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

final class AddGestionnaireController extends AbstractController
{
    private UserRepository $ur;
    private AllPasswordRepository $apr;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $ur, AllPasswordRepository $apr, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->apr = $apr;
        $this->em = $em;
    }

    #[Route('/gestionnaire/add-password-list', name: 'app_gestionnaire_add')]
    public function addNewPassword(Request $request, ImageFormatService $imageFormatService, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() && !$user->getMasterSalt()) {
            return $this->redirectToRoute('app_gestionnaire_add_master_key');
        }

        if ($request->isMethod('POST')) {

            $url = $request->request->get('url');
            $identifier = $request->request->get('identifier');
            $site = $request->request->get('site');
            $password = $request->request->get('password');

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


            $this->em->persist($addNewPasswordList);
            $this->em->flush();
            return $this->redirectToRoute('app_gestionnaire');
        }



        return $this->render('gestionnaire/add_update_delete/gestionnaire_add.html.twig', []);
    }
}
