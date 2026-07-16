<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Routing\HostContextHolder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function index(HostContextHolder $holder): Response
    {
        $context = $holder->get();

        return $this->json([
            'app' => 'WebHemi.PHP',
            'surface' => $context->getSurface()->value,
            'site' => $context->getSite()?->getSlug(),
            'host' => $context->getSiteHost()?->getHost(),
            'resolved' => $context->isResolved(),
        ]);
    }
}
