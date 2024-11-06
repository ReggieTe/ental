<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserFCMTokenRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "app_fcm_token")]
#[ORM\Entity(repositoryClass: UserFCMTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserFCMToken
{
    use Timestampable;

    //*
    #[ORM\Column(name: "id", type: "string", length: 256, nullable: false)]
    #[ORM\Id]

    private $id;

    #[ORM\Column(type: "text")]

    private $token;

    #[ORM\OneToOne(targetEntity: UserAdmin::class, inversedBy: "userFCMToken", cascade: ["persist", "remove"])]

    private $user;

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
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
}
