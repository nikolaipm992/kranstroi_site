<?php

/**
 * Внедрение js функции
 *
 * param object $obj
 * param array $data
 */
function boxberrywidget_delivery_hook($obj, $data) {

    $result = $data[0];

    include_once '../modules/boxberrywidget/class/BoxberryWidget.php';
    $BoxberryWidget = new BoxberryWidget();
    $PHPShopOrder = new PHPShopOrderFunction();

    try {
        if($BoxberryWidget->isCourierDeliveryId((int) $data[1]) && (int) $_POST['zip'] > 0) {
            if((int) $result['free_delivery'] === 1) {
                $result['delivery'] = 0;
            } else {
                $result['delivery'] = $BoxberryWidget->getCourierPrice((int) $_POST['zip'], $_POST['weight'], $_POST['depth'], $_POST['height'], $_POST['width']);
            }
            $result['total'] = $PHPShopOrder->returnSumma((float) $_REQUEST['sum'], $PHPShopOrder->ChekDiscount($_REQUEST['sum']),' ', $result['delivery']);
            $result['message'] = PHPShopString::win_utf8('Стоимость доставки по индексу ' . (int) $_POST['zip'] . ' составит ' . $result['delivery'] . ' руб.');

            return $result;
        }
        if($BoxberryWidget->isPvzDelivery((int) $data[1])) {
            $result['hook'] = 'boxberrywidgetStart();';
            return $result;
        }
    } catch (\Exception $exception) {
        $result['success'] = 'indexError';
        $result['message'] = PHPShopString::win_utf8($exception->getMessage());

        return $result;
    }
}

$addHandler = array('delivery' => 'boxberrywidget_delivery_hook');
?>
