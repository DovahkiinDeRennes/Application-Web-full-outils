<?php

namespace App\Controller\ApiExtension;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AllPasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PasswordApiController extends AbstractController
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

    #[Route('/api/password', name: 'api_password', methods: ['GET'])]
    public function getPassword(Request $request): JsonResponse
    {
        $token = $request->headers->get('X-API-TOKEN');
        $SECRET_API_TOKEN = 'test';

        if ($token !== $SECRET_API_TOKEN) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $url = $request->query->get('url');
        $identifier = $request->query->get('identifier');

        if (!$url || !$identifier) {
            return new JsonResponse(['error' => 'Missing parameters'], 400);
        }
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $session = $request->getSession();
        $key = base64_decode($session->get('vault_key'));
        if (!$key) {
            return new JsonResponse(['error' => 'Vault locked'], 403);
        }

        $entry = $this->apr->findOneBy([
            'user' => $user->getId(),
            'url' => $url,
            'identifier' => $identifier
        ]);

        if (!$entry) {
            return new JsonResponse(['error' => 'Password not found'], 404);
        }

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
            return new JsonResponse(['error' => 'Decryption failed'], 500);
        }

        $origin = $request->headers->get('Origin');
        if ($origin && strpos($origin, 'chrome-extension://') !== 0) {
            return new JsonResponse(['error' => 'Invalid origin'], 403);
        }

        return new JsonResponse([
            'url' => $entry->getUrl(),
            'identifier' => $entry->getIdentifier(),
            'password' => $plaintext
        ]);
    }
}
