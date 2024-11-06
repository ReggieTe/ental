<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AppLocationCityRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "app_location_city")] //)]//{@ORM\UniqueConstraint(name:"area_name", columns={"name"})})
#[ORM\Entity(repositoryClass: AppLocationCityRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AppLocationCity
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 50, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "name", type: "string", length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/[a-zA-Z ]+$/", message: "'{{ value }}' is not a valid subject.")]

    private $name;

    #[ORM\Column(name: "latitude", type: "string", length: 60, nullable: false)]

    private $latitude;

    #[ORM\Column(name: "longitude", type: "string", length: 60, nullable: false)]

    private $longitude;

    #[ORM\ManyToOne(targetEntity: AppLocationProvince::class, inversedBy: "locationCities")]
    #[ORM\JoinColumn(nullable: false)]

    private $province;

    #[ORM\ManyToOne(targetEntity: AppCountry::class, inversedBy: "cities")]
    #[ORM\JoinColumn(nullable: false)]

    private $country;

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function __toString()
    {
        return (isset($this->name) ? $this->name : '');
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

    public function getCountry(): ?AppCountry
    {
        return $this->country;
    }

    public function setCountry(AppCountry $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getProvince(): ?AppLocationProvince
    {
        return $this->province;
    }

    public function setProvince(?AppLocationProvince $province): self
    {
        $this->province = $province;

        return $this;
    }

}
