<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\HasLifecycleCallbacks]

#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 6,
            order: ['publishedAt' => 'DESC'], // Nombre de résultats par page
            normalizationContext: ['groups' => ['api_list_review']] // Tri par date de publication
        ),
        new Post(
            normalizationContext: ['groups' => ['api_create_review']],
            denormalizationContext: ['groups' => ['api_create_review']]
        )

    ]

)]

class Review
{
    use UuidTrait;
    use DateTrait;

    #[ORM\Column(length: 100)]
    #[Groups(['default','api_list_review','api_create_review'])]
    private ?string $pseudo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['default','api_list_review','api_create_review'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['default'])]
    private ?bool $published = false;

    #[ORM\Column(type: 'datetime_immutable',nullable: true)]
    #[Groups(['default','api_list_review'])]
    private ?\DateTimeImmutable $publishedAt = null;


    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }


    
}
