<?php
/**
 * Добавление js
 * param obj $obj
 * param array $row
 * param string $rout
 */
function order_grastin_hook($obj, $row, $rout) {

    if ($rout == 'MIDDLE') {

        $PHPShopCart = new PHPShopCart();
        $weight = ceil($PHPShopCart->getWeight() / 1000);
       
        $obj->set('order_action_add', '<input type="hidden" name="grastinWeight" value="' . $weight . '"><script type="text/javascript" src="phpshop/modules/grastinwidget/templates/grastin.js" /></script><div id="grastin-container"></div>', true);
    }
}

$addHandler = array
    (
    'order' => 'order_grastin_hook'
);
?>