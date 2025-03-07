<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['race:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['race:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['race:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer des races"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['race:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent modifier des races"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent supprimer des races"
        ),
    ]
)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['race:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['race:read', 'race:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Hero>
     */
    #[ORM\OneToMany(targetEntity: Hero::class, mappedBy: 'race')]
    #[Groups(['race:read'])]
    private Collection $heroes;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'race')]
    #[Groups(['race:read'])]
    private Collection $users;

    public function __construct()
    {
        $this->heroes = new ArrayCollection();
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

    /**
     * @return Collection<int, Hero>
     */
    public function getHeroes(): Collection
    {
        return $this->heroes;
    }

    public function addHero(Hero $hero): static
    {
        if (!$this->heroes->contains($hero)) {
            $this->heroes->add($hero);
            $hero->setRace($this);
        }

        return $this;
    }

    public function removeHero(Hero $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            // set the owning side to null (unless already changed)
            if ($hero->getRace() === $this) {
                $hero->setRace(null);
            }
        }

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
            $user->setRace($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRace() === $this) {
                $user->setRace(null);
            }
        }

        return $this;
    }
}
