<?php

declare(strict_types=1);

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Frontend (site) login — separate security context from admin.
 */
final class FrontendLoginController extends AbstractController
{
    #[Route('/login', name: 'frontend_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('site_home');
        }

        return $this->render('site/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()?->getMessageKey(),
        ]);
    }

    #[Route('/logout', name: 'frontend_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('Logout is handled by the frontend firewall.');
    }
}
