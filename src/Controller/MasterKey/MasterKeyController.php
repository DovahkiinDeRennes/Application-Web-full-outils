<?php

namespace App\Controller\MasterKey;

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

final class MasterKeyController extends AbstractController
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



    #[Route('/gestionnaire/add-master-key', name: 'app_gestionnaire_add_master_key')]
    public function addMasterKey(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();

        if (!$user->getMasterKeyHash()) {

            if ($request->isMethod('POST')) {

                $masterkey = $request->request->get('masterkey');

                if (empty($masterkey)) {
                    throw new \Exception('Master key vide');
                }

                $hash = password_hash($masterkey, PASSWORD_ARGON2ID);

                $salt = bin2hex(random_bytes(16));

                $user->setMasterKeyHash($hash);
                $user->setMasterSalt($salt);

                $em->persist($user);
                $em->flush();

                $key = hash_pbkdf2(
                    'sha256',
                    $masterkey,
                    hex2bin($salt),
                    200000,
                    32,
                    true
                );

                $session->set('vault_key', base64_encode($key));

                return $this->redirectToRoute('app_gestionnaire');
            }
        }

        return $this->render(
            'gestionnaire/masterkey/gestionnaire_unlock_login.html.twig',
            ['user' => $user]
        );
    }

    
    #[Route('/gestionnaire/login-master-key', name: 'app_gestionnaire_login_master_key')]
    public function loginMasterKey(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();

        if ($request->isMethod('POST')) {

            $masterkey = $request->request->get('masterkey');

            if (!password_verify($masterkey, $user->getMasterKeyHash())) {
                throw new \Exception('Master key incorrecte');
            }

            $key = hash_pbkdf2(
                'sha256',
                $masterkey,
                hex2bin($user->getMasterSalt()),
                200000,
                32,
                true
            );

            $session->set('vault_key', base64_encode($key));

            return $this->redirectToRoute('app_gestionnaire');
        }

        return $this->render(
            'gestionnaire/masterkey/gestionnaire_unlock_login.html.twig',
            ['user' => $user]
        );
    }
}
