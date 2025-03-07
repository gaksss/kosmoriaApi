<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\MarkerRepository;
use App\Repository\MarkerTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MarkerRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['markerType:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['markerType:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['markerType:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer des types de marqueurs"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['markerType:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent modifier des types de marqueurs"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent supprimer des types de marqueurs"
        ),
    ]
)]
class MarkerType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['markerType:read', 'marker:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['markerType:read', 'markerType:write', 'marker:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Marker::class)]
    private Collection $markers;

    public function __construct()
    {
        $this->markers = new ArrayCollection();
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
    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    public function addMarker(Marker $marker): static
    {
        if (!$this->markers->contains($marker)) {
            $this->markers->add($marker);
            $marker->setType($this);
        }

        return $this;
    }

    public function removeMarker(Marker $marker): static
    {
        if ($this->markers->removeElement($marker)) {
            // set the owning side to null (unless already changed)
            if ($marker->getType() === $this) {
                $marker->setType(null);
            }
        }

        return $this;
    }
}
