<?php

namespace App\Controller\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
  #[Route(path: '/login', name: 'app_login')]
public function login(
    AuthenticationUtils $authenticationUtils,
    UserRepository $userRepository
): Response {
    // Erreur de connexion éventuelle
    $error = $authenticationUtils->getLastAuthenticationError();

    // Dernier username saisi
    $lastUsername = $authenticationUtils->getLastUsername();

    // Utilisateurs disponibles pour le sélecteur
    $users = $userRepository->findAll();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
        'users' => $users,
    ]);
}


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Le code ici ne sera jamais exécuté, Symfony s'occupe du logout automatiquement
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}