<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Entity\SiteHost;
use App\Entity\SurfaceType;
use App\Entity\User;
use App\Repository\SiteHostRepository;
use App\Repository\SiteRepository;
use App\SiteHost\Verification\HostOwnershipVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function dashboard(SiteRepository $sites, SiteHostRepository $hosts): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'siteCount' => $sites->count([]),
            'hostCount' => $hosts->count([]),
            'userLabel' => $this->getUserLabel(),
            'navItems' => $this->navItems('dashboard'),
        ]);
    }

    #[Route('/sites', name: 'admin_sites', methods: ['GET', 'POST'])]
    #[IsGranted('site.list')]
    public function sites(Request $request, SiteRepository $sites, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST') && $this->isGranted('site.edit')) {
            $name = trim((string) $request->request->get('name'));
            $slug = trim((string) $request->request->get('slug'));
            if ('' !== $name && '' !== $slug) {
                $site = (new Site())->setName($name)->setSlug($slug)->setIsEnabled(true);
                $em->persist($site);
                $em->flush();
                $this->addFlash('success', sprintf('Site "%s" created.', $name));

                return $this->redirectToRoute('admin_sites');
            }
            $this->addFlash('danger', 'Name and slug are required.');
        }

        $rows = array_map(static function (Site $site): array {
            return [
                'id' => $site->getId(),
                'slug' => $site->getSlug(),
                'name' => $site->getName(),
                'enabled' => $site->isEnabled(),
                'hostCount' => $site->getHosts()->count(),
            ];
        }, $sites->findBy([], ['name' => 'ASC']));

        return $this->render('admin/sites.html.twig', [
            'sites' => $rows,
            'userLabel' => $this->getUserLabel(),
            'navItems' => $this->navItems('sites'),
            'canEdit' => $this->isGranted('site.edit'),
        ]);
    }

    #[Route('/hosts', name: 'admin_hosts', methods: ['GET', 'POST'])]
    #[IsGranted('host.list')]
    public function hosts(
        Request $request,
        SiteHostRepository $hosts,
        SiteRepository $sites,
        EntityManagerInterface $em,
        HostOwnershipVerifier $verifier,
    ): Response {
        if ($request->isMethod('POST') && $this->isGranted('host.edit')) {
            $hostname = strtolower(trim((string) $request->request->get('host')));
            $siteId = (int) $request->request->get('site_id');
            $surface = (string) $request->request->get('surface', 'site');
            $site = $sites->find($siteId);

            $hostnamePattern = '/^(?=.{1,191}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)'
                . '(\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/';
            if ($site instanceof Site && preg_match($hostnamePattern, $hostname)) {
                $host = (new SiteHost())
                    ->setHost($hostname)
                    ->setSite($site)
                    ->setSurface(SurfaceType::from($surface))
                    ->setIsActive(true)
                    ->setStatus('pending');

                if (SurfaceType::Site === $host->getSurface()) {
                    $result = $verifier->verify($hostname);
                    $host->setStatus($result->verified ? 'verified' : 'pending');
                } else {
                    $host->setStatus('active');
                }

                $em->persist($host);
                $em->flush();
                $this->addFlash('success', sprintf('Host %s saved (%s).', $hostname, $host->getStatus()));

                return $this->redirectToRoute('admin_hosts');
            }
            $this->addFlash('danger', 'Invalid host or site.');
        }

        $rows = array_map(static function (SiteHost $host): array {
            return [
                'id' => $host->getId(),
                'host' => $host->getHost(),
                'siteName' => $host->getSite()->getName(),
                'surface' => $host->getSurface()->value,
                'status' => $host->getStatus(),
                'active' => $host->isActive(),
            ];
        }, $hosts->findBy([], ['host' => 'ASC']));

        $siteOptions = array_map(static function (Site $site): array {
            return [
                'id' => $site->getId(),
                'name' => $site->getName(),
            ];
        }, $sites->findBy([], ['name' => 'ASC']));

        return $this->render('admin/hosts.html.twig', [
            'hosts' => $rows,
            'sites' => $siteOptions,
            'userLabel' => $this->getUserLabel(),
            'navItems' => $this->navItems('hosts'),
            'canEdit' => $this->isGranted('host.edit'),
        ]);
    }

    #[Route('/hosts/{id}/verify', name: 'admin_hosts_verify', methods: ['POST'])]
    #[IsGranted('host.verify')]
    public function verifyHost(SiteHost $host, HostOwnershipVerifier $verifier, EntityManagerInterface $em): Response
    {
        $result = $verifier->verify($host->getHost());
        $host->setStatus($result->verified ? 'verified' : 'pending');
        $em->flush();
        $this->addFlash(
            $result->verified ? 'success' : 'warning',
            $result->verified ? 'Host verified.' : 'Verification failed; left pending.',
        );

        return $this->redirectToRoute('admin_hosts');
    }

    /**
     * @return list<array{id: string, label: string, href: string, icon: string, active: bool}>
     */
    private function navItems(string $active): array
    {
        return [
            [
                'id' => 'dashboard',
                'label' => 'Dashboard',
                'href' => $this->generateUrl('admin_dashboard'),
                'icon' => 'dashboard',
                'active' => 'dashboard' === $active,
            ],
            [
                'id' => 'sites',
                'label' => 'Sites',
                'href' => $this->generateUrl('admin_sites'),
                'icon' => 'sites',
                'active' => 'sites' === $active,
            ],
            [
                'id' => 'hosts',
                'label' => 'Hosts',
                'href' => $this->generateUrl('admin_hosts'),
                'icon' => 'hosts',
                'active' => 'hosts' === $active,
            ],
        ];
    }

    private function getUserLabel(): string
    {
        $user = $this->getUser();

        return $user instanceof User ? $user->getEmail() : 'guest';
    }
}
