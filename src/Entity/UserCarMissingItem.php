<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserCarAvailableItemRepository;
use App\Repository\UserCarMissingItemRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;


  
 #[ORM\Table(name:"user_car_missing_item")]
 #[ORM\Entity(repositoryClass: UserCarMissingItemRepository::class)]
 #[ORM\HasLifecycleCallbacks]
 
class UserCarMissingItem
{
    use Timestampable;
    
     #[ORM\Id]
     #[ORM\Column(type:"string")]
     
    private $id;

    
     #[ORM\Column(type:"string", length:255)]
     
    private $description;

    
     #[ORM\Column(type:"integer")]
     
    private $amount;

    
     #[ORM\Column(type:"string", length:255)]
     
    private $measurement;

    
     #[ORM\ManyToOne(targetEntity:UserTripChecklist::class, inversedBy:"userCarMissingItems")]
     #[ORM\JoinColumn(nullable:false)]
     
    private $checklist;

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
        return $this->description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMeasurement(): ?string
    {
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    public function getChecklist(): ?UserTripChecklist
    {
        return $this->checklist;
    }

    public function setChecklist(?UserTripChecklist $checklist): self
    {
        $this->checklist = $checklist;

        return $this;
    }
}
