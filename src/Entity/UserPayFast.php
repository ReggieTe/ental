<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserPayFastRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;


  
 #[ORM\Table(name:"user_pay_fast")]
 #[ORM\Entity(repositoryClass: UserPayFastRepository::class)]
 #[ORM\HasLifecycleCallbacks]
 
class UserPayFast
{
    use Timestampable;
    
     #[ORM\Id]
     #[ORM\Column(type:"string")]
     
    private $id;

    
     #[ORM\Column(type:"string", length:255,name:"merchant_id")]
     
    private $merchantId;

    
     #[ORM\Column(type:"string", length:255,name:"merchant_key")]
     
    private $merchantKey;

    
     #[ORM\OneToOne(targetEntity:UserAdmin::class, inversedBy:"userPayFast", cascade:["persist", "remove"])]
     #[ORM\JoinColumn(nullable:false)]
     
    private $client;


    public static function loadValidatorMetadata(ClassMetadata $metadata){   
        $metadata->addPropertyConstraint("merchantId",new Assert\NotBlank(['message'=>'merchant id must not be blank']));
        $metadata->addPropertyConstraint("merchantKey",new Assert\NotBlank(['message'=>'merchant key must not be blank']));       
    }

    public function __construct()
    {
        $this->id = Validate::genKey(); 
    }

    public function __toString()
    {
        return $this->merchantId;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(?string $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function getMerchantKey(): ?string
    {
        return $this->merchantKey;
    }

    public function setMerchantKey(?string $merchantKey): self
    {
        $this->merchantKey = $merchantKey;

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
