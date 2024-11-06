<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserCarAvailableItemRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;


  
 #[ORM\Table(name:"user_car_available_item")]
 #[ORM\Entity(repositoryClass: UserCarAvailableItemRepository::class)]
 #[ORM\HasLifecycleCallbacks]
 
class UserCarAvailableItem
{
    use Timestampable;
    
     #[ORM\Id]
     #[ORM\Column(type:"string")]
     
    private $id;


    
     #[ORM\ManyToOne(targetEntity:UserTripChecklist::class, inversedBy:"userCarAvailableItems")]
     #[ORM\JoinColumn(nullable:false)]
     
    private $checklist;

    
     #[ORM\Column(type:"string", length:255)]
     
    private $description;

    
     #[ORM\Column(type:"float")]
     
    private $amount;

    
     #[ORM\Column(type:"int", length:255)]
     
    private $measurement;

    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function __toString()
    {
        return $this->description;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMeasurement(): ?int
    {
        return $this->measurement;
    }

    public function setMeasurement(int $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }
}
