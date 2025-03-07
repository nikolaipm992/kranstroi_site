<?php

include_once dirname(__DIR__) . '/class/Sberbank.php';

/**
 * ������� ���, ��������� ���������� ���������� �������
 * @param object $obj ������ �������
 * @param array $value ������ � ������
 */
function success_mod_sberbankrf_hook($obj, $value) {
    if (isset($_REQUEST['module']) && $_REQUEST['module'] === 'sberbankrf') {

        if($_REQUEST['status'] === 'success'){
            $obj->order_metod = 'modules" and id="10010';

            $mrh_ouid = explode("-", $_REQUEST['uid']);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}
$addHandler = array('index' => 'success_mod_sberbankrf_hook');
?>