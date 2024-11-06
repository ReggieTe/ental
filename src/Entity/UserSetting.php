<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserSettingRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "user_setting")]
#[ORM\Entity(repositoryClass: UserSettingRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserSetting
{
    use Timestampable;

    #[ORM\Column(name: "id", type: "string", length: 60, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(name: "notifications", type: "boolean", nullable: false)]

    private $notifications;

    #[ORM\Column(name: "account", type: "boolean", nullable: false)]

    private $account;

    #[ORM\OneToOne(targetEntity: UserAdmin::class, inversedBy: "userSetting", cascade: ["persist", "remove"])]

    private $addedby;

    #[ORM\Column(type: "boolean")]

    private $sms;

    #[ORM\Column(type: "boolean")]

    private $email;

    public function __construct()
    {
        $this->id = Validate::genKey();
        $this->notifications = 1;
        $this->account = 1;
        $this->sms = 1;
        $this->email = 1;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    //Get the value of account

    ////@return  string

    public function getAccount()
    {
        return $this->account;
    }

    //Set the value of account

    //@param  string  $account

    //@return  self

    public function setAccount(string $account)
    {
        $this->account = $account;

        return $this;
    }

    //Get the value of notifications

    ////@return  string

    public function getNotifications()
    {
        return $this->notifications;
    }

    //Set the value of notifications

    //@param  string  $notifications

    //@return  self

    public function setNotifications(string $notifications)
    {
        $this->notifications = $notifications;

        return $this;
    }

    public function getSms(): ?bool
    {
        return $this->sms;
    }

    public function setSms(bool $sms): self
    {
        $this->sms = $sms;

        return $this;
    }

    public function getEmail(): ?bool
    {
        return $this->email;
    }

    public function setEmail(bool $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isNotifications(): ?bool
    {
        return $this->notifications;
    }

    public function isAccount(): ?bool
    {
        return $this->account;
    }

    public function isSms(): ?bool
    {
        return $this->sms;
    }

    public function isEmail(): ?bool
    {
        return $this->email;
    }
}
