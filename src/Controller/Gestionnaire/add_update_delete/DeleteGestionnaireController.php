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

final class DeleteGestionnaireController extends AbstractController
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

    #[Route('/gestionnaire/delete-password-list/{uuid}', name: 'app_gestionnaire_delete', methods: ['POST'])]
    public function deleteNewPassword($uuid,Request $request, ImageFormatService $imageFormatService, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getMasterKeyHash() && !$user->getMasterSalt()) {
            return $this->redirectToRoute('app_gestionnaire_add_master_key');
        }
     

        $deletePassword = $this->apr->findOneBy([
            'uuid' => $uuid,
            'user' => $user
        ]);
        
        if (!$deletePassword) {
            throw new \Exception("L'utilisateur connecté n'a pas ce password associé");
        }

        if ($request->isMethod('POST')) {

            $key = base64_decode($request->getSession()->get('vault_key'));

            if (!$key) {
                throw new \Exception('Vault locked');
            }

    
            $this->em->remove($deletePassword);
            $this->em->flush();
            return $this->redirectToRoute('app_gestionnaire');
        }



        return $this->redirectToRoute('app_gestionnaire');
    }
}
