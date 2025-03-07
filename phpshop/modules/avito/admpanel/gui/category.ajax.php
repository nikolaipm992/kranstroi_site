<?php

session_start();

$_classPath = "../../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/avito/class/Avito.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");

if(isset($_REQUEST['xml_price_id']) && (int) $_REQUEST['xml_price_id'] > 0) {
    $result = [];
    try {
        $categories = Avito::getAvitoCategories((int) $_REQUEST['xml_price_id']);
        $result['data'] = array();
        foreach ($categories as $key => $category) {
            $result['data'][$category[1]] = PHPShopString::win_utf8($category[0]);
        }
        $result['success'] = true;
    } catch (\Exception $exception) {
        $result = array('success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage()));
    }
} else {
    $result = array('success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр categoryId'));
}

echo (json_encode($result)); exit;