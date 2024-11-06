<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserCarIssueRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;


  
 #[ORM\Table(name:"user_car_issue")]
 #[ORM\Entity(repositoryClass: UserCarIssueRepository::class)]
 #[ORM\HasLifecycleCallbacks]
 
class UserCarIssue
{
    use Timestampable;
    
     #[ORM\Id]
     #[ORM\Column(type:"string")]
     
    private $id;


    
     #[ORM\ManyToOne(targetEntity:UserTripChecklist::class, inversedBy:"userCarIssues")]
     #[ORM\JoinColumn(nullable:false)]
     
    private $checklist;

    
     #[ORM\Column(type:"string", length:255)]
     
    private $description;

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
}
