<?php 
namespace App\Util;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileSystem{


    private $params;
    
    private $em;

    private $availableDirectory;

    public function __construct(ParameterBagInterface $params,EntityManagerInterface $em)
    {
        $this->params = $params; 
        $this->em = $em;
        $this->availableDirectory = [
            "profile" => $this->params->get("app.path.profile"),
            "document" => $this->params->get("app.path.profile.verification"),
            "eft"=> $this->params->get("app.path.eft"),
            "car"=> $this->params->get('app.path.car')
         ];
    }

    public function getAbsolute(){
        return $this->params->get('public.dir.path');
    }

    
    public function getRoot($remove="../public/uploads/"){
        return  str_replace($remove,'',$this->getAbsolute());
    }

    public function getDomainUrl(){
        return $this->params->get('domain.url');
    }


    public function sortObjects($type){
        $path = null;
        if(isset($this->availableDirectory[$type])){
            $path = $this->availableDirectory[$type];
        }                    
        return ["path"=>$path];
    }

    public function getPath($userId,$type){
        if(isset($this->availableDirectory[$type])){
           $path =  $this->getAbsolute().$userId.$this->availableDirectory[$type];
           if(is_dir($path)){
             return $path;
           }
        } 
        return null;
    }
   

    public function createUserDirectory($userId){
        //create user directory first 
        $user = $this->getAbsolute().$userId;
        !is_dir($user)?mkdir($user):false;
        //create user sub directory
        foreach($this->availableDirectory as $dir){
            $userPath =  $user.$dir;
            if(!is_dir($userPath)){
                mkdir($userPath);
            }
        }
    }

    public function deleterAllUserDirectory($userId){
        foreach($this->availableDirectory as $dir){
            $userPath =  $this->getAbsolute().$userId.$dir;
            $this->deleteDir($userPath);
        }
        rmdir($this->getAbsolute().$userId);
    }

    public function deleterUseFile($file,$userId,$type="profile"){        
        if(isset($this->availableDirectory[$type])){
            $filePath = $this->getAbsolute().$userId.$this->availableDirectory[$type].$file;
            if(is_file($filePath)){
                unlink($filePath);
                return true;
            }
        }
        return false; 
    }

    private function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            return false;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }  
 
}