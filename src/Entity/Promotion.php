<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PromotionRepository;
use App\Service\Validate;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[ORM\Table(name: "user_promotion")]
#[ORM\HasLifecycleCallbacks]

class Promotion
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "promotions")]
    #[ORM\JoinColumn(nullable: false)]

    private $owner;

    #[ORM\Column(type: "string", length: 255)]

    private $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]

    private $description;

    #[ORM\Column(type: "string", length: 255)]

    private $type;

    #[ORM\Column(type: "float")]

    private $amount;

    #[ORM\Column(name: "start_date", type: "datetime")]

    private $startDate;

    #[ORM\Column(name: "end_date", type: "datetime")]

    private $endDate;

    #[ORM\Column(type: "boolean")]

    private $active;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint("name", new Assert\NotBlank(['message' => 'Name must not be blank']));
        $metadata->addPropertyConstraint("amount", new Assert\NotBlank(['message' => 'Amount must not be blank']));
        $metadata->addPropertyConstraint("type", new Assert\NotBlank(['message' => 'Type must not be blank']));
        $metadata->addPropertyConstraint("startDate", new Assert\NotBlank(['message' => 'Start date must not be blank']));
        $metadata->addPropertyConstraint("endDate", new Assert\NotBlank(['message' => 'End date must not be blank']));
    }

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->active = 0;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwner(): ?UserAdmin
    {
        return $this->owner;
    }

    public function setOwner(?UserAdmin $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate) : self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate) : self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }
}
