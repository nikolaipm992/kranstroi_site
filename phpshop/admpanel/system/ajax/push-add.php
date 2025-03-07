<?php
if (!empty($_POST['token'])) {
    $_classPath = '../../../../';
    include($_classPath . 'phpshop/class/obj.class.php');
    PHPShopObj::loadClass(array("base", "orm","push","system"));
    $PHPShopBase = new PHPShopBase($_classPath . "phpshop/inc/config.ini", true, true);
    $PHPShopSystem = new PHPShopSystem();

    $PHPShopBase->chekAdmin();
    
    // Подписка на PUSH
    $PHPShopPush = new PHPShopPush();
    $PHPShopPush->add($_POST['token']);
}
?>