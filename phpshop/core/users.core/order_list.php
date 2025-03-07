<?php

/**
 * ����� ������� ������������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 * @param Int $tip ���� ������ [1 - ������ �������], [2 - ������ �������� ������]
 * @param int $uid  �� ������
 */
function order_list($obj, $tip, $uid = null) {
    $tr = null;

    // ����� ������� ��������
    if ($tip == 1)
        $where = array('user' => '=' . $obj->UsersId);
    // ����� �������� ������
    elseif ($tip == 2 and !empty($uid))
        $where = array('uid' => '="' . htmlspecialchars($uid) . '"');

    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.orders'));
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'datas desc'), array('limit' => 100));

    // ���������� ������ � �������
    $PHPShopOrderFunction = new PHPShopOrderFunction(false);

    // ������
    $currency = $PHPShopOrderFunction->default_valuta_code;

    // ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

    if (is_array($data))
        foreach ($data as $row) {

            // ����������� ������
            $PHPShopOrderFunction->import($row);

            if ($tip == 1)
                $link = "?order_info=" . $row['uid'] . "#Order";
            else
                $link = "/users/register.html";

            $td1 = PHPShopText::a($link, $row['uid'], $obj->lang('order_info') . $row['uid'], false, false, false, 'text-primary');
            $td2 = PHPShopDate::dataV($row['datas']);
            $td3 = $PHPShopOrderFunction->getNum();
            $td4 = '' . $PHPShopOrderFunction->getDiscount();
            $td5 = $PHPShopOrderFunction->getTotal() . ' ' . $currency;
            $td6 = PHPShopText::b($PHPShopOrderFunction->getStatus($PHPShopOrderStatusArray), 'color:' . $PHPShopOrderFunction->getStatusColor($PHPShopOrderStatusArray));

            $tr.=$obj->tr($td1, $td2, $td3, $td4, $td5, $td6);
        }

    $caption = $obj->caption($obj->lang('order_table_title_1'), $obj->lang('order_table_title_2'), $obj->lang('order_table_title_3'), $obj->lang('order_table_title_4'), $obj->lang('order_table_title_5'), $obj->lang('order_table_title_6'));

    if (!empty($tr))
        $table = PHPShopText::table($caption . $tr, 3, 1, 'center', '100%', false, 0, 'order-list', 'list table table-striped table-bordered table-hover');
    else
        $table = __("� ��� ��� ��� �� ������ ������.");

    $obj->set('formaTitle', $obj->lang('user_order_title'));
    $obj->set('formaContent', $table);
}

?>