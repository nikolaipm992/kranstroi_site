<?php

function Compile_seourlpro_hook($obj, $data, $rout) {

    if ($rout == 'END') {
        $GLOBALS['PHPShopSeoPro']->Compile($obj);
        return true;
    }
}

function setError404_seourlpro_hook($obj) {
    if (!defined("HostID") and ! defined("HostMain")) {
        $url = $obj->PHPShopNav->getName(true);
        preg_match("/([0-9]{2,8})/", $url, $match);
        $PHPShopProduct = new PHPShopProduct($match[0]);
        if ($PHPShopProduct->getName() != '' and $PHPShopProduct->getParam('enabled') != 0) {
            header('Location: /shop/UID_' . $match[0] . '.html', true, 301);
            return true;
        }
    }
}

$addHandler = array
    (
    'Compile' => 'Compile_seourlpro_hook',
    '#setError404' => 'setError404_seourlpro_hook'
);
?>