<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserTripChecklistRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_trip_checklist")]
#[ORM\Entity(repositoryClass: UserTripChecklistRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserTripChecklist
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userTripChecklists")]
    #[ORM\JoinColumn(nullable: false)]

    private $owner;

    #[ORM\ManyToOne(targetEntity: Rental::class, inversedBy: "userTripChecklists")]
    #[ORM\JoinColumn(nullable: false)]

    private $rental;

    #[ORM\Column(type: "string", length: 255)]

    private $type;

    #[ORM\Column(type: "string", length: 255, nullable: true)]

    private $damageDescription;

    #[ORM\Column(type: "integer")]

    private $milleage;

    #[ORM\Column(type: "boolean", nullable: true)]

    private $ownerAgreed;

    #[ORM\Column(type: "boolean", nullable: true)]

    private $renterAgreed;

    #[ORM\Column(type: "integer")]

    private $fuel_available;

    #[ORM\Column(type: "datetime")]

    private $dateSignedByOwner;

    #[ORM\Column(type: "datetime")]

    private $dateSignedByRenter;

    #[ORM\OneToMany(targetEntity: UserCarIssue::class, mappedBy: "checklist", orphanRemoval: true)]

    private $userCarIssues;

    #[ORM\OneToMany(targetEntity: UserCarAvailableItem::class, mappedBy: "checklist", orphanRemoval: true)]

    private $userCarAvailableItems;

    #[ORM\OneToMany(targetEntity: UserCarMissingItem::class, mappedBy: "checklist", orphanRemoval: true)]

    private $userCarMissingItems;

    #[ORM\Column(type: "string", length: 255)]

    private $status;

    #[ORM\Column(type: "boolean", nullable: true, name: "request_renter_to_sign")]

    private $requestRenterToSign;

    public function __construct()
    {
        $this->userCarIssues = new ArrayCollection();
        $this->userCarAvailableItems = new ArrayCollection();
        $this->id = Validate::genKey(true);
        $this->userCarMissingItems = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->type;
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

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(?Rental $rental): self
    {
        $this->rental = $rental;

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

    public function getDamageDescription(): ?string
    {
        return $this->damageDescription;
    }

    public function setDamageDescription(?string $damageDescription): self
    {
        $this->damageDescription = $damageDescription;

        return $this;
    }

    public function getMilleage(): ?int
    {
        return $this->milleage;
    }

    public function setMilleage(int $milleage): self
    {
        $this->milleage = $milleage;

        return $this;
    }

    public function getOwnerAgreed(): ?bool
    {
        return $this->ownerAgreed;
    }

    public function setOwnerAgreed(?bool $ownerAgreed): self
    {
        $this->ownerAgreed = $ownerAgreed;

        return $this;
    }

    public function getRenterAgreed(): ?bool
    {
        return $this->renterAgreed;
    }

    public function setRenterAgreed(?bool $renterAgreed): self
    {
        $this->renterAgreed = $renterAgreed;

        return $this;
    }

    public function getFuelAvailable(): ?int
    {
        return $this->fuel_available;
    }

    public function setFuelAvailable(int $fuel_available): self
    {
        $this->fuel_available = $fuel_available;

        return $this;
    }

    public function getDateSignedByOwner(): ?\DateTimeInterface
    {
        return $this->dateSignedByOwner;
    }

    public function setDateSignedByOwner(\DateTimeInterface $dateSignedByOwner) : self
    {
        $this->dateSignedByOwner = $dateSignedByOwner;

        return $this;
    }

    public function getDateSignedByRenter(): ?\DateTimeInterface
    {
        return $this->dateSignedByRenter;
    }

    public function setDateSignedByRenter(\DateTimeInterface $dateSignedByRenter) : self
    {
        $this->dateSignedByRenter = $dateSignedByRenter;

        return $this;
    }

    //@return Collection|UserCarIssue[]

    public function getUserCarIssues(): Collection
    {
        return $this->userCarIssues;
    }

    public function addUserCarIssue(UserCarIssue $userCarIssue): self
    {
        if (!$this->userCarIssues->contains($userCarIssue)) {
            $this->userCarIssues[] = $userCarIssue;
            $userCarIssue->setChecklist($this);
        }

        return $this;
    }

    public function removeUserCarIssue(UserCarIssue $userCarIssue): self
    {
        if ($this->userCarIssues->removeElement($userCarIssue)) {
            // set the owning side to null (unless already changed)
            if ($userCarIssue->getChecklist() === $this) {
                $userCarIssue->setChecklist(null);
            }
        }

        return $this;
    }

    //@return Collection|UserCarAvailableItem[]

    public function getUserCarAvailableItems(): Collection
    {
        return $this->userCarAvailableItems;
    }

    public function addUserCarAvailableItem(UserCarAvailableItem $userCarAvailableItem): self
    {
        if (!$this->userCarAvailableItems->contains($userCarAvailableItem)) {
            $this->userCarAvailableItems[] = $userCarAvailableItem;
            $userCarAvailableItem->setChecklist($this);
        }

        return $this;
    }

    public function removeUserCarAvailableItem(UserCarAvailableItem $userCarAvailableItem): self
    {
        if ($this->userCarAvailableItems->removeElement($userCarAvailableItem)) {
            // set the owning side to null (unless already changed)
            if ($userCarAvailableItem->getChecklist() === $this) {
                $userCarAvailableItem->setChecklist(null);
            }
        }

        return $this;
    }

    //@return Collection|UserCarMissingItem[]

    public function getUserCarMissingItems(): Collection
    {
        return $this->userCarMissingItems;
    }

    public function addUserCarMissingItem(UserCarMissingItem $userCarMissingItem): self
    {
        if (!$this->userCarMissingItems->contains($userCarMissingItem)) {
            $this->userCarMissingItems[] = $userCarMissingItem;
            $userCarMissingItem->setChecklist($this);
        }

        return $this;
    }

    public function removeUserCarMissingItem(UserCarMissingItem $userCarMissingItem): self
    {
        if ($this->userCarMissingItems->removeElement($userCarMissingItem)) {
            // set the owning side to null (unless already changed)
            if ($userCarMissingItem->getChecklist() === $this) {
                $userCarMissingItem->setChecklist(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRequestRenterToSign(): ?bool
    {
        return $this->requestRenterToSign;
    }

    public function setRequestRenterToSign(?bool $requestRenterToSign): self
    {
        $this->requestRenterToSign = $requestRenterToSign;

        return $this;
    }

    public function isOwnerAgreed(): ?bool
    {
        return $this->ownerAgreed;
    }

    public function isRenterAgreed(): ?bool
    {
        return $this->renterAgreed;
    }

    public function isRequestRenterToSign(): ?bool
    {
        return $this->requestRenterToSign;
    }
}
