<?php 
namespace App\Service;
use Symfony\Component\Validator\Constraints as Assert;

class Validate{

    public static function Regex($field="Field",$wordsOnly=true,$pattern="/[()_a-zA-Z0-9 ]+$/",$message="must contains letters and numbers only"){
        $pattern = $wordsOnly?"/[()_a-zA-Z0-9 ]+$/":$pattern;
        $message = $wordsOnly?"must contains letters only":$message;
        return new Assert\Regex([
            'pattern'=>$pattern,
            'message'=>ucfirst($field)." ".$message
        ]);
    }

    public  static  function sortDate($dateString){
        if($dateString instanceof \DateTime){
            return $dateString;
        }else{
            $timeInput =date('d-M-Y H:i:s', strtotime($dateString)); 
            $date = new \DateTime($timeInput);
            return $date;
        }
	}

    
    public  static  function null($item=null,$returnArray=false){
        if ($returnArray) {
            return !empty($item)? ['id'=>$item->getId(),'name'=>$item->getName()]:['id'=>'','name'=>''];
        }
         return $item!=null?$item:'';
     }

    public static function sortPhone($phone){
        $arr1 = str_split($phone);
        if($arr1[0]=="0"){
            $arr1[0]='';
        }
        $phone = implode("",$arr1);

        return trim($phone);
    }

    public static function genKey($numbersOnly = false){
        $code = uniqid();
        if($numbersOnly){
            $code =  rand(100000,1000000);
        }        
        return $code;
    }


    public static function array_merge($arrays){
        $arrayItems = [];
        foreach($arrays as $array){
           // if(is_array($array)){    
                foreach($array as $item){
                    array_push($arrayItems,$item);
               // }
            }        
        }
        return $arrayItems;
    }

    public static function sortArrayToMultiDem($allAdds,$arraySize=5){
        $finalAds = [];
        $secArray = [];
        $count = 1;
        if (count($allAdds)>5) {
            foreach ($allAdds as $ad) {
                if ($count%$arraySize!=0) {
                    dump($ad);
                    array_push($secArray, $ad);
                } else {
                    array_push($finalAds, $secArray);
                    dump($finalAds);
                    //reset
                    $secArray = [];
                    $count = 1;
                }
                $count++;
            }
        }else{
            array_push($finalAds, $allAdds);  
        }
        return $finalAds;
    }
    	/**
	 *
	 * @param string $algo The algorithm (md5, sha1, whirlpool, etc)
	 * @param string $data The data to encode
	 * @param string $salt The salt (This should be the same throughout the system probably)
	 * @return string The hashed/salted data
	 */
	public static function hash($data, $salt,$algo='sha256')
	{		
        if ($data!="" && $salt!="") {
            $context = hash_init($algo, HASH_HMAC, $salt);
            hash_update($context, $data);
            
            return hash_final($context);
        }
		return false;	
		
	}

    public static function parameter($parameter){
        return $parameter!=null ?$parameter:null;
    }
}