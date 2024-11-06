<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\RentalDiscountRepository;
use App\Service\Validate;
use Doctrine\ORM\Mapping as ORM;


 #[ORM\Entity(repositoryClass:RentalDiscountRepository::class)]
  #[ORM\Table(name:"rental_discount")]
 #[ORM\HasLifecycleCallbacks]
 
class RentalDiscount
{
    use Timestampable;
    
     #[ORM\Id]
     #[ORM\Column(type:"string")]
     
    private $id;

    
     #[ORM\Column(name:"rental_id",type:"string", length:255)]
     
    private $rental;

    
     #[ORM\Column(name:"discount_id",type:"string", length:255)]
     
    private $discount;

    
     #[ORM\Column(type:"float")]
     
    private $total;


    public function __construct()
    {
        $this->id = Validate::genKey();
    }

    public function getId(): ?string
    {
        return $this->id;
    }


    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    
      //Get the value of rental
      
    public function getRental()
    {
        return $this->rental;
    }

    
      //Set the value of rental
     
      //@return  self
      
    public function setRental($rental)
    {
        $this->rental = $rental;

        return $this;
    }

    
      //Get the value of total
      
    public function getTotal()
    {
        return $this->total;
    }

    
      //Set the value of total
     
      //@return  self
      
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }
}
