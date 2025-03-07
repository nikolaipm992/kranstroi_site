<?php

PHPShopObj::loadClass(['order', 'mail', 'bonus']);
PHPShopObj::importCore('users');

$PHPShopOrder = new PHPShopOrderFunction();

/**
 * ���������� ������ ������
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopCore
 */
class PHPShopDone extends PHPShopCore {

    /**
     * ������� ������� ����� ������
     * @var bool 
     */
    public $cart_clean_enabled = true;
    public $delivery_mod = false;
    public $manager_comment = null;
    public $delivery_free = false;
    public $bot_enabled = false;

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopOrder;

        // �������
        $this->debug = false;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['orders'];

        // ������ �������
        $this->action = array('nav' => 'index', "post" => 'send_to_order');
        parent::__construct();

        PHPShopObj::loadClass('cart');
        $this->PHPShopCart = new PHPShopCart();

        // ����� ������
        $this->setNum();

        $this->PHPShopOrder = $PHPShopOrder;

        // ���������� ��������
        if (PHPShopSecurity::true_num($_POST['d'])) {
            PHPShopObj::loadClass('delivery');
            $this->PHPShopDelivery = new PHPShopDelivery($_POST['d']);
        }

        // ���������� ��������� ������
        if (PHPShopSecurity::true_num($_POST['order_metod'])) {
            PHPShopObj::loadClass('payment');
            $this->PHPShopPayment = new PHPShopPayment($_POST['order_metod']);
        }

        // ��������� ������� ������
        $this->navigation(false, __('���������� ������'));
    }

    /**
     * ����� �� ���������
     */
    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $this->set('mesageText', $this->message($this->lang('bad_cart_1'), $this->lang('bad_order_mesage_2')));
        $disp = ParseTemplateReturn($this->getValue('templates.order_forma_mesage'));
        $this->set('orderMesage', $disp);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.order_forma_mesage_main'));
    }

    /**
     * ��������� ������ ������
     */
    function setNum() {
        $row = $this->PHPShopOrm->select(array('uid'), false, array('order' => 'id desc'), array('limit' => 1));
        $last = $row['uid'];
        $all_num = explode("-", $last);
        $ferst_num = $all_num[0];
        $order_num = (int)$ferst_num + 1;

        if (empty($_SESSION['order_prefix']))
            $_SESSION['order_prefix'] = substr(rand(1000, 99999), 0, $this->format);

        $this->ouid = $order_num . "-" . $_SESSION['order_prefix'];

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * ���������
     * @param string $title ���������
     * @param string $content ����������
     * @return string
     */
    function message($title, $content) {

        // �������� ������
        $Arg = func_get_args();
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $Arg);
        if ($hook)
            return $hook;

        $message = PHPShopText::h4($title, 'text-danger');
        $message .= PHPShopText::message($content, false, false, false, 'text-muted');

        return $message;
    }

    /**
     *  �������� ����� ������� � ������� ������
     */
    function check_user_status() {
        $status = $this->PHPShopPayment->getParam('status');

        if (!empty($status)) {

            // ����� �������
            if (!empty($this->userId)) {
                
                $_SESSION['UsersStatus'] = $status;
                
                (new PHPShopUser($this->userId))->updateParam(['status_new' => (int) $status]);
                $_SESSION['UsersStatusPice'] = (new PHPShopUserStatus($_SESSION['UsersStatus']))->getPrice();

            }

            foreach ($this->PHPShopCart->_CART as $id => $product) {

                // ������� �����
                $this->PHPShopCart->del($id);

                // ��������� ����� � ������ ����� ����
                $this->PHPShopCart->add($id, $product['num'], $product['parent']);
            }
        }
    }

    /**
     * ����� ������ ������
     */
    function send_to_order() {
        global $SysValue, $link_db, $PHPShopAnalitica, $PHPShopOrder;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        if ($this->PHPShopCart->getNum() > 0) {

            if (isset($_SESSION['UsersLogin']) AND ! empty($_SESSION['UsersLogin']))
                $_POST['mail'] = $_SESSION['UsersMail'];

            // ������ ������ ������������, ��� ���������� �������
            if (!class_exists('PHPShopUsers'))
                PHPShopObj::importCore('users');

            // ��������� ����������
            if (empty($_POST['mail']))
                $_POST['mail'] = 'guest@' . $_SERVER['SERVER_NAME'];

            $PHPShopUsers = new PHPShopUsers();
            $this->userId = $PHPShopUsers->add_user_from_order($_POST['mail']);

            // �������� ����� ������� � ������� ������
            $this->check_user_status();

            if (PHPShopSecurity::true_email($_POST['mail']) AND $this->userId) {
                $_POST['ouid'] = $this->ouid;

                $order_metod = intval($_POST['order_metod']);
                $PHPShopOrm = new PHPShopOrm($this->getValue('base.payment_systems'));
                $row = $PHPShopOrm->select(array('path', 'company'), array('id' => '=' . $order_metod, 'enabled' => "='1'"), false, array('limit' => 1));
                $path = $row['path'];

                // ��. ����
                $this->company = $row['company'];

                // ��������� ������� API
                $LoadItems['System'] = $this->PHPShopSystem->getArray();

                $this->sum = $this->PHPShopCart->getSum(false);
                $this->num = $this->PHPShopCart->getNum();
                $this->weight = $this->PHPShopCart->getWeight();

                // ����� ��� �������
                $this->adminmail = $this->PHPShopSystem->getEmail();

                // ������
                $this->currency = $this->PHPShopOrder->default_valuta_code;

                // ��������� ��������
                if ($this->PHPShopDelivery) {
                    $this->PHPShopDelivery->checkMod($this->delivery_mod);
                    $this->delivery = $this->PHPShopDelivery->getPrice($this->PHPShopCart->getSum(false), $this->PHPShopCart->getWeight());
                    $this->delivery = intval(str_replace(" ", "", $this->delivery));
                } else
                    $this->delivery = 0;

                // ������ � %
                $this->discount = $this->PHPShopOrder->ChekDiscount($this->sum);

                $sum_cart = $this->PHPShopCart->getSum();
                $sum_discount_off = $this->PHPShopCart->getSumNoDiscount();

                // ����� ������ �� �����
                $sum_discount_on = (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumPromo(false));

                // ����� ������ ��� �����
                $sum_discount_on += (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumWithoutPromo(false), $this->discount);

                // ������
                $PHPShopBonus = new PHPShopBonus((int) $_SESSION['UsersId']);
                $this->bonus_minus = $PHPShopBonus->getUserBonus($sum_discount_on);

                // ����� � ������ �������
                $sum_discount_on -= (float) $this->bonus_minus;

                $this->bonus_plus = $PHPShopBonus->setUserBonus($sum_discount_on);

                // ����� ������ � ���
                if ($sum_cart > $sum_discount_on)
                    $discount_sum = $sum_discount_off - $sum_discount_on;
                elseif ($sum_discount_off > $sum_cart)
                    $discount_sum = $sum_discount_off - $sum_cart;
                else
                    $discount_sum = 0;

                $this->discount_sum = number_format($discount_sum * $this->PHPShopSystem->getDefaultValutaKurs(true), $PHPShopOrder->format, '.', ' ');

                // �����
                $this->total = $sum_discount_on + $this->delivery;
                $this->set('total', $this->total);

                // ������� ������ � �������� �������
                $this->setHook(__CLASS__, __FUNCTION__, $_POST, 'MIDDLE');

                // ���������
                $PHPShopAnalitica->init(__FUNCTION__, $this);

                // ����������� ������ ������ �� ������
                if (file_exists("./payment/$path/order.php"))
                    include_once("./payment/$path/order.php");
                elseif ($order_metod < 1000)
                    exit("��� ����� ./payment/$path/order.php");

                // ������ ������ � ��
                $this->orderId = $this->write();

                // ������ �� ����
                if ($path == 'bank')
                    $this->set('account', '//' . $_SERVER['SERVER_NAME'] . '/phpshop/forms/account/forma.html?orderId=' . $this->orderId . '&tip=1&datas=' . $this->datas);

                // ������ �� ������� ������
                if (!empty($disp))
                    $this->set('orderMesage', Parser($disp));

                // ��������� �� E-mail
                $this->mail();

                // SMS ��������������
                $this->sms();

                // PUSH ��������������
                $this->push();

                // ������ �������
                $this->bonus($this->orderId);

                // �������������� ������� �������
                if ($this->cart_clean_enabled)
                    $this->PHPShopCart->clean();

                // ��������� �������� �������
                $PHPShopCartElement = new PHPShopCartElement(true);
                $PHPShopCartElement->init('miniCart');
            }
            else {
                $disp = PHPShopText::alert($this->lang('bad_order_mesage_2'), 'danger');
                $this->set('orderMesage', $disp);
            }
        } else {
            $disp = PHPShopText::alert($this->lang('bad_order_mesage_2'), 'danger');
            $this->set('orderMesage', $disp);
        }

        // ������� ������ � ����� �������
        $this->setHook(__CLASS__, __FUNCTION__, $_POST, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.order_forma_mesage_main'));
    }

    /**
     *  ���������� �������
     */
    function bonus($orderId) {
        $PHPShopBonus = new PHPShopBonus($_SESSION['UsersId']);
        $PHPShopBonus->updateUserBonus($this->bonus_minus, 0);
        $PHPShopBonus->updateBonusLog($orderId, $this->ouid, $this->bonus_minus, 0);
    }

    /**
     * ��� ���
     */
    function bot() {

        // �������� ���� � ������������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $data = $PHPShopOrm->getOne(array('name,bot'), array('id' => '=' . $this->userId));

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.telegram_enabled', 1)) {
            $this->bot = 'telegram';
            $bot = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_bot');
            $this->bot_path = 'https://telegram.me/' . $bot . '?start=' . $data['bot'];
            $this->set('bot', ' ' . __('�') . ' <a href="' . $this->bot_path . '" target="_blank"><img src="http://' . $_SERVER['SERVER_NAME'] . '/phpshop/lib/templates/messenger/' . $this->bot . '.png" width="18" height="18" alt=" " title="' . $this->bot . '"> ' . ucfirst($this->bot) . '</a> ' . __('���'), true);
        }

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_enabled', 1)) {
            $this->bot = 'vk';
            $bot = $this->PHPShopSystem->getSerilizeParam('admoption.vk_bot');
            $this->bot_path = 'https://vk.me/' . $bot . '?ref=' . $data['bot'];
            $this->set('bot', ' ' . __('�') . ' <a href="' . $this->bot_path . '" target="_blank"><img src="http://' . $_SERVER['SERVER_NAME'] . '/phpshop/lib/templates/messenger/' . $this->bot . '.png" width="18" height="18" alt=" " title="' . $this->bot . '"> ' . ucfirst($this->bot) . '</a> ' . __('���'), true);
        }

        // ����������� ������ 
        if ($this->bot) {
            PHPShopObj::loadClass('bot');

            // Telegram
            $chat_id_telegram = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_admin');
            if (!empty($chat_id_telegram) and $this->PHPShopSystem->ifSerilizeParam('admoption.telegram_order', 1)) {

                $bot = new PHPShopTelegramBot();
                $link = '(' . $bot->protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/admin.php?path=order&id=' . $this->orderId . ')';

                $msg = $this->lang('mail_title_adm') . $this->ouid . " - " . $data['name'] . " [" . $this->total . " " . $this->currency . ']' . $link;
                $bot->send($chat_id_telegram, PHPShopString::win_utf8($msg));
            }

            // VK
            $chat_id_vk = $this->PHPShopSystem->getSerilizeParam('admoption.vk_admin');
            if (!empty($chat_id_vk) and $this->PHPShopSystem->ifSerilizeParam('admoption.vk_order', 1)) {

                $bot = new PHPShopVKBot();
                $link = $bot->protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/admin.php?path=order&id=' . $this->orderId;

                $buttons[][] = array(
                    'action' => array(
                        'type' => 'open_link',
                        'link' => $link,
                        'label' => PHPShopString::win_utf8($this->total . " " . $this->currency)
                    )
                );

                $msg = $this->lang('mail_title_adm') . $this->ouid . " - " . $data['name'];
                $bot->send($chat_id_vk, PHPShopString::win_utf8($msg), array('buttons' => $buttons, 'one_time' => false, 'inline' => true));
            }
        }
    }

    /**
     *  ��������� �� �������� ������
     */
    function mail() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        $this->set('sum', $this->currencyMultibase($this->sum));
        $this->set('cart', $this->PHPShopCart->display('mailcartforma', array('currency' => $this->currency, 'rate' => $this->rate)));
        $this->set('currency', $this->currency);
        $this->set('discount', $this->discount);
        $this->set('discount_sum', $this->discount_sum);
        $this->set('deliveryPrice', $this->currencyMultibase($this->delivery));
        $this->set('total', $this->currencyMultibase($this->total));
        $this->set('shop_name', $this->PHPShopSystem->getName());
        $this->set('ouid', $this->ouid);
        $this->set('orderId', $this->orderId);
        $this->set('date', date("d-m-y"));
        $this->set('adr_name', PHPShopSecurity::TotalClean($_POST['adr_name']));
        $this->set('mail', $_POST['mail']);
        $this->set('bonus_minus', $this->bonus_minus);
        $this->set('bonus_plus', $this->bonus_plus);

        // ��� ���
        $this->bot();

        if ($this->PHPShopPayment)
            $this->set('payment', $this->PHPShopPayment->getName());

        $this->set('company', $this->PHPShopSystem->getParam('name'));

        // ��������� ������ ������ ����� ��������.
        if ($this->PHPShopDelivery) {
            $this->set('deliveryCity', $this->PHPShopDelivery->getCity());
            $this->set('adresList', $this->PHPShopDelivery->getAdresListFromOrderData($_POST));
        }

        // �������
        if (!empty($_SESSION['UsersTel']))
            $_POST['tel_new'] = $_SESSION['UsersTel'];
        if (!empty($_POST['tel_new']))
            $this->set('tel', $_POST['tel_new']);

        // ���� �����������, ��� ���� �� ������, ����� �� �����.
        if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId']))
            $this->set('user_name', $_SESSION['UsersName']);
        elseif (!empty($_POST['name_new']))
            $this->set('user_name', $_POST['name_new']);
        else
            $this->set('user_name', $_POST['name_person']);

        // �������������� ���������� �� ������
        if (!empty($_POST['dop_info']))
            $this->set('dop_info', $_POST['dop_info']);

        // ��������� ������ ����������
        $title = $this->lang('mail_title_user_start') . $this->ouid . $this->lang('mail_title_user_end');

        // �������� ������ � �������� �������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'MIDDLE'))
            return true;

        // �������� ������ ����������
        $PHPShopMail = new PHPShopMail($_POST['mail'], $this->adminmail, $title, '', true, true);
        $content = ParseTemplateReturn('./phpshop/lib/templates/order/usermail.tpl', true);
        $PHPShopMail->sendMailNow($content);

        $this->set('shop_admin', "http://" . $_SERVER['SERVER_NAME'] . $this->getValue('dir.dir') . "/phpshop/admpanel/");
        $this->set('time', date("d-m-y H:i a"));
        $this->set('ip', $_SERVER['REMOTE_ADDR']);

        $title_adm = $this->lang('mail_title_adm') . $this->ouid . "/" . date("d-m-y");

        // �������� ������ ��������������
        $PHPShopMail = new PHPShopMail($this->adminmail, $this->adminmail, $title_adm, '', true, true, array('replyto' => $_POST['mail']));

        $content_adm = ParseTemplateReturn('./phpshop/lib/templates/order/adminmail.tpl', true);
        // �������� ������ � ����� �������
        if ($this->setHook(__CLASS__, __FUNCTION__, $content_adm, 'END'))
            return true;


        // �������� ������ ��������������
        $PHPShopMail->sendMailNow($content_adm);
    }

    /**
     * PUSH ����������
     */
    function push() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.push_enabled')) {

            $msg = $this->lang('mail_title_adm') . $this->ouid . " - " . $this->total . " " . $this->currency;

            PHPShopObj::loadClass(array("push"));
            $PHPShopPush = new PHPShopPush();
            $PHPShopPush->send($msg);
        }
    }

    /**
     * SMS ����������
     */
    function sms() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.sms_enabled')) {

            $msg = $this->lang('mail_title_adm') . $this->ouid . " - " . $this->total . " " . $this->currency;
            $phone = $this->getValue('sms.phone');

            include_once($this->getValue('file.sms'));
            SendSMS($msg, $phone);
        }
    }

    /**
     * ���� ������ �������
     * @param float $sum
     */
    function currencyMultibase($sum) {

        if (defined("HostID")) {
            $this->rate = $this->PHPShopSystem->getDefaultValutaKurs(true);
            $sum = $sum * $this->rate;
            $this->currency = $this->PHPShopSystem->getDefaultValutaCode(true);
        } else {
            $this->rate = 1;
        }

        return $sum = number_format($sum, $this->PHPShopOrder->format, '.', ' ');
    }

    /**
     * �������� ������ � ��� [���������/����]
     * @param array $order_id ID ������
     */
    function ofd($order_id) {
        global $_classPath;

        $ofd = 'atol';
        include_once($_classPath . 'modules/' . substr($ofd, 0, 15) . '/api.php');

        if (function_exists('OFDStart')) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = false;
            $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($order_id)), false, array('limit' => '1'));
            OFDStart($data);
        }
    }

    /**
     * ������ ������ � ��
     */
    function write() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        // ������ ���������� // ������ ������
        $person = array(
            "ouid" => $this->ouid,
            "data" => date("U"),
            "time" => date("H:s a"),
            "mail" => PHPShopSecurity::TotalClean($_POST['mail'], 3),
            "name_person" => PHPShopSecurity::TotalClean($this->get('user_name')),
            "dostavka_metod" => (int) $_POST['dostavka_metod'],
            "discount" => $this->discount,
            "user_id" => $this->userId,
            "order_metod" => (int) $_POST['order_metod']);

        // ������ �� �������
        $cart = array(
            "cart" => $this->PHPShopCart->getArray(),
            "num" => $this->num,
            "sum" => $this->sum,
            "weight" => $this->weight,
            "dostavka" => $this->delivery);

        // ���������� ��������
        if ($this->delivery_free)
            $cart['delivery_free'] = true;

        // ������ ������
        $this->status = array(
            "maneger" => $this->manager_comment,
            "time" => "");

        // ��������������� ������ ������
        $this->order = serialize(array("Cart" => $cart, "Person" => $person));

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'MIDDLE'))
            return true;

        // ������ ��� ������
        $insert = $_POST;
        $insert['datas_new'] = $insert['date_new'] = $this->datas = time();
        $insert['uid_new'] = $this->ouid;
        $insert['orders_new'] = $this->order;
        $insert['status_new'] = serialize($this->status);
        $insert['user_new'] = $this->userId;
        $insert['dop_info_new'] = PHPShopSecurity::TotalClean($_POST['dop_info']);
        $insert['sum_new'] = $this->total;

        if (defined('HostID')) {
            $insert['servers_new'] = HostID;
            $insert['admin_new'] = HostAdmin;
        }

        $insert['bonus_minus_new'] = $this->bonus_minus;
        $insert['bonus_plus_new'] = $this->bonus_plus;
        $insert['company_new'] = $this->company;

        // ��������� ������ ��� ������ ������ � ������������ � �������
        if (!class_exists('PHPShopUsers'))
            PHPShopObj::importCore('users');
        $PHPShopUsers = new PHPShopUsers();
        $adresData = $PHPShopUsers->update_user_adres();

        if (empty($insert['tel_new'])) {
            $insert['tel_new'] = $_SESSION['UsersTel'];
        }
        if (empty($insert['fio_new'])) {
            $insert['fio_new'] = $_SESSION['UsersName'];
        }

        if (is_array($adresData))
            $insert = array_merge($insert, $adresData);

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $insert, 'END'))
            return true;

        // ������ ������ � ��
        $result = $this->PHPShopOrm->insert($insert);

        // ��� ����
        //$this->ofd($result);
        // �������� ������ ��� ������ ������
        $this->error_report($result, array("Cart" => $cart, "Person" => $person, 'insert' => $insert));

        return $result;
    }

    /**
     * ����� �������������� �� ������
     * @param mixed $result ��������� ���������� ������ ������ � ��
     * @param array $var ������ ������
     * @return boolean 
     */
    function error_report($result, $var) {

        if (!is_int($result)) {

            // ��������� ������ ��������������
            $title = '������ ������ ������ �' . $this->ouid . ' �� ' . $this->PHPShopSystem->getName() . "/" . date("d-m-y");

            $content = '������� ������ ������: ' . $result . '
����:
';
            ob_start();
            print_r($var);
            $content .= ob_get_clean();

            // �������� ������ � ����� �������
            if ($this->setHook(__CLASS__, __FUNCTION__, $content))
                return true;

            // �������� ������ � ������� ��������������
            new PHPShopMail($this->PHPShopSystem->getParam('adminmail2'), $this->PHPShopSystem->getParam('adminmail2'), $title, $content);
        }
    }

}

/**
 * ������ ������ ������� �������
 */
function mailcartforma($val, $option) {
    global $PHPShopModules, $PHPShopOrder;

    if (empty($val['name']))
        return true;

    // �������� ������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option);
    if ($hook)
        return $hook;

    // �������
    if (!empty($val['parent_uid']))
        $val['uid'] = $val['parent_uid'];

    if (empty($val['ed_izm']))
        $val['ed_izm'] = __('��.');

    $val['price'] *= $option['rate'];

    $price = number_format($val['price'], $PHPShopOrder->format, '.', ' ');
    $price_n = number_format($val['price_n'], $PHPShopOrder->format, '.', ' ');
    $sum = number_format($val['price'] * $val['num'], $PHPShopOrder->format, '.', ' ');


    PHPShopParser::set('product_mail_price', $price);
    PHPShopParser::set('product_mail_price_n', $price_n);
    PHPShopParser::set('product_mail_pic', $val['pic_small']);
    PHPShopParser::set('product_mail_uid', $val['uid']);
    PHPShopParser::set('product_mail_name', $val['name']);
    PHPShopParser::set('product_mail_num', $val['num']);
    PHPShopParser::set('product_mail_sum', $sum);
    PHPShopParser::set('product_mail_ed_izm', $val['ed_izm']);
    PHPShopParser::set('product_mail_currency', $option['currency']);
    PHPShopParser::set('product_mail_id', $val['id']);

    return PHPShopParser::file('./phpshop/lib/templates/order/product_mail.tpl', true, true, true);
}

?>