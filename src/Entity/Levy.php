<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\LevyRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: LevyRepository::class)]
#[ORM\Table(name: "levy")]
#[ORM\HasLifecycleCallbacks]

class Levy
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]

    private $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]

    private $description;

    #[ORM\Column(type: "integer")]

    private $amount;

    #[ORM\Column(type: "boolean")]

    private $mandatory;

    #[ORM\Column(type: "boolean")]

    private $active;

    #[ORM\ManyToMany(targetEntity: UserAdmin::class, inversedBy: "levies")]

    private $user;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint("name", new Assert\NotBlank(['message' => 'Name must not be blank']));
        $metadata->addPropertyConstraint("amount", new Assert\NotBlank(['message' => 'Amount must not be blank']));
    }

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->active = 0;
        $this->mandatory = 0;
        $this->user = new ArrayCollection();
    }
    public function getId(): ?string
    {
        return $this->id;
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMandatory(): ?bool
    {
        return $this->mandatory;
    }

    public function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;

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

    //@return Collection<int, UserAdmin>

    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(UserAdmin $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(UserAdmin $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function isMandatory(): ?bool
    {
        return $this->mandatory;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }
}
