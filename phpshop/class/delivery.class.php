<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
    require_once(dirname(__FILE__) . "/array.class.php");
}

/**
 * ���������� ��������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopDelivery extends PHPShopObj {

    /**
     * ����� ������� ����� ������� � �������
     * @var int 
     */
    var $fee = 100;
    var $mod_price = false;

    /**
     * �����������
     * @param Int $objID �� ��������
     */
    function __construct($objID = false) {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['delivery'];
        parent::__construct();
    }

    /**
     * ����� ������ ����� ������ �������� ��������� ������ ������ � ��������� ��������
     * @param array $option ����� ������
     * @param string $delim ����������� ��������
     * @return string
     */
    function getAdresListFromOrderData($option, $delim = "<br>") {
        $data_fields = unserialize($this->getParam('data_fields'));
        if (is_array($data_fields)) {
            $num = $data_fields['num'];
            asort($num);
            $enabled = $data_fields['enabled'];
            foreach ($num as $key => $value) {
                if ($enabled[$key]['enabled'] == 1) {
                    $adres .= __($enabled[$key]['name']) . ": " . $option[$key . "_new"] . $delim;
                }
            }
        }

        if (!$adres)
            $adres = __("�� ���������");

        return $adres;
    }

    /**
     * ������ ��������� ��������
     * @param float $sum ����� ������
     * @param float $weight ��� ������
     * @return float
     */
    function getPrice($sum, $weight = 0) {

        if ($this->mod_price !== false)
            return $this->mod_price;

        $row = $this->objRow;

        // ��������� �����
        if ($row['price_null_enabled'] == 1 and $sum >= $row['price_null']) {
            return 0;
        }

        // ��������� ��������� �������
        elseif ($row['categories_check'] == 1 and ! empty($row['categories'])) {
            $categories = explode(",", $row['categories']);

            if (!empty($_SESSION['cart']) and is_array($_SESSION['cart']) and is_array($categories)) {
                foreach ($_SESSION['cart'] as $product) {
                    if (in_array($product['category'], $categories))
                        return 0;
                }
            }
        }

        if ($row['taxa'] > 0) {
            $addweight = $weight - $this->fee;
            if ($addweight < 0) {
                $addweight = 0;
            }
            $result = $row['price'] + ceil($addweight / $this->fee) * $row['taxa'];
        } else {
            $result = $row['price'];
        }


        return $result;
    }

    /**
     * ����� ������ ��������
     * @return string
     */
    function getCity() {
        return parent::getParam("city");
    }

    /**
     * ������������ �������, �� ������ ���������
     * @paramm $sum float ����� �������� � ������
     * @return bool
     */
    function checkMod($sum) {

        $mod = parent::getParam("is_mod");
        if ($mod == 2)
            $this->mod_price = $sum;
    }

    static function getPriceDefault() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
        $row = $PHPShopOrm->select(array('price'), array('flag' => "='1'", 'is_folder' => "='0'", 'enabled' => "='1'"), false, array('limit' => 1));

        if (!is_array($row))
            $row['price'] = 0;

        return $row['price'];
    }

    /**
     * @param int $value
     */
    public function setMod($value) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
        $PHPShopOrm->update(array('is_mod_new' => (string) $value), array('id' => '="' . (int) $this->objID . '"'));
    }

    public function isFree($sum) {
        return (int) $this->getParam('price_null_enabled') === 1 && $sum >= $this->getParam('price_null');
    }

}

/**
 * ������ ��������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopArray
 */
class PHPShopDeliveryArray extends PHPShopArray {

    function __construct($sql = false, $args = array()) {
        $this->objSQL = $sql;
        $this->order = array('order' => 'id');
        $this->objBase = $GLOBALS['SysValue']['base']['delivery'];

        if (is_array($args))
            $this->args = $args;

        parent::__construct('id', "city", 'price', 'enabled', 'PID', 'is_folder', 'warehouse');
    }

}

?>