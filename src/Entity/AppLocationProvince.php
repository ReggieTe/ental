<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AppLocationProvinceRepository;
use App\Service\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "app_location_province")]
#[ORM\Entity(repositoryClass: AppLocationProvinceRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AppLocationProvince
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 50, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "name", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid subject.")]

    private $name;

    #[ORM\OneToMany(targetEntity: AppLocationCity::class, mappedBy: "province", orphanRemoval: true)]

    private $cities;

    #[ORM\OneToMany(targetEntity: AppLocationCity::class, mappedBy: "province", orphanRemoval: true)]

    private $locations;

    #[ORM\ManyToOne(targetEntity: AppCountry::class, inversedBy: "appLocationProvinces")]

    private $country;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->cities = new ArrayCollection();
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $city->setProvince($this);
        }

        return $this;
    }

    public function removeCity(AppLocationCity $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getProvince() === $this) {
                $city->setProvince(null);
            }
        }

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
            $location->setProvince($this);
        }

        return $this;
    }

    public function removeLocation(AppLocationCity $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getProvince() === $this) {
                $location->setProvince(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?AppCountry
    {
        return $this->country;
    }

    public function setCountry(?AppCountry $country): self
    {
        $this->country = $country;

        return $this;
    }
}
