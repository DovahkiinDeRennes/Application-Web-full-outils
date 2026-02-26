<?php
// src/EventListener/VaultTimeoutListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VaultTimeoutListener
{
    private int $timeout; // en secondes

    public function __construct(int $timeout = 600)
    {
        $this->timeout = $timeout; // ici 10 minutes
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if (!$session->has('vault_key')) {
            return; // coffre déjà verrouillé
        }

        $lastActivity = $session->get('vault_last_activity', time());

        if (time() - $lastActivity > $this->timeout) {
            // Timeout atteint → verrouillage automatique
            $session->remove('vault_key');
        }

        // Mettre à jour le timestamp
        $session->set('vault_last_activity', time());
    }
}