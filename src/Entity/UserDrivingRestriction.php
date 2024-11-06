<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserDrivingRestrictionRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_driving_restriction")]
#[ORM\Entity(repositoryClass: UserDrivingRestrictionRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserDrivingRestriction
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userDrivingRestrictions")]

    private $owner;

    #[ORM\Column(type: "string", length: 255)]

    private $description;

    #[ORM\Column(type: "float")]

    private $fine;

    #[ORM\Column(type: "boolean")]

    private $state;

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

    public function getFine(): ?float
    {
        return $this->fine;
    }

    public function setFine(float $fine): self
    {
        $this->fine = $fine;

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

    public function isState(): ?bool
    {
        return $this->state;
    }
}
