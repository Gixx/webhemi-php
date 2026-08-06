<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final class SiteUpdater
{
    public function __construct(
        private readonly SiteRepository $sites,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws SiteSlugTakenException
     */
    public function update(Site $site, UpdateSiteInput $input): Site
    {
        if (!$input->isValid()) {
            throw new \InvalidArgumentException('UpdateSiteInput must be valid before update().');
        }

        if ($input->nameProvided && null !== $input->name) {
            $site->setName($input->name);
        }

        if ($input->slugProvided && null !== $input->slug) {
            $other = $this->sites->findOneBy(['slug' => $input->slug]);
            if ($other instanceof Site && $other->getId() !== $site->getId()) {
                throw new SiteSlugTakenException();
            }
            $site->setSlug($input->slug);
        }

        if ($input->enabledProvided && null !== $input->enabled) {
            $site->setIsEnabled($input->enabled);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new SiteSlugTakenException(previous: $e);
        }

        return $site;
    }
}
