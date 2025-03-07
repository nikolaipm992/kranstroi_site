<?php
/**
 * �������� CSV ��� ������� ����� ����� PHPShop.Cron
 * ��� ��������� ��������� �������� enabled �� true
 */

// ��������� ��� SSH Cron
$enabled=false;

$_classPath="../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang"));
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini",true,true);

// �����������
if($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'].$PHPShopBase->SysValue['connect']['dbase'].$PHPShopBase->SysValue['connect']['user_db'].$PHPShopBase->SysValue['connect']['pass_db']))
        $enabled = true;

if (empty($enabled))
    exit("������ �����������!");

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));
mb_internal_encoding($GLOBALS['PHPShopBase']->codBase);

// �������� GUI
$PHPShopGUI = new PHPShopGUI();
$PHPShopInterface = new PHPShopInterface();

// ��������� CSV
include($_classPath . "admpanel/exchange/admin_export.php");

$_POST['exchanges'] = intval($_GET['id']);
$_REQUEST['file'] = $_GET['file'];
actionSave();
echo __('����������').' '.$GLOBALS['csv_export_count'].' '.__('�����');
?>