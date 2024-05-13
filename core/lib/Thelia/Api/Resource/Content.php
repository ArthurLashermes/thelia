<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Model\Map\ContentTableMap;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/contents'
        ),
        new GetCollection(
            uriTemplate: '/admin/contents'
        ),
        new Get(
            uriTemplate: '/admin/contents/{id}',
            normalizationContext: ['groups' => [self::GROUP_ADMIN_READ, self::GROUP_ADMIN_READ_SINGLE]]
        ),
        new Put(
            uriTemplate: '/admin/contents/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/contents/{id}'
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]]
)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/front/contents'
        ),
        new Get(
            uriTemplate: '/front/contents/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]]
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class Content extends AbstractTranslatableResource
{
    public const GROUP_ADMIN_READ = 'admin:content:read';
    public const GROUP_ADMIN_READ_SINGLE = 'admin:content:read:single';
    public const GROUP_ADMIN_WRITE = 'admin:content:write';

    public const GROUP_FRONT_READ = 'front:content:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:content:read:single';

    #[Groups([
        self::GROUP_ADMIN_READ,
        self::GROUP_FRONT_READ,
        ContentFolder::GROUP_ADMIN_READ,
        ContentFolder::GROUP_FRONT_READ,
        ContentImage::GROUP_ADMIN_READ_SINGLE,
        ContentImage::GROUP_FRONT_READ_SINGLE,
        ContentDocument::GROUP_ADMIN_READ_SINGLE,
        ProductAssociatedContent::GROUP_ADMIN_READ,
    ])]
    public ?int $id = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ, self::GROUP_ADMIN_WRITE])]
    public bool $visible;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $position;

    #[Relation(targetResource: ContentFolder::class)]
    #[Groups([self::GROUP_ADMIN_READ_SINGLE, self::GROUP_FRONT_READ_SINGLE])]
    public array $contentFolders;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ, self::GROUP_ADMIN_WRITE])]
    public I18nCollection $i18ns;

    public function __construct()
    {
        $this->contentFolders = [];
        parent::__construct();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getContentFolders(): array
    {
        return $this->contentFolders;
    }

    public function setContentFolders(array $contentFolders): self
    {
        $this->contentFolders = $contentFolders;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new ContentTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return ContentI18n::class;
    }
}