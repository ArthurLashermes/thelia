<?php

namespace Thelia\Api\Resource;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/content_documents'
        ),
        new GetCollection(
            uriTemplate: '/admin/content_documents'
        ),
        new Get(
            uriTemplate: '/admin/content_documents/{id}'
        ),
        new Put(
            uriTemplate: '/admin/content_documents/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/content_documents/{id}'
        )
    ],
    normalizationContext: ['groups' => [self::GROUP_READ]],
    denormalizationContext: ['groups' => [self::GROUP_WRITE]]
)]
class ContentDocument extends AbstractTranslatableResource
{
    public const GROUP_READ = 'content_document:read';
    public const GROUP_READ_SINGLE = 'content_document:read:single';
    public const GROUP_WRITE = 'content_document:write';

    #[Groups([self::GROUP_READ])]
    public ?int $id = null;

    #[Relation(targetResource: Folder::class)]
    #[Groups([self::GROUP_READ])]
    public Content $content;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public string $file;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public bool $visible;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public ?int $position;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $createdAt;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $updatedAt;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public I18nCollection $i18ns;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ContentDocument
    {
        $this->id = $id;
        return $this;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): ContentDocument
    {
        $this->content = $content;
        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): ContentDocument
    {
        $this->file = $file;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): ContentDocument
    {
        $this->visible = $visible;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): ContentDocument
    {
        $this->position = $position;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): ContentDocument
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): ContentDocument
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function getPropelModelClass(): string
    {
        return \Thelia\Model\ContentDocument::class;
    }

    public static function getI18nResourceClass(): string
    {
        return ContentDocumentI18n::class;
    }
}
