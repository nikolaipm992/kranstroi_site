<?php

PHPShopObj::loadClass(['order','bonus']);
$PHPShopOrder = new PHPShopOrderFunction();

/**
 * ���������� ���������� ������
 * @author PHPShop Software
 * @version 1.6
 * @package PHPShopCore
 */
class PHPShopOrder extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {

        // �������
        $this->debug = false;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['orders'];

        // ������ �������
        $this->action = array("post" => array('id_edit', 'id_delete'), "get" => "cart", 'nav' => 'index');
        parent::__construct();

        // ���-�� ������ � ��������� ������ �_XX, �� ��������� 2
        $format = $this->getValue('my.order_prefix_format');
        if (!empty($format))
            $this->format = $format;
        else
            $this->format = 2;

        PHPShopObj::loadClass('cart');
        $this->PHPShopCart = new PHPShopCart();
    }

    /**
     * ����� �� ���������
     */
    function index() {
        global $PHPShopOrder;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;
        
        // ��� ������
        if($this->PHPShopSystem->getParam("shop_type") > 0)
             return $this->setError404();

        // ������ ������
        $this->import();

        // ���������� lastmodified
        $this->SysValue['cache']['last_modified'] = false;

        // Title
        $this->title = $this->lang('order_title') . ' - ' . $this->PHPShopSystem->getValue("title");

        // ������
        if ($PHPShopOrder->default_valuta_iso == 'RUR' or $PHPShopOrder->default_valuta_iso == "RUB")
            $this->set('currency', 'p');
        else
            $this->set('currency', $PHPShopOrder->default_valuta_code);

        // ���� ���� ������� �������
        if ($this->PHPShopCart->getNum() > 0)
            $this->order();
        else
            $this->error();

        $PHPShopCartElement = new PHPShopCartElement(true);
        $PHPShopCartElement->init('miniCart');
        $this->set('productValutaName', $this->get('currency'));
    }

    /**
     * ����� ������� �������
     */
    function cart() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        $this->PHPShopCart->clean();
        $this->index();
    }

    /**
     * ����� �������� ������ � ������
     */
    function id_delete() {
        global $PHPShopAnalitica;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        // ���������
        $PHPShopAnalitica->init(__FUNCTION__, $_POST);

        $this->PHPShopCart->del($_POST['id_delete']);

        $this->index();
    }

    /**
     * ����� �������������� ������ � ������
     */
    function id_edit() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (PHPShopSecurity::true_num($_POST['num_new']))
            $this->PHPShopCart->edit($_POST['id_edit'], $_POST['num_new'], $_POST['edit_num']);
        $this->index();
    }

    /**
     * ������ ������� � ������
     * @return string
     */
    function product() {
        global $PHPShopOrder;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // ������
        $this->set('currency', $this->PHPShopSystem->getValutaIcon(true));

        $cart = $this->PHPShopCart->display('ordercartforma');
        $this->set('display_cart', $cart);
        $this->set('cart_num', $this->PHPShopCart->getNum());
        $this->set('discount', $PHPShopOrder->ChekDiscount($this->PHPShopCart->getSum(true), $this->PHPShopCart->getArray()));

        $sum_cart = $this->PHPShopCart->getSum(true);
        $sum_discount_off = $this->PHPShopCart->getSumNoDiscount(true);

        // ����� ������ �� �����
        $sum_discount_on = (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumPromo(true));

        // ����� ������ ��� �����
        $sum_discount_on += (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumWithoutPromo(true), $this->get('discount'));

        // ����� � ������ �������
        $sum_discount_on -= (float) (new PHPShopBonus((int) $_SESSION['UsersId']))->getUserBonus($sum_discount_on);

        // ����� ������
        if ($sum_cart > $sum_discount_on)
            $discount_sum = $sum_discount_off - $sum_discount_on;
        elseif ($sum_discount_off > $sum_cart)
            $discount_sum = $sum_discount_off - $sum_cart;
        else
            $discount_sum = 0;

        $this->set('discount_sum', number_format($discount_sum * $this->PHPShopSystem->getDefaultValutaKurs(false), $PHPShopOrder->format, '.', ''));
        $this->set('cart_sum', $sum_cart);
        $this->set('cart_sum_discount_off', number_format($sum_discount_off, $PHPShopOrder->format, '.', ''));
        $this->set('cart_weight', $this->PHPShopCart->getWeight());

        // ��������� ��������
        PHPShopObj::loadClass('delivery');
        $this->set('delivery_price', PHPShopDelivery::getPriceDefault());

        // �������� ���������
        $this->set('total', number_format($sum_discount_off + $this->get('delivery_price') - $discount_sum, $PHPShopOrder->format, '.', ' '));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        if (PHPShopParser::checkFile('order/cart.tpl'))
            return ParseTemplateReturn('order/cart.tpl');
        else
            return ParseTemplateReturn('phpshop/lib/templates/order/cart.tpl', true);
    }

    /**
     * ����� ������ ��������
     * ������� �������� � ��������� ���� /order.core/delivery.php
     * @return mixed
     */
    function delivery() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        return $this->doLoadFunction(__CLASS__, __FUNCTION__, @$_GET['d']);
    }

    /**
     * ��������� �� ������, ������ �������
     */
    function error() {
        $message = '<div class="phpshop-empty-cart">' . $this->message($this->lang('bad_cart_1'), $this->lang('bad_order_mesage_2')) . '</div>';
        $message .= "<script language='JavaScript'>
document.getElementById('num').innerHTML = '0';
document.getElementById('sum').innerHTML = '0';
document.getElementById('order').style.display = 'none';
</script>";
        $this->set('mesageText', $message);
        $this->set('orderMesage', ParseTemplateReturn($this->getValue('templates.order_forma_mesage')));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__);

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.order_forma_mesage_main'));
    }

    /**
     * ���������
     * @param string $title ���������
     * @param string $content ����������
     * @return string
     */
    function message($title, $content) {
        $message = PHPShopText::h4($title, 'text-danger');
        $message .= PHPShopText::message($content, false, false, false, 'text-muted');
        return $message;
    }

    /**
     * ����� ������� ������
     */
    function payment() {
        PHPShopObj::loadClass('payment');

        $where['name'] = '!=""';
        $disp = $showYurDataForPaymentClass = null;

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['name'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopPayment = new PHPShopPaymentArray($where);
        $Payment = $PHPShopPayment->getArray();
        
        if (is_array($Payment))
            foreach ($Payment as $val) {
            
                // ���������� �� �����
                if((!empty($val['sum_max']) and $this->PHPShopCart->getSum(false) > $val['sum_max']) or (!empty($val['sum_min']) and $this->PHPShopCart->getSum(false) < $val['sum_min'])){
                  continue;
                }

                // ���������� �� ������
                if((!empty($val['discount_max']) and $this->get('discount') > $val['discount_max']) or (!empty($val['discount_min']) and $this->get('discount') < $val['discount_min'])){
                  continue;
                }
                  
                if (!empty($val['enabled']) OR $val['path'] == 'modules') {
                    $this->value[$val['id']] = array($val['name'], $val['id'], false);
                    $this->set('paymentIcon', '');
                    if (!empty($val['icon'])) {
                        $this->set('paymentIcon', $val['icon']);
                    }
                    $this->set('paymentId', $val['id']);
                    $this->set('paymentId', $val['id']);
                    $this->set('paymentTitle', $val['name']);

                    if (PHPShopParser::checkFile('order/payment.tpl'))
                        $disp .= ParseTemplateReturn('order/payment.tpl');
                    else
                        $disp .= ParseTemplateReturn('phpshop/lib/templates/order/payment.tpl', true);
                }
                // ��������� ����� ������� ��� ��������� ������� ��� ������ ���. ����� ��. ������ � ����������
                // ���� ��� ������� ���� ������ ��� ��������� 
                if (!empty($val['yur_data_flag'])) {
                    $showYurDataForPaymentClass .= " showYurDataForPaymentClass" . $val['id'];
                }
            }

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $this->value);
        if ($hook)
            return $hook;

        if (!empty($showYurDataForPaymentClass)) {
            $this->set('showYurDataForPaymentClass', $showYurDataForPaymentClass);
            if (PHPShopParser::checkFile('payment/showYurDataForPayment.tpl')) {
                $this->set('showYurDataForPayment', ParseTemplateReturn('payment/showYurDataForPayment.tpl'));
            } else {
                $this->set('showYurDataForPayment', ParseTemplateReturn('phpshop/lib/templates/order/nt/showYurDataForPayment.tpl', true));
            }
        }
        $this->set('orderOplata', PHPShopText::select('order_metod', $this->value, 250, "", false, ""));
        $this->set('orderOplata', $disp);
    }

    /**
     * ����� ������
     */
    function order() {
        // ������ ����� ������ ������ ������
        $this->template_order_forma = $this->getValue('templates.main_order_forma');

        // ������ �������� ���������� ������
        $this->template_order_list = $this->getValue('templates.main_order_list');

        // �������� ������ � ������ �������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // ����� ������
        $this->setNum();

        // �������
        $this->set('orderContentCart', $this->product());
        $this->set('orderNum', $this->order_num);
        $this->set('orderDate', date("d-m-y"));

        // ����� ������ ���������� �� ������
        $cart_min = $this->PHPShopSystem->getSerilizeParam('admoption.cart_minimum');
        if ($cart_min <= $this->PHPShopCart->getSum(false)) {

            // ��������
            $this->delivery();
            // ��������� ������������� ������
            $this->payment();

            // ������ ������������ �� ������� ��������
            if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
                $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
                $this->set('UserMail', $PHPShopUser->getValue('mail'));

                $this->set('UserAdresList', $PHPShopUser->getAdresList());

                $this->set('UserName', $PHPShopUser->getValue('name'));
                $this->set('UserTel', $PHPShopUser->getValue('tel'));
                $this->set('UserTelCode', $PHPShopUser->getValue('tel_code'));
                $this->set('UserAdres', $PHPShopUser->getValue('adres'));
                $this->set('UserComp', $PHPShopUser->getValue('company'));
                $this->set('UserInn', $PHPShopUser->getValue('inn'));
                $this->set('UserKpp', $PHPShopUser->getValue('kpp'));
                $this->set('formaLock', 'readonly=1');
                $this->set('ComStartReg', PHPShopText::comment());
                $this->set('ComEndReg', PHPShopText::comment('>'));

                $this->set('authData', parseTemplateReturn($this->getValue('templates.main_order_forma_auth_data')));
            } else {
                if (PHPShopParser:: checkFile($this->getValue('templates.main_order_forma_no_auth')))
                    $this->set('noAuth', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth')));
                else
                    $this->set('noAuth', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_nt'), true));
                if (PHPShopParser::checkFile($this->getValue('templates.main_order_forma_no_auth_adr')))
                    $this->set('noAuthAdr', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_adr')));
                else
                    $this->set('noAuthAdr', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_adr_nt'), true));
            }

            // �������� ������ � ����� �������
            $this->setHook(__CLASS__, __FUNCTION__, false, 'MIDDLE');

            // ����� ������, ��������, �����, ������
            // ��������� ���� �� ����� ������ �������, ���� ���, ���� ������ �� phpshop/lib/templates/order/
            if (PHPShopParser::check($this->template_order_forma, 'checkLabelForOldTemplatesNoDelete')) {
                $this->set('orderContent', parseTemplateReturn($this->template_order_forma));
            } else {
                $this->temp = true;
                $this->set('orderContent', parseTemplateReturn($this->getValue('templates.main_order_forma_nt'), true));
            }
            // �������� ������ � ����� �������
            $this->setHook(__CLASS__, __FUNCTION__, false, 'MIDDLE-END');
        } else {
            // ����� ���������, ��� ����� ������ ������ �����������.
            $this->set('orderContent', $this->message($this->lang('cart_minimum') . ' ' . $cart_min . ' ' . $this->get('currency'), $this->lang('bad_order_mesage_2')));
        }

        // �������� ������ � ����� �������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        // ���������� ������ �������� ������
        if (empty($this->temp))
            $this->parseTemplate($this->template_order_list);
        else
            $this->parseTemplate($this->getValue('templates.main_order_list_nt'), true);
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

        $this->order_num = $order_num . "-" . $_SESSION['order_prefix'];

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * ������ ������
     * ������� �������� � ��������� ���� /order.core/import.php
     */
    function import() {

        if (!empty($_GET['from']))
            $from = $_GET['from'];
        else
            $from = null;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $from);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $_GET);
    }

}

/**
 * ������ ������ ������� �������
 * �������� ������ �������� ����� ���������� � phpshop/lib/templates/cart/product.tpl
 */
PHPShopObj::loadClass('parser');

function ordercartforma($val, $option) {
    global $PHPShopModules, $PHPShopSystem;

    // �������� ������ � ������ �������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option, 'START');
    if ($hook)
        return $hook;

    // �������� ������� ������, ������ ������ � ����������� �������� ������
    if (empty($val['parent'])) {
        PHPShopParser::set('cart_id', $val['id']);

        // �������
        if (!empty($val['parent_uid']))
            $val['uid'] = $val['parent_uid'];
    } else {
        PHPShopParser::set('cart_id', $val['parent']);
    }

    PHPShopParser::set('cart_pic_small', $val['pic_small']);
    PHPShopParser::set('cart_xid', $option['xid']);
    PHPShopParser::set('cart_name', $val['name']);
    PHPShopParser::set('cart_art', $val['uid']);
    PHPShopParser::set('cart_num', $val['num']);
    PHPShopParser::set('cart_price', number_format($val['price'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' '));
    PHPShopParser::set('cart_price_all', number_format($val['price'] * $val['num'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' '));

    if ((float) $val['price_n'] > 0)
        PHPShopParser::set('cart_price_all_old', number_format($val['price_n'] * $val['num'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' ') . '<span class="rubznak">' . PHPShopParser::get('currency') . '</span>');
    else
        PHPShopParser::set('cart_price_all_old', null);


    PHPShopParser::set('cart_izm', $val['ed_izm']);
    PHPShopParser::set('cart_weight', $val['weight']);

    // �������� ������ � ����� �������
    $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option, 'END');

    if (PHPShopParser::checkFile('order/product.tpl'))
        return ParseTemplateReturn('order/product.tpl');
    else
        return ParseTemplateReturn('./phpshop/lib/templates/order/product.tpl', true);
}

?>