<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class SiteCreator
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws SiteSlugTakenException
     */
    public function create(CreateSiteInput $input): Site
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('CreateSiteInput must be valid before create().');
        }

        if ($this->sites->findOneBy(['slug' => $input->slug]) instanceof Site) {
            throw new SiteSlugTakenException();
        }

        $site = (new Site())
            ->setName($input->name)
            ->setSlug($input->slug)
            ->setIsEnabled($input->enabled);

        $this->em->persist($site);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new SiteSlugTakenException(previous: $e);
        }

        return $site;
    }
}
