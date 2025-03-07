<?php

function grastinwidgetSend($data) {
    global $_classPath;

    if ($data['statusi'] != $_POST['statusi_new'] or !empty($_POST['grastin_send_now'])) {

        include_once($_classPath . 'modules/grastinwidget/class/GrastinWidget.php');
        $GrastinWidget = new GrastinWidget();



        if(!empty($data['grastin_order_data'])) {
            if ($_POST['statusi_new'] == $GrastinWidget->option['status'] or !empty($_POST['grastin_send_now'])) {

                $GrastinWidget->setDataFromOrderEdit($data);
                $result = $GrastinWidget->send();
               // if($result)
                    //$_POST['grastin_order_data_new'] = '';
            }
        }
    }
}

function addGrastinTab($data) {
    global $PHPShopGUI, $_classPath;

    include_once($_classPath . 'modules/grastinwidget/class/GrastinWidget.php');
    $GrastinWidget = new GrastinWidget();

    $order = unserialize($data['orders']);

	//print_r(unserialize($data['grastin_order_data']));

    if(in_array($order['Person']['dostavka_metod'], explode(",", $GrastinWidget->option['delivery_id']))) {
        if(!empty($data['grastin_order_data'])) {
            $Tab1 = $PHPShopGUI->setField('Синхронизация заказа', $PHPShopGUI->setCheckbox('grastin_send_now', 1, 'Отправить заказ в Grastin сейчас', 0));
            $PHPShopGUI->addTab(array("Grastin", $Tab1, true));
        }
    }
}

$addHandler = array(
    'actionStart'  => 'addGrastinTab',
    'actionDelete' => false,
    'actionUpdate' => 'grastinwidgetSend'
);
?>