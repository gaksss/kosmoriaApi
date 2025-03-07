<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MapRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['map:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['map:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['map:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer des maps"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['map:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent modifier des maps"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent supprimer des maps"
        ),
    ]
)]
class Map
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['map:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['map:read', 'map:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Marker>
     */
    #[ORM\OneToMany(targetEntity: Marker::class, mappedBy: 'map')]
    #[Groups(['map:read', 'map:write'])]
    private Collection $marker;

    #[ORM\Column(length: 255)]
    #[Groups(['map:read', 'map:write'])]
    private ?string $mapImg = null;

    public function __construct()
    {
        $this->marker = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Marker>
     */
    public function getMarker(): Collection
    {
        return $this->marker;
    }

    public function addMarker(Marker $marker): static
    {
        if (!$this->marker->contains($marker)) {
            $this->marker->add($marker);
            $marker->setMap($this);
        }

        return $this;
    }

    public function removeMarker(Marker $marker): static
    {
        if ($this->marker->removeElement($marker)) {
            // set the owning side to null (unless already changed)
            if ($marker->getMap() === $this) {
                $marker->setMap(null);
            }
        }

        return $this;
    }

    public function getMapImg(): ?string
    {
        return $this->mapImg;
    }

    public function setMapImg(string $mapImg): static
    {
        $this->mapImg = $mapImg;

        return $this;
    }
}
