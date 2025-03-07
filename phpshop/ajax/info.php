<?php

session_start();

/**
 * ��������
 * @package PHPShopAjaxElements
 */
$_classPath = "../";

// ����������
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("security");

// ����������� � ��
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();

@$fp = fopen("../../phpshop/inc/config.ini", "r");
if ($fp) {
    $fstat = fstat($fp);
    fclose($fp);
    $FileDate = PHPShopDate::dataV($fstat['mtime']);
}

// ����� �����
function GetFile($dir) {
    global $SysValue;
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            $fstat = explode(".", $file);
            if ($fstat[1] == "lic")
                return $file;
        }
        closedir($dh);
    }
}

// ���� �������� ���. ���������
$GetFile = GetFile("../../license/");
@$License = parse_ini_file_true("../../license/" . $GetFile, 1);

// ������ �������
$Template = parse_ini_file_true("../../phpshop/templates/" . $PHPShopSystem->getParam('skin') . '/php/inc/config.ini', 1);
if (!empty($Template['sys']['version']))
    $Template['sys']['version'] = ' ' . $Template['sys']['version'];
else
    $Template['sys']['version'] = null;

$TechPodUntilUnixTime = $License['License']['SupportExpires'];
if (is_numeric($TechPodUntilUnixTime))
    $TechPodUntil = PHPShopDate::dataV($TechPodUntilUnixTime);
else
    $TechPodUntil = " - ";

$LicenseUntilUnixTime = $License['License']['Expires'];
if (is_numeric($LicenseUntilUnixTime))
    $LicenseUntil = PHPShopDate::dataV($LicenseUntilUnixTime);
else
    $LicenseUntil = " - ";

if ($License['License']['Pro'] == 'Start') {
    $product_name = 'Basic';
} else {
    if ($License['License']['Pro'] == 'Enabled')
        $product_name = 'Pro';
    else
        $product_name = 'Enterprise';
}

if (PHPShopSecurity::true_skin($_COOKIE['bootstrap_theme']))
    $theme = ' + ' . $_COOKIE['bootstrap_theme'] . '.css';
else
    $theme = null;


$version = null;
foreach (str_split($GLOBALS['SysValue']['upload']['version']) as $w)
    $version .= $w . '.';

if (empty($License['License']['DomenLocked']))
    $License['License']['DomenLocked'] = '-';

$shop_type_value = array('��������-�������', '������� ���������', '���� ��������');

$YandexCloudUntilUnixTime = $License['License']['YandexCloud'];
if (is_numeric($YandexCloudUntilUnixTime) and $YandexCloudUntilUnixTime > time())
    $YandexCloudUntil = PHPShopDate::dataV($YandexCloudUntilUnixTime);
else
    $YandexCloudUntil = "-";


$Info = "���������� � ���������
---------------------------------------------

������: PHPShop " . $product_name . "
������: " . substr($version, 0, strlen($version) - 1) . "
������������: " . $shop_type_value[(int) $PHPShopSystem->getParam("shop_type")] . "
������: " . $_SESSION['skin'] . $Template['sys']['version'] . " " . $theme . "
�����������: " . $FileDate . "
��������� ��������: " . $LicenseUntil . "
��������� ���������: " . $TechPodUntil . "
��������� �������� Yandex Cloud: " . $YandexCloudUntil . "
����������� �� �����: " . $License['License']['DomenLocked'] . "

---------------------------------------------

Copyright � PHPShop�, 2004-" . date("Y") . "";

// ��������� ��������� ����� � ���� PHP-�������!
$_RESULT = array(
    "info" => $Info,
    'success' => 1
);

// JSON 
$_RESULT['info'] = PHPShopString::win_utf8($_RESULT['info'], true);
echo json_encode($_RESULT);
?>