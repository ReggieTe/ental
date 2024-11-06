<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserLevyRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;


 #[ORM\Entity(repositoryClass:UserLevyRepository::class)]
     #[ORM\Table(name:"levy_user_admin")]
    #[ORM\HasLifecycleCallbacks]
    
   class UserLevy
   {
       use Timestampable;
       
        #[ORM\Id]
        #[ORM\Column(type:"string")]
        
       private $id;
   
       
        #[ORM\Column(name:"user_admin_id",type:"string", length:255)]
        
       private $user_admin;
   
       
        #[ORM\Column(name:"levy_id",type:"string", length:255)]
        
       private $levy;
   
       
        #[ORM\Column(type:"boolean")]
        
       private $active;
   
   
       public function __construct()
       {
           $this->id = Validate::genKey();
       }
   
       public function getId(): ?string
       {
           return $this->id;
       }
   
       public function getUserAdmin(): ?string
       {
           return $this->user_admin;
       }
   
       public function setUserAdmin(string $user_admin): self
       {
           $this->user_admin = $user_admin;
   
           return $this;
       }
   
       public function getLevy(): ?string
       {
           return $this->levy;
       }
   
       public function setLevy(string $levy): self
       {
           $this->levy = $levy;
   
           return $this;
       }
   
       public function getActive(): ?bool
       {
           return $this->active;
       }
   
       public function setActive(bool $active): self
       {
           $this->active = $active;
   
           return $this;
       }

       public function isActive(): ?bool
       {
           return $this->active;
       }
   }
