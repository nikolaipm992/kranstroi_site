<?php

function shiptor_delivery_hook($obj, $data) {

    $_RESULT = $data[0];

    // API
    include_once '../modules/shiptor/class/Shiptor.php';
    $Shiptor = new Shiptor();

    if ($Shiptor->isShiptorDeliveryMethod($data[1])) {

        $hook['dellist'] = $_RESULT['dellist'];
        $hook['hook'] = 'shiptorStart();';
        $hook['delivery'] = $_RESULT['delivery'];
        $hook['total'] = $_RESULT['total'];
        $hook['adresList'] = $_RESULT['adresList'];
        $hook['free_delivery'] = $_RESULT['free_delivery'];
        $hook['success'] = 1;

        return $hook;
    }
}

$addHandler = ['delivery' => 'shiptor_delivery_hook'];
?>
