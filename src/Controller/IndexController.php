<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ImageFormatService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class IndexController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(Request $request, ImageFormatService $imageFormatService): Response
    {
        if ($request->isMethod('POST')) {

            $file = $request->files->get('file');

            $format = $request->request->get('format');

            $name = $request->request->get('name');

            if ($format === "pdf") {

                $extension = $file->guessExtension(); 

                if (!$extension) {
                    $extension = 'jpg'; 
                }

                $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
                $file->move(sys_get_temp_dir(), basename($tempPath));

                $filenamepdf = $imageFormatService->imageToPdf(
                    $tempPath,
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $name
                );

                unlink($tempPath);
            } else {
                $filename = $imageFormatService->convert(
                    $name,
                    $file,
                    $format,
                    $this->getParameter('kernel.project_dir') . '/public/uploads'
                );
            }
        }

        $dir = $this->getParameter('kernel.project_dir') . '/public/uploads';

        $filesLocals = scandir($dir);

        $filesLocals = array_diff($filesLocals, ['.', '..']);

        return $this->render('/index.html.twig', [
            'files' => $filesLocals,
        ]);
    }


    #[Route('/test/uploads', name: 'app_test_uploads', methods: ['POST'])]
    public function fileUploads(Request $request): BinaryFileResponse
    {
        $filename = $request->request->get('name');

        if ($filename) {
            $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

            if (!file_exists($filePath)) {
                throw $this->createNotFoundException("Le fichier n'existe pas !");
            }

            $response = new BinaryFileResponse($filePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            return $response;
        }

        return $this->redirectToRoute('app_test');
    }
}
