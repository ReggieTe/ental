<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserOtpRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_otp")]
#[ORM\Entity(repositoryClass: UserOtpRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserOtp
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 60, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "otp_code", type: "string", nullable: false)]

    private $otp;

    #[ORM\Column(name: "phone", type: "string", length: 65535, nullable: false)]

    private $phone;

    #[ORM\Column(name: "email", type: "string", length: 65535, nullable: false)]

    private $email;

    #[ORM\Column(name: "state", type: "string", length: 65535, nullable: false)]

    private $state;

    #[ORM\ManyToOne(targetEntity: UserAdmin::class, inversedBy: "userOtps")]

    private $addedby;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->state = 0;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOtp(): ?string
    {
        return $this->otp;
    }

    public function setOtp(string $otp): self
    {
        $this->otp = $otp;

        return $this;
    }

    //Get the value of phone

    ////@return  string

    public function getPhone()
    {
        return $this->phone;
    }

    //Set the value of phone

    //@param  string  $phone

    //@return  self

    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddedby(): ?UserAdmin
    {
        return $this->addedby;
    }

    public function setAddedby(?UserAdmin $addedby): self
    {
        $this->addedby = $addedby;

        return $this;
    }

    //Get the value of email

    ////@return  string

    public function getEmail()
    {
        return $this->email;
    }

    //Set the value of email

    //@param  string  $email

    //@return  self

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    //Get the value of state

    ////@return  string

    public function getState()
    {
        return $this->state;
    }

    //Set the value of state

    //@param  string  $state

    //@return  self

    public function setState(string $state)
    {
        $this->state = $state;

        return $this;
    }
}
