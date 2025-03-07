<?php
/**
 * ���������� �������������� ����
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 * @subpackage Helper
 */
class PHPShopMath {

    /**
     * �������������� ������ ����
     * @param float $price ����
     * @return mixed 
     */
    static function DoZero($price) {
        if(empty($price)) return 0;
        else return $price;
    }

    /**
     * �������������� ������ ����
     * @param float $price ����
     * @return mixed 
     */
    static function Zero($price) {
        return PHPShopMath::DoZero($price);
    }

    /**
     * ������� ����� �� ������ � �����
     * @param float $sum �����
     * @param float $disc ������
     * @return float
     */
    static function ReturnSumma($sum,$disc) {
        global $PHPShopSystem;

        if(!$PHPShopSystem) {
            $PHPShopSystem = new PHPShopSystem();
        }
        
        $kurs=$PHPShopSystem->getDefaultValutaKurs();
        $sum*=$kurs;
        $sum=$sum-($sum*$disc/100);
        return number_format($sum,"2",".","");
    }
}
?>