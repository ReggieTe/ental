<?php 
namespace  App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;



 
 #[ORM\HasLifecycleCallbacks]
 
trait Timestampable
{

    
      
     
     #[ORM\Column(name:"date_created", type:"datetime")]
     
    private $dateCreated;

    
      
     
     #[ORM\Column(name:"date_modified", type:"datetime")]
     
    private $dateModified;

    
    

     
     #[ORM\PrePersist]
     
    public function onPrePersist()
    {
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    

     
     #[ORM\PreUpdate]
     
    public function onPreUpdate()
    {
        $this->dateModified = new \DateTime();
    }

    

     
      //@param \DateTime $dateCreated
     
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }


      //@return \DateTime
     
    public function getDateCreated()
    {
        return $this->dateCreated;
    }


     
      //@param \DateTime $dateModified
     
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

 
     
      //@return \DateTime
     
    public function getDateModified()
    {
        return $this->dateModified;
    }
}