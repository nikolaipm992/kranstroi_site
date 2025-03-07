<?php

/**
 * C��������� ������� �� ������
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 * @return mixe�
 */
function query_filter($obj) {
    global $SysValue;

    if (!empty($_REQUEST['v']))
        $v = $_REQUEST['v'];
    else
        $v = null;

    $s = intval(@$_REQUEST['s']);
    $f = intval(@$_REQUEST['f']);

    if (!empty($_REQUEST['set']))
        $set = intval($_REQUEST['set']);
    else
        $set = 2;


    if (!empty($_REQUEST['p']))
        $p = intval($_REQUEST['p']);
    else
        $p = 1;

    $num_row = $obj->num_row;
    $num_ot = 0;
    $q = 0;
    $sort = $sortQuery = null;

    // ������ ������
    $filter_logic = (int) $obj->PHPShopSystem->getSerilizeParam('admoption.filter_logic');

    switch ($filter_logic) {

        case 0:
            $filter_sort = 'and';
            $filter_sort_search = 'and';
            break;

        case 1:
            $filter_sort = 'or';
            $filter_sort_search = 'and';
            break;

        case 2:
            $filter_sort = $filter_sort_search = 'or';
            break;
    }

    // ���������� �� ���������������
    if (is_array($v)) {
        $sort .= ' and (';
        foreach ($v as $key => $value) {

            // ������������� ����� [][]
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($v)) {
                        $hash = $key . "-" . $v;
                        $sort .= " vendor REGEXP 'i" . $hash . "i' " . $filter_sort;
                        $sortQuery .= "&v[$key][]=$value";
                    }
                }
            }

            // ������� ����� []
            elseif (PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($value)) {
                $hash = $key . "-" . $value;
                $sort .= " vendor REGEXP 'i" . $hash . "i' " . $filter_sort;
                $sortQuery .= "&v[$key]=$value";
            }
        }
        $sort = substr($sort, 0, strlen($sort) - strlen($filter_sort));
        $sort .= ")";
    }


    // ���������� �������������� �������������
    switch ($f) {
        case(1): $order_direction = "";
            break;
        case(2): $order_direction = " desc";
            break;
        default: $order_direction = " desc";
            break;
    }
    switch ($s) {
        case(1): $order = array('order' => 'name' . $order_direction);
            break;
        case(2): $order = array('order' => 'price' . $order_direction);
            break;
        case(3): $order = array('order' => 'num' . $order_direction);
            break;
        default: $order = array('order' => 'num, name' . $order_direction);
    }

    // ����������� ������ ������ ���������� � ������
    foreach ($order as $key => $val)
        $string = $key . ' by ' . $val;

    // ��� ��������
    if ($p == "all") {
        $sql = "select * from " . $SysValue['base']['products'] . " where enabled='1' and parent_enabled='0' $sort  $string";
    } else
        while ($q < $p) {

            $sql = "select * from " . $SysValue['base']['products'] . " where enabled='1' and parent_enabled='0' $sort  $string LIMIT $num_ot, $num_row";
            $q++;
            $num_ot = $num_ot + $num_row;
        }

    $obj->selection_order = array(
        'sortQuery' => $sortQuery,
        'sortV' => $sort
    );

    return $sql;
}

?>