<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\RentalRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_rental")]
#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Rental
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "rentals")]
    #[ORM\JoinColumn(nullable: false)]

    private $user;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: "rentals")]
    #[ORM\JoinColumn(nullable: false)]

    private $car;

    #[ORM\Column(type: "string", length: 255)]

    private $location;

    #[ORM\Column(type: "datetime")]

    private $pickupdate;

    #[ORM\Column(type: "datetime")]

    private $dropoffdate;

    #[ORM\Column(type: "float", name: "quote_amount")]

    private $quoteAmount;

    #[ORM\Column(type: "float", nullable: true, name: "paid_amount")]

    private $paidAmount;

    #[ORM\Column(type: "string", length: 255, name: "payment_status")]

    private $paymentStatus;

    #[ORM\Column(type: "string", length: 255)]

    private $status;

    #[ORM\Column(type: "string", length: 255)]

    private $paymentType;

    #[ORM\OneToMany(targetEntity: UserTripChecklist::class, mappedBy: "rental", orphanRemoval: true)]

    private $userTripChecklists;

    #[ORM\Column(type: "string", length: 255, name: "drop_off_location")]

    private $dropOffLocation;

    #[ORM\ManyToMany(targetEntity: Levy::class)]

    private $levies;

    public function __construct()
    {
        $this->id = Validate::genKey(true);
        $this->userTripChecklists = new ArrayCollection();
        $this->levies = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->location;
    }

    public function getUser(): ?UserAdmin
    {
        return $this->user;
    }

    public function setUser(?UserAdmin $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPickupdate(): ?\DateTimeInterface
    {
        return $this->pickupdate;
    }

    public function setPickupdate(\DateTimeInterface $pickupdate) : self
    {
        $this->pickupdate = $pickupdate;

        return $this;
    }

    public function getDropoffdate(): ?\DateTimeInterface
    {
        return $this->dropoffdate;
    }

    public function setDropoffdate(\DateTimeInterface $dropoffdate) : self
    {
        $this->dropoffdate = $dropoffdate;

        return $this;
    }

    public function getQuoteAmount(): ?float
    {
        return $this->quoteAmount;
    }

    public function setQuoteAmount(float $quoteAmount): self
    {
        $this->quoteAmount = $quoteAmount;

        return $this;
    }

    public function getPaidAmount(): ?float
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(?float $paidAmount): self
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    //@return Collection|UserTripChecklist[]

    public function getUserTripChecklists(): Collection
    {
        return $this->userTripChecklists;
    }

    public function addUserTripChecklist(UserTripChecklist $userTripChecklist): self
    {
        if (!$this->userTripChecklists->contains($userTripChecklist)) {
            $this->userTripChecklists[] = $userTripChecklist;
            $userTripChecklist->setRental($this);
        }

        return $this;
    }

    public function removeUserTripChecklist(UserTripChecklist $userTripChecklist): self
    {
        if ($this->userTripChecklists->removeElement($userTripChecklist)) {
            // set the owning side to null (unless already changed)
            if ($userTripChecklist->getRental() === $this) {
                $userTripChecklist->setRental(null);
            }
        }

        return $this;
    }

    public function getDropOffLocation(): ?string
    {
        return $this->dropOffLocation;
    }

    public function setDropOffLocation(string $dropOffLocation): self
    {
        $this->dropOffLocation = $dropOffLocation;

        return $this;
    }

    //@return Collection<int, Levy>

    public function getLevies(): Collection
    {
        return $this->levies;
    }

    public function addLevy(Levy $levy): self
    {
        if (!$this->levies->contains($levy)) {
            $this->levies[] = $levy;
        }

        return $this;
    }

    public function removeLevy(Levy $levy): self
    {
        $this->levies->removeElement($levy);

        return $this;
    }
}
