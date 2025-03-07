<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\HeroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HeroRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['hero:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['hero:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['hero:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer des heros"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['hero:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent modifier des heros"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent supprimer des heros"
        ),
    ]
)]
class Hero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hero:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hero:read', 'hero:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hero:read', 'hero:write'])]
    private ?string $imgPath = null;

    #[ORM\ManyToOne(inversedBy: 'heroes')]
    #[Groups(['hero:read', 'hero:write'])]
    private ?Race $race = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'hero')]
    #[Groups(['hero:read'])]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function setImgPath(string $imgPath): static
    {
        $this->imgPath = $imgPath;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setHero($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getHero() === $this) {
                $user->setHero(null);
            }
        }

        return $this;
    }
}
