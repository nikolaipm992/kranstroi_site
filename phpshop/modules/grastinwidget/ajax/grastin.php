<?php
session_start();
$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/grastinwidget/class/GrastinWidget.php");
include_once($_classPath . "class/string.class.php");
include_once($_classPath . "class/cart.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("parser");

$GrastinWidget = new GrastinWidget();

$widget = $GrastinWidget->renderWidget((float) $_REQUEST['weight']);

header('Content-type: text/html; charset=windows-1251');
exit($widget);
