<?php

function order_mod_kvk_hook($obj, $row, $rout) {
    if ($rout == 'MIDDLE') {
        $kvk_pay = ceil($obj->PHPShopCart->getSum()/19);
        $payments = $obj->get('orderOplata');
        $payments = str_replace('TinkoffCredit', "� ������ �� $kvk_pay ��� � ���", $payments);
        $obj->set('orderOplata', $payments);   
    }
}

$addHandler = array
(
    'order' => 'order_mod_kvk_hook',
);


?>