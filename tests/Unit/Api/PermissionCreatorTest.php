<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api;

use App\Api\CreatePermissionInput;
use App\Api\PermissionCreator;
use App\Api\PermissionNameTakenException;
use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PermissionCreatorTest extends TestCase
{
    public function testCreatesPermission(): void
    {
        $input = CreatePermissionInput::fromPayload([
            'name' => 'Content.Edit',
            'label' => 'Edit content',
            'description' => 'Allows editing site content.',
        ]);
        self::assertTrue($input->isValid());
        self::assertSame('content.edit', $input->name);

        $permissions = $this->createMock(PermissionRepository::class);
        $permissions->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'content.edit'])
            ->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->with(self::callback(
            static function (Permission $permission): bool {
                return 'content.edit' === $permission->getName()
                    && 'Edit content' === $permission->getLabel()
                    && 'Allows editing site content.' === $permission->getDescription();
            },
        ));
        $em->expects(self::once())->method('flush');

        $permission = (new PermissionCreator($permissions, $em))->create($input);

        self::assertSame('content.edit', $permission->getName());
    }

    public function testDuplicateNameThrows(): void
    {
        $input = CreatePermissionInput::fromPayload([
            'name' => 'content.edit',
            'label' => 'Edit content',
        ]);
        $existing = (new Permission())->setName('content.edit')->setLabel('Existing');

        $permissions = $this->createStub(PermissionRepository::class);
        $permissions->method('findOneBy')->willReturn($existing);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');

        $this->expectException(PermissionNameTakenException::class);
        (new PermissionCreator($permissions, $em))->create($input);
    }

    public function testInvalidNameRejected(): void
    {
        $input = CreatePermissionInput::fromPayload([
            'name' => 'Bad Name!',
            'label' => 'Nope',
        ]);
        self::assertFalse($input->isValid());
        self::assertArrayHasKey('name', $input->fieldErrors);
    }
}
