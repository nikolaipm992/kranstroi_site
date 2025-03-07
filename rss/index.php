<?php

/**
 * ��������� RSS ������ ��������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopUtil
 */
// ���������� ����������
$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("modules");

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/");


// ���� ������ SEOURL
if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
    $seourl_enabled = true;
}
else
    $seourl_enabled = false;

// �������� ������ RSS
$xml = '<?xml version="1.0" encoding="windows-1251" ?>
<rss version="2.0">
<channel>
<title>RSS ������� - ' . $PHPShopSystem->getParam('title') . '</title>
<description>RSS ������� �� ' . $PHPShopSystem->getParam('company') . '</description>
<link>http://' . $_SERVER['SERVER_NAME'] . '</link>
<language>ru</language>
<generator>PHPShop</generator>';

// SQL ������
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
$data = $PHPShopOrm->select(array('id', 'datas', 'zag', 'kratko'), false, array('order' => 'id DESC'), array('limit' => 15));

$seourl = null;
if (is_array($data))
    foreach ($data as $row) {

        if ($seourl_enabled)
            $seourl = '_' . PHPShopString::toLatin($row['zag']);

        $xml.='<item>
    <title>' . trim($row['zag']) . '</title>
    <link>http://' . $_SERVER['SERVER_NAME'] . '/news/ID_' . $row["id"] . $seourl . '.html</link>
    <pubDate>' . trim($row['datas']) . '</pubDate>
    <description><![CDATA[' . trim($row['kratko']) . ']]></description>
    <author>' . $PHPShopSystem->getName() . '</author>
    </item>';
    }
$xml.='</channel>
</rss>';

// ����� XML
echo $xml;
?>