<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * � ��������� ������
 */
function promotion_fix_array($in) {
    $data = array();
    $data[0] = $in;

    return $data;
}

/**
 * ���������� ���� ������ ���� �������� ��� 0
 */
function promotion_conv_date($date) {
    $arr = explode('-', $date);
    $date_new = $arr[2] . $arr[1] . $arr[0];
    if (is_numeric($date_new)) {
        return intval($date_new);
    } else {
        return 0;
    }
}

/**
 * ��������� ���������� ����������, ���������� 1 - ������� ��� 0 - ���������
 */
function promotion_check_activity($active, $start, $end) {
    // ��-��������� ��������
    $result = 1;
    $now = intval(date('Ymd'));

    // ���� ����� ��������� ������ � �������� ����
    if ($active == 1) {
        $date_start = promotion_conv_date($start);
        $date_end = promotion_conv_date($end);

        if (!empty($date_start) || !empty($date_end)) {
            // ���� ������� ���� ������ ���� ��������� ����������
            if ($now > $date_end && !empty($date_end)) {
                $result = 0;
            }
            // ���� ������� ���� ������ ���� ������ ����������
            if ($now < $date_start) {
                $result = 0;
            }
        }
    }
    // ���������� ��������
    return $result;
}

/**
 * ��������� ������� ������� ���������� � ���������� � ����������
 */
function promotion_check_userstatus($active, $statuses) {
    // ��-��������� 
    $result = true;
    if ($active == 1 && is_array($statuses)) {
        if (isset($_SESSION['UsersStatus'])) {
            $us = $_SESSION['UsersStatus'];
        } else {
            $us = '-';
        }
        $result = in_array($us, $statuses);
    }
    return $result;
}

/**
 * ���������� ���������� � ������� �� ����������� �����������
 * $with_desc - ���������� �������� (��� �������� ������) 
 */
function promotion_get_discount($row, $with_desc = false) {
    global $promotionslist, $promotionslistCode;

    //��������� ������ ���� ������ ����
    if (!isset($promotionslist[0]['code'])) {
        $data[0] = $promotionslist;
    } else {
        $data = $promotionslist;
    }

    $hidePrice = null;

    //������� ���������� �� ������� �� ���������� �� �������� ������
    if ($with_desc) {
        if (!empty($promotionslistCode)) {
            if (!isset($promotionslistCode[0]['code'])) {
                $data[] = $promotionslistCode;
            } else {
                foreach ($promotionslistCode as $pro)
                    $data[] = $pro;
            }
        }
    }

    //if ($with_desc) { print_r($data); die(0); }

    $promo_discount = $promo_discountsum = 0;
    $description = $lab = '';
    $labels = $descriptions = $hidePrices = array();

    if (isset($data)) {
        foreach ($data as $pro) {

            if (empty($pro['active_check']))
                $pro['active_check'] = null;

            if (empty($pro['active_date_ot']))
                $pro['active_date_ot'] = null;

            if (empty($pro['active_date_do']))
                $pro['active_date_do'] = null;

            if (empty($pro['status_check']))
                $pro['status_check'] = null;

            if (empty($pro['statuses']))
                $pro['statuses'] = null;

            if (empty($pro['categories_check']))
                $pro['categories_check'] = null;

            if (empty($pro['products_check']))
                $pro['products_check'] = null;

            // �������� ���������� ����������                
            $date_act = promotion_check_activity($pro['active_check'], $pro['active_date_ot'], $pro['active_date_do']);
            $user_act = promotion_check_userstatus($pro['status_check'], unserialize($pro['statuses']));

            if ($date_act == 1 && $user_act) {
                //������ ��������� ��� ����� ����
                if ($pro['categories_check'] == 1):
                    //��������� ������
                    $category_ar = explode(',', $pro['categories']);
                endif;

                if ($pro['products_check'] == 1):
                    //��������� ������
                    $products_ar = explode(',', $pro['products']);
                endif;

                $sumche = $sumchep = 0;
                // �� ������� ���� ��� �������� ����� �������� ������� ����
                if (empty($row['price_n']) or empty($pro['block_old_price'])) {

                    //������ �� ����� ����������
                    if (isset($category_ar)) {
                        foreach ($category_ar as $val_c) {
                            if ($val_c == $row['category']) {
                                $sumche = 1;
                                break;
                            } else {
                                $sumche = 0;
                            }
                        }
                    }

                    //������ �� ����� �������
                    if (isset($products_ar)) {
                        foreach ($products_ar as $val_p) {
                            if ($val_p == $row['id']) {
                                $sumchep = 1;
                                break;
                            } else {
                                $sumchep = 0;
                            }
                        }
                    }
                }

                //�������� ��������� � ������
                unset($category_ar);
                unset($products_ar);

                if ($sumche == 1 || $sumchep == 1) {
                    //���� �������
                    if ($pro['discount_tip'] == 1) {
                        if ($with_desc && $pro['code_check'] == 1)
                            $discount[] = 0;
                        else {
                            $discount[] = $pro['discount'];
                            $labels[$pro['discount']] = $pro['label'];
                        }
                        if ($with_desc)
                            $descriptions[$pro['id']] = '<div>' . $pro['description'] . '</div>';

                        $hidePrices[$pro['discount']] = $pro['hide_old_price'];
                    }
                    if ($pro['discount_tip'] == 0) {
                        if ($with_desc && $pro['code_check'] == 1)
                            $discountsum[] = 0;
                        else {
                            $discountsum[] = $pro['discount'];
                            $labels[$pro['discount']] = $pro['label'];
                        }
                        if ($with_desc)
                            $descriptions[$pro['id']] = '<div>' . $pro['description'] . '</div>';

                        $hidePrices[$pro['discount']] = $pro['hide_old_price'];
                    }
                }
            }
        }

        //����� ����� ������� ������
        if (isset($discount)) {
            $promo_discount = max($discount) / 100;
            $lab = $labels[$promo_discount * 100];
            $hidePrice = $hidePrices[$promo_discount * 100];
        }

        if (isset($discountsum)) {
            $promo_discountsum = max($discountsum);
            $lab = $labels[$promo_discountsum];
            $hidePrice = $hidePrices[$promo_discountsum];
        }

        if ($with_desc && !empty($descriptions))
            $description = implode('', $descriptions);
        else
            $description = null;
    }

    return array('percent' => $promo_discount, 'sum' => $promo_discountsum, 'label' => $lab, 'description' => $description, 'hidePrice' => $hidePrice);
}

/**
 * ��������� ������� � �������. � ������ ������ ����������� ��� �������� ������, ��� � �������.
 * @param string $products
 */
function getProductsInPromo($products) {
    global $PHPShopSystem;

    $PHPShopOrm = new PHPShopOrm('phpshop_products');
    $parents = $PHPShopOrm->select(array('parent'), array('id' => ' IN ("' . str_replace(',', '","', $products) . '")'), array('order' => 'num,name DESC'), array('limit' => 10000));

    $prnt = array();
    // ������� �� 1�
    if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option')) {
        foreach ($parents as $parent) {
            $row = $PHPShopOrm->select(array('id'), array('uid' => ' IN ("' . str_replace(',', '","', $parent['parent']) . '")'), array('order' => 'num,name DESC'), array('limit' => 100));
            foreach ($row as $parentProduct) {
                $prnt[] = $parentProduct['id'];
            }
        }
    } else {
        if (is_array($parents))
            foreach ($parents as $parent) {
                foreach (explode(',', $parent['parent']) as $value) {
                    $prnt[] = $value;
                }
            }
    }

    return implode(',', array_merge(explode(',', str_replace(' ', '', $products)), $prnt));
}

class AddToTemplateRegionElement extends PHPShopElements {

    var $debug = false;

    function display() {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
        $PHPShopOrm->debug = false;
        $where['code'] = '="' . PHPShopSecurity::TotalClean(trim('*')) . '"';
        $where['enabled'] = '="1"';
        $GLOBALS['promotionslist'] = $promotionslist = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));

        $whereCode['code'] = '!="*"';
        $whereCode['enabled'] = '="1"';
        $GLOBALS['promotionslistCode'] = $promotionslistCode = $PHPShopOrm->select(array('*'), $whereCode, array('order' => 'id'), array('limit' => '300'));
    }

}

$AddToTemplateRegionElement = new AddToTemplateRegionElement();
$AddToTemplateRegionElement->display();
?>