<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

enum Suit: string {
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isDone;

    #[ORM\Column(length: 256)]
    private string $info;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('done', 'new', 'asap', 'viewed')", options: ["default" => 'new'])]
    private string $status;

    #[ORM\Column(type: "integer",  options: ["default" => 0])]
    private int $countView;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): void
    {
        $this->isDone = $isDone;
    }

    public function getInfo(): string
    {
        return $this->info;
    }

    public function setInfo(string $info): void
    {
        $this->info = $info;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCountView(): int
    {
        return $this->countView;
    }

    public function setCountView(int $countView): void
    {
        $this->countView = $countView;
    }

    public function getCreateAt(): \DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): void
    {
        $this->createAt = $createAt;
    }
}
