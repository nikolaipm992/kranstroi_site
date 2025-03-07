<?php

/**
 * ����� ������ ���������� �� ������ ������������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 * @param Int $tip ���� ������ [1 - ������ �������], [2 - ������ �������� ������]
 */
function action_order_info($obj, $tip) {

    // �������� ������ ������� 
    if ($tip == 1) {
        $order_info = $_GET['order_info'];
        $where = array('uid' => '="' . $order_info . '"');
    }
    // ��-���� �������� ������
    elseif ($tip == 2) {
        $order_info = $_REQUEST['order'];
        $where = array('uid' => '="' . $order_info . '"', 'user' => '=0', 'datas' => '<' . time("U") - ($obj->order_live * 2592000));
    }
    if (PHPShopSecurity::true_order($order_info)) {

        $PHPShopOrm = new PHPShopOrm($obj->getValue('base.orders'));
        $PHPShopOrm->debug = $obj->debug;
        $row = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1));

        // ���������� ������ � �������
        $PHPShopOrderFunction = new PHPShopOrderFunction(false);

        // ������
        $currency = $PHPShopOrderFunction->default_valuta_code;

        // ������� �������
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

        $files = null;

        if (is_array($row)) {

            // ����������� ������
            $PHPShopOrderFunction->import($row);

            // �������� ��-���� ������
            if ($tip == 2) {
                if (PHPShopSecurity::true_email($_REQUEST['mail']))
                    if ($_REQUEST['mail'] != $PHPShopOrderFunction->getMail())
                        return $obj->action_index();
            }

            // ������ �������
            $cart = $PHPShopOrderFunction->cart('usercartforma', array('obj' => $obj, 'currency' => $currency));

            // �������� �������
            if ($obj->PHPShopSystem->getSerilizeParam('admoption.digital_product_enabled') == 1) {
                if ($PHPShopOrderStatusArray->getParam($row['statusi'] . '.sklad_action') == 1 or $row['statusi'] == 101) {
                    $files = PHPShopText::tr(PHPShopText::b( __('�����')), $PHPShopOrderFunction->cart('userfiles', array('obj' => $obj)), '-');
                }
            }

            // ���������
            $title = PHPShopText::div(PHPShopText::notice(__('���������� �� ������ �') . $row['uid'] . __(' �� ') . PHPShopDate::dataV($row['datas'], false)));

            // ��������
            $delivery = $PHPShopOrderFunction->delivery('userdeleveryforma', array('obj' => $obj, 'currency' => $currency, 'row' => $row));

            // ��. ������
            $yurData = $PHPShopOrderFunction->yurData($row);

            // �����
            $total = PHPShopText::tr(PHPShopText::b(__('����� � ������ ������ ') . $PHPShopOrderFunction->getDiscount() . '%'), '', PHPShopText::b($PHPShopOrderFunction->getTotal()) . ' ' . $currency);

            // ����������� �� ������
            if ($PHPShopOrderFunction->getSerilizeParam('status.maneger') != '')
                $comment = PHPShopText::p(PHPShopText::message($PHPShopOrderFunction->getSerilizeParam('status.maneger')));
            else
                $comment = null;

            // ������
            $PHPShopOrderFunction->PHPShopPayment = new PHPShopPayment($PHPShopOrderFunction->order_metod_id);

            // ���������������
            if ($PHPShopOrderFunction->PHPShopPayment->getPath() == 'bank')
                $docs = userorderdoclink($row, $obj);
            else
                $docs = null;

            // ��������������� �����
            $docs .= userorderfiles($row['files'], $obj);

            // �������
            $slide = PHPShopText::slide('Order');
            $slide .= PHPShopText::slide('checkout');
            $table = $slide . $title;

            $editTime = $PHPShopOrderFunction->getStatusTime();
            if (!$editTime)
                $editTime = __("�� ���������");

            // ����� ��������� ������
            $time = PHPShopText::b($PHPShopOrderFunction->getStatus($PHPShopOrderStatusArray), 'color:' . $PHPShopOrderFunction->getStatusColor($PHPShopOrderStatusArray)) .
                    PHPShopText::br() . PHPShopText::b(__('����� ��������� ������:')) . ' ' .
                    $editTime . $comment;
            // ������ ������
            $payment = userorderpaymentlink($obj, $PHPShopOrderFunction, $tip, $row);

            // �������� ��������
            $caption = $obj->caption(__('������ ������'), __('������ ������'));
            $table .= PHPShopText::p(PHPShopText::table($caption . $payment = PHPShopText::tr($time, $payment), 3, 1, 'left', '99%', false, 0, 'allspecwhite', 'list table table-striped table-bordered'));

            // �������� ��������
            if (!empty($yurData)) {
                $caption = $obj->caption(__('������� ��������'), __('����� ��������'), __('����������� ������'));
                $temp = PHPShopText::tr($delivery['name'], $delivery['adres'], $yurData);
            } else {
                $caption = $obj->caption(__('������� ��������'), __('����� ��������'));
                $temp = PHPShopText::tr($delivery['name'], $delivery['adres']);
            }

            $table .= PHPShopText::p(PHPShopText::table($caption . $temp, 3, 1, 'left', '100%', false, 0, '', 'list table table-striped table-bordered'));

            // �������
            if (!empty($row['tracking']))
                $table .= PHPShopText::p(PHPShopText::table($obj->caption(__('��� ������������')) . PHPShopText::tr($row['tracking']), 3, 1, 'left', '99%', false, 0, 'allspecwhite', 'list table table-striped table-bordered'));

            // �������� ��������
            $caption = $obj->caption(__('������������'), __('���-��'), __('�����'));
            $table .= PHPShopText::p(PHPShopText::table($caption . $cart . $delivery['tr'] . $total . $docs . $files, 3, 1, 'left', '99%', false, 0, 'allspecwhite', 'list table table-striped table-bordered'));


            $obj->set('formaContent', $table, true);
        } else
            $obj->action_index();
    }
}

/**
 * �������� �������
 */
function userfiles($val, $option) {
    global $PHPShopModules;

    $dis = null;

    // �������� ������ � ������ �������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, $val, $option, 'START');
    if ($hook)
        return $hook;

    $PHPShopOrm = new PHPShopOrm($option['obj']->getValue('base.products'));
    $row = $PHPShopOrm->select(array('files'), array('id' => '=' . $val['id']), false, array('limit' => 1));
    if (is_array($row)) {
        $files = unserialize($row['files']);
        if (is_array($files)) {
            foreach ($files as $cfile) {

                // �������� ����������
                $extension = pathinfo($cfile['path'])['extension'];

                if ($extension == 'txt') {

                    if (empty($cfile['name']))
                        $cfile['name'] = $content;

                    $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $cfile['path']);
                    $dis .= PHPShopText::a($content, $cfile['name'], false, false, false, '_blank');
                } else {
                    $F = $option['obj']->link_encode($cfile['path']);
                    $link = '../files/filesSave.php?F=' . $F;
                    $dis .= PHPShopText::a($link, urldecode($cfile['name']), urldecode($cfile['name']), false, false, '_blank');
                }

                $dis .= PHPShopText::br();
            }
        }
    }

    return $dis;
}

/**
 * ������� ������� � ������
 */
function usercartforma($val, $option) {
    global $PHPShopModules;

    // �������� ������ � ������ �������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, $val, $option, 'START');
    if ($hook)
        return $hook;

    // �������� ������� ������, ������ ������ �������� ������
    if (empty($val['parent']))
        $link = '/shop/UID_' . $val['id'] . '.html';
    else
        $link = '/shop/UID_' . $val['parent'] . '.html';

    if (!empty($val['pic_small']))
        $img = PHPShopText::img($val['pic_small'], null, 'left', 'width:30px;padding-right:5px');

    $dis = PHPShopText::tr($img . PHPShopText::a($link, $val['name'], $val['name'], false, false, '_blank', 'b'), $val['num'], $val['total'] . ' ' . $option['currency']);
    return $dis;
}

/**
 * ��������
 */
function userdeleveryforma($val, $option) {
    global $PHPShopModules;

    // �������� ������ � ������ �������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, $option, $val, 'START');
    if ($hook)
        return $hook;

    $adres = null;
    $data_fields = unserialize($val['data_fields']);
    if (is_array($data_fields)) {
        $num = $data_fields['num'];
        asort($num);
        $enabled = $data_fields['enabled'];
        foreach ($num as $key => $value) {
            if (!empty($enabled[$key]['enabled']) and $enabled[$key]['enabled'] == 1) {
                $adres .= PHPShopText::b($enabled[$key]['name'] . ": ") . $option['row'][$key] . "<br>";
            }
        }
    }

    if (!$adres)
        $adres = __("�� ���������");

    $dis = PHPShopText::tr(__('��������') . ' - ' . $val['name'], 1, $val['price'] . ' ' . $option['currency']);
    return array('tr' => $dis, 'name' => $val['name'], 'adres' => $adres);
}

/**
 * ���������������
 */
function userorderdoclink($val, $obj) {

    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.1c_docs'));
    $PHPShopOrm->debug = $obj->debug;
    $where['uid'] = '=' . $val['id'];
    $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1000));

    if (is_array($data)) {

        // �������� ��������
        $dis = $obj->caption(__('���������������'), __('����'), __('��������'));
        $n = $val['id'];
        foreach ($data as $row) {

            // �����
            if ($obj->PHPShopSystem->ifValue('1c_load_accounts')) {
                $link_def = '../files/docsSave.php?orderId=' . $n . '&list=accounts&datas=' . $row['datas'];
                $link_html = '../files/docsSave.php?orderId=' . $n . '&list=accounts&tip=html&datas=' . $row['datas'];
                $link_doc = '../files/docsSave.php?orderId=' . $n . '&list=accounts&tip=doc&datas=' . $row['datas'];
                $link_xls = '../files/docsSave.php?orderId=' . $n . '&list=accounts&tip=xls&datas=' . $row['datas'];
                $link_pdf = '../files/docsSave.php?orderId=' . $n . '&list=accounts&tip=pdf&datas=' . $row['datas'];
                $dis .= PHPShopText::tr(PHPShopText::a($link_def, __('���� �� ������'), false, false, false, '_blank', 'b'), PHPShopDate::dataV($row['datas']), PHPShopText::a($link_html, __('HTML'), __('������ Web'), false, false, '_blank', 'b') . ' ' .
                                PHPShopText::a($link_pdf, 'PDF', 'PDF', false, false, '_blank', 'b') . ' ' .
                                PHPShopText::a($link_xls, 'XLS', 'Excel', false, false, '_blank', 'b'));
            }

            // �����-�������
            if (!empty($row['datas_f']) and $obj->PHPShopSystem->ifValue('1c_load_invoice')) {
                $link_def = '../files/docsSave.php?orderId=' . $n . '&list=invoice&datas=' . $row['datas'];
                $link_html = '../files/docsSave.php?orderId=' . $n . '&list=invoice&tip=html&datas=' . $row['datas'];
                $link_doc = '../files/docsSave.php?orderId=' . $n . '&list=invoice&tip=doc&datas=' . $row['datas'];
                $link_xls = '../files/docsSave.php?orderId=' . $n . '&list=invoice&tip=xls&datas=' . $row['datas'];
                $link_pdf = '../files/docsSave.php?orderId=' . $n . '&list=invoice&tip=pdf&datas=' . $row['datas'];
                $dis .= PHPShopText::tr(PHPShopText::a($link_def, __('����-�������'), false, false, false, '_blank', 'b'), PHPShopDate::dataV($row['datas_f']), PHPShopText::a($link_html, 'HTML', 'HTML', false, false, '_blank', 'b') . ' ' .
                                PHPShopText::a($link_pdf, 'PDF', 'PDF', false, false, '_blank', 'b') . ' ' .
                                PHPShopText::a($link_xls, 'XLS', 'Excel', false, false, '_blank', 'b'));
            }
        }

        // �������� ������
        $hook = $obj->setHook(__FUNCTION__, __FUNCTION__, array('row' => $data, 'val' => $val));
        if ($hook) {
            return $hook;
        }

        return $dis;
    }
}

/**
 * ������ �� ������
 */
function userorderpaymentlink($obj, $PHPShopOrderFunction, $tip, $row) {
    global $PHPShopSystem;

    $disp = null;
    $path = $PHPShopOrderFunction->PHPShopPayment->getPath();
    $name = $PHPShopOrderFunction->PHPShopPayment->getName();
    $id = $row['id'];
    $datas = $row['datas'];
    $icon = $PHPShopOrderFunction->PHPShopPayment->getParam('icon');


    if (!empty($icon))
        $icon = PHPShopText::img($icon, 5, 'absmiddle', 'max-height:50px');

    // ������ �� ������
    switch ($path) {

        // ���������
        case("message"):
            $disp .= $icon . PHPShopText::b($name);
            break;

        // ���� � ����
        case("bank"):
            if (!$PHPShopSystem->ifValue('1c_load_accounts')) {

                $disp = PHPShopText::a("phpshop/forms/account/forma.html?orderId=$id&tip=$tip&datas=$datas", $icon . $name, $name, false, false, '_blank', 'b');
            } else {
                $disp .= PHPShopText::b($name) . '.<br>' . __('�������� �����, ����� ���������� ���������<br> �� ������������� �������� � ������ �������<br> ������� ��������.');
            }
            break;

        // ��������� ���������
        case("sberbank"):
            $disp .= PHPShopText::a("phpshop/forms/receipt/forma.html?orderId=$id&tip=$tip&datas=$datas", $icon . $name, $name, false, false, '_blank', 'b');
            break;

        // ��������� ������
        case("modules"):

            // �������� ������
            if ($payment_date = $PHPShopOrderFunction->checkPay()) {
                return '�������� ' . PHPShopDate::get($payment_date);
            } else {
                // �������� ������
                $hook = $obj->setHook(__FUNCTION__, __FUNCTION__, $PHPShopOrderFunction);
                if ($hook) {
                    $disp .= $hook;
                }
            }

            break;


        /*
         * ������� ���������� ������� ���������� [name]_users_repay() �� ����� � ������ ��������� ������� /payment/[name]/users.php
         * ������ ���������� /payment/webmoney/users.php
         */
        default:
            $users_file = './payment/' . $path . '/users.php';
            $users_function = $path . '_users_repay';
            $disp = null;
            if (is_file($users_file)) {
                include_once($users_file);
                if (function_exists($users_function)) {
                    $disp = $icon . call_user_func_array($users_function, array(&$obj, $PHPShopOrderFunction));
                }
            } else
                $disp .= $icon . PHPShopText::b($name);
            break;
    }

    return $disp;
}

/**
 * ����������� ����� � ������
 */
function userorderfiles($val, $obj) {

    $files = unserialize($val);
    $dis = PHPShopText::br();
    $dis .= $obj->caption(__('���������'));

    if (is_array($files)) {
        foreach ($files as $cfile) {

            $dis .= PHPShopText::tr(PHPShopText::a(urldecode($cfile['path']), urldecode($cfile['name']), urldecode($cfile['name']), false, false, '_blank'));
        }

        $table = PHPShopText::p(PHPShopText::table($dis, 3, 1, 'left', '99%', false, 0, 'allspecwhite', 'list table table-striped table-bordered'));
        return $table;
    }
}

?>