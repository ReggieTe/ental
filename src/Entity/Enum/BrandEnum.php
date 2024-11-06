<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class BrandEnum extends ReadableEnum
{
    
    const Nissan = "Nissan";
    const TOYOTA = "TOYOTA";
    const Ford = "Ford";
    const Hyundai = "Hyundai";
    const Isuzu = "Isuzu";
    const Renault = "Renault";
    const Kia = "Kia";
    const Suzuki = "Suzuki";
    const Haval = "Haval";
    const BMW = "BMW";
    const Mercedes = "Mercedes Benz";
    const Volkswagen = "Volkswagen";
    const Mazda = "Mazda";
    const Honda = "Honda";
    const Audi = "Audi";
    const BAIC = "BAIC";
    const Fiat = "Fiat";
    const Opel = "Opel";
    const Alfa = "Alfa Romeo";
    const Land = "Land Rover";
    const Porsche = "Porsche";
    const Citroën = "Citroën";
    const Peugeot = "Peugeot";



    protected static $default = self::Nissan ;

    public static function choices(): array
    {
        return [            
            self::Nissan =>  "Nissan",
            self::TOYOTA =>  "TOYOTA",
            self::Ford =>  "Ford",
            self::Hyundai =>  "Hyundai",
            self::Isuzu =>  "Isuzu",
            self::Renault =>  "Renault",
            self::Kia =>  "Kia",
            self::Suzuki =>  "Suzuki",
            self::Haval =>  "Haval",
            self::BMW =>  "BMW",
            self::Mercedes =>  "Mercedes Benz",
            self::Volkswagen =>  "Volkswagen",
            self::Mazda =>  "Mazda",
            self::Honda =>  "Honda",
            self::Audi =>  "Audi",
            self::BAIC =>  "BAIC",
            self::Fiat =>  "Fiat",
            self::Opel =>  "Opel",
            self::Alfa =>  "Alfa Romeo",
            self::Land =>  "Land Rover",
            self::Porsche =>  "Porsche",
            self::Citroën =>  "Citroën",
            self::Peugeot =>  "Peugeot",
        ];
    }
}