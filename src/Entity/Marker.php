<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\MarkerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MarkerRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['marker:read']]),
        new GetCollection(normalizationContext: ['groups' => ['marker:read']]),
        new Post(denormalizationContext: ['groups' => ['marker:write']]),
        new Patch(denormalizationContext: ['groups' => ['marker:write']]),
        new Delete(),
    ]
)]
class Marker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['marker:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['marker:read', 'marker:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['marker:read', 'marker:write'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['marker:read', 'marker:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'markers')]
    #[Groups(['marker:read', 'marker:write'])]
    private ?MarkerType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?MarkerType
    {
        return $this->type;
    }

    public function setType(?MarkerType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
