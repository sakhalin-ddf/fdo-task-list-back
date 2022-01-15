<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(
    repositoryClass: TaskRepository::class
)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[SerializedName('id')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[SerializedName('title')]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[SerializedName('text')]
    private ?string $text = null;

    #[ORM\Column(type: 'datetimetz_immutable', nullable: false)]
    #[SerializedName('created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt ??= CarbonImmutable::now();
    }
}
