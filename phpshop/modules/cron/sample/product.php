<?php
/**
 * ������ � ������ ������ � ����� ��������� ����� � ����
 */

// ��������� [true/false]
$enabled = false;

// ��� [1-100]
$day=3;

// 1 - ������� � �����, 2 - ��� �����
$option=2;

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
}
else
    $_classPath = "../../../";

$SysValue = parse_ini_file($_classPath . "inc/config.ini", 1);
$host = $SysValue['connect']['host'];
$dbname = $SysValue['connect']['dbase'];
$uname = $SysValue['connect']['user_db'];
$upass = $SysValue['connect']['pass_db'];

// �����������
if($_GET['s'] == md5($host.$dbname.$uname.$upass))
        $enabled = true;

if (empty($enabled))
    exit("������ �����������!");

$link_db = @mysqli_connect($host, $uname, $upass);
mysqli_select_db($link_db,$dbname);

switch($option){
    case 1:
        $sql="enabled='0'";
        break;
    
    case 2:
        $sql="sklad='1'";
    break;

    default: $sql="enabled='0'";
}

mysqli_query($link_db,"update phpshop_products set $sql where datas<".(time()-(86400*$day)));

echo "���������";
?>