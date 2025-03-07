<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
}

/**
 * ���������� ������ ���������������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopObj
 */
class PHPShopUser extends PHPShopObj {

    /**
     * �����������
     * @param Int $objID �� ������������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->cache = false;
        $this->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        parent::__construct();
    }

    /**
     * ����� ������ �� �����
     * @param string $str ����
     * @return string 
     */
    function getParam($str) {
        return str_replace("\"", "&quot;", parent::getParam($str));
    }

    /**
     * ����� ������ ������� ������������ ��� ������
     * @return string 
     */
    function getAdresList() {
        $data_adres = unserialize(parent::getParam('data_adres'));
        if (!is_array($data_adres) OR !count($data_adres['list']))
            return "";
        
        $disp = null;

        foreach ($data_adres['list'] as $index => $data_adres_one) {
            
            
            
            $dis = "";
            foreach ($data_adres_one as $key => $value) {
                if ($value)
                    $dis .= ", " . $value;
                
                $data_adres['list'][$index][$key]=htmlspecialchars_decode($value,ENT_QUOTES);
                
            }
            if ($dis) {
                if ($index == $data_adres['main'])
                    $sel = 'selected="selected"';
                else
                    $sel = "";
                $disp .= '<option value="' . $index . '" ' . $sel . '>' . substr($dis, 2) . '</option>';
            }
        }
        if ($disp)
            $disp = '
                <b>'.__('������� ����� ��������').'</b>    
                <select name="adres_id" id="adres_id" class="form-control selectpicker show-menu-arrow">
                <option value="none">'.__('������� ����� �����').'</option>
                ' . $disp . '
                </select><br>
                <input type="checkbox" name="adres_this_default" value="1">  '.__('������� ��������� ������� ������� �� ���������').'
                <input type="hidden" class="adresListJson" value=\'' . PHPShopString::json_safe_encode($data_adres['list']) . '\'>
                ';
        return $disp;
    }

    /**
     * ����� ������ �� �����
     * @param string $str ����
     * @return string 
     */
    function getValue($str) {
        return $this->getParam($str);
    }

    /**
     * ����� ������ ������������
     * @return string
     */
    function getLogin() {
        return $this->getParam("login");
    }
    
    /**
     * ����� ����� ������������
     * @return string
     */
    function getName() {
        return $this->getParam("name");
    }

    /**
     * ����� ������������ ������
     * @return string
     */
    function getPersonalDiscount() {
        return $this->getParam("cumulative_discount");
    }


    /**
     * ����� ID �������
     * @return int 
     */
    function getStatus() {
        return $this->getParam("status");
    }

    /**
     * ����� �������� �������
     * @return string 
     */
    function getStatusName() {
        $PHPShopUserStatus = new PHPShopUserStatus($this->getStatus());
        return $PHPShopUserStatus->getParam("name");
    }

    /**
     * ����� ������� ������
     * @return float 
     */
    function getDiscount() {
        $PHPShopUserStatus = new PHPShopUserStatus($this->getStatus());
        return $PHPShopUserStatus->getDiscount();
    }
    
    /**
     * ����� �������
     * @return int
     */
    function getBonus(){
        return intval($this->getParam("bonus"));
    }
    
}

/**
 * ���������� ������ �������������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopObj
 */
class PHPShopUserStatus extends PHPShopObj {

    /**
     * �����������
     * @param Int $objID �� ������� ������������
     * @param array $import_data ������ ������� ������
     */
    function __construct($objID, $import_data = null) {
        $this->objID = $objID;
        $this->cache = true;
        $this->objBase = $GLOBALS['SysValue']['base']['table_name28'];
        parent::__construct('id', $import_data);
    }

    /**
     * ����� ������� ������ � ������������
     * @return int
     */
    function getPrice() {
        return parent::getParam("price");
    }

    /**
     * ����� ������ � ������������
     * @return float
     */
    function getDiscount() {
        //������ �� �������
        $discount_status = parent::getParam("discount");
        //������������ ������
        $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
        $discount_user = $PHPShopUser->getPersonalDiscount();
        //������������
        $discount = max($discount_status, $discount_user);

        return $discount;
    }

    public function isDisplayWarehouse()
    {
        return (int) $this->getParam('warehouse') === 1;
    }
}

/**
 * ���������� ������� ��� �������������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class PHPShopUserFunction {

    /**
     * �������� ��������� ������ � ������������
     * @param float $mysum ��������� ������
     * @return array
     */
    static function ChekDiscount($mysum) {
        global $PHPShopSystem,$link_db;

        $maxsum = 0;
        $userdiscount = 0;
        $maxdiscount = 0;

        $sql = "select * from " . $GLOBALS['SysValue']['base']['table_name23'] . " where sum < '$mysum' and enabled='1'";
        $result = mysqli_query($link_db,$sql);
        while ($row = mysqli_fetch_array($result)) {
            $sum = $row['sum'];
            if ($sum > $maxsum) {
                $maxsum = $sum;
                $maxdiscount = $row['discount'];
            }
        }

        if (!empty($_SESSION['UsersStatus'])) {
            $PHPShopUserStatus = new PHPShopUserStatus($_SESSION['UsersStatus']);
            $userdiscount = $PHPShopUserStatus->getDiscount();
        }
        else
            $userdiscoun = 0;

        if ($userdiscount > $maxdiscount)
            $maxdiscount = $userdiscount;

        $sum = $mysum - ($mysum * @$maxdiscount / 100);
        $format = $PHPShopSystem->getSerilizeParam("admoption.price_znak");
        $array = array(0 + @$maxdiscount, number_format($sum, $format, ".", ""));

        return $array;
    }

}


/**
 * ������ ������ �� �������� �����������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopArray
 */
class PHPShopUserStatusArray extends PHPShopArray {

    function __construct() {
        $this->objBase=$GLOBALS['SysValue']['base']['shopusers_status'];
        $this->objSQL=array('enabled'=>"='1'");
        parent::__construct('id',"name",'discount','price');
    }
    
    
    
    
}
?>