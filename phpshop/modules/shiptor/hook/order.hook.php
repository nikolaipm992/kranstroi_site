<?php

include_once dirname(__DIR__) . '/class/Shiptor.php';

function order_shiptor_hook($obj, $row, $rout) {
    global $PHPShopSystem;

    if ($rout === 'MIDDLE') {

        $Shiptor = new Shiptor();

        $methods = unserialize($Shiptor->options['companies']);

        if(empty($Shiptor->options['api_key']) or !is_array($methods) or count($methods) === 0) {
            return;
        }

        $PHPShopCart = new PHPShopCart();
        $weight = (int) $PHPShopCart->getWeight();

        if($weight === 0) {
            $weight = $Shiptor->options['weight'];
        }

        // Яндекс.Карты
        $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
        if (empty($yandex_apikey))
            $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

        PHPShopParser::set('shiptor_dimensions', json_encode($Shiptor->getDimensions($PHPShopCart->getArray())));
        PHPShopParser::set('shiptor_weight', $weight / 1000);
        PHPShopParser::set('shiptor_deliery_id', $Shiptor->options['delivery_id']);
        PHPShopParser::set('shiptor_cod', $Shiptor->options['cod']);
        PHPShopParser::set('shiptor_price', $PHPShopCart->getSum());
        PHPShopParser::set('shiptor_declared_cost', ((int) $PHPShopCart->getSum() * $Shiptor->options['declared_percent']) / 100);
        PHPShopParser::set('shiptor_fee', (int) $Shiptor->options['fee']);
        PHPShopParser::set('shiptor_round', $Shiptor->options['round']);
        PHPShopParser::set('shiptor_courier', implode(',', $methods));
        PHPShopParser::set('shiptor_add_days', $Shiptor->options['add_days']);
        PHPShopParser::set('shiptor_yandex_key', $yandex_apikey);
        PHPShopParser::set('shiptor_api_key', $Shiptor->options['api_key']);

        $obj->set('order_action_add', ParseTemplateReturn($GLOBALS['SysValue']['templates']['shiptor']['shiptor_template'], true) , true);
    }
}

$addHandler = ['order' => 'order_shiptor_hook'];
?>