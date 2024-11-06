<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\CarRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_car")]
#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Car
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

    private $seat_number;

    #[ORM\Column(type: "integer")]

    private $bag_number;

    #[ORM\Column(type: "integer")]

    private $door_number;

    #[ORM\Column(type: "string", length: 255)]

    private $transmission;

    #[ORM\Column(type: "string", length: 255)]

    private $fuel;

    #[ORM\Column(type: "boolean")]

    private $aircon;

    #[ORM\Column(type: "boolean", name: "leather_upholstery")]

    private $leatherUpholstery;

    #[ORM\Column(type: "boolean")]

    private $gps;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "cars")]
    #[ORM\JoinColumn(nullable: false)]

    private $owner;

    #[ORM\Column(type: "boolean")]

    private $active;

    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: "car", orphanRemoval: true)]

    private $rentals;

    #[ORM\Column(type: "float", name: "rate_per_day")]

    private $ratePerDay;

    #[ORM\Column(type: "float", name: "refundable_deposit")]

    private $refundableDeposit;

    #[ORM\Column(type: "boolean", nullable: true)]

    private $booked;

    #[ORM\Column(type: "string", length: 255, nullable: true)]

    private $brand;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

        $metadata->addPropertyConstraint("name", new Assert\NotBlank(['message' => 'Name must not be blank']));
        $metadata->addPropertyConstraint("seat_number", new Assert\NotBlank(['message' => 'Seat number must not be blank']));
        $metadata->addPropertyConstraint("bag_number", new Assert\NotBlank(['message' => 'Bag number must not be blank']));
        $metadata->addPropertyConstraint("door_number", new Assert\NotBlank(['message' => 'Door number must not be blank']));
        $metadata->addPropertyConstraint("transmission", new Assert\NotBlank(['message' => 'Transmission must not be blank']));
        $metadata->addPropertyConstraint("fuel", new Assert\NotBlank(['message' => 'Fuel type must not be blank']));
        $metadata->addPropertyConstraint("ratePerDay", new Assert\NotBlank(['message' => 'Rate/day  must not be blank']));
        $metadata->addPropertyConstraint("refundableDeposit", new Assert\NotBlank(['message' => 'Refundable Deposit must not be blank']));
        $metadata->addPropertyConstraint("brand", new Assert\NotBlank(['message' => 'Brand must not be blank']));

    }

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->aircon = 0;
        $this->leatherUpholstery = 0;
        $this->gps = 0;
        $this->active = 0;
        $this->booked = 0;
        $this->rentals = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function getSeatNumber(): ?int
    {
        return $this->seat_number;
    }

    public function setSeatNumber(int $seat_number): self
    {
        $this->seat_number = $seat_number;

        return $this;
    }

    public function getBagNumber(): ?int
    {
        return $this->bag_number;
    }

    public function setBagNumber(int $bag_number): self
    {
        $this->bag_number = $bag_number;

        return $this;
    }

    public function getDoorNumber(): ?int
    {
        return $this->door_number;
    }

    public function setDoorNumber(int $door_number): self
    {
        $this->door_number = $door_number;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): self
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getAircon(): ?bool
    {
        return $this->aircon;
    }

    public function setAircon(bool $aircon): self
    {
        $this->aircon = $aircon;

        return $this;
    }

    public function getLeatherUpholstery(): ?bool
    {
        return $this->leatherUpholstery;
    }

    public function setLeatherUpholstery(bool $leatherUpholstery): self
    {
        $this->leatherUpholstery = $leatherUpholstery;

        return $this;
    }

    public function getGps(): ?bool
    {
        return $this->gps;
    }

    public function setGps(bool $gps): self
    {
        $this->gps = $gps;

        return $this;
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    //@return Collection|Rental[]

    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): self
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals[] = $rental;
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): self
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }

    public function getRatePerDay(): ?float
    {
        return $this->ratePerDay;
    }

    public function setRatePerDay(float $ratePerDay): self
    {
        $this->ratePerDay = $ratePerDay;

        return $this;
    }

    public function getRefundableDeposit(): ?float
    {
        return $this->refundableDeposit;
    }

    public function setRefundableDeposit(float $refundableDeposit): self
    {
        $this->refundableDeposit = $refundableDeposit;

        return $this;
    }

    public function getBooked(): ?bool
    {
        return $this->booked;
    }

    public function setBooked(?bool $booked): self
    {
        $this->booked = $booked;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function isAircon(): ?bool
    {
        return $this->aircon;
    }

    public function isLeatherUpholstery(): ?bool
    {
        return $this->leatherUpholstery;
    }

    public function isGps(): ?bool
    {
        return $this->gps;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function isBooked(): ?bool
    {
        return $this->booked;
    }

}
