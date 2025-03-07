<?php
// ��������� ������
session_start();

$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("file");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", false);

// �������� �� UTF ������� �����
if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
    header('Location: ./index_utf.php');

$version = null;
foreach (str_split($GLOBALS['SysValue']['upload']['version']) as $w)
    $version .= $w . '.';
$brand = 'PHPShop ' . substr($version, 0, 5);

$ok = '<span class="glyphicon glyphicon-ok text-success pull-right"></span>';
$error = '<span class="glyphicon glyphicon-remove text-danger pull-right"></span>';
$alert = 'list-group-item-danger';

// Server
if (stristr($_SERVER['SERVER_SOFTWARE'], 'Apache') or stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') or stristr($_SERVER['SERVER_SOFTWARE'], 'nginx')) {
    $API = $ok;
    $api_style = null;
} else {
    $API = $error;
    $api_style = $alert;
}

// PHP
if (floatval(phpversion()) < 5.6)
    $php = $error;
else
    $php = $ok;

// Mysql
if ($PHPShopBase->connect(false)) {
    $mysql = $ok;
    $mysql_style = null;
    $mysql_break_install = ' data-toggle="modal" ';
} else {
    $mysql = $error;
    $mysql_style = $alert;
    $mysql_break_install = ' data-toggle="error" ';
}

// GD Support
if (function_exists("gd_info"))
    $gd_support = $ok;
else
    $gd_support = $error;

// XML Support
if (function_exists("simplexml_load_string"))
    $xml_support = $ok;
else
    $xml_support = $error;

// ����� ������������� *.sql
function getDump($file) {
    $fstat = explode(".", $file);
    if ($fstat[1] == "sql")
        return $file;
}

// ����� ����������� *.sql
function getDumpUpdate($dir) {
    global $value;
    if (is_dir('update/' . $dir)) {
        $value[] = array($dir, $dir, false);
    }
}

PHPShopObj::loadClass('file');
PHPShopObj::loadClass('text');
$value[] = array('�������...', '', true);
$warning = $done = null;
PHPShopFile::searchFile('./update/', 'getDumpUpdate');

$update_select = PHPShopText::select('version_update', $value, 200, null, false, false);

// ����������
if (!empty($_POST['version_update'])) {
    $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
    $sql_file = 'update/' . $_POST['version_update'] . '/' . PHPShopFile::searchFile('update/' . $_POST['version_update'] . '/', 'getDump');

    if (file_exists($sql_file))
        $content = file_get_contents($sql_file);

    if (!empty($content)) {

        $sqlArray = PHPShopFile::sqlStringToArray($content);
        
        foreach ($sqlArray as $val) {
            if (!mysqli_query($link_db, $val))
                $result .= '<div>' . mysqli_error($link_db) . '</div>';
        }
    }

    $result = mysqli_error($link_db);

    if (empty($result)) {
        $done = '<div class="alert alert-success alert-dismissible" role="alert">
  <strong>�����������</strong>, PHPShop ������� �������� � ������ ' . $_POST['version_update'] . ' �� ' . $brand . '
</div> 
<div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-exclamation-sign"></span> �������� �����������</h3>
                </div>
               <div class="panel-body">
               ���������� ������� ����� <kbd>/install</kbd> ��� ������������ ������ �������.
               </div>
            </div>';
    } else
        $warning = '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>������!</strong> ' . $result . '
</div>';
}
// ��������� ����
elseif (!empty($_POST['password'])) {

    $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true);
    PHPShopObj::loadClass('orm');
    PHPShopObj::loadClass('lang');
    include($_classPath . "lib/phpass/passwordhash.php");

    // ����
    $GLOBALS['PHPShopLang'] = new PHPShopLang();

    if ($sql_file = PHPShopFile::searchFile('./', 'getDump'))
        $fp = file_get_contents($sql_file);

    if (!empty($fp)) {

        // ����������� ����� ��������������
        $content = str_replace("admin@localhost", $_POST['mail'], $fp);

        if ($_POST['shop_type'] == 1)
            $content = str_replace("��������-��������", "��������-��������", $content);

        elseif ($_POST['shop_type'] == 2)
            $content = str_replace("��������-��������", "���-�����", $content);

        $sqlArray = PHPShopFile::sqlStringToArray($content);

        $result = null;
        foreach ($sqlArray as $val) {
            if (!mysqli_query($link_db, $val))
                $result .= '<div>' . mysqli_error($link_db) . '</div>';
        }
    }

    if (empty($result)) {

        $hasher = new PasswordHash(8, false);
        $PHPShopOrm = new PHPShopOrm($PHPShopBase->getParam('base.users'));

        $insert = array(
            'status' => 'a:24:{s:5:"gbook";s:5:"1-1-1";s:4:"news";s:5:"1-1-1";s:5:"order";s:7:"1-1-1-1";s:5:"users";s:7:"1-1-1-1";s:9:"shopusers";s:5:"1-1-1";s:7:"catalog";s:11:"1-1-1-0-0-0";s:6:"report";s:5:"1-1-1";s:4:"page";s:5:"1-1-1";s:4:"menu";s:5:"1-1-1";s:6:"banner";s:5:"1-1-1";s:6:"slider";s:5:"1-1-1";s:5:"links";s:5:"1-1-1";s:3:"csv";s:5:"1-1-1";s:5:"opros";s:5:"1-1-1";s:6:"rating";s:5:"1-1-1";s:8:"exchange";s:5:"1-1-0";s:6:"system";s:3:"1-1";s:8:"discount";s:5:"1-1-1";s:6:"valuta";s:5:"1-1-1";s:8:"delivery";s:5:"1-1-1";s:7:"servers";s:5:"1-1-1";s:10:"rsschanels";s:5:"0-0-0";s:6:"update";i:1;s:7:"modules";s:9:"1-1-1-0-0";}',
            'login' => $_POST['login'],
            'password' => $hasher->HashPassword($_POST['password']),
            'mail' => $_POST['mail'],
            'enabled' => 1,
            'name' => $_POST['user']
        );

        $PHPShopOrm->insert($insert, '');

        // ��� ������
        $PHPShopOrm = new PHPShopOrm($PHPShopBase->getParam('base.system'));
        $data = $PHPShopOrm->select();
        $PHPShopOrm->update(array('shop_type_new' => $_POST['shop_type']), false, '_new');

        // �������� �����
        if (!empty($_POST['send-welcome'])) {

            PHPShopObj::loadClass("parser");
            PHPShopObj::loadClass("mail");
            PHPShopObj::loadClass("system");

            PHPShopParser::set('user_name', $_POST['user']);
            PHPShopParser::set('login', $_POST['login']);
            PHPShopParser::set('password', $_POST['password']);

            $PHPShopSystem = new PHPShopSystem();

            $PHPShopMail = new PHPShopMail($_POST['mail'], $_POST['mail'], "������ �������������� " . $_SERVER['SERVER_NAME'], '', true, true);
            $content_adm = PHPShopParser::file('../phpshop/admpanel/tpl/changepass.mail.tpl', true);

            if (!empty($content_adm)) {
                $PHPShopMail->sendMailNow($content_adm);
            }
        }
        $done = '
            <p>�����������, PHPShop ������� ���������� �� ��� ������. ��� �������� � ������ ���������� ����������� <a href="../phpshop/admpanel/" class="btn btn-primary btn-xs" target="_blank"><span class="glyphicon glyphicon-share-alt"></span>������</a></p>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-ok"></span> ��������� ���������</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">���: ' . $_POST['user'] . '</li>
                    <li class="list-group-item">�����: ' . $_POST['login'] . '</li>
                    <li class="list-group-item">������: ' . $_POST['password'] . '</li>
                    <li class="list-group-item">E-mail: ' . $_POST['mail'] . '</li>
                    <li class="list-group-item">����������: <a href="http://' . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/</a></li>
                </ul>
            </div>
            
 <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-exclamation-sign"></span> �������� �����������</h3>
                </div>
               <div class="panel-body">
               ������� ����� <kbd>/install</kbd> ��� ������������ ������ �������.
               </div>
            </div>
';
    } else {
        $warning = '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>������!</strong> ' . $result . '
</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="windows-1251">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>��������� <?php echo $brand; ?></title>
        <meta name="author" content="PHPShop Software">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="icon" href="/favicon.ico"> 
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="icon" href="/apple-touch-icon.png" type="image/x-icon">
        <style>
            html {
                position: relative;
                min-height: 100%;
            }
            body {
                margin-bottom: 60px;
            }
            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                height: 60px;
                background-color: #f5f5f5;
            }

            .container .text-muted {
                margin: 20px 0;
            }
            a .glyphicon{
                padding-right: 3px;
            }

            .panel{
                margin-top:20px;
            }
            pre,.alert {
                margin-top:10px;
            }
            .modal-body{
                height: 500px;
            }
            #step-2{
                padding-top:30px;
            }
        </style>
    </head>
    <body role="document">
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <div class="container">

            <div class="page-header">
                <ul class="nav nav-pills pull-right hidden-sm hidden-xs <?php if (!empty($_POST['password']) or ! empty($_POST['version_update'])) echo 'hidden'; ?>">
                    <li role="presentation"><a href="#sys">����������</a></li>
                    <li role="presentation"><a href="#inst">���������</a></li>
                    <li role="presentation"><a href="#upd">����������</a></li>
                    <li role="presentation"><a href="#tran">�������</a></li>
                </ul>
                <h1><span class="glyphicon glyphicon-hdd"></span> ��������� <?php echo $brand; ?></h1>
            </div>
            <ol class="breadcrumb">
                <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="/install/">���������</a></li>
                <li class="active"><?php echo $brand; ?></li>
            </ol>

            <?php
            if (!empty($done)) {
                echo $done;
                $system = 'hide';
            } elseif (!empty($warning))
                echo $warning;
            else
                $system = null;
            ?>   
            <p class="<?php echo $system; ?>">   
                ��� ���������� ��� ������ ��������� PHPShop �� �������. ����� ����������, ����������� ������������ ��
                ������� <a class="btn btn-info btn-xs" href="https://phpshop.ru/page/hosting-list.html" target="_blank" title="��������"><span class="glyphicon glyphicon-share-alt"></span> ������������� ���������</a> �� ������������ ��������� ����������� PHPShop.</p>




            <div class="panel panel-info <?php echo $system; ?>" id="sys">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-signal"></span> ������������ ��������� �����������</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item <?php echo $api_style ?>">���-������ <?php echo $API ?>
                    <li class="list-group-item <?php echo $mysql_style ?>">MySQL <?php echo $PHPShopBase->mysql_error . $mysql ?>
                    <li class="list-group-item">PHP<?php echo $php ?>
                    <li class="list-group-item">GD Support ��� PHP <?php echo $gd_support ?>
                    <li class="list-group-item">XML Parser ��� PHP <?php echo $xml_support ?>
                </ul>
            </div>


            <div class="panel panel-default <?php echo $system; ?>" id="inst">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-download-alt"></span> ��������� � ������ ������</h3>
                </div>
                <div class="panel-body">
                    <ol>
                        <li>������������ � ������ ������� ����� FTP-������ (FileZilla, CuteFTP, Total Commander � ��.) ��� ����� �������� �������� �� ��������.
                        <li>��������� ������������� ����� � PHPShop � �������� ���������� ��� ���-���������� (www, public_html � �.�.).
                        <li>�������� ����� ���� MySQL �� ������� ��� ������� ������ ������� � ��� ��������� ���� � ������ ����-����������.
                        <li>�������������� ���� ����� � ����� MySQL <kbd>config.ini</kbd> � ����� <code><?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/inc/</code>. �������� ������ � �������� " " �� ���� ������. ��������� ���� ����� ����� �������� cp1251 (������������� �� ���������) ��� utf-8 (�������������). ��� ������������� utf-8 ����� ������, ��������� ������� ����� ������ ���� utf-8. ��� ���������� ���������� ������� ����� ������������ �������� <code>AddDefaultCharset utf-8</code> � �������� ����� <code>.htaccess</code>. �� ���������, ��������� ���������� �� ��������� ������� windows-1251 � ���� ������ cp1251. �������� ��������� ��������� ������������� ������ ��� ������, �� �������  ������������� �������� (���������, ��������������� � �.�.). ���� ������ � ������������� ��������� cp1251 �������� ������� � �������� ������ �����.

                            <pre>[connect]
host="localhost";   # ��� ����� ���� ������
user_db="user";     # ��� ������������
pass_db="mypas";    # ������ ����
dbase="mybase";     # ��� ����
charset="cp1251";   # ��������� ����</pre>
                        </li>
                        <li>
                            <p>�������������� ���������� <a href="#" class="btn btn-success btn-xs" <?php echo $mysql_break_install; ?>  data-target="#install"><span class="glyphicon glyphicon-download-alt"></span> ������������ ���� ������</a></p>
                            <div class="alert alert-warning" role="alert"><b>��������!</b> ���������� ���� ��������� ����������, � ��������� ������, �� ����� ������ ����� ����. </div>
                        </li>
                        <li>��� ������������ ������� ����� <kbd>/install</kbd>
                        <li>���������� ����� <kbd>CHMOD 777</kbd> (UNIX �������) ��� �����:
                            <pre>
/license
/UserFiles/Image
/UserFiles/Files
/1cManager/upload
/1cManager/goods
/1cManager/sklad
/phpshop/admpanel/csv
/phpshop/admpanel/dumper/backup</pre>

                        <li>��� ����� � <b>���������������� ������</b>, ������� ���������� ������ <kbd>CTRL</kbd> + <kbd>F12</kbd>  ��� �� ������  <a href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/admpanel/">http://<?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/admpanel/</a><br>
                            ������������ � ������ �������� ��� ��������� �������. ��� ��������� ������������ � ������ �������� � ������ ������. �� �������, ��������������� ������ ���������� �� E-mail.

                        <li>���� ����������� ���������� 2-� � ����� ����������� ��������-���������, � ����� ����������� ������. ������ ����������� ��������� ��������� ������������ ������� � ������������, ��������� ���� ��������. ��� ������� ����� ����������, ��������� ��������� ����� ��������� �����:

                            <ol>
                                <li>���������� ������ � ����� ����������, �������� <code>/market/</code>
                                    <div class="alert alert-warning" role="alert"><b>��������!</b> ������������� ������������������ ������ � ������� <em>shop, news, gbook, spec, users</em>  ���������.</div>
                                <li>� ����� ������������ <code>market/phpshop/inc/config.ini</code> ������� ��� ����������, ���� ���������� ������
                                    <pre>[dir]
dir="/market";</pre>
                            </ol>

                            </div>

                            </div>

                            <div class="panel panel-default <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-download"></span> ��������� ����� ��������� ������</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>�������� ����� ���� MySQL �� ����� ������� ��� ������� ������ ������� � ��� ��������� ���� � ������ ����-����������.
                                        <li>�������� � ���������� ����� phpshop6.zip � �������� ���������� �����
                                            <pre>wget https://www.phpshop.ru/loads/files/phpshop6.zip
unzip phpshop6.zip</pre>
                                        <li>��������� ������ ����������� <kbd>install.sh</kbd>, � ��� ������� ������ ������� � ��������� �� ������ ���� ���� MySQL � ��������� ������ ��������������. ����� ��� ������� � ��������� ������ ������� � ����� <kbd>/sh</kbd><pre>
cd sh
sh install.sh</pre>
                                        <li>��� ������������ � ������������� ���������, ��������� ����� ������� � �������� ���������� (������ ��, ��� ��, ������ ��, ����� ������, ������ ������, ����� ������), ������ <code>install.sh localhost phpshop_bd1 phpshop_bd1 dGyEySHRO admin 123456 mail@phpshop.ru</code><pre>
sh install.sh host user_db dbase pass_db admin admin_pass admin_mail</pre>

                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>" id="upd">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-refresh"></span> ���������� � ������ ������</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>�������� ����� ������� ���� ������ ����� ���� <kbd>����</kbd> - <kbd>��������� �����������</kbd>
                                        <li>�������� ����� <code>/old</code> � ���������� � ��� ��� ����� �� �������� ���������� � PHPShop (<em>www, httpdocs, docs, public_html</em>)
                                        <li>��������� � ��������� �������� ���������� ����� �� ������ ����� ������
                                        <li>�� ������� ����������������� ����� <code>/old/phpshop/inc/config.ini</code> �������� ��������� ����������� � ���� ������ (������ 5 �����) � �������� � ����� ���������������� ���� <code>/phpshop/inc/config.ini</code>
                                            <pre>[connect]
host="localhost";   # ��� ����� ���� ������
user_db="user";     # ��� ������������
pass_db="mypas";    # ������ ����
dbase="mybase";     # ��� ����</pre>
                                        <li>��������� <a href="#" class="btn btn-success btn-xs update" <?php echo $mysql_break_install; ?> target="_blank" data-target="#install"><span class="glyphicon glyphicon-refresh"></span> ���������� ���� ������</a>, �������� ���������� ������ (������� ���� ����� �����������), ���� �� ��� ���, �� ��������� ���� �� �����. 
                                        <li>������� ����� <code>/install</code>
                                        <li>���������� ����� <code>/old/UserFiles</code>, <code>/old/license</code> �� ������� ������������� � ��������� � ����������� ������
                                        <li>�� �������������, ���������� ��� ������ ������ <code>/old/phpshop/templates/{���-�������}</code>
                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-download"></span> ���������� ����� ��������� ������</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>��������� ������ ���������� <kbd>update.sh</kbd>. ����� ��� ������� � ��������� ������ ������� � ����� <kbd>/sh</kbd><pre>
cd sh
sh update.sh</pre>
                                        <li>����������� �������� �� ����������
                                        <li>��� ����������, ��������� ��������� ����� ����������� ������ � ���� ������. ��������� ����� �������� � <code>/backup/backups</code>   
                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>" id="tran">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-transfer"></span> ������� ������ � ������� �������</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>�������� ����� ������� ���� ������ �� ������ ������� ����� ���� <kbd>����</kbd> - <kbd>��������� �����������</kbd>
                                        <li>��������� ����� ������������ ������� �� �������� ���-���������� � PHPShop (<em>www, httpdocs, docs, public_html</em>) � �������� ���-���������� �� ����� �������.  ��� ����������� �������� ������ � ������� �� ������, ����� ��������������� �������� <a href="https://ru.wikipedia.org/wiki/PuTTY" target="_blank">PyTTY</a> �  ���������� SSH. ������� �������� ����� ����������� �� ������ ������� (www ���������� �� ��� ����� ����� �������� ���-������):
                                            <pre>tar cvf file.tar ./
gzip file.tar</pre>
                                            ������� �������� ����� ����������� �� ����� �������:

                                            <pre>wget http://���_������/file.tar.gz
tar -zxf file.tar.gz</pre>

                                        <li>������������ �� ������ ������� ����� <kbd>/install</kbd> � ���������� ��, ������ � ��������� � ��� �������, �� ����� ������.
                                        <li>��������� � ���� ������������  <code>/phpshop/inc/config.ini</code> �� ����� ������� ����� ��������� ������� � ���� ������ MySQL.
                                            <pre>[connect]
host="localhost";       # ��� �����
user_db="user";         # ��� ������������
pass_db="mypas";        # ������ ����
dbase="mybase";         # ��� ����</pre>
                                        <li>��������� <a href="#" class="btn btn-success btn-xs" <?php echo $mysql_break_install; ?>  data-target="#install"><span class="glyphicon glyphicon-download-alt"></span> ���������� ���� ������</a>. ��������� ��������� ��� � ����, ������� ������ ������� � ������ ���������� (��������� - ����� ����������, ������ ����� ��������� ������� �������). ����� ����������� �������� ���� ��������.
                                        <li>������� ����� <code>/install</code>
                                        <li>������������� � � ������ ����������  <code>/phpshop/admpanel/</code>, ��������� ����� ��������� ������ �������.
                                        <li>������������ ��������� ����� ���� ����� ���� <kbd>����</kbd> - <kbd>��������� �����������</kbd>. 
                                        <li>������, ��� ����� � ������ ����������, ������� ������� ������ �� ������� �������.
                                    </ol>

                                </div>
                            </div>

                            <div class="panel panel-warning <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-alert"></span> ���� ������</h3>
                                </div>
                                <div class="panel-body">
                                    <ul>
                                        <li><b>101 ������ ����������� � ���� ������</b>
                                            <ol>
                                                <li>��������� ��������� ����������� � ���� ������: <em>host, user_db, pass_db, dbase</em>.
                                                <li>� ����� <code>phpshop/inc/config.ini</code> �������������� ���������� ��� ���� ���� (�������� ������ ����� ���������).<br>
                                                    <pre>[connect]
host="localhost";       # ��� �����
user_db="user";         # ��� ������������
pass_db="mypas";        # ������ ����
dbase="mybase";         # ��� ����</pre>
                                            </ol>
                                        <li><b>102 �� ����������� ����</b>
                                            <ol><li>��������� ��������� ���� ������ <code>/install/</code></ol>
                                        <li><b>105 ������ ������������� ����� /install</b>
                                            <ol>
                                                <li>������� ����� <code>/install</code>
                                            </ol>
                                    </ul>
                                </div>
                            </div>


                            </div>
                            <footer class="footer">
                                <div class="container">
                                    <p class="text-muted text-center">
                                        ������� <a href="https://www.phpshop.ru" target="_blank" title="�����������"><span class="glyphicon glyphicon-home"></span>�����</a> ��� ��������������� <a href="https://help.phpshop.ru" target="_blank" title="����������� ���������"><span class="glyphicon glyphicon-user"></span>����������� ����������</a>
                                    </p>
                                </div>
                            </footer>

                            <!-- Modal  -->
                            <div class="modal" id="install" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form class="form-horizontal" role="form"  method="post" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title">������������ ����������</h4>
                                            </div>
                                            <div class="modal-body">

                                                <!-- �������� -->
                                                <div id="step-1" style="overflow-y:scroll;height: 450px;" class="small"> 
                                                    <p>��������� ������������ ���������� �������� ��������� ������� �� ������� ������ ����������� (������ 317774600500544, ��� 505307534990), ���������� � ���������� ���������������, ������������ - ����������� ��� ������������ ����, � ����� ��������������� ���������������, ���������� � ���������� ������������ � ��������� �� ����� �� ������ <a target="_blank" href="https://www.phpshop.ru/page/license.html">www.phpshop.ru/page/license.html</a> ��� ������������ � �������� ��� ������� ��� ������������ ������������� ����� �� ������������� ������������ ����������� PHPShop. ������������ ���������� ���������� ����������� � ������� ��� ������� �������������. ��� �������� ������������� ���������� ���������� ���� ������ �������������� �� ������������� ����������, ���� ���� ������ ������������� ��������� ��� ��� PHPShop, � ����������� �� ����, ����� ������� �������� ������.</p>
                                                    <h4>1. ������� � �����������</h4>
                                                    <p>1.1. PHPShop, ����� ������� &minus; ��������� ���������������� ������������ &minus; ��������� ��� ��� PHPShop (� ��� ����� �������������� ������ � ���� ������, ����������� ������������ ������������ �������������� ���������� PHPShop), ����������� �� ������� ��������������� � ������������������ � ����������� ������ �� ���������������� ������������� ���������� ��������� (������������� �� ����������� ����������� ��������� ��� ��� "PHPShop" �2006614274 � ������������� � ��������������� ����������� ��������� ����� "PHPShop" �455381).</p>
                                                    <p>1.2. ���-���� - ������ � ���� ��������, ��������� � �������� ������ ������.</p>
                                                    <p>1.3. Web-c����� - ���������, �� ������� ��������������� ���������.</p>
                                                    <p>1.4. ����� - ���������� �����-��������-���������� �����������, ��������������� ��� ������������� ������� � ���� ��������, � �������� ��������� �������� PHPShop.</p>
                                                    <p>1.5. ���������� �������� -  ��������������� ���������������� ������������ �������������� �� ������� ����� �� ������������� PHPShop �� ����� c������, � �������� ������ ������.</p>
                                                    <p>1.6. ������ �������� - ��������������� ���������������� ������������ ������������ �� ������� � � ������������ � �������� ������, ����� �� ������������� PHPShop �� ����� ������.</p>
                                                    <p>1.7. �������� ���� - ������������ ��������������� ���������������� ���������������� ���� ������������� �������� ��� ��� � ����������� �� �����/ ����������� �����, � ������� ���������� �����. ������ ��������� ����� ������������ ����������� �����-������.</p>
                                                    <p>1.8. �����-���� - ������������ ����� ������������� ��������, ��������, ���������� ������� �������� ���������������, ������ �������� ������ � ��������� �������������� �����. ����������� �������� �����-����� ����������� �� ����� <a target="_blank" href="https://www.phpshop.ru/order/">www.phpshop.ru/order/</a>.</p>
                                                    <h4>2. ������� ������������� ����������</h4>
                                                    <p>2.1. ��������������� ������������� ������������ ���������������� ����� ������������� PHPShop �� �������� ������� (����������������) �������� � ��������, ��������������� ������������ �����������, � ������������ ��������� ������� � �������� ��������������� ����� � �������, ������������� ������������ �����������.</p>
                                                    <p>2.2. ��� ��������� ���������� ���������� ���������������� ��� �� ���� ������� � �����, ��� � �� ��� ��������� ����������.</p>
                                                    <p>2.3. ������ ���������� ���� ����� ������������ �� ������������� ����� ����� �������� �� ����� web-������� � �������� ������ ������.</p>
                                                    <p>2.4. ������������ ����� ���������� ���������� ��������, ��� �������� ������ �������� PHPShop �� ����, ������� ��������� � �������� �����.</p>
                                                    <p>2.5. ��� �������������, ������������ ����� ���� ��������� ������� ���� ������, � ����� ��������� ������, ��������������� ���������-������� �� ������������ � �����-������ ���������������.</p>
                                                    <p>2.6. �������� ��������� ������������ ������ ����������� ���������������� �� ����� <a target="_blank" href="https://www.phpshop.ru/page/privacy.html">www.phpshop.ru/page/privacy.html</a></p>
                                                    <p>2.7. ����������� ��������� PHPShop (� ������, ���� ����� ������������� �������� ������ ��� ������������� ������������� ��������) �������������� � ���� ������������ ������������ ����� �������� ������ �� ������� ������������ ��� ����� ��������� PHPShop, ��� ����������� �� ���������� ���������. </p>
                                                    <p>2.8. �������-������ �� ������������ � ���������� �������������� ������ ����������� �� ����� ��������������� <a target="_blank" href="https://www.phpshop.ru/page/dogovor-oferta-support.html">www.phpshop.ru/page/dogovor-oferta-support.html</a></p>

                                                    <h4>3. ��������� �����</h4>
                                                    <p>3.1. ��� ��������� �������������� ����� �� �������, ������� ������������ � �������� �����, ����������� ���������������, �� ��������� ������������ � ��������������� ����������� ��������� ��� ��� "PHPShop" �2006614274 � ������������� � ��������������� ����������� ��������� ����� "PHPShop" �455381.</p>
                                                    <p>3.2. ������� � ����� ��� �� ����������� �������� �������� ���������� ����� � ������� ������� �� "� �������� ������ �������� ��� ����������� �������������� ����� � ��� ������" �� 23 �������� 1992 ����, ������� �� "�� ��������� ����� � ������� ������" �� 9 ���� 1993 ����, � ����� �������������� ����������.</p>
                                                    <p>3.3. � ������ ��������� ��������� ���� ����������������� ��������������� � ������������ � ����������� ����������������� ��.</p>
                                                    <h4>4. ������� ������������� �������� � �����������</h4>
                                                    <p>4.1. ������������ ����� ����� ��������� ��������������� ����-������� ��������, ������ � ����� ��������������� www.phpshop.ru � ��������� �� ������ � ������� 30 ����, � ��� ����������� ������� ��� ��������� �� ��������� ����������. ����-������ PHPShop �������� ��� �����-���� ����������� ����������������, ����� ���������� �������� ������� � ������ ����� � 1� � ������ ��������� PHPShop Pro.</p>
                                                    <p>4.2. ��� ������ ����� ��������� PHPShop �� ������ ����� web-�������, ������ ���� ����������� ��������� ��������. ������� �������� �� ����� ����� �������� ������ ��� ������� �������� ����������� ���������.</p>
                                                    <p>4.3. ��������������� ��������� �� ����� ����� ��������� ���������� �������� ������, � ��������� ���������� ����� �� �����, ��� ������������ �������. ������������� �������� � ���������� ������� ������� ����������, �������� ���������� ������� �� ��������� �����, � ����� �������������� � ������������ � ����������� �����������������. ����� �� ���������� �������� ������ � ��������� ���������� ����� �������� ���������� ���������� � ������������ ������� � �������������� ����������� ��������� ���������������� �� ��� ����� ������������.</p>
                                                    <p>4.4. ��� ������ � ���������� ������ �������� ����������������, ��� ������ �� ��������� ���������. � ����� ���������� ������� ����� � ������������ ��������, �������� ��������� ����� ������ (�������� ���������).</p>
                                                    <p>4.5. ������������ ����� ���������� �������� PHPShop ��� ���������� ���������������, �������� �����-�����, ��������������� �� �����.</p>
                                                    <p>4.6. ����������� ��������� ���������������� ������ �� ���� ����� PHPShop. ����������� ����������� �������� � ������������� ������������� �������������� ����� �������� ������������� � ����� ������������ ��� �������� ��������� � �������� ���, ��� �������, ��� ����� ����� �� ����� �������� ������� �����.</p>
                                                    <p>4.7. ����� ������ ���������� �������� PHPShop, ���������� ��������������� �������� �������� ���� php-���������� �� ����������� ����� index.php, � ������� ���������� �������� �������� � ������ �� �������������������� ��������������� ���������, � ����� ������� ����� � 1�. �������������� ������� EasyControl ��������������� � ���������������� ���� ��� ����������� �������� ��������� � ���. ����� ������ ������ �������� PHPShop, ������������ ��������������� �������� �� ���� � � ������� � �������, ��������� � ������������ � ��������� ������ � �����</p>
                                                    <p>4.8. ������������ ����� ��������, ��������� ��� ������� �������� ����� �������������� PHPShop � ������������ � ����������������� �� �� ��������� �����. ��������� ���������������� ������ ��������� � ������ ��������� ������� ���������� � ������������ � 273 ������� �� ��.</p>
                                                    <h4>5. ��������������� ������</h4>
                                                    <p>5.1. ������������ �� ����� ����������, ���������� ������� ����� ��� ��������������, ������� � ������/������ ������� � ��� ����������, � ��� �����, ��������� �� ���� �������� �����, � ����� �����, � ��� �����, � ���� ��������� ������, �����-���� ������ ��������.</p>
                                                    <p>5.2. ����� ��������������� �������� ��� ���������������� �������� ���������������, ������� ��������������, �������� ���������� ������� ����������, � ������ ��������������� �������� ������������ ����������������.</p>
                                                    <p>5.3. PHPShop ������������ �� �������� "��� ����" ("AS IS") ��� �������������� �������� ������������������, ������������� �����������, ����������� ������, � ����� ���� ���� ���������� ��� �������������� ��������.</p>
                                                    <p>5.4. ��������������� �� ����� �����-���� ��������������� �� ���������� ��� ����������� ���������� ����� ������������, ��� ���������� ��� ��� ������� ���������� ������������� ��� ������������� ������������� PHPShop.</p>
                                                    <p>5.5. ��������������� �� ����� ���������������, ��������� � ������������ ������������ � ���������������� ��� ��������� ��������������� �� ������������� PHPShop � ��������������� ����� (�������, �� �� �������������, �������� ����� PHPShop ��������, ������� �� ������� ��� ������� ���������� �����, ��������������� ��� ���������� ���������� ��� ��������������� ������; � �.�.).</p>
                                                    <p>5.6. ��������������� �� ����� ��������������� �� ����������������� PHPShop, � ������ �������� ������������� ����� �� �� �� ���� ��������� � ��� ���������.</p>
                                                    <p>5.7. ����������� ����� ������������� PHPShop, �������������� ������������ ���������������� ��.</p>
                                                    <p>5.8. �� ��������� ������� ���������� ���������� ��������� ���������������, ��������������� ����������������� ��.</p>
                                                    <p>5.9.��������������� ����� ����� ���������, ��������, ���������, ������������� � ������������ �������������� ����������� ��������.</p>
                                                    <p>5.10.��������������� ������ �� ������ ���������� � ������������� ������� ������� ��������� � ������. �� ��������� ������� ����������� ������ ���������� ��������� ����� ���������� ��������������� ���������� �� �����.</p>

                                                    <h4>6. ������� ����������� ���������</h4>
                                                    <p>6.1.���������� ������ PHPShop Enterprise ��� PHPShop Pro, ������������ �������� ���������� ������� ����������� ��������� � ������� 6 �������. ��� ������ PHPShop Basic ���� ���������� ������������ ���������� 3 ������. 
                                                    </p><p>6.2.����������� ��������� ��������������� ������ � �����������, ����������� ������������, ����� ����� ������ � ��������, ���������� ������ � ����������� �������� PHPShop, ���������� � ������� ������������ ������� �� ���������� ������ PHPShop, �������������� �� ����� ��������������� �� ������ ������ ������������.</p>
                                                    <p>6.3. � ������������ �������� ������������ �� ���������� � ���������� ��������, ��������� ����� � 1� (����������� � ������� � ������������� ������), �� ��������� �������� �� ������, ������� �������, �������������� ��������� �������� �� ������.</p>
                                                    <p>6.4. ����������� ��������� �������������� ����� �������� ������ �� ����� ������ ����������� ��������� <a target="_blank" href="https://help.phpshop.ru/">help.phpshop.ru</a> � ������� ������������ ����� �� ������� ���� (�� ����������� �������� � ��������� ����������� ���� ���������� ���������) � 10 �� 18 ����� ����������� �������.</p>
                                                    <p>6.5. �� ��������� ����� ���������� ����������� ���������, ������������ ����� ���������� ��������� �� �������, ��������� � �����-�����. ���� �������� ����������� ��������� ������������ �� ������� ��� ���� ��� � ������� ������ ���������. ����� ������ ����������� ��������� ������������ �������� ����������� ��������� � ���������� ��� ��������� � ����������, ������� ���� �������� � ������������ �������� �� ������� ������ ��������� ����������� ���������.</p>
                                                    <p>6.6. �������� ��� ����������� �������, �� ���������� � ����������� ���������, �������� �.6.2.� 6.3, �� ����� ���� ������ ���������, ��������� ������� ������� �����������. ��� ��������� � ������� ����� ������� ������� �������� �������������� ����������� ������. ����� �������������� ������ ������������ �� ������� ��������� ������ �� ����������� ������: 3000 ���. �� 1 ��� ������ ��� ������������� � �������� �������������. ����� ��������� �������������� ����� ����� ���� ������������ ���������������� � ����� �����, ��� ������������� ���������� � ���������� ����� �����.</p>
                                                    <p>6.7. ������������ ��������� � ������, ����������� ��� �����, �������������� ��������� � ������� 1 ������.</p>
                                                    <h4>7. �������� �������� �������� �������</h4>
                                                    <p>7.1. � ����� � ���, ��� ����� �������������, ���������������� ������������� ����� ��������� ������������ ��������� PHPShop ������������ ������������, � ������: ���������� ����-������ PHPShop, �������� �. 3.1. ���������� ����������, � �����, �� ������� ����, ��� ��������������� ��� ������� �������� �������������� �������, �� ���������� �������� ���������, ������� �������� ������� ������������ ��������, ���� PHPShop ���� �� ������������� ��������, ������� ������� � ����������� ������������ �� ������: <a href="https://docs.phpshop.ru">docs.phpshop.ru</a></p>
                                                    <p>7.2. ����� ������ ��������������� ��������, ��������������, �� �� ������������ ������������� ��� �������, ����� ��������� � ����������� ������������ � �� ����� ���������������, �� ����� �������� �������� ��� �������� �������� �������.</p>
                                                    <p>7.3. ������� �������� ������� �������������� ��� �������� ������ � ������ ��������, �� ����������� ��������� ������������, �� ������� 30 (��������) ����������� ���� � ������� ������������ PHPShop. � ��������� ���������� �������, ��� ������������ ����������� ��������������� PHPShop, � ����� �������� ���� ��� ���������� ���������� �� ��������������� ������, ������� ��������� � PHPShop.</p>
                                                    <p>7.4. �� ��������� 30 (��������) ���� � ������� ������������ PHPShop, ��������� ���������������� �� ����������� � �������� �������� �� ������������.</p>
                                                    <p>7.5. ������� �������������� � ������� 15 (����������) ����������� ���� � ������� ��������� ����������� ��������� � ������ �������� ���������������� ������� � �������� �������� ������� ������������.</p>
                                                    <p>7.6. ���������� ������� EasyControl, ����� ������� ������������� � 1�, ��������������� �� �������� ������ ��� ����" ("AS IS"), � �� ����� ���� �������� �������� �������� ������� �� PHPShop.</p>
                                                    <h4>8. ��������� � ����������� ����������</h4>
                                                    <p>8.1. � ������ ������������ ������������� ������ �� ������������� ���������, ��������������� ����� ����� � ������������� ������� ����������� ��������� ����������, �������� �� ���� ������������.</p>
                                                    <p>8.2. ��� ����������� ���������� ������������ ������ ���������� ������������� �������� � ������� �������� ���������.</p>
                                                    <p>8.3. ������������ ������ ����������� ������ ���������� � ����� �����, ��������� ������ PHPShop, ��� ����, ����������� ���������� �� ��������� ��������������� ���������� ��������, ����������� ������������� �� ������������ ��������.</p>
                                                    <p>8.4. � ������ ���� ������������ ��� �������� �����-���� ��������� ���������� ���������� �����������������, ���������� ���������� ����������� � ��������� �����.</p>
                                                    <p>8.5. ��������� ������������ ���������� ����� ���������������� �� ��� ����������, ��������������� ������������ � ������ ����������� ���������.</p>
                                                    <h4>9. ���������</h4>
                                                    <p>�������������� ��������������� ������� ����� ����������, 
                                                        <br />������ 317774600500544 <br />��� 505307534990 <br />�/� 40802810202490001590 � �� "�����-����" <br />�/� 30101810200000000593 <br />��� 044525593 <br />���.:+7 (495) 989-11-15; �����: 109431 �. ������, ����������� �������, �.33. �.1</p>
                                                </div>

                                                <!-- ���������� -->
                                                <div id="step-3" class="hide">
                                                    <h4 class="title hide">����������</h4>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label">������</label>
                                                        <div class="col-sm-10">
                                                            <?php echo $update_select; ?>
                                                            <p></p>
                                                            <p class="text-muted"><span class="glyphicon glyphicon-info-sign"></span> ���������� ������� ���� ������� ������ PHPShop (�� ����������). ���� ����� ������ ��� � ������, �� ������� ������ ���� � ����� ������� � ����� � ������� �������. ��������, ���� ������ ������ Enterprise 3.6.6.0.1, �� ������� ������� � ������ 3.6.7.1.3.</p><p class="text-muted">�������� <kbd>CMS</kbd> ���������� ����������� ������. ������ PHPShop 5 � 6 ����� ���������� ��������� ������ � ���� ������������ ������ Basic, Enterprise � Pro.</p><p class="text-muted">��� ���������� ������ PHPShop 3 � ���� ����������� ��������� �������������� ������ �������������� �� email.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ��������� -->
                                                <div id="step-2" class="hide">

                                                    <h4 class="title hide">���������</h4>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">������������</label>
                                                        <div class="col-sm-5">
                                                            <select name="shop_type" size="1" class="form-control">
                                                                <option value="0" selected="">��������-�������</option>
                                                                <option value="1">������� ���������</option>
                                                                <option value="2">���� ��������</option></select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">���</label>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="user" required class="form-control" placeholder="�������������" value="�������������">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">������������</label>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="login" required class="form-control" placeholder="admin" value="admin">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">E-mail</label>
                                                        <div class="col-sm-5">
                                                            <input type="email" name="mail" required class="form-control" placeholder="mail@<?php echo $_SERVER['SERVER_NAME'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">������</label>
                                                        <div class="col-sm-5">
                                                            <input type="password" name="password" required class="form-control" placeholder="������">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-offset-4 col-sm-6">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="send-welcome" checked value="1"> ��������� ��������������� ������ �� E-mail
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-offset-4 col-sm-4">
                                                            <button type="button" class="btn btn-default" id="generator" data-password="<?php echo "P" . substr(md5(time()), 0, 6) ?>"><span class="glyphicon glyphicon-lock"></span> ��������� �������</button>
                                                            <div id="password-message">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">

                                                <span class="pull-left"><label><input type="checkbox" id="licence-ok" checked> � �������� ������� ������������� ����������</label>.</span>

                                                <button type="button" class="btn btn-default btn-sm back hide"><span class="glyphicon glyphicon-arrow-left"></span> �����</button>
                                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> ��������</button>
                                                <button type="button" class="btn btn-primary btn-sm steps" data-step="1" name="install" value="1">����� <span class="glyphicon glyphicon-arrow-right"></span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--/ Modal-->

                            <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
                            <script>

                                $().ready(function () {

                                    // ������ MySQL
                                    $('[data-toggle="error"]').on('click', function (event) {
                                        event.preventDefault();
                                        alert($('.list-group-item-danger').text());
                                    });

                                    // ����������
                                    $('.update').on('click', function () {
                                        $('#step-2 .title').text($('#step-3 .title').text());
                                        $('#step-2').html($('#step-3').html());
                                    });

                                    // �������� � ���������
                                    $('#licence-ok').on('click', function () {
                                        if (!this.checked) {
                                            $('#install').modal('hide');
                                            this.checked = true;
                                        }
                                    });

                                    // ������
                                    $("body").on('click', '.steps', function () {
                                        var step = new Number($(this).attr('data-step'));

                                        switch ($(this).attr('data-step')) {

                                            case "1":
                                                $('#step-1').hide();
                                                $('.back').removeClass('hide');
                                                $('#step-2').removeClass('hide');
                                                $('#step-2').show();
                                                $('.modal-title').text($('#step-2 .title').text());
                                                $('#licence-ok').closest('.pull-left').hide();
                                                break;

                                            case "2":
                                                $(this).attr('type', 'submit');
                                                break;
                                        }

                                        $(this).attr('data-step', step + 1);
                                    });

                                    // �����
                                    $('.back').on('click', function () {
                                        $('.steps').attr('data-step', 1);
                                        $('.modal-title').text($('#step-1 .title').text());
                                        $('#step-1').show();
                                        $('#step-2').hide();
                                        $('#licence-ok').closest('.pull-left').show();
                                        $(this).addClass('hide');
                                        $('.steps').attr('type', 'button');
                                    });


                                    $('#install').on('hidden.bs.modal', function (e) {
                                        location.reload();
                                    });

                                    // ��������� �������
                                    $('#generator').on('click', function () {
                                        var password = $(this).attr('data-password');
                                        $('input[type=password]').val(password);
                                        $('#password-message').html('<div class="alert alert-success" role="alert">��� ������: <b>' + password + '</b></div>');
                                    });

                                    // ���������
                                    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

                                    $('select').addClass('form-control');
                                });
                            </script>

                            </body>
                            </body>
                            </html>