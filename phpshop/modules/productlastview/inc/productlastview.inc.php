<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * ������� �������
 */
PHPShopObj::loadClass('order');

class ProductLastView extends PHPShopProductElements {

    var $debug = false;

    /**
     * �����������
     */
    function __construct() {

        $this->option();

        if ($this->option['num'] == 0)
            $this->option['num'] = 1;

        $this->_PRODUCT = &$_SESSION['product'];

        // ���� ������
        if ($this->option['memory'] == 1)
            $this->init_memory();

        parent::__construct();
    }

    /**
     * ������������� ����� ������
     */
    function init_memory() {
        if (empty($_COOKIE['productlastview_memory']))
            $this->memory = md5(session_id());
        else
            $this->memory = $_COOKIE['productlastview_memory'];

        $this->get_memory();
    }

    /**
     * ���������
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['productlastview']['productlastview_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * �������� ������
     * @param int $xid �� ������
     */
    function del($xid) {
        if (is_numeric($xid)) {
            $this->PHPShopCart->del($xid);
            $this->clean_memory();
        }
    }

    /**
     * ��������� ������ ������
     */
    function clean_memory() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['productlastview']['productlastview_memory']);
        $PHPShopOrm->delete(array('memory' => "='" . $this->memory . "'"));
    }

    function add($objID) {

        // ������ �� ������
        $objProduct = new PHPShopProduct(intval($objID));

        // ������ �������
        $array = array(
            "id" => $objProduct->getParam("id"),
            "category" => $objProduct->getParam('category'),
            "name" => PHPShopSecurity::CleanStr($objProduct->getParam("name")),
            "price" => PHPShopProductFunction::GetPriceValuta($objID, $objProduct->getParam("price"), $objProduct->getParam("baseinputvaluta"), true),
            "price_n" => PHPShopProductFunction::GetPriceValuta($objID, $objProduct->getParam("price_n"), $objProduct->getParam("baseinputvaluta"), true, false),
            "uid" => $objProduct->getParam("uid"),
            "pic_small" => $objProduct->getParam("pic_small"),
        );


        $this->_PRODUCT[$array["id"]] = $array;

        // ������� �����
        $this->first_remove();
    }

    /**
     * ������� ������ ������� �� ������
     * @return boolean
     */
    function first_remove() {
        $i = 0;
        if (is_array($this->_PRODUCT) and count($this->_PRODUCT) > $this->option['num']) {
            foreach ($this->_PRODUCT as $key => $v) {
                if (empty($i)) {
                    unset($this->_PRODUCT[$key]);
                    $i++;
                } else
                    return true;
            }
        }
    }

    /**
     * ������ ������� � ��
     */
    function add_memory() {
        $insert = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['productlastview']['productlastview_memory']);
        $insert['memory_new'] = $this->memory;
        $insert['date_new'] = time();
        
        if(!empty($_SESSION['UsersId']))
            $UsersId = $_SESSION['UsersId'];
        else $UsersId=null;
        
        $insert['user_new'] = $UsersId;
        $insert['product_new'] = serialize($this->_PRODUCT);
        $insert['ip_new'] = $_SERVER["REMOTE_ADDR"];
        $PHPShopOrm->insert($insert);
    }

    /**
     * ����� ������ ������ � �����
     */
    function add_cookie() {
        @setcookie("productlastview_memory", $this->memory, time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
    }

    /**
     * �������� ����� ���������
     */
    function true_key($str) {
        return preg_match("/^[a-zA-Z0-9_]{4,35}$/", $str);
    }

    function get_memory() {
        if ($this->true_key($_COOKIE['productlastview_memory'])) {
            $this->memory = $_COOKIE['productlastview_memory'];
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['productlastview']['productlastview_memory']);
            $data = $PHPShopOrm->select(array('product'), array('memory' => "='" . $this->memory . "'"), false, array('limit' => 1));
            if (is_array($data)) {

                $this->_PRODUCT = unserialize($data['product']);
            }
        }
    }

    function display($function, $option = false) {
        global $PHPShopOrder;
        $list = null;

        // ������ ������ � ������ ������ ��� ������
        if (is_array($this->_PRODUCT)) {
            krsort($this->_PRODUCT);
            foreach ($this->_PRODUCT as $key => $val) {
                $cart[$key]['price'] = $PHPShopOrder->ReturnSumma($val['price'], 0);

                if(!empty($val['num']))
                $cart[$key]['total'] = $PHPShopOrder->ReturnSumma($val['price'] * $val['num'], 0);
            }
        }

        if (is_array($this->_PRODUCT))
            foreach ($this->_PRODUCT as $k => $v)
                if (function_exists($function)) {

                    // ����������
                    $promotions = $this->PHPShopPromotions->getPrice($v);
                    if (is_array($promotions)) {
                        $v['price'] = $promotions['price'];
                        $v['price_n'] = $promotions['price_n'];
                        $v['promo_label'] = $promotions['label'];
                    }

                    $option['xid'] = $k;
                    $list .= call_user_func_array($function, array($v, $option));
                }

        return $list;
    }

    function lastview() {

        // ���� ������ SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system']))
            PHPShopObj::loadClass('string');

        $GLOBALS['PHPShopOrder'] = new PHPShopOrderFunction();

        // ������
        if ($GLOBALS['PHPShopOrder']->default_valuta_iso == 'RUR' or $GLOBALS['PHPShopOrder']->default_valuta_iso == "RUB")
            $this->currency = '<span class="rubznak">p</span>';
        else
            $this->currency = $GLOBALS['PHPShopOrder']->default_valuta_code;

        $this->set('productlastview_pic_width', $this->option['pic_width']);

        // ���� ���� ������ � �������
        if (is_array($this->_PRODUCT) and count($this->_PRODUCT) > 0) {
            $list = $this->display('productlastviewform', array('currency' => $this->currency, 'user_price_activate' => $this->user_price_activate, 'format' => $this->format));
            $this->set('productlastview_list', $list, true);
            $product = PHPShopParser::file($GLOBALS['SysValue']['templates']['productlastview']['productlastview_forma'], true, false, true);

            $this->set('leftMenuContent', $product);
            $this->set('leftMenuName', $this->option['title']);

            // ���������� ������
            $dis = $this->parseTemplate($this->getValue('templates.left_menu'));


            // ��������� ���������� �������
            switch ($this->option['enabled']) {

                case 1:
                    $this->set('leftMenu', $dis, true);
                    break;

                case 2:
                    $this->set('rightMenu', $dis, true);
                    break;

                default: {
                        $this->set('productlastview', $product);
                        $this->set('productlastview_title', $this->option['title']);
                    }
            }
        }
    }

}

/**
 * ������ ������ ������� �������
 */
function productlastviewform($val, $option) {
    global $SysValue, $PHPShopSystem;

    //������ � ���� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($val['id'])), false, array('limit' => 1));

    if(!isset($data['id'])) {
        return;
    }

    // ���� ������ SEOURLPRO
    if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
        if(empty($GLOBALS['PHPShopSeoPro'])) {
            include_once dirname(dirname(dirname(__DIR__))) . '/modules/seourlpro/inc/option.inc.php';
            $GLOBALS['PHPShopSeoPro'] = new PHPShopSeoPro();
        }

        if (empty($data['prod_seo_name']))
            $url = '/id/' . str_replace("_", "-", $GLOBALS['PHPShopSeoPro']->setLatin($data['name'])) . '-' . $data['id'];
        else
            $url = '/id/' . $data['prod_seo_name'] . '-' . $data['id'];

        PHPShopParser::set('productlastview_product_url', $url);
    }
    else {
        $url = '/shop/UID_' . $val['id'];
        PHPShopParser::set('productlastview_product_url', $url);
    }

    $val['price'] = number_format($val['price'], $option['format'], '.', ' ');

    // ���� ���� ���������� ������ ����� �����������
    if ($option['user_price_activate'] == 1 && empty($_SESSION['UsersId'])) {
        $val['price'] = $val['price_n'] = $option['currency'] = null;
    }

    PHPShopParser::set('productlastview_product_id', $val['id']);
    PHPShopParser::set('productlastview_product_xid', $val['id']);
    PHPShopParser::set('productlastview_product_name', $val['name']);
    PHPShopParser::set('productlastview_product_pic_small', !empty($val['pic_small']) ? $val['pic_small'] : 'images/shop/no_photo.gif');
    PHPShopParser::set('productlastview_product_price', $val['price']);
    PHPShopParser::set('productlastview_product_currency', $option['currency']);
    
    if(!empty($option['rate']))
    PHPShopParser::set('productlastview_product_rating', $option['rate']);

    // ����� � �������
    if (empty($data['sklad'])) {
        PHPShopParser::set('productlastview_com_start_cart', '');
        PHPShopParser::set('productlastview_com_end_cart', '');
        PHPShopParser::set('productlastview_com_start_notice', PHPShopText::comment('<'));
        PHPShopParser::set('productlastview_com_end_notice', PHPShopText::comment('>'));
    }

    // ����� ��� �����
    else {
        PHPShopParser::set('productlastview_com_start_notice', '');
        PHPShopParser::set('productlastview_com_end_notice', '');
        PHPShopParser::set('productlastview_com_start_cart', PHPShopText::comment('<'));
        PHPShopParser::set('productlastview_com_end_cart', PHPShopText::comment('>'));
    }

    // �������� �������
    if (!empty($data['parent']) && empty($data['sklad'])) {
        PHPShopParser::set('productlastview_com_start_cart', PHPShopText::comment('<'));
        PHPShopParser::set('productlastview_com_end_cart', PHPShopText::comment('>'));
        PHPShopParser::set('productlastview_com_start_parent', '');
        PHPShopParser::set('productlastview_com_end_parent', '');
    } else {
        PHPShopParser::set('productlastview_com_start_parent', PHPShopText::comment('<'));
        PHPShopParser::set('productlastview_com_end_parent', PHPShopText::comment('>'));
    }

    if ((float) $val['price_n'] > 0)
        PHPShopParser::set('productlastview_product_price_old', number_format($val['price_n'], $option['format'], '.', ' ') . ' ' . $PHPShopSystem->getValutaIcon());
    else
        PHPShopParser::set('productlastview_product_price_old', null);


    $dis = PHPShopParser::file($SysValue['templates']['productlastview']['productlastview_product'], true, false, true);
    return $dis;
}

// ��������� � ������ �������
if ($PHPShopNav->notPath(array('order', 'done'))) {
    $GLOBALS['ProductLastView'] = new ProductLastView();
    $GLOBALS['ProductLastView']->lastview();
}
?>