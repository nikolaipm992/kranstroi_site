<?php

/**
 * ���� ������
 */
function UID_promotions_hook($obj, $row, $rout) {

    if ($rout == "MIDDLE") {
        
        // �������� ���������� � ������� �� ����������� �����������
        $discount_info = promotion_get_discount($row, true); 
        $obj->set('promotionInfo', $discount_info['description']);
    }
}



$addHandler = array
    (
    'UID' => 'UID_promotions_hook',
);
?>