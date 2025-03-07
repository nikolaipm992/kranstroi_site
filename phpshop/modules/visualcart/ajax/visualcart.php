<?php

session_start();
$_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass('order');
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");

// ������ �����
$PHPShopValutaArray = new PHPShopValutaArray();

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

$PHPShopModules = new PHPShopModules('../../');

class AddToTemplateVisualCartAjax {

    var $debug = false;

    /**
     * �����������
     */
    function __construct() {

        $this->option();

        // ���� ������
        if ($this->option['memory'] == 1)
            $this->init();

        $this->PHPShopCart = new PHPShopCart();
    }

    /**
     * ������������� ����� ������
     */
    function init() {
        if (empty($_COOKIE['visualcart_memory']))
            $this->memory = md5(session_id());
        else
            $this->memory = $_COOKIE['visualcart_memory'];

        if (empty($_SESSION['cart_sig'])) {
            $this->get_memory();
        }

        if (isset($_POST['update'])) {
            $this->update_memory(true);
        }
    }

    /**
     * ���������
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_system']);
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

        // ���������� ������ ������ ����������
        $this->get_memory(false);

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $PHPShopOrm->delete(array('memory' => "='" . $this->memory . "'"));
    }

    /**
     * ���������� ��������� �������
     */
    function update_memory($order = false) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $update['date_new'] = time();

        if (empty($order)) {
            $update['cart_new'] = serialize($_SESSION['cart']);
            $update['sum_new'] = $this->PHPShopCart->getSum();
        }

        if (PHPShopSecurity::true_tel($_POST['tel']))
            $update['tel_new'] = $_SESSION['UsersTel'] = $_POST['tel'];

        if (PHPShopSecurity::true_email($_POST['mail']))
            $update['mail_new'] = $_POST['mail'];

        if (!empty($_POST['name']))
            $update['name_new'] = PHPShopSecurity::TotalClean(PHPShopString::utf8_win1251($_POST['name']));

        if (!empty($_POST['fio']))
            $update['name_new'] = $_SESSION['UsersName'] = PHPShopSecurity::TotalClean(PHPShopString::utf8_win1251($_POST['fio']));

        $PHPShopOrm->update($update, array('memory' => '="' . $this->memory . '"'));

        if ($order)
            exit();
    }

    /**
     * ������ ������� � ��
     */
    function add_memory() {
        $insert = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $insert['memory_new'] = $this->memory;
        $insert['date_new'] = time();

        if (!empty($_SESSION['UsersId']))
            $insert['user_new'] = $_SESSION['UsersId'];
        else
            $insert['user_new'] = $this->data['user'];

        $insert['cart_new'] = serialize($_SESSION['cart']);
        $insert['sum_new'] = $this->PHPShopCart->getSum();
        $insert['ip_new'] = $_SERVER["REMOTE_ADDR"];

        if (defined("HostID"))
            $insert['server_new'] = HostID;

        if (!empty($_SESSION['UsersTel']))
            $insert['tel_new'] = $_SESSION['UsersTel'];
        else
            $insert['tel_new'] = $this->data['tel'];

        if (!empty($_SESSION['UsersLogin']))
            $insert['mail_new'] = $_SESSION['UsersLogin'];
        else
            $insert['mail_new'] = $this->data['mail'];

        if (!empty($_SESSION['UsersName']))
            $insert['name_new'] = $_SESSION['UsersName'];
        else
            $insert['name_new'] = $this->data['name'];

        if (isset($_COOKIE['ps_referal']))
            $insert['referal_new'] = base64_decode($_COOKIE['ps_referal']);
        else
            $insert['referal_new'] = $this->data['referal'];

        $insert['sendmail_new'] = 0;

        $PHPShopOrm->insert($insert);
    }

    /**
     * ����� ������ ������ � cookie
     */
    function add_cookie() {
        setcookie("visualcart_memory", $this->memory, time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
    }

    /**
     * �������� ����� ���������
     */
    function true_key($str) {
        return preg_match("/^[a-zA-Z0-9_]{4,35}$/", $str);
    }

    function get_memory($cart = true) {
        if ($this->true_key($_COOKIE['visualcart_memory'])) {
            $this->memory = $_COOKIE['visualcart_memory'];
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
            $data = $PHPShopOrm->select(array('*'), array('memory' => "='" . $this->memory . "'"), false, array('limit' => 1));
            if (is_array($data)) {
                if ($cart)
                    $_SESSION['cart'] = unserialize($data['cart']);
                $this->data = $data;
            }
        }
    }

    /**
     * ����� �������
     * @return string
     */
    function visualcart() {

        // ���� ������ SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system']))
            PHPShopObj::loadClass('string');

        $GLOBALS['PHPShopOrder'] = new PHPShopOrderFunction();

        // ������
        $this->currency = $GLOBALS['PHPShopSystem']->getValutaIcon();

        PHPShopParser::set('visualcart_pic_width', $this->option['pic_width']);

        // ���� ���� ������ � �������
        if ($this->PHPShopCart->getNum() > 0) {
            $list = $this->PHPShopCart->display('visualcartform', array('currency' => $this->currency));

            // ����������� �������
            if ($_SESSION['cart_sig'] != md5($list)) {
                $_SESSION['cart_sig'] = md5($list);

                // ������
                $this->clean_memory();

                // ������ � ��
                $this->add_memory();

                // ���� ������ � ����
                $this->add_cookie();
            }

            return $list;
        }
    }

}

/**
 * ������ ������ ������� �������
 */
function visualcartform($val, $option) {
    global $_classPath;

    // �������� ������� ������, ������ ������ �������� ������
    if (empty($val['parent'])) {
        PHPShopParser::set('visualcart_product_id', $val['id']);

        // ���� ������ SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
            PHPShopParser::set('visualcart_product_seo', '_' . PHPShopString::toLatin($val['name']));
        }
    } else {
        PHPShopParser::set('visualcart_product_id', $val['parent']);
        PHPShopParser::set('visualcart_product_seo', null);
    }

    PHPShopParser::set('visualcart_product_xid', $val['id']);
    PHPShopParser::set('visualcart_product_name', $val['name']);
    PHPShopParser::set('visualcart_product_pic_small', $val['pic_small']);
    PHPShopParser::set('visualcart_product_price', $val['price'] * $val['num']);
    PHPShopParser::set('visualcart_product_currency', $option['currency']);
    PHPShopParser::set('visualcart_product_num', $val['num']);

    // �������� ������������� ������� ������
    $path = '../templates/product.tpl';
    $path_template = $_classPath . 'templates/' . $_SESSION['skin'] . '/modules/visualcart/templates/product.tpl';
    if (is_file($path_template))
        $path = $path_template;

    $dis = PHPShopParser::file($path, true, true, true);
    return $dis;
}

$AddToTemplateVisualCartAjax = new AddToTemplateVisualCartAjax();

// ��������
if (!empty($_REQUEST['xid'])) {
    $AddToTemplateVisualCartAjax->del($_REQUEST['xid']);
}

// �������
$visualcart = $AddToTemplateVisualCartAjax->visualcart();


// ��������� ���������
if (!empty($_SESSION['cart']))
    $_RESULT = array(
        "visualcart" => $visualcart,
        "sum" => $AddToTemplateVisualCartAjax->PHPShopCart->getSum(true, ' '),
        "num" => $AddToTemplateVisualCartAjax->PHPShopCart->getNum()
    );
elseif (!empty($_REQUEST['xid']) and empty($_SESSION['cart'])) {

    $_RESULT = array(
        "visualcart" => "<tr><td>" . $GLOBALS['SysValue']['lang']['visualcart_empty'] . "</td></tr>",
        "sum" => $AddToTemplateVisualCartAjax->PHPShopCart->getSum(true, ' '),
        "num" => $AddToTemplateVisualCartAjax->PHPShopCart->getNum()
    );
}

// ��������� ���� ���������� �������
setcookie("cart_update_time", '', 0, "/", $_SERVER['SERVER_NAME'], 0);

if ($_REQUEST['type'] == 'json') {
    $_RESULT['success'] = 1;
    $_RESULT['visualcart'] = PHPShopString::win_utf8($_RESULT['visualcart']);

    if (!isset($_REQUEST['load']))
        echo json_encode($_RESULT);
}
?>