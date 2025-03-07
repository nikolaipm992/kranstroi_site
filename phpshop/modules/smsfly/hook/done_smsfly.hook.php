<?php

function sms_mod_smsfly_hook($obj) {

    // ��������� ������
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopSmsfly = new PHPShopSmsfly();

    // ���������
    $msg = $obj->lang('mail_title_adm') . $obj->ouid . " - " . $obj->sum . ' '.$obj->currency;
    $PHPShopSmsfly->send($msg);
    
    return true;
}

$addHandler = array
    (
    'sms' => 'sms_mod_smsfly_hook'
);
?>