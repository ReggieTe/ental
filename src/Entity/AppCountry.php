<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AppCountryRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "app_country")] // uniqueConstraints={@ORM\UniqueConstraint(name:"code", columns={"id"})}, indexes={@ORM\Index(name:"continent", columns={"continent"})})]
#[ORM\Entity(repositoryClass: AppCountryRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AppCountry
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 2, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "name", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid subject.")]

    private $name;

    #[ORM\Column(name: "native", type: "string", length: 50, nullable: false)]

    private $native;

    #[ORM\Column(name: "phone", type: "string", length: 15, nullable: false)]

    private $phone;

    #[ORM\Column(name: "continent", type: "string", length: 2, nullable: false)]

    private $continent;

    #[ORM\Column(name: "capital", type: "string", length: 50, nullable: false)]

    private $capital;

    #[ORM\Column(name: "currency", type: "string", length: 30, nullable: false)]

    private $currency;

    #[ORM\Column(name: "languages", type: "string", length: 30, nullable: false)]

    private $languages;

    #[ORM\Column(name: "active", type: "integer", nullable: false)]

    private $active;

    #[ORM\OneToMany(targetEntity: AppLocationCity::class, mappedBy: "country", orphanRemoval: true)]

    private $locations;

    #[ORM\OneToMany(targetEntity: AppLocationProvince::class, mappedBy: "country")]

    private $provinces;

    #[ORM\OneToMany(targetEntity: AppLocationCity::class, mappedBy: "country", orphanRemoval: true)]

    private $cities;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->locations = new ArrayCollection();
        $this->provinces = new ArrayCollection();
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString()
    {
        return "(+" . $this->phone . ") " . $this->name;
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

    public function getNative(): ?string
    {
        return $this->native;
    }

    public function setNative(string $native): self
    {
        $this->native = $native;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getContinent(): ?string
    {
        return $this->continent;
    }

    public function setContinent(string $continent): self
    {
        $this->continent = $continent;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    //@return Collection|AppLocationCity[]

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(AppLocationCity $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCountry($this);
        }

        return $this;
    }

    public function removeLocation(AppLocationCity $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getCountry() === $this) {
                $location->setCountry($this);
            }
        }

        return $this;
    }

    //@return Collection|AppLocationProvince[]

    public function getProvinces(): Collection
    {
        return $this->provinces;
    }

    public function addProvince(AppLocationProvince $province): self
    {
        if (!$this->provinces->contains($province)) {
            $this->provinces[] = $province;
            $province->setCountry($this);
        }

        return $this;
    }

    public function removeProvince(AppLocationProvince $province): self
    {
        if ($this->provinces->removeElement($province)) {
            // set the owning side to null (unless already changed)
            if ($province->getCountry() === $this) {
                $province->setCountry(null);
            }
        }

        return $this;
    }

    //@return Collection|AppLocationCity[]

    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(AppLocationCity $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(AppLocationCity $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry($this);
            }
        }

        return $this;
    }
}
