<?php 
namespace App\Service;

use App\Entity\Images\AccountDocument;
use App\Entity\Images\Eft;
use App\Entity\Images\Image;
use App\Entity\Images\ImageProfile;
use App\Util\FileSystem;
use Doctrine\ORM\EntityManagerInterface;

class Common{
 
    private $em;
    private $fileSystem;
    private $classes;

    public function __construct(EntityManagerInterface $em ,FileSystem $fileSystem)
    {
        $this->em = $em;
        $this->fileSystem = $fileSystem;
        
        $this->classes = [
            "car"=>Image::class,
            "profile"=>ImageProfile::class,
            "document"=>AccountDocument::class,
            "eft"=>Eft::class
        ];
    }


    public function sortObjects($type){
        return $this->fileSystem->sortObjects($type);
    }
    
    public function getFiles($id,$userId,$type){
        $files = [];
        $items = [];
        $path = null;
        if(isset($this->classes[$type])) {
            
            $items = $this->em->getRepository($this->classes[$type])->findBy(['owner'=>$id]);
            
            if(count($items)) {
                $path = $this->fileSystem->getPath($userId, $type);
                    if($path) {
                        foreach($items as $item) {
                            $filePath = $path.$item->getImage();
                            if(is_file($filePath)) {
                                array_push($files, [
                                    "file"=>str_replace("../public",$this->fileSystem->getDomainUrl(),$filePath),
                                    "id"=> $item->getId(),
                                    "type"=> method_exists($item,'getType')? $item->getType():(method_exists($item,'getName')? $item->getName():"File"),
                                    "approved"=> method_exists($item,'getApprove')? $item->getApprove():null,
                                ]);
                            }
                        }
                    }
            }
        }
        return $files;
    }

    public function deleteFile($id,$userId,$type){       
        if(isset($this->classes[$type])) {
            $item = $this->em->getRepository($this->classes[$type])->findOneBy(['id'=>$id]);
            if($item) {
                $path = $this->fileSystem->getPath($userId, $type);
                    if($path){                        
                            $filePath = $path.$item->getImage();
                            if(is_file($filePath)) { 
                                $this->em->remove($item);
                                $this->em->flush();
                                unlink($filePath);
                                return true;
                            }
                        }
                    }
            }
        return false;
    }

    public function deleteMultipleFiles($id,$userId,$type){ 
        if(isset($this->classes[$type])) {
            $items = $this->em->getRepository($this->classes[$type])->findBy(['owner'=>$id]);            
                $path = $this->fileSystem->getPath($userId, $type); 
                    if($path){                         
                        foreach($items as $item) {
                            $filePath = $path.$item->getImage();
                            if(is_file($filePath)) {
                                $this->em->remove($item);
                                $this->em->flush();
                                unlink($filePath);
                            }
                        }
                    }
            }

    }

}