<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserCarAdditionalRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_car_additional")]
#[ORM\Entity(repositoryClass: UserCarAdditionalRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserCarAdditional
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userCarAdditionals")]

    private $owner;

    #[ORM\Column(type: "string", length: 255)]

    private $description;

    #[ORM\Column(type: "float")]

    private $amount;

    #[ORM\Column(type: "boolean")]

    private $state;

    #[ORM\Column(type: "boolean", name: "add_to_booking_total")]

    private $addToBookingtotal;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint("description", new Assert\NotBlank(['message' => 'Description must not be blank']));
    }

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function __toString()
    {
        return $this->description;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAddToBookingtotal(): ?bool
    {
        return $this->addToBookingtotal;
    }

    public function setAddToBookingtotal(bool $addToBookingtotal): self
    {
        $this->addToBookingtotal = $addToBookingtotal;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function isAddToBookingtotal(): ?bool
    {
        return $this->addToBookingtotal;
    }
}
