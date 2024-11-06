<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserPayPalRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: "user_paypal")]
#[ORM\Entity(repositoryClass: UserPayPalRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserPayPal
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: "string")]

    private $id;

    #[ORM\Column(type: "string", length: 255, name: "merchant_email")]

    private $merchantEmail;

    #[ORM\OneToOne(targetEntity: UserAdmin::class, inversedBy: "userPayPal", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: false)]

    private $client;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint("merchantEmail", new Assert\NotBlank(['message' => 'merchant id must not be blank']));
    }

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function __toString()
    {
        return $this->merchantEmail;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMerchantEmail(): ?string
    {
        return $this->merchantEmail;
    }

    public function setMerchantEmail(?string $merchantEmail): self
    {
        $this->merchantEmail = $merchantEmail;

        return $this;
    }

    public function getClient(): ?UserAdmin
    {
        return $this->client;
    }

    public function setClient(UserAdmin $client): self
    {
        $this->client = $client;

        return $this;
    }
}
