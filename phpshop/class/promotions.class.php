<?php

PHPShopObj::loadClass('order');

/**
 * ���������� ����������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopPromotions {

    /** @var PHPShopCart */
    private $cart;

    /**
     * �����������
     */
    function __construct() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['promotion']);
        $PHPShopOrm->debug = false;
        $where['enabled'] = '="1"';
        $this->promotionslist = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'), array('limit' => 1000), __CLASS__, __FUNCTION__);

        $this->cart = new PHPShopCart();
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
            $date_start = $this->promotion_conv_date($start);
            $date_end = $this->promotion_conv_date($end);

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
    function promotion_check_userstatus($statuses) {

        $result = true;
        if (is_array($statuses) and $statuses[0] != 'null') {

            if (isset($_SESSION['UsersStatus'])) {
                $us = $_SESSION['UsersStatus'];
            } else {
                $us = 0;
            }

            $result = in_array($us, $statuses);
        }
        return $result;
    }

    /**
     * ��������� ������� ���������� � �������
     */
    function promotion_check_cart($num_check, $num = 0) {
        if (!empty($num_check) and $num < $num_check)
            $result = false;
        else
            $result = true;

        return $result;
    }

    /**
     * ���������� ���������� � ������� �� ����������� �����������
     */
    function promotion_get_discount($row) {

        $data = $this->promotionslist;
        $promo_discount = $promo_discountsum = $num_check = $action = 0;
        $lab = $hidePrice = $id = $sum_order_check = null;

        $labels = $ids = $hidePrices = $numChecks = $actions = [];

        if (isset($data)) {
            foreach ($data as $pro) {

                // �������� ���������� ����������
                $date_act = $this->promotion_check_activity($pro['active_check'], $pro['active_date_ot'], $pro['active_date_do']);

                // ��������� ������ ������������
                $user_act = $this->promotion_check_userstatus(unserialize($pro['statuses']));

                if ($date_act == 1 && $user_act) {
                    $sum_order_check = $pro['sum_order_check'];

                    // ������ ���������
                    if ($pro['categories_check'] == 1)
                        $category_ar = array_diff(explode(',', $pro['categories']), ['']);

                    // ������ �������
                    if ($pro['products_check'] == 1)
                        $products_ar = explode(',', $pro['products']);

                    $sumche = $sumchep = 0;

                    // �� ������� ���� ��� �������� ����� �������� ������� ����
                    if (empty($row['price_n']) or empty($pro['block_old_price'])) {
                        if (isset($category_ar)) {
                            foreach ($category_ar as $val_c) {
                                if (
                                        ((int) $val_c === (int) $row['category'] && (int) $pro['disable_categories'] !== 1) ||
                                        ((int) $val_c !== (int) $row['category'] && (int) $pro['disable_categories'] === 1)
                                ) {
                                    $sumche = 1;
                                    break;
                                }
                                $sumche = 0;
                            }
                        }

                        // ������ �� ����� �������
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

                        // �������� ��������� � ������
                        unset($category_ar);
                        unset($products_ar);

                        if ($sumche == 1 || $sumchep == 1) {

                            // ���� �������
                            if ($pro['discount_tip'] == 1) {

                                $discount[] = $pro['discount'];
                                $labels[$pro['discount']] = $pro['label'];
                                $hidePrices[$pro['discount']] = $pro['hide_old_price'];
                            }
                            // ���� ������
                            else {

                                $discountsum[] = $pro['discount'];
                                $labels[$pro['discount']] = $pro['label'];
                                $hidePrices[$pro['discount']] = $pro['hide_old_price'];
                            }

                            $ids[$pro['discount']] = $pro['id'];
                            $numChecks[$pro['discount']] = $pro['num_check'];
                            $actions[$pro['discount']] = $pro['action'];
                        }
                    }
                }
            }

            // ����� ����� ������� ������
            if (isset($discount)) {
                $promo_discount = max($discount) / 100;
                $lab = $labels[$promo_discount * 100];
                $hidePrice = $hidePrices[$promo_discount * 100];
                $id = $ids[$promo_discount * 100];
                $num_check = $numChecks[$promo_discount * 100];
                $action = $actions[$promo_discount * 100];
            }

            if (isset($discountsum)) {
                $promo_discountsum = max($discountsum);
                $lab = $labels[$promo_discountsum];
                $hidePrice = $hidePrices[$promo_discountsum];
                $id = $ids[$promo_discountsum];
                $num_check = $numChecks[$promo_discountsum];
                $action = $actions[$promo_discountsum];
            }
        }

        return [
            'status' => $sum_order_check,
            'percent' => $promo_discount,
            'sum' => $promo_discountsum,
            'label' => $lab,
            'hidePrice' => $hidePrice,
            'num_check' => $num_check,
            'id' => $id,
            'action' => (int) $action
        ];
    }

    /**
     * ����� ���� � ���� ����������
     * @param array $row ������ ������ ������
     * @return array
     */
    function getPrice($row) {
        
        // �������� ���������� � ������� �� ����������� �����������
        $discount_info = $this->promotion_get_discount($row);

        if (isset($this->cart->_CART[$row['id']]) && isset($this->cart->_CART[$row['id']]['promotion_discount'])) {
            unset($this->cart->_CART[$row['id']]['promo_price']);
        }

        // ��������� ���������� � �������
        $isNeedCount = true;
        if ((int) $discount_info['num_check'] > 1) {
            // ����� ������� �� �����
            $isNeedCount = $this->promotion_check_cart((int) $discount_info['num_check'], $this->getCntPromoProdsInCart((int) $discount_info['id']));
        }

        $system = new PHPShopSystem();
        $discount = $discount_info['percent'];
        $discountsum = $discount_info['sum'];
        $status = $discount_info['status'];
        $priceColumn = $system->getPriceColumn();
        if (empty($row[$priceColumn])) {
            $priceColumn = 'price';
        }

        // ���� ���� ������
        if (!empty($discount) || !empty($discountsum)) {

            // ������
            if ($status == 0) {

                $priceDiscount[] = $row[$priceColumn] - ($row[$priceColumn] * $discount);
                $priceDiscount[] = $row[$priceColumn] - $discountsum;
                $priceDiscounItog = min($priceDiscount);
                $priceDiscount = $priceDiscounItog;
            }
            // �������
            else {
                $priceDiscount[] = $row[$priceColumn] + ($row[$priceColumn] * $discount);
                $priceDiscount[] = $row[$priceColumn] + $discountsum;
                $priceDiscounItog = max($priceDiscount);
                $priceDiscount = $priceDiscounItog;
            }
            if ($isNeedCount) {
                if ($discount_info['action'] === 1 && (int) $discount_info['status'] !== 1) {
                    $priceDiscount = $this->applyRegularDiscount($priceDiscount, $row[$priceColumn], $row['id']);
                }

                $productPrice = $priceDiscount;
                $productPriceNew = $priceDiscount < $row[$priceColumn] ? $row[$priceColumn] : $row['price_n'];
            } else {
                $productPrice = $row[$priceColumn];
                $productPriceNew = $row['price_n'];
            }

            // �� ���������� ������ ����
            if ($discount_info['hidePrice'] == 1) {
                $productPriceNew = 0;
            }

            return array('price' => $productPrice, 'price_n' => $productPriceNew, 'label' => $discount_info['label'], 'num_check' => $discount_info['num_check'], 'id' => $discount_info['id']);
        }
    }

    private function getCntPromoProdsInCart($discountId) {
        $num = 0;
        foreach ($this->cart->_CART as $k => $cartProduct) {
            $discount = $this->promotion_get_discount($cartProduct);

            if ((!empty($discount['sum']) or ! empty($discount['percent'])) and (int) $discount['id'] === $discountId) {
                $num += $cartProduct['num'];
            }
        }

        return $num;
    }

    private function applyRegularDiscount($promoPrice, $price, $productId) {
        $order = new PHPShopOrderFunction();
        $regularDiscount = (float) $order->ChekDiscount($this->cart->getSum());
        if ($regularDiscount === 0) {
            return $promoPrice;
        }

        $regularDiscountPrice = $price - ($price * $regularDiscount / 100);

        // ������ �� ������ ������, �� ��������� ����� - ���������� ������������ ���� ������ - ��� �� ����������� ������ �� ������.
        if ($regularDiscountPrice < $promoPrice) {
            return $price;
        }

        // ������ �� ����� ������, ��������� ������ �� ����� � ��������� ������� ��� �� �� ����������� ������.
        if (is_array($this->cart->_CART))
            foreach ($this->cart->_CART as $key => $cartProduct) {
                if ($cartProduct['id'] == $productId) {
                    $this->cart->_CART[$key]['promo_price'] = $promoPrice;
                    $this->cart->_CART[$key]['promotion_discount'] = true;
                }
            }

        return $promoPrice;
    }

}

?>