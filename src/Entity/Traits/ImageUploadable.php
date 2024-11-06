<?php 
namespace  App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\Mapping as ORM;


trait ImageUploadable
{
    
     #[ORM\Column(type:"string", length:255)]
     
     
    private $image;

    private $dispatcher;

    


    public function __construct(){
        
        $this->dispatcher = new EventDispatcher();
    }
  

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            
            $this->dateModified = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }
}