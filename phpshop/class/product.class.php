<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
    require_once(dirname(__FILE__) . "/array.class.php");
}

/**
 * ���������� ������ �� �������
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopObj
 */
class PHPShopProduct extends PHPShopObj {

    /**
     * �����������
     * @param Int $objID �� ������
     * @param string $var �������� ������ ������ [id|uid]
     */
    function __construct($objID, $var = 'id') {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        $this->cache = true;
        $this->debug = false;
        $this->cache_format = [];

        // ���� �������� ��� ������� �� ��������
        if (empty($var)) {
            if (PHPShopProductFunction::true_parent($objID))
                $var = 'uid';
            else {
                $this->objID = PHPShopSecurity::TotalClean($objID, 1);
                $var = 'id';
            }
        }
        // ������������� ����� ������ ������
        else
            $this->objID = PHPShopSecurity::true_search($objID);

        parent::__construct($var);
    }

    /**
     * ��� ������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * �� ������ ������
     * @return int
     */
    function getValutaID() {
        return parent::getParam("baseinputvaluta");
    }

    /**
     * ���� ������
     * @return float 
     */
    function getPrice() {
        $price_array = array($this->objRow['price'], $this->objRow['price2'], $this->objRow['price3'], $this->objRow['price4'], $this->objRow['price5']);
        return PHPShopProductFunction::GetPriceValuta($this->objID, $price_array, $this->objRow['baseinputvaluta']);
    }
    
     /**
     * ������ ���� ������
     * @return float 
     */
    function getPriceOld() {
        $price_array = array($this->objRow['price_n'], $this->objRow['price2'], $this->objRow['price3'], $this->objRow['price4'], $this->objRow['price5']);
        return PHPShopProductFunction::GetPriceValuta($this->objID, $price_array, $this->objRow['baseinputvaluta']);
    }

    /**
     * ����������� ������
     * @return string 
     */
    function getImage() {
        return parent::getParam("pic_small");
    }

    // ������� ����� �� ������
    public function removeFromWarehouse($count, $parent = 0, $warehouseId = null)
    {
        // �����
        if (!empty($warehouseId)) {
            @$this->objRow['items' . $warehouseId] -= $count;
        }
        $this->objRow['items'] -= $count;

        $this->applyWarehouseControl($parent, $warehouseId,$count);
    }

    // �������� ����� �� �����
    public function addToWarehouse($count, $parent = 0, $warehouseId = null)
    {
        // �����
        if (!empty($warehouseId)) {
            $this->objRow['items' . $warehouseId] += $count;
        }
        $this->objRow['items'] += $count;

        $this->applyWarehouseControl($parent, $warehouseId,$count);
    }

    // �������� ������
    public function applyWarehouseControl($parent = 0, $warehouseId = null,$count = 0)
    {
        $PHPShopSystem = new PHPShopSystem();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;

        $product_update = [
            'items_new' => $this->objRow['items'],
            'datas_new' => time()
        ];
        if(!empty($warehouseId)) {
            $product_update['items' . $warehouseId . '_new'] = $this->objRow['items' . $warehouseId];
        }

        $disabled = true;
        
        // ���� ����� �� ������ - ��������� ������� �������� � �� ������� �� ������
        if((int) $this->getParam('parent_enabled') === 0) {
            $parentsIds = explode(",", $this->getParam('parent'));

            if(is_array($parentsIds)) {
                $parentsIds = array_diff($parentsIds, ['']);
                if(count($parentsIds) > 0) {
                    // �������� ���������� � �� ��� ����� �������
                    if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
                        $parents = $PHPShopOrm->getList(['*'], ['uid' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);
                    else
                        $parents = $PHPShopOrm->getList(['*'], ['id' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);

                    foreach ($parents as $parentItem) {
                        if((int) $parentItem['items'] > 0) {
                            $disabled = false;
                            break;
                        }
                    }
                } else {
                    $disabled = $this->objRow['items'] < 1;
                }
            }
        } else {
            $disabled = $this->objRow['items'] < 1;
        }

        switch ($PHPShopSystem->getSerilizeParam('admoption.sklad_status')) {
            case(3):
                if ($disabled) {
                    $product_update['sklad_new'] = 1;
                    $this->objRow['sklad'] = 1;
                    $product_update['enabled_new'] = 1;
                    $this->objRow['enabled'] = 1;
                    $product_update['p_enabled_new'] = 0;
                    $this->objRow['p_enabled'] = 0;
                } else {
                    $product_update['sklad_new'] = 0;
                    $this->objRow['sklad'] = 0;
                    $product_update['enabled_new'] = 1;
                    $this->objRow['enabled'] = 1;
                    $product_update['p_enabled_new'] = 1;
                    $this->objRow['p_enabled'] = 1;
                }
                break;

            case(2):
                if ($disabled) {
                    $product_update['enabled_new'] = 0;
                    $this->objRow['enabled'] = 0;
                    $product_update['sklad_new'] = 0;
                    $this->objRow['sklad'] = 0;
                    $product_update['p_enabled_new'] = 0;
                    $this->objRow['p_enabled'] = 0;
                } else {
                    $product_update['enabled_new'] = 1;
                    $this->objRow['enabled'] = 1;
                    $product_update['sklad_new'] = 0;
                    $this->objRow['sklad'] = 0;
                    $product_update['p_enabled_new'] = 1;
                    $this->objRow['p_enabled'] = 1;
                }
                break;
            default:
                break;
        }

        // ��������� ������
        $PHPShopOrm->update($product_update, ['id' => '=' . (int) $this->objID]);

        // ���� ����� ������
        if($parent > 0) {
            $mainProduct = new PHPShopProduct($parent);
            $parentsIds = explode(",", $mainProduct->getParam('parent'));

            if(is_array($parentsIds)) {
                $mainProductDisabled = true; // �� ��������� ��������, ���� ���� � ������� �����-�� ������ - �������
                // �������� ���������� � �� ��� ����� �������
                if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
                    $parents = $PHPShopOrm->getList(['*'], ['uid' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);
                else
                    $parents = $PHPShopOrm->getList(['*'], ['id' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);

                foreach ($parents as $parentItem) {
                    if((int) $parentItem['items'] > 0) {
                        $mainProductDisabled = false;
                        break;
                    }
                }

                $mainProductUpdate = [];
                switch ($PHPShopSystem->getSerilizeParam('admoption.sklad_status')) {
                    case(3):
                        if ($mainProductDisabled) {
                            $mainProductUpdate['sklad_new'] = 1;
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['p_enabled_new'] = 0;
                        } else {
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['p_enabled_new'] = 1;
                        }
                        break;

                    case(2):
                        if ($mainProductDisabled) {
                            $mainProductUpdate['enabled_new'] = 0;
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['p_enabled_new'] = 0;
                        } else {
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['p_enabled_new'] = 1;
                        }
                        break;
                    default:
                        break;
                }
                
                // ����� �������� ������
                $mainProductUpdate['items_new'] = $mainProduct->getParam('items') - $count;

                // ��������� ������
                $PHPShopOrm->update($mainProductUpdate, ['id' => '=' . (int) $parent]);
            }
        }
    }
}

/**
 * ������ ������ �� �������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopArray
 */
class PHPShopProductArray extends PHPShopArray {

    /**
     * �����������
     * @param array $sql SQL ������� �������
     */
    function __construct($sql = false) {
        $this->objSQL = $sql;
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct('id', 'uid', 'name', 'category', 'price', 'price_n', 'sklad', 'odnotip', 'vendor', 'title_enabled', 'datas', 'page', 'user', 'descrip_enabled', 'keywords_enabled', 'pic_small', 'pic_big', 'parent', 'baseinputvaluta','items','ed_izm');
    }

}

/**
 * ���������� ������� �� �������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 */
class PHPShopProductFunction {

    static function getLink() {
        $Arg = func_get_args();
        $link = '/shop/UID_' . $Arg[0] . '.html';
        return $link;
    }

    /**
     * �������� �� ������ ������ �� 1� ��� CML
     * @param string $str ������� ������ ��� ������ ��������
     * @return bool
     */
    static function true_parent($str) {
        if (strstr($str, '-')){
            if (strstr($str, ',')){
                $str_array = explode(",",$str);
                if(is_array($str_array))
                    return preg_match("/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/", $str_array[0]);
                
            }
            else return preg_match("/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/", $str);
        }
        else
            return preg_match("/^[a-zA-Z0-9]{36}$/", $str);

    }

    /**
     * ���� � ������ ������
     * @param int $id �� ������
     * @param mixed $price_array ��������� ������
     * @param int $baseinputvaluta �� ������ ������
     * @param bool $order �������� ������� ������ [true/false]
     * @param bool $check_user_price ��������� ������������ ������� ��� ������������ [true/false]
     * @return format
     */
    static function GetPriceValuta($id, $price_array, $baseinputvaluta = false, $order = false, $check_user_price = true) {
        global $PHPShopValutaArray, $PHPShopSystem;

        if (!is_array($price_array))
            $price = $price_array;
        else
            $price = $price_array[0];

        if (!$PHPShopValutaArray)
            $PHPShopValutaArray = new PHPShopValutaArray();

        $LoadItems['Valuta'] = $PHPShopValutaArray->getArray();
        $LoadItems['System'] = $PHPShopSystem->getArray();
        $LoadItems['System']['dengi'] = $PHPShopSystem->getParam("dengi");

        // �������������� ����
        $format = intval($PHPShopSystem->getSerilizeParam("admoption.price_znak"));

        if (!empty($_SESSION['UsersStatus']) and !empty($check_user_price)) {

            if (empty($_SESSION['UsersStatusPice'])) {

                // ������� �� ���� ������ ������� ���� ��� �����. ������������
                $PHPShopUser = new PHPShopUserStatus($_SESSION['UsersStatus']);
                $GetUsersStatusPrice = $PHPShopUser->getPrice();
                $_SESSION['UsersStatusPice'] = $GetUsersStatusPrice;
            }
            else
                $GetUsersStatusPrice = $_SESSION['UsersStatusPice'];

            if ($GetUsersStatusPrice > 1) {
                $pole = "price" . $GetUsersStatusPrice;

                // ���� �� �������� ������ ������� ���
                if (!is_array($price_array)) {
                    $PHPShopProduct = new PHPShopProduct($id);
                    $user_price = $PHPShopProduct->getParam($pole);
                } else {
                    // ����� ���� �� �������
                    $user_price = $price_array[$GetUsersStatusPrice - 1];
                }
                if (!empty($user_price))
                    $price = $user_price;
            }
        }

        // ������� � ��������� ���
        if (defined("HostPrice") and HostPrice > 1) {
            $pole = "price" . HostPrice;
            $PHPShopProduct = new PHPShopProduct($id);
            $server_price = $PHPShopProduct->getParam($pole);

            if (!empty($server_price))
                $price = $server_price;
        }

        // ���� ������ ������
        if ($baseinputvaluta) { //���� �������� ���. ������
            if ($baseinputvaluta !== $LoadItems['System']['dengi']) {//���� ���������� ������ ���������� �� �������
                if ($LoadItems['Valuta'][$baseinputvaluta]['kurs'] > 0)
                    $price = $price / $LoadItems['Valuta'][$baseinputvaluta]['kurs']; //�������� ���� � ������� ������
            }
        }

        // ���� ������� ������ ������, order - ���� ��� ������� ������� ������ � ���. ������
        if ($order)
            $valuta = $LoadItems['System']['dengi'];
        elseif (isset($_SESSION['valuta']))
            $valuta = $_SESSION['valuta'];
        else
            $valuta = $LoadItems['System']['dengi'];

        // ������ �� ����
        if (!empty($valuta))
            $price = $price * $LoadItems['Valuta'][$valuta]['kurs'];

        // �������
        $price = ($price + (($price * intval($LoadItems['System']['percent'])) / 100));

        return number_format($price, $format, '.', '');
    }
}

?>