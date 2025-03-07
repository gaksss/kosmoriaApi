<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\FactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FactRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['fact:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['fact:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['fact:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer des facts"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['fact:write']],
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent modifier des facts"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent supprimer des facts"
        ),
    ]
)]
class Fact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fact:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fact:read', 'fact:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fact:read', 'fact:write'])]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fact:read', 'fact:write'])]
    private ?string $answer = null;

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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }
}
