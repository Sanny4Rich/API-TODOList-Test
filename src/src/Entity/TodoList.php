<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\TodoListRepository;
use App\Utils\StatusConverter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TodoListRepository::class)]
#[ORM\Index(columns: ['title', 'description'], flags: ['fulltext'])]
class TodoList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column('id')]
    private ?int $id;

    #[Assert\NotBlank(message: 'User key cannot be empty')]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $userKey;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $status = false;

    #[Assert\Range(
        notInRangeMessage: 'Priority must be in range from 0 to 5',
        min: 0,
        max: 5,
    )]
    #[ORM\Column(type: Types::INTEGER)]
    private int $priority = 0;

    #[Assert\Length(max: 255, maxMessage: 'Title cannot be longer than 255 symbol')]
    #[Assert\NotBlank(message: 'Title can not be blank')]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $doneAt;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private ?DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subTask')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private $parentTask;

    #[ORM\OneToMany(mappedBy: 'parentTask', targetEntity: self::class, cascade: ['remove'])]
    private $subTask;

    public function __construct()
    {
        $this->subTask = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserKey(): ?string
    {
        return $this->userKey;
    }

    public function setUserKey(string $userKey): self
    {
        $this->userKey = $userKey;

        return $this;
    }

    public function getStatus(): string
    {
        return StatusConverter::toString($this->status);
    }

    public function getBooleanStatus(): bool
    {
        return $this->status;
    }

    public function setBooleanStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = StatusConverter::toBoolean($status);

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDoneAt(): ?DateTime
    {
        return $this->doneAt;
    }

    public function setDoneAt(?DateTime $doneAt): self
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    public function getParentTask(): ?self
    {
        return $this->parentTask;
    }

    public function setParentTask(?self $parentTask): self
    {
        $this->parentTask = $parentTask;

        return $this;
    }

    public function getSubTask(): Collection
    {
        return $this->subTask;
    }

    public function addSubTask(self $subTask): self
    {
        if (!$this->subTask->contains($subTask)) {
            $this->subTask[] = $subTask;
            $subTask->setParentTask($this);
        }

        return $this;
    }

    public function removeSubTask(self $subTask): self
    {
        if ($this->subTask->removeElement($subTask)) {
            // set the owning side to null (unless already changed)
            if ($subTask->getParentTask() === $this) {
                $subTask->setParentTask(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
}
