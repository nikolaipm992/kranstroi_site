<?php

/**
 * Доставка YandexDelivery
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/yandexdelivery/class/YandexDelivery.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("lang");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini",true,true);

$YandexDelivery = new YandexDelivery();
$data = new YandexDeliveryOrderData();

$weight = (int) $_POST['weight'];
if(empty($weight))
    $weight=$YandexDelivery->options['weight'];

$data->weight = $weight;
$data->delivery_variant_id = $_POST['delivery_variant_id'];

echo $YandexDelivery->getApproxDeliveryPrice($data);