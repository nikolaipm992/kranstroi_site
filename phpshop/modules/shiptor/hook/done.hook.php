<?php

include_once dirname(__DIR__) . '/class/Shiptor.php';

function send_to_order_shiptor_hook($obj, $row, $rout)
{
    $Shiptor = new Shiptor();

    if($Shiptor->isShiptorDeliveryMethod($_POST['d'])) {
        if(!empty($_POST['shiptorSum'])) {
            if ($rout === 'START') {
                $obj->delivery_mod = number_format($_POST['shiptorSum'], 0, '.', '');
                $obj->set('deliveryInfo', $_POST['shiptorInfo']);
                $obj->manager_comment = $_POST['shiptorInfo'];

                $_POST['shiptor_order_data_new'] = serialize(array(
                    'type'      => $_POST['shiptorType'],
                    'method_id' => $_POST['shiptorMethodId'],
                    'pvz_id'    => $_POST['shiptorType'] === 'pvz' ? $_POST['shiptorPVZId'] : '',
                    'kladr'     => $_POST['shiptorKladr'],
                    'info'      => $_POST['shiptorInfo'],
                    'status'    => Shiptor::STATUS_NEW
                ));
            }
        }
    }
}

$addHandler = array('send_to_order' => 'send_to_order_shiptor_hook');
?>