<?php

namespace App\Entity\Images;

use App\Entity\Traits\Timestampable;
use App\Repository\AccountDocumentRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
      
#[ORM\Table(name: "user_verification_document")]
#[ORM\Entity(repositoryClass: AccountDocumentRepository::class)]
#[ORM\HasLifecycleCallbacks]

class AccountDocument
{
    use Timestampable;

    #[ORM\Column(name: "type", type: "string", length: 100, nullable: false)]

    private $type;

    #[ORM\Column(name: "approved", type: "integer", nullable: false)]

    private $approved;

    #[ORM\Column(name: "image", type: "string", length: 100, nullable: false)]

    private $image;

    #[ORM\Column(name: "owner_id", type: "string", length: 100, nullable: false)]

    private $owner;

    #[ORM\Column(name: "id", type: "string", length: 100, nullable: false)]
    #[ORM\Id]

    private $id;

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
        return $this->type;
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

    //Get the value of type

    ////@return  string

    public function getType()
    {
        return $this->type;
    }

    //Set the value of type

    //@param  string  $type

    //@return  self

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    //Get the value of approved

    ////@return  string

    public function getApproved()
    {
        return $this->approved;
    }

    //Set the value of approved

    //@param  string  $approved

    //@return  self

    public function setApproved(string $approved)
    {
        $this->approved = $approved;

        return $this;
    }
}
