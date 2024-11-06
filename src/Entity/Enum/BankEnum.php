<?php 
namespace App\Entity\Enum;

use Elao\Enum\Attribute\ReadableEnum;


  
 
final class BankEnum extends ReadableEnum
{
    
    const ABSA  = "Absa";
    const AFRICAN  = "African Bank";
    const BIDVEST  = "Bidvest Bank";
    const CAPITEC  = "Capitec Bank";
    const DISCOVERY  = "Discovery ";
    const FNB  = "First National Bank";
    const FIRSTRAND  = "FirstRand Bank ";
    const GRINDROD  = "Grindrod Bank ";
    const IMPERIAL  = "Imperial Bank ";
    const INVESTEC  = "Investec Bank ";
    const MERCANTILE  = "Mercantile Bank ";
    const NEDBANK  = "Nedbank ";
    const SASFIN  = "Sasfin Bank ";
    const STANDARD  = "Standard Bank";
    const UBANK  = "Ubank Limited";
    const TYME  = "TymeBank";


    protected static $default = self::NEDBANK;

    public static function choices(): array
    {
        return [
            self::ABSA  => self::ABSA ,
            self::AFRICAN  => self::AFRICAN,
            self::BIDVEST  => self::BIDVEST ,
            self::CAPITEC  => self::CAPITEC,
            self::DISCOVERY  => self::DISCOVERY,
            self::FNB  => self::FNB,
            self::FIRSTRAND  => self::FIRSTRAND,
            self::GRINDROD  => self::GRINDROD,
            self::IMPERIAL  => self::IMPERIAL,
            self::INVESTEC  => self::INVESTEC,
            self::MERCANTILE  => self::MERCANTILE,
            self::NEDBANK  => self::NEDBANK,
            self::SASFIN  => self::SASFIN,
            self::STANDARD  => self::STANDARD,
            self::UBANK  => self::UBANK,
            self::TYME  => self::TYME,
        ];
    }

}