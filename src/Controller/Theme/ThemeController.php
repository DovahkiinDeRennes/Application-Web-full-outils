<?php

namespace App\Controller\Theme;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ImageFormatService;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ThemeController extends AbstractController
{
    private const ALLOWED_THEMES = ['dark-cyan', 'dark-orange', 'dark-yellow', 'day', 'green', 'night', 'purple', 'default'];

    #[Route('/theme', name: 'switch_theme', methods: ['GET'])]
    public function switchTheme(Request $request): RedirectResponse
    {
        $name = $request->query->get('theme');
        $response = $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_home'));

        if (in_array($name, self::ALLOWED_THEMES, true)) {
            $response->headers->setCookie(
                Cookie::create('theme', $name)
                    ->withExpires(strtotime('+1 year'))
                    ->withPath('/')
                    ->withSameSite('lax')
            );
        }

        return $response;
    }
}
