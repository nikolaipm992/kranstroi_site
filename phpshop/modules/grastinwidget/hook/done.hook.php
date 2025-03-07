<?php

/**
 * Добавление данных в заказ, регистрация заказа в службе доставки
 * param obj $obj
 * param array $row
 * param string $rout
 */
function send_to_order_grastinwidget_hook($obj, $row, $rout)
{
    include_once 'phpshop/modules/grastinwidget/class/GrastinWidget.php';
    $GrastinWidget = new GrastinWidget();

    if(in_array($_POST['d'], explode(",", $GrastinWidget->option['delivery_id'])))
    {
        if(!empty($_POST['grastinInfo']))
        {
            if ($rout == 'START') {
                $obj->delivery_mod = number_format($_POST['grastinSum'], 0, '.', '');
                $obj->manager_comment = $_POST['grastinInfo'];
                $obj->set('deliveryInfo', $_POST['grastinInfo']);
                $_POST['grastin_order_data_new'] = serialize(array(
                    'pvz_id'     => $_POST['grastinPVZCode'],
                    'partner_id' => $_POST['grastinPartnerId']
                ));
            }

            if ($rout == 'MIDDLE' and $GrastinWidget->option['status'] == 0) {

                $GrastinWidget->setDataFromDoneHook($obj, $row);
                $GrastinWidget->send();
                $_POST['grastin_order_data_new'] = '';
            }
        }
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_grastinwidget_hook'
);
?>