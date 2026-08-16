<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiJson;
use App\Api\ContentNodeApiMapper;
use App\Api\ContentNodeCreator;
use App\Api\ContentNodeInvalidBodyException;
use App\Api\ContentNodeInvalidParentException;
use App\Api\ContentNodePurger;
use App\Api\ContentNodeRestoreConflictException;
use App\Api\ContentNodeRestorer;
use App\Api\ContentNodeSlugTakenException;
use App\Api\ContentNodeSoftDeleter;
use App\Api\ContentNodeUpdater;
use App\Api\ContentReservedSlugException;
use App\Api\CreateContentNodeInput;
use App\Api\ExplorerForestMapper;
use App\Api\MediaAssetApiMapper;
use App\Api\MediaAssetCreator;
use App\Api\MediaAssetInUseException;
use App\Api\MediaAssetInvalidFolderException;
use App\Api\MediaAssetPurger;
use App\Api\MediaAssetRestorer;
use App\Api\MediaAssetSoftDeleter;
use App\Api\MediaBlobStore;
use App\Api\UpdateContentNodeInput;
use App\Entity\ContentNode;
use App\Entity\ContentTree;
use App\Entity\MediaAsset;
use App\Entity\Site;
use App\Entity\User;
use App\Repository\ContentNodeRepository;
use App\Repository\MediaAssetRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[Route('/admin/api/sites/{siteId}', requirements: ['siteId' => '\d+'])]
final class ContentAdminApiController extends AbstractController
{
    #[Route('/nodes', name: 'api_admin_content_nodes', methods: ['GET'])]
    public function listNodes(
        #[MapEntity(id: 'siteId')] Site $site,
        Request $request,
        ContentNodeRepository $nodes,
    ): JsonResponse {
        $this->requireSitePermission('content.list', $site);
        $treeRaw = strtolower((string) $request->query->get('tree', ContentTree::Site->value));
        $tree = ContentTree::tryFrom($treeRaw);
        if (!$tree instanceof ContentTree) {
            return ApiJson::error('validation_failed', 'Invalid tree.', 422, [
                'tree' => 'Tree must be site or media.',
            ]);
        }

        $parent = null;
        if ($request->query->has('parentId') && '' !== (string) $request->query->get('parentId')) {
            $parentId = (int) $request->query->get('parentId');
            $parent = $nodes->find($parentId);
            if (
                !$parent instanceof ContentNode
                || $parent->getSite()?->getId() !== $site->getId()
                || $parent->isDeleted()
            ) {
                throw new NotFoundHttpException('Parent not found.');
            }
        }

        $data = array_map(
            static fn (ContentNode $node): array => ContentNodeApiMapper::toArray($node),
            $nodes->findLiveChildren($site, $tree, $parent),
        );

        return ApiJson::data($data);
    }

    #[Route('/nodes/{id}', name: 'api_admin_content_nodes_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showNode(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] ContentNode $node,
    ): JsonResponse {
        $this->requireSitePermission('content.list', $site);
        $this->assertNodeSite($site, $node);

        return ApiJson::data(ContentNodeApiMapper::toArray($node));
    }

    #[Route('/nodes', name: 'api_admin_content_nodes_create', methods: ['POST'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createNode(
        #[MapEntity(id: 'siteId')] Site $site,
        Request $request,
        ContentNodeCreator $creator,
    ): JsonResponse {
        $this->requireSitePermission('content.edit', $site);
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = CreateContentNodeInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error('validation_failed', 'Node could not be created.', 422, $input->fieldErrors);
        }

        try {
            $node = $creator->create($site, $input);
        } catch (ContentNodeSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A node with this slug already exists under the same parent.',
                409,
                [
                'slug' => 'Slug is already taken.',
                ]
            );
        } catch (ContentReservedSlugException $e) {
            return ApiJson::error('reserved_slug', $e->getMessage(), 409, [
                'slug' => $e->getMessage(),
            ]);
        } catch (ContentNodeInvalidParentException $e) {
            return ApiJson::error('invalid_parent', $e->getMessage(), 422);
        }

        return ApiJson::data(ContentNodeApiMapper::toArray($node), 201);
    }

    #[Route('/nodes/{id}', name: 'api_admin_content_nodes_update', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function updateNode(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] ContentNode $node,
        Request $request,
        ContentNodeUpdater $updater,
    ): JsonResponse {
        $this->requireSitePermission('content.edit', $site);
        $this->assertNodeSite($site, $node);

        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ApiJson::error('invalid_json', 'Request body must be valid JSON.', 400);
        }

        $input = UpdateContentNodeInput::fromPayload($payload);
        if (!$input->isValid()) {
            return ApiJson::error('validation_failed', 'Node could not be updated.', 422, $input->fieldErrors);
        }

        try {
            $updated = $updater->update($node, $input);
        } catch (ContentNodeSlugTakenException) {
            return ApiJson::error(
                'slug_taken',
                'A node with this slug already exists under the same parent.',
                409,
                [
                'slug' => 'Slug is already taken.',
                ]
            );
        } catch (ContentReservedSlugException $e) {
            return ApiJson::error('reserved_slug', $e->getMessage(), 409, [
                'slug' => $e->getMessage(),
            ]);
        } catch (ContentNodeInvalidParentException $e) {
            return ApiJson::error('invalid_parent', $e->getMessage(), 422);
        } catch (ContentNodeInvalidBodyException $e) {
            return ApiJson::error('validation_failed', $e->getMessage(), 422, [
                'body' => $e->getMessage(),
            ]);
        }

        return ApiJson::data(ContentNodeApiMapper::toArray($updated));
    }

    #[Route('/nodes/{id}', name: 'api_admin_content_nodes_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteNode(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] ContentNode $node,
        ContentNodeSoftDeleter $deleter,
    ): JsonResponse {
        $this->requireSitePermission('content.delete', $site);
        $this->assertNodeSite($site, $node);
        $user = $this->getUser();
        $deleter->softDelete($node, $user instanceof User ? $user : null);

        return ApiJson::data(ContentNodeApiMapper::toArray($node));
    }

    #[Route(
        '/nodes/{id}/restore',
        name: 'api_admin_content_nodes_restore',
        requirements: ['id' => '\d+'],
        methods: ['POST'],
    )]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function restoreNode(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] ContentNode $node,
        ContentNodeRestorer $restorer,
    ): JsonResponse {
        $this->requireSitePermission('content.edit', $site);
        $this->assertNodeSite($site, $node);

        try {
            $restored = $restorer->restore($node);
        } catch (ContentNodeRestoreConflictException $e) {
            return ApiJson::error('restore_conflict', $e->getMessage(), 409);
        } catch (ContentNodeInvalidParentException $e) {
            return ApiJson::error('invalid_parent', $e->getMessage(), 422);
        }

        return ApiJson::data(ContentNodeApiMapper::toArray($restored));
    }

    #[Route(
        '/nodes/{id}/purge',
        name: 'api_admin_content_nodes_purge',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'],
    )]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function purgeNode(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] ContentNode $node,
        ContentNodePurger $purger,
    ): JsonResponse {
        $this->requireSitePermission('content.delete', $site);
        $this->assertNodeSite($site, $node);

        try {
            $purger->purge($node);
        } catch (ContentNodeInvalidParentException $e) {
            return ApiJson::error('not_deleted', $e->getMessage(), 422);
        }

        return ApiJson::data(['id' => (int) $node->getId(), 'purged' => true]);
    }

    #[Route('/explorer', name: 'api_admin_content_explorer', methods: ['GET'])]
    public function explorer(
        #[MapEntity(id: 'siteId')] Site $site,
        ExplorerForestMapper $forest,
    ): JsonResponse {
        $this->requireSitePermission('content.list', $site);
        $this->requireSitePermission('media.list', $site);

        return ApiJson::data($forest->build($site));
    }

    #[Route('/trash', name: 'api_admin_content_trash', methods: ['GET'])]
    public function trash(
        #[MapEntity(id: 'siteId')] Site $site,
        ContentNodeRepository $nodes,
        MediaAssetRepository $media,
    ): JsonResponse {
        $this->requireSitePermission('content.list', $site);
        $this->requireSitePermission('media.list', $site);

        return ApiJson::data([
            'nodes' => array_map(
                static fn (ContentNode $node): array => ContentNodeApiMapper::toArray($node),
                $nodes->findTrash($site),
            ),
            'media' => array_map(
                static fn (MediaAsset $asset): array => MediaAssetApiMapper::toArray($asset),
                $media->findTrash($site),
            ),
        ]);
    }

    #[Route('/media', name: 'api_admin_media_list', methods: ['GET'])]
    public function listMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        Request $request,
        MediaAssetRepository $media,
    ): JsonResponse {
        $this->requireSitePermission('media.list', $site);
        $folderNodeId = null;
        if ($request->query->has('folderNodeId') && '' !== (string) $request->query->get('folderNodeId')) {
            $folderNodeId = (int) $request->query->get('folderNodeId');
        }

        $data = array_map(
            static fn (MediaAsset $asset): array => MediaAssetApiMapper::toArray($asset),
            $media->findLiveInFolder($site, $folderNodeId),
        );

        return ApiJson::data($data);
    }

    #[Route('/media', name: 'api_admin_media_create', methods: ['POST'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function createMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        Request $request,
        MediaAssetCreator $creator,
    ): JsonResponse {
        $this->requireSitePermission('media.edit', $site);
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            return ApiJson::error('validation_failed', 'Multipart file field "file" is required.', 422, [
                'file' => 'File is required.',
            ]);
        }

        $folderNodeId = null;
        if ($request->request->has('folderNodeId') && '' !== (string) $request->request->get('folderNodeId')) {
            $folderNodeId = (int) $request->request->get('folderNodeId');
        }

        try {
            $result = $creator->createFromUpload(
                $site,
                $file->getPathname(),
                $file->getClientOriginalName() ?: 'upload.bin',
                $file->getClientMimeType(),
                $folderNodeId,
            );
        } catch (MediaAssetInvalidFolderException $e) {
            return ApiJson::error('invalid_folder', $e->getMessage(), 422);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            return ApiJson::error('upload_failed', $e->getMessage(), 400);
        }

        return ApiJson::data([
            ...MediaAssetApiMapper::toArray($result['asset']),
            'deduped' => $result['deduped'],
        ], $result['deduped'] ? 200 : 201);
    }

    #[Route('/media/{id}', name: 'api_admin_media_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] MediaAsset $asset,
    ): JsonResponse {
        $this->requireSitePermission('media.list', $site);
        $this->assertMediaSite($site, $asset);

        return ApiJson::data(MediaAssetApiMapper::toArray($asset));
    }

    #[Route('/media/{id}/file', name: 'api_admin_media_file', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function fileMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] MediaAsset $asset,
        MediaBlobStore $blobs,
    ): BinaryFileResponse {
        $this->requireSitePermission('media.list', $site);
        $this->assertMediaSite($site, $asset);
        if ($asset->isDeleted()) {
            throw new NotFoundHttpException('Media asset not found.');
        }
        $absolute = $blobs->absolutePath($asset->getStorageKey());
        if (!is_file($absolute) || !is_readable($absolute)) {
            throw new NotFoundHttpException('Media file missing.');
        }

        $response = new BinaryFileResponse($absolute);
        $response->headers->set('Content-Type', $asset->getMimeType() ?: 'application/octet-stream');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $asset->getOriginalFilename(),
        );

        return $response;
    }

    #[Route('/media/{id}', name: 'api_admin_media_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function deleteMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] MediaAsset $asset,
        MediaAssetSoftDeleter $deleter,
    ): JsonResponse {
        $this->requireSitePermission('media.delete', $site);
        $this->assertMediaSite($site, $asset);
        $user = $this->getUser();
        $deleter->softDelete($asset, $user instanceof User ? $user : null);

        return ApiJson::data(MediaAssetApiMapper::toArray($asset));
    }

    #[Route('/media/{id}/restore', name: 'api_admin_media_restore', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function restoreMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] MediaAsset $asset,
        MediaAssetRestorer $restorer,
    ): JsonResponse {
        $this->requireSitePermission('media.edit', $site);
        $this->assertMediaSite($site, $asset);

        return ApiJson::data(MediaAssetApiMapper::toArray($restorer->restore($asset)));
    }

    #[Route('/media/{id}/purge', name: 'api_admin_media_purge', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsCsrfTokenValid('admin_api', tokenKey: 'X-CSRF-TOKEN', tokenSource: IsCsrfTokenValid::SOURCE_HEADER)]
    public function purgeMedia(
        #[MapEntity(id: 'siteId')] Site $site,
        #[MapEntity(id: 'id')] MediaAsset $asset,
        MediaAssetPurger $purger,
    ): JsonResponse {
        $this->requireSitePermission('media.delete', $site);
        $this->assertMediaSite($site, $asset);
        $id = (int) $asset->getId();

        try {
            $purger->purge($asset);
        } catch (MediaAssetInUseException $e) {
            return ApiJson::error('media_in_use', $e->getMessage(), 409);
        } catch (ContentNodeInvalidParentException $e) {
            return ApiJson::error('not_deleted', $e->getMessage(), 422);
        }

        return ApiJson::data(['id' => $id, 'purged' => true]);
    }


    private function requireSitePermission(string $attribute, Site $site): void
    {
        $this->denyAccessUnlessGranted($attribute, (int) $site->getId());
    }

    private function assertNodeSite(Site $site, ContentNode $node): void
    {
        if ($node->getSite()?->getId() !== $site->getId()) {
            throw new NotFoundHttpException('Node not found on this site.');
        }
    }

    private function assertMediaSite(Site $site, MediaAsset $asset): void
    {
        if ($asset->getSite()?->getId() !== $site->getId()) {
            throw new NotFoundHttpException('Media asset not found on this site.');
        }
    }
}
