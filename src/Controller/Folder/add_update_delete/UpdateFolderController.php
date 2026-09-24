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

final class UpdateFolderController extends AbstractController
{
    private UserRepository $ur;
    private FolderRepository $fr;
    private AllPasswordRepository $apr;
    private EntityManagerInterface $em;

    public function __construct(FolderRepository $fr, UserRepository $ur, AllPasswordRepository $apr, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->apr = $apr;
        $this->em = $em;
        $this->fr = $fr;
    }

    #[Route('/gestionnaire/dossier/{id}/modifier', name: 'app_gestionnaire_update_folder', methods: ['GET','POST'])]
    public function updateNewPassword(
        $id,
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

        $name = $request->request->get('name');
        $ref = $request->request->get('ref');
        
        
        $updateFolder = $this->fr->findOneBy([
            'id' => $id,
        ]);

        $findPasswords = $this->apr->findBy(['folder' => $updateFolder, 'user' => $user]);

         foreach ($findPasswords as $password) {
                if ($password->getUser() == $user) {

                } else {
                    throw new \Exception("Ce dossier ne correspond pas à l'utilisateur connecter ou bien le dossier n'existe pas");
                }
            }
   
    
        if ($request->isMethod('POST')) {
    
        
    
            $key = base64_decode($request->getSession()->get('vault_key'));
    
            if (!$key) {
                throw new \Exception('Vault locked');
            }
    
       
    
            $updateFolder->setName($name);
            $updateFolder->setRef($ref); 

            $this->em->flush();
    
            return $this->redirectToRoute('app_gestionnaire');
        }
    
        return $this->render(
            'folder/add_update_delete/folder_update.html.twig',
            [
                'folderEntry' => $updateFolder
            ]
        );
    }
   
}
