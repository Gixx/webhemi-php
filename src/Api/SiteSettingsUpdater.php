<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SiteSettingsUpdater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MediaAssetRepository $media,
    ) {
    }

    public function update(Site $site, UpdateSiteSettingsInput $input): Site
    {
        if ($input->nameProvided && null !== $input->name) {
            $site->setName($input->name);
        }

        if ($input->descriptionProvided) {
            $site->setDescription($input->description);
        }

        if ($input->faviconMediaIdProvided) {
            if (null === $input->faviconMediaId) {
                $site->setFaviconMedia(null);
            } else {
                $asset = $this->media->find($input->faviconMediaId);
                if (
                    !$asset instanceof MediaAsset
                    || $asset->getSite()?->getId() !== $site->getId()
                    || $asset->isDeleted()
                ) {
                    throw new SiteSettingsInvalidFaviconException(
                        'Favicon must be a live media asset on this site.',
                    );
                }
                $site->setFaviconMedia($asset);
            }
        }

        $this->em->flush();

        return $site;
    }
}
