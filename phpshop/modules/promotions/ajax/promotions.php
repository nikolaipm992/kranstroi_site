<?php

/**
 * ����������
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("bonus");

$_REQUEST['promocode'] = PHPShopString::utf8_win1251($_REQUEST['promocode']);
$_REQUEST['sum'] = PHPShopString::utf8_win1251($_REQUEST['sum']);
$_REQUEST['ssum'] = PHPShopString::utf8_win1251($_REQUEST['ssum']);
$_REQUEST['tipoplcheck'] = PHPShopString::utf8_win1251($_REQUEST['tipoplcheck']);
$_REQUEST['wsum'] = PHPShopString::utf8_win1251($_REQUEST['wsum']);

// ���������� ���������� ��������
require_once $_classPath . "core/order.core/delivery.php";

// ������� ��� ������
$PHPShopOrder = new PHPShopOrderFunction();

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();

// �������������� ������� �� promotion/inc
require_once($_classPath . 'modules/promotions/inc/promotionselement.inc.php');

$currency = $PHPShopSystem->getDefaultValutaCode();

// ��������� ����� 10 ������
if (trim(mb_strlen($_REQUEST['promocode']) == 10)) {
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_codes"));
    $PHPShopOrm->debug = false;
    $where['code'] = '="' . PHPShopSecurity::TotalClean(trim($_REQUEST['promocode'])) . '"';
    $where['enabled'] = '="1"';
    $data_code = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));
    $PHPShopOrm->clean();
    unset($where);
}
if (!empty($data_code['promo_id'])) {

    $_SESSION['multicodes'] = 1; // ���� ���������� ������� �����

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
    $PHPShopOrm->debug = false;
    $where['id'] = '="' . $data_code['promo_id'] . '"';
    $where['enabled'] = '="1"';
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));

    $PHPShopOrm->clean();
    unset($where);
}
// ������� ��������
else {
    $_SESSION['multicodes'] = 0;
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
    $PHPShopOrm->debug = false;
    $where['code'] = '="' . PHPShopSecurity::TotalClean(trim($_REQUEST['promocode'])) . '"';
    $where['enabled'] = '="1"';
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));
    $PHPShopOrm->clean();
    unset($where);
}

if (!empty($data_code['code']))
    $data['code'] = $data_code['code'];


//���� ����� ��� ����������
if ($_REQUEST['promocode'] != '*') {

    // ���� ��� ��������
    if ($data['code'] != ''):
        
        //���� �� ������
        if ($data['discount_check'] == 1):
            $data['products'] = getProductsInPromo($data['products']);
        
            //��������� ��������� ���� ������
            if ($_REQUEST['tipoplcheck'] != $data['delivery_method'] and $data['delivery_method_check'] == 1) {

                //������ ��� ������
                $sq_pay = 'select name from ' . $SysValue['base']['payment_systems'] . ' where id=' . $data['delivery_method'];
                $qu_pay = mysqli_query($link_db, $sq_pay);
                $ro_pay = mysqli_fetch_array($qu_pay);

                $messageinfo = '<b style="color:#7e7a13;">�� �������� ��� ������!</b><br> ��� ������� �����-���� ��� ������ ����� ���� ������ <b>' . $ro_pay['name'] . '</b>. �������� ���� ��� ������ � ������� ����� ������ �� ��� ���������� ������';
                $action = '1'; //�������� ��������������� �� ������ �����
                $status = '0'; //�� ��������� ������
            } else {

                //�������� ���������� �� ����
                $date_act = promotion_check_activity($data['active_check'], $data['active_date_ot'], $data['active_date_do']);
                $user_act = promotion_check_userstatus($data['status_check'], unserialize($data['statuses']));

                if ($date_act == 1 && $user_act) {

                    //������ ��������� ��� ����� ����
                    if ($data['categories_check'] == 1):
                        //��������� ������
                        $category_ar = explode(',', $data['categories']);
                    endif;

                    if ($data['products_check'] == 1):
                        //��������� ������
                        $products_ar = explode(',', $data['products']);
                    endif;
                    
                    
                    // ������� ������ ����� � �������
                    if($data['discount_tip'] == 0){
                        
                       // ����� �������
                       foreach ($_SESSION['cart'] as $valuecart) {
                           $sum += $valuecart['num'] * $valuecart['price'];
                       }
                       
                       if($sum < 0)
                           $sum=0;
                       
                       $data['discount']=round($data['discount']*100/$sum,2);
                       
                       if($data['discount'] > 100)
                           $data['discount']=100;
                       
                       if($data['discount'] > 0)
                           $data['discount_tip']=1;
                    }
                    
                    foreach ($_SESSION['cart'] as $rs => $valuecart) {

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                        $row = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($valuecart['id'])), array('order' => 'id desc'), array('limit' => 1));

                        $sumche = $sumchep = 0;

                        // �� ������� ���� ��� �������� ����� �������� ������� ����
                        if (empty($row['price_n']) or empty($data['block_old_price'])) {

                            //������ �� ����� ���������� ����� ������ �� �������
                            if (isset($category_ar)) {
                                foreach ($category_ar as $val_c) {
                                    if ($val_c == $row['category']) {
                                        $sumche = 1;
                                        $info_prod_d_f .= $row['name'] . ', ';
                                        break;
                                    } else {
                                        $sumche = 0;
                                    }
                                }
                            }

                            //������ �� ����� ������� ����� ������ �� �������
                            if (isset($products_ar)) {
                                foreach ($products_ar as $val_p) {
                                    if ($val_p == $row['id']) {
                                        $sumchep = 1;
                                        $info_prod_d_f .= $row['name'] . ', ';
                                        break;
                                    } else {
                                        $sumchep = 0;
                                    }
                                }
                            }
                        }

                        if (($sumche == 1 or $sumchep == 1)):
                            $sumnew += $valuecart['price'] * $valuecart['num'];
                        
                          
                            //���� �������
                            if ($data['discount_tip'] == 1) {
                                // ������ � ���
                                $discount = $data['discount'];
                                $tip_disc = '%';
                                $idgg = intval($valuecart['id']);
                                if ($idgg >= 1) {
                                    // ������ � ������ (���-�� | ���)
                                    $_SESSION['cart'][$rs]['discount'] = $discount;
                                    $_SESSION['cart'][$rs]['discount_tip'] = $tip_disc;
                                    $_SESSION['cart'][$rs]['test'] = 1;
                                    $_SESSION['cart'][$rs]['promo_price'] = $valuecart['price'];
                                }
                            } 
                            // ������ �� ������������, ��� ����������� � ������� �� ��� ������
                            else { //���� �����
                                //������ � ���
                                $discount_sum = $data['discount'];
                                $tip_disc = $currency;
                                $idgg = intval($valuecart['id']);
                                if ($idgg >= 1) {

                                    // ������ � ������ (���-�� | ���)
                                    $_SESSION['cart'][$rs]['promo_sum'] = $discount_sum;
                                    $_SESSION['cart'][$rs]['discount_tip_sum'] = $tip_disc;
                                    $_SESSION['cart'][$rs]['promo_code'] = $_REQUEST['promocode'];
                                    $_SESSION['cart'][$rs]['test'] = 2;
                                    $_SESSION['cart'][$rs]['promo_price'] = $valuecart['price'];
                                }
                            }
                        else:
                            $sumoldi += $valuecart['price'] * $valuecart['num'];
                        endif;
                    }
                    //���������� � ������� � ������� ��������� ������
                    if ($info_prod_d_f != '') {
                        $info_prod_d = '<hr><b>������ ��������� ��� �������:</b> ' . substr($info_prod_d_f, 0, strlen($info_prod_d_f) - 2);
                    }

                    //���� �������
                    if ($data['discount_tip'] == 1) {
                        //������� ������
                        $discount = $data['discount'] / 100;
                        //����� �� ������� ���������� ������
                        $sumtot_new = $sumnew - ($sumnew * $discount);
                        //����� ��� ������
                        $sumtot_old = $sumoldi;
                        //��� ������
                        $tip_disc = '%';
                        //���������� � �������
                        $discountAll = $data['discount'] . ' ' . $tip_disc;
                        //������ � ������
                        $_SESSION['discpromo'] = $data['discount'];
                        $_SESSION['tip_disc'] = 1;
                    } else { //���� �����
                    
                        //����� ������
                        $discount_sum = $data['discount'];
                        
                        //����� �� ������� ���������� ������
                        $sumtot_new = $sumnew - $discount_sum;
                        
                        //���� ����� ����� ���� � �����, �� ������ ����
                        if ($sumtot_new < 0) {
                            $sumtot_new = 0;
                        }
                        //����� ��� ������
                        $sumtot_old = $sumoldi;
                        //��� ������
                        $tip_disc = $currency;
                        //���������� � �������
                        $discountAll = $data['discount'] . ' ' . $tip_disc;
                        $_SESSION['discpromo'] = $data['discount'];
                        $_SESSION['tip_disc'] = 0;
                    }

                    
                    //���� �� ��������� ������
                    if (isset($sumtot_new)):
     
                        //����� �����
                        $totalsumma_t = $sumtot_new + $sumtot_old;
                        //��������� ����� ��
                        if ($data['sum_order_check'] == 1):
                            if ($totalsumma_t >= $data['sum_order']) {
                                $sumordercheck = 1;
                            } else {
                                $sumordercheck = 0;
                            }
                        else:
                            $sumordercheck = 1; //������ �������� ����� �� ���� ������� � ���������� �� �����������
                        endif;

                        //��������� ���������� ��������
                        if ($data['free_delivery'] == 1):
                            $freedelivery = 0;
                            $_SESSION['freedelivery'] = 0;
                            $delivery = 0;
                        else:
                            //������� ������ ��� ���������� ��������
                            $_SESSION['freedelivery'] = 1;
                            if ($_REQUEST['dostavka'] > 0)
                                $delivery = intval($_REQUEST['dostavka']);
                        endif;

                        if ($sumordercheck == 1):
                            $status = '1'; //������ ���������
                            //������� ������
                            if ($data['delivery_method_check'] == 1) {
                                $delivery_method_check = $data['delivery_method'];

                                //$totalsummainput = $sumtot_new + $sumtot_old;
                            } else {
                                $delivery_method_check = 0;
                            }
                            $totalsumma = $sumtot_new + $sumtot_old;

                            if ($_REQUEST['sum'] > $totalsumma) {

                                $totalsummainput = $sumtot_new + $sumtot_old;
                                $_SESSION['totalsumma'] = $totalsumma;
                                $_SESSION['promocode'] = $data['code'];
                                $_SESSION['codetip'] = $data['code_tip'];


                                //������� ���� ��� ������
                                foreach ($_SESSION['cart'] as $is => $valcar) {

                                    // �������� ��� ����� � done.hook.php
                                    $_SESSION['cart'][$is]['promo_percent'] = $valcar['discount'];
                                    $_SESSION['cart'][$is]['promo_code'] = $_REQUEST['promocode'];

                                    //������� ���� � ������
                                    unset($_SESSION['cart'][$is]['discount']);
                                    unset($_SESSION['cart'][$is]['discount_tip']);
                                    unset($_SESSION['cart'][$is]['id_sys']);
                                }
                                $messageinfo = '<b>����������� � �������������!</b><br> ����� ��� ������ �����! ���� ������ ' . $data['discount'] . ' ' . $tip_disc . $info_cat_d . $info_prod_d;
                            } else {
                                $totalsumma = $_REQUEST['sum'];
                                $totalsummainput = $_REQUEST['sum'];
                            }
                        else:
                            $messageinfo = '<b>�� ���������!</b><br> ����� ��� ������ �����.<br> �� ����� ������ ������ ���� �� ' . $data['sum_order'] . ' ' . $currency;
                            $status = '0'; //������ �� ���������
                            $_SESSION['totalsumma'] = '0';
                        endif;
                    else:
                        $messageinfo = '<b>�� ���������!</b><br> ����� ��� ������ �����.<br> �� �� ���� �� ������� � ����� ������� �� ��������� � �����';
                        $status = '0'; //������ �� ���������
                        $_SESSION['totalsumma'] = '0';
                    endif;
                } else { //���� ���� �� �������
                    $messageinfo = '<b>�� ���������!</b><br> ����� ��� ������ �����.<br> �� ���� �������� ����� ��������';
                    $status = '0'; //������ �� ���������
                    $_SESSION['totalsumma'] = '0';
                }
            }
        else:
            $messageinfo = '<b>������!</b><br> ������ ��� ��������� �� �����������, ��������� � ���� ��� ��������� ����������!';
            $status = '0'; //�� ��������� ������
            $_SESSION['totalsumma'] = '0';
        endif;
    else:
        $messageinfo = '<b>������!</b><br> ������� �����-���� � ���� ������ �� ����������!';
        $status = '0'; //�� ��������� ������
    endif;

    //������� ������ ������ ��� JS
    $numc = 3; //��� ��������� ������� �������
    if (is_array($_SESSION['cart']))
        foreach ($_SESSION['cart'] as $cartjs) {
            $discountcart[$cartjs['id']]['n'] = $numc;
            $numc++;
        }
} else {
    unset($_SESSION['discpromo']);
    unset($_SESSION['freedelivery']);
    unset($_SESSION['tip_disc']);
    unset($_SESSION['totalsumma']);
    unset($_SESSION['promocode']);
    unset($_SESSION['codetip']);
    unset($_SESSION['discpromo']);

    // ������ ������ ��� ������������ ��������
    if (is_array($_SESSION['cart']))
        foreach ($_SESSION['cart'] as $k => $cart) {
            unset($_SESSION['cart'][$k]['promo_sum']);
            unset($_SESSION['cart'][$k]['promo_code']);
            unset($_SESSION['cart'][$k]['discount_tip_sum']);
            unset($_SESSION['cart'][$k]['promo_percent']);
            if (!isset($_SESSION['cart'][$k]['promotion_discount']) && !isset($_SESSION['cart'][$k]['order_discount_disabled'])) {
                unset($_SESSION['cart'][$k]['promo_price']);
            }
        }
}

$PHPShopCart = new PHPShopCart();

$promoSum = 0;

foreach ($PHPShopCart->getArray() as $product) {
    
    // ����� � ���������
    if (!empty($product['promo_sum'])) {
        $promoSum += $product['num'] * number_format($product['price'] - $product['promo_sum'] / $product['num'], $PHPShopOrder->format, '.', '');
    }
    // ������� �� ����� � ���������
    else if (!empty($product['promo_percent'])) {
        $promoSum += $product['num'] * number_format($product['price'] - ($product['price'] * $product['promo_percent'] / 100), $PHPShopOrder->format, '.', '');
    }
    // ���������� � ����������
    elseif(!empty($product['promo_price'])) {
        $promoSum += $product['price'];
    }
}

// ����������
$totalsummainput = number_format($totalsummainput, $PHPShopSystem->format, '.', '');

// ����� ������ �� �����
if ($promoSum > 0){
    
    $total = $promoSum;
    
    // ����� ������ ��� ����������, ��������� ������ ������� ������������\����� ������
    $total += (float) $PHPShopOrder->returnSumma($PHPShopCart->getSumWithoutPromo(true), $PHPShopOrder->ChekDiscount($totalsumma), '', (float) $delivery);

    // ����� � ������ �������
    $total -= (float) (new PHPShopBonus((int) $_SESSION['UsersId']))->getUserBonus($total);
    
    // �������
    if (strstr($discountAll, '%')) {
        $discount = ($data['discount'] * $_REQUEST['sum'] / 100);
        $discount = "- " . number_format($_REQUEST['ssum'] + $discount, $PHPShopSystem->format, '.', '');
    } else
        $discount = "- " . $_REQUEST['ssum'] + $data['discount'];
}
else{
    $total = $totalsummainput;
    $discount=0;
}



// ���������
$_RESULT = array(
    'delivery' => $delivery,
    'total' => number_format($total, $PHPShopSystem->format, '.', ' '),
    'discount' => $discount,
    'discountall' => $discountAll,
    'mes' => $messageinfo,
    'action' => $action,
    'status' => $status,
    'freedelivery' => $freedelivery,
    'totalsummainput' => $totalsummainput,
    'deliverymethodcheck' => $delivery_method_check,
    'success' => 1
);

// ����� ������� ��� ����� ��������
$_SESSION['totalsummainput'] = $totalsummainput;

// JSON 
if ($_REQUEST['type'] == 'json') {
    $_RESULT['mes'] = PHPShopString::win_utf8($_RESULT['mes']);
    $_RESULT['discountall'] = PHPShopString::win_utf8($_RESULT['discountall']);
    $_RESULT['mesclean'] = strip_tags($_RESULT['mes']);
}
echo json_encode($_RESULT);
?>