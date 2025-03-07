<?php

function footer_copy_hook() {
    $sign = 1575018039;
    if (!empty($GLOBALS['RegTo']['SupportExpires']) and $GLOBALS['RegTo']['SupportExpires'] < $sign){
        echo ('<div class="container"><div class="alert alert-danger alert-dismissible text-center" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Внимание!</strong> Для использования этого шаблона требуется продлить <a href="http://www.phpshop.ru/order/?from=' . $_SERVER['SERVER_NAME'] . '&action=pay_new_template" target="_blank" class="alert-link">техническую поддержку</a>.</div></div></div>');
    }
}

$addHandler = array
    (
    'footer' => 'footer_copy_hook'
);
?>