<?php

/**
 * ������� ����� ������ �� ����� � VK
 */
function uid_mod_wbseller_hook($obj, $row, $rout) {
   
    if ($rout === 'MIDDLE') {

        // ��������� ������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['wbseller']['wbseller_system']);
        $options = $PHPShopOrm->select();
        
        if($options['link'] == 1 and !empty($row['export_wb_id'])){
            $obj->set('wbseller_link','https://www.wildberries.ru/catalog/' . $row['export_wb_id'] . '/detail.aspx');
        }
    }
}

$addHandler = array('UID' => 'uid_mod_wbseller_hook');
?>