<?php

function footer_copy_hook() {
    $sign = 1657715157;
    if (!empty($GLOBALS['RegTo']['SupportExpires']) and $GLOBALS['RegTo']['SupportExpires'] < $sign){
        echo ('<div class="container"><div class="alert alert-danger alert-dismissible text-center" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <span class="fa fa-exclamation-triangle"></span> <strong>��������!</strong> ��� ������������� ����� ������� ��������� �������� <a href="http://www.phpshop.ru/order/?from=' . $_SERVER['SERVER_NAME'] . '&action=pay_new_template" target="_blank" class="alert-link">����������� ���������</a>.</div></div>');
    }
}

$addHandler = array
    (
    'footer' => 'footer_copy_hook'
);
?>