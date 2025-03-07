<?php

/**
 * Внедрение js функции
 *
 * param object $obj
 * param array $data
 */
function cdekwidget_delivery_hook($obj, $data) {

    $_RESULT = $data[0];
    $xid = $data[1];

    // API
    include_once '../modules/cdekwidget/class/CDEKWidget.php';
    $CDEKWidget = new CDEKWidget();

    if (in_array($xid, @explode(",",$CDEKWidget->option['delivery_id']))) {

        $hook['dellist'] = $_RESULT['dellist'];
        $hook['hook'] = 'cdekwidgetStart();';
        $hook['delivery'] = $_RESULT['delivery'];
        $hook['total'] = $_RESULT['total'];
        $hook['adresList'] = $_RESULT['adresList'];
        $hook['free_delivery'] = $_RESULT['free_delivery'];
        $hook['success'] = 1;

        return $hook;
    }
}

$addHandler = array('delivery' => 'cdekwidget_delivery_hook');
?>
