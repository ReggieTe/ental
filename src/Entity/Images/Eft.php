<?php

namespace App\Entity\Images;

use App\Entity\Traits\Timestampable;
use App\Repository\EftRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_rental_eft")]
#[ORM\Entity(repositoryClass: EftRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Eft
{
    use Timestampable;

    #[ORM\Column(name: "image", type: "string", length: 100, nullable: false)]

    private $image;

    #[ORM\Column(name: "owner_id", type: "string", length: 100, nullable: false)]

    private $owner;

    #[ORM\Column(name: "id", type: "string", length: 100, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(type: "string", length: 255)]

    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __construct()
    {
        $this->id = Validate::genKey();

    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }

    //Get the value of image

    ////@return  string

    public function getImage()
    {
        return $this->image;
    }

    //Set the value of image

    //@param  string  $image

    //@return  self

    public function setImage(string $image)
    {
        $this->image = $image;

        return $this;
    }

    //Get the value of owner

    public function getOwner()
    {
        return $this->owner;
    }

    //Set the value of owner

    //@return  self

    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }
}
