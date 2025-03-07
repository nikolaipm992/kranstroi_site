<?php

session_start();

$_classPath = "../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();
$PHPShopSystem = new PHPShopSystem();

include_once 'class/VkSeller.php';
$VkSeller = new VkSeller();

if (empty($_GET['code']) and ! empty($_GET['client_id'])) {
    if ($_GET['client_id'] == $VkSeller->client_id)
        $VkSeller->getCode();
}
elseif (!empty($_GET['code'])) {
    $result = $VkSeller->getToken($_GET['code']);
    $token = $result['access_token'];
    if (!empty($token))
        $text = '<h3>API key</h3><textarea style="width:500px;height:100px">' . $token . '</textarea>';
    else
        $text = '<h3>' . $result['error'] . '</h3>' . $result['error_description'];

    echo '<p><center>' . $text . '</center></p>';
}
