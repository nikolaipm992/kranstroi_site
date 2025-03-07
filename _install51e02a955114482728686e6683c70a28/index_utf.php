<?php
$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("file");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", false);

// Редирект на Windows-1251 верссию файла
if ($GLOBALS['PHPShopBase']->codBase != 'utf-8')
    header('Location: ./index.php');

$version = null;
foreach (str_split($GLOBALS['SysValue']['upload']['version']) as $w)
    $version .= $w . '.';
$brand = 'PHPShop ' . substr($version, 0, 5).' Unicode';

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

// Поиск установочного *.sql
function getDump($file) {
    $fstat = explode(".", $file);
    if ($fstat[1] == "sql")
        return $file;
}

// Поиск обновояемых *.sql
function getDumpUpdate($dir) {
    global $value;
    if (is_dir('update/' . $dir)) {
        $value[] = array($dir, $dir, false);
    }
}

PHPShopObj::loadClass('file');
PHPShopObj::loadClass('text');
$value[] = array('Выбрать...', '', true);
$warning = $done = null;
PHPShopFile::searchFile('./update/', 'getDumpUpdate');

$update_select = PHPShopText::select('version_update', $value, 200, null, false, false);

// Обновление
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
  <strong>Поздравляем</strong>, PHPShop успешно обновлен с версии ' . $_POST['version_update'] . ' до ' . $brand . '
</div> 
<div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-exclamation-sign"></span> Удаление установщика</h3>
                </div>
               <div class="panel-body">
               Необходимо удалить папку <kbd>/install</kbd> для безопасности Вашего сервера.
               </div>
            </div>';
    } else
        $warning = '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Ошибка!</strong> ' . $result . '
</div>';
}
// Установка базы
elseif (!empty($_POST['password'])) {

    $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true);
    PHPShopObj::loadClass('orm');
    PHPShopObj::loadClass('lang');
    include($_classPath . "lib/phpass/passwordhash.php");

    // Язык
    $GLOBALS['PHPShopLang'] = new PHPShopLang();

    if ($sql_file = PHPShopFile::searchFile('./', 'getDump'))
        $fp = file_get_contents($sql_file);

    if (!empty($fp)) {

        // Кодировка UTF
        $fp = str_replace("CHARSET=cp1251", "CHARSET=utf8", $fp);
		$content = PHPShopString::win_utf8($fp,true);

        // Подстановка почты администратора
        $content = str_replace("admin@localhost", $_POST['mail'], $content);

		if ($_POST['shop_type'] == 1)
            $content = str_replace("интернет-магазина", "интернет-каталога", $content);

        elseif ($_POST['shop_type'] == 2)
            $content = str_replace("интернет-магазина", "веб-сайта", $content);
        
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

        // UTF
        $PHPShopOrm = new PHPShopOrm($PHPShopBase->getParam('base.system'));
		$data = $PHPShopOrm->select();
        $admoption = unserialize($data['admoption']);
		$admoption['lang_adm']=$admoption['lang']='russian_utf';
		$admoption['dadata_enabled']=0;

        $bank = unserialize($data['bank']);
		if(is_array($bank))
			foreach($bank as $key =>$val)
			  $bank[$key] = null;

		$PHPShopOrm->update(array('admoption_new'=>serialize($admoption),'bank_new'=>serialize($bank),'shop_type_new' => $_POST['shop_type']), false,'_new');
		$PHPShopOrm->query('TRUNCATE `phpshop_orders`');

        // Отправка почты
        if (!empty($_POST['send-welcome'])) {

            PHPShopObj::loadClass("parser");
            PHPShopObj::loadClass("mail");
            PHPShopObj::loadClass("system");

            PHPShopParser::set('user_name', __($_POST['user']));
            PHPShopParser::set('login', $_POST['login']);
            PHPShopParser::set('password', $_POST['password']);
			PHPShopParser::set('shopName', __('Установка завершена'));

            $PHPShopSystem = new PHPShopSystem();

            $PHPShopMail = new PHPShopMail($_POST['mail'], $_POST['mail'], "Пароль администратора " . $_SERVER['SERVER_NAME'], '', true, true);
            $content_adm = PHPShopParser::file('../phpshop/admpanel/tpl/changepass.mail.tpl', true);

            if (!empty($content_adm)) {
                $PHPShopMail->sendMailNow($content_adm);
            }
        }
        $done = '
            <p>Поздравляем, PHPShop успешно установлен на Ваш сервер. Для перехода в панель управления, воспользуйтесь <a href="../phpshop/admpanel/" class="btn btn-primary btn-xs" target="_blank"><span class="glyphicon glyphicon-share-alt"></span>ссылкой</a></p>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-ok"></span> Установка завершена</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">Имя: ' . $_POST['user'] . '</li>
                    <li class="list-group-item">Логин: ' . $_POST['login'] . '</li>
                    <li class="list-group-item">Пароль: ' . $_POST['password'] . '</li>
                    <li class="list-group-item">E-mail: ' . $_POST['mail'] . '</li>
                    <li class="list-group-item">Управление: <a href="http://' . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/</a></li>
                </ul>
            </div>
            
 <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-exclamation-sign"></span> Удаление установщика</h3>
                </div>
               <div class="panel-body">
               Удалите папку <kbd>/install</kbd> для безопасности Вашего сервера.
               </div>
            </div>
';
    } else {
        $warning = '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Ошибка!</strong> ' . $result . '
</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Установка <?php echo $brand; ?></title>
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
                <ul class="nav nav-pills pull-right hidden-sm hidden-xs">
                    <li role="presentation"><a href="#sys">Требования</a></li>
                    <li role="presentation"><a href="#inst">Установка</a></li>
                    <li role="presentation"><a href="#upd">Обновление</a></li>
                    <li role="presentation"><a href="#tran">Перенос</a></li>
                </ul>
                <h1><span class="glyphicon glyphicon-hdd"></span> Установка <?php echo $brand; ?></h1>
            </div>
            <ol class="breadcrumb">
                <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="/install/">Установка</a></li>
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
                Ниже приведена инструкция для ручной установки PHPShop на хостинг. Перед установкой, рекомендуем ознакомиться со
                списком <a class="btn btn-info btn-xs" href="http://phpshop.ru/page/hosting-list.html" target="_blank" title="Хостинги"><span class="glyphicon glyphicon-share-alt"></span> рекомендуемых хостингов</a> на соответствие системным требованиям PHPShop.</p>

            <p class="<?php echo $system; ?>">Если Вы не хотите, или по каким-то причинам, не можете воспользоваться автоматическим установщиком <a href="http://install.phpshop.ru" target="_blank" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-share-alt"></span> Web Installer</a>, то приведенная ниже информация, поможет Вам выполнить установку в ручном режиме.</p>


            <div class="panel panel-info <?php echo $system; ?>" id="sys">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-signal"></span> Соответствие системным требованиям</h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item <?php echo $api_style ?>">Веб-сервер <?php echo $API ?>
                    <li class="list-group-item <?php echo $mysql_style ?>">MySQL <?php echo $PHPShopBase->mysql_error . $mysql ?>
                    <li class="list-group-item">PHP<?php echo $php ?>
                    <li class="list-group-item">GD Support для PHP <?php echo $gd_support ?>
                    <li class="list-group-item">XML Parser для PHP <?php echo $xml_support ?>
                </ul>
            </div>


            <div class="panel panel-default <?php echo $system; ?>" id="inst">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-download-alt"></span> Установка в ручном режиме</h3>
                </div>
                <div class="panel-body">
                    <ol>
                        <li>Подключитесь к своему серверу через FTP-клиент (FileZilla, CuteFTP, Total Commander и др.) или через файловый менеджер на хостинге.
                        <li>Загрузите распакованный архив с PHPShop в корневую директорию для веб-документов (www, public_html и т.д.).
                        <li>Создайте новую базу MySQL на своем сервере или узнайте пароли доступа к уже созданной базе у хост-провайдера.
                        <li>Отредактируйте файл связи с базой MySQL <kbd>config.ini</kbd> в папке <code><?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/inc/</code>. Измените данные в кавычках " " на свои данные. Кодировка базы может иметь значения cp1251 (кириллическая по умолчанию) или utf-8 (международная). Для использования utf-8 базой данных кодировка сервера так же должна быть utf-8. Для управления кодировкой сервера, можно использовать параметр <code>AddDefaultCharset utf-8</code> в корневом файле <code>.htaccess</code>. По умолчанию, установка рассчитана на кодировку сервера windows-1251 и базы данных cp1251. Изменять настройки кодировки рекомендуется только для языков, не имеющих  кириллических символов (армянская, азербайджанская и т.д.). База данных в кириллической кодировке cp1251 работает быстрее и занимает меньше места.

                            <pre>[connect]
host="localhost";   # имя хоста базы данных
user_db="user";     # имя пользователя
pass_db="mypas";    # пароль базы
dbase="mybase";     # имя базы
charset="utf-8";    # кодировка базы</pre>

                        </li>
                        <li>
                            <p>Воспользуйтесь встроенным <a href="#" class="btn btn-success btn-xs" <?php echo $mysql_break_install; ?>  data-target="#install"><span class="glyphicon glyphicon-download-alt"></span> Установщиком базы данных</a></p>
                            <div class="alert alert-warning" role="alert"><b>Внимание!</b> Установщик базы запускать необходимо, в противном случае, не будет создан образ базы. </div>
                        </li>
                        <li>Для безопасности удалите папку <kbd>/install</kbd>
                        <li>Установите опцию <kbd>CHMOD 777</kbd> (UNIX сервера) для папок:
                            <pre>
/license
/UserFiles/Image
/UserFiles/Files
/phpshop/admpanel/csv
/phpshop/admpanel/dumper/backup</pre>

                        <li>Для входа в <b>Административную панель</b>, нажмите комбинацию клавиш <kbd>CTRL</kbd> + <kbd>F12</kbd>  или по ссылке  <a href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/admpanel/">http://<?php echo $_SERVER['SERVER_NAME'] ?>/phpshop/admpanel/</a><br>
                            Пользователь и пароль задается при установке скрипта. При установке, пользователь и пароль задается в ручном режиме. По желанию, регистрационные данные отсылаются на E-mail.

                        <li>Существует возможность размещения 2-х и более независимых интернет-магазинов в любых директориях домена. Данная особенность позволяет создавать многоязычные проекты и гипермаркеты, используя одну лицензию. Для задания папки размещения, требуется выполнить:

                            <ol>
                                <li>Скопирйте скрипт в любую директорию, например <code>/market/</code>
                                    <div class="alert alert-warning" role="alert"><b>Внимание!</b> Использование зарегистрированных ссылок с именами <em>shop, news, gbook, spec, users</em>  запрещено.</div>
                                <li>В файле конфигурации <code>market/phpshop/inc/config.ini</code> укажите имя директории, куда установлен скрипт
                                    <pre>[dir]
dir="/market";</pre>
                            </ol>

                            </div>

                            </div>

                            <div class="panel panel-default <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-download"></span> Установка через командную строку</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>Создайте новую базу MySQL на своем сервере или узнайте пароли доступа к уже созданной базе у хост-провайдера.
                                        <li>Скачайте и распакуйте архив PHPShop-Enterprise-Trial.tar.gz в корневую директорию сайта
                                            <pre>wget http://www.phpshop.ru/loads/files/PHPShop-Enterprise-Trial.tar.gz
tar -zxf PHPShop-Enterprise-Trial.tar.gz</pre>
                                        <li>Запустите скрипт установщика <kbd>install.sh</kbd>, в нем укажите данные доступа к созданной на первом шаге базе MySQL и параметры нового администратора. Файлы для запуска в командной строке собраны в папке <kbd>/sh</kbd><pre>
cd sh
sh install.sh</pre>
                                        <li>Для компактности и автоматизации установки, параметры можно указать в качестве аргументов (сервер БД, имя БД, пароль БД, логин админа, пароль админа, почта админа), пример <code>install.sh localhost phpshop_bd1 phpshop_bd1 dGyEySHRO admin 123456 mail@phpshop.ru</code><pre>
sh install.sh host user_db dbase pass_db admin admin_pass admin_mail</pre>



                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>" id="upd">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-refresh"></span> Обновление в ручном режиме</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>Создайте копию текущей базы данных через меню <kbd>База</kbd> - <kbd>Резервное копирование</kbd>
                                        <li>Создайте папку <code>/old</code> и перенесите в нее все файлы из корневой директории с PHPShop (<em>www, httpdocs, docs, public_html</em>)
                                        <li>Загрузите в очищенную корневую директорию файлы из архива новой версии
                                        <li>Из старого конфигурационного файла <code>/old/phpshop/inc/config.ini</code> возьмите параметры подключения к базе данных (первые 5 строк) и вставьте в новый конфигурационный файл <code>/phpshop/inc/config.ini</code>
                                            <pre>[connect]
host="localhost";   # имя хоста базы данных
user_db="user";     # имя пользователя
pass_db="mypas";    # пароль базы
dbase="mybase";     # имя базы</pre>
                                        <li>Запустите <a href="#" class="btn btn-success btn-xs update" <?php echo $mysql_break_install; ?> target="_blank" data-target="#install"><span class="glyphicon glyphicon-refresh"></span> Обновление базы данных</a>, выберите предыдущую версию (которая была перед обновлением), если ее там нет, то обновлять базу не нужно. 
                                        <li>Удалите папку <code>/install</code>
                                        <li>Скопируйте папки <code>/old/UserFiles</code>, <code>/old/license</code> со старыми изображениями и лицензией в обновленный скрипт
                                        <li>По необходимости, скопируйте Ваш старый шаблон <code>/old/phpshop/templates/{имя-шаблона}</code>
                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-download"></span> Обновление через командную строку</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>Запустите скрипт обновления <kbd>update.sh</kbd>. Файлы для запуска в командной строке собраны в папке <kbd>/sh</kbd><pre>
cd sh
sh update.sh</pre>
                                        <li>Подтвердите согласие на обновления
                                        <li>При обновлении, создается резервная копия обновленных файлов и базы данных. Резервные копии хранятся в <code>/backup/backups</code>   
                                    </ol>
                                </div>
                            </div>

                            <div class="panel panel-default <?php echo $system; ?>" id="tran">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-transfer"></span> Перенос файлов с другого сервера</h3>
                                </div>
                                <div class="panel-body">
                                    <ol>
                                        <li>Создайте копию текущей базы данных на старом сервере через меню <kbd>База</kbd> - <kbd>Резервное копирование</kbd>
                                        <li>Загрузите файлы переносимого скрипта из корневой веб-директории с PHPShop (<em>www, httpdocs, docs, public_html</em>) в корневую веб-директорию на новом сервере.  Для мгновенного переноса файлов с сервера на сервер, можно воспользоваться утилитой <a href="https://ru.wikipedia.org/wiki/PuTTY" target="_blank">PyTTY</a> и  протоколом SSH. Команды оболочки после подключения на старом сервере (www заменяется на имя своей папки хранения веб-файлов):
                                            <pre>tar cvf file.tar ./
gzip file.tar</pre>
                                            Команды оболочки после подключения на новом сервере:

                                            <pre>wget http://имя_домена/file.tar.gz
tar -zxf file.tar.gz</pre>

                                        <li>Восстановите из архива скрипта папку <kbd>/install</kbd> и скопируйте ее, вместе с входящими в нее файлами, на новый сервер.
                                        <li>Пропишите в файл конфигурации  <code>/phpshop/inc/config.ini</code> на новом сервере новые параметры доступа к базе данных MySQL.
                                            <pre>[connect]
host="localhost";       # имя хоста
user_db="user";         # имя пользователя
pass_db="mypas";        # пароль базы
dbase="mybase";         # имя базы</pre>
                                        <li>Запустите <a href="#" class="btn btn-success btn-xs" <?php echo $mysql_break_install; ?>  data-target="#install"><span class="glyphicon glyphicon-download-alt"></span> Установщик базы данных</a>. Выполните установку баз с нуля, укажите пароли доступа к панели управления (временные - после завершения, пароли будут идентичны старому серверу). Будет установлена тестовая база временно.
                                        <li>Удалите папку <code>/install</code>
                                        <li>Авторизуйтесь в панели управления  <code>/phpshop/admpanel/</code>, используя новые временные пароли доступа.
                                        <li>Восстановите резервную копию базы, через меню <kbd>База</kbd> - <kbd>Резервное копирование</kbd>. 
                                        <li>Теперь для входа в панель управления следует вводить пароли со старого сервера.
                                    </ol>

                                </div>
                            </div>

                            <div class="panel panel-warning <?php echo $system; ?>">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><span class="glyphicon glyphicon-alert"></span> Коды ошибок</h3>
                                </div>
                                <div class="panel-body">
                                    <ul>
                                        <li><b>101 Ошибка подключения к базе данных</b>
                                            <ol>
                                                <li>Проверьте настройки подключения к базе данных: <em>host, user_db, pass_db, dbase</em>.
                                                <li>В файле <code>phpshop/inc/config.ini</code> отредактируйте переменные под вашу базу (замените данные между кавычками).<br>
                                                    <pre>[connect]
host="localhost";       # имя хоста
user_db="user";         # имя пользователя
pass_db="mypas";        # пароль базы
dbase="mybase";         # имя базы</pre>
                                            </ol>
                                        <li><b>102 Не установлены базы</b>
                                            <ol><li>Запустите установку базы данных <code>/install/</code></ol>
                                        <li><b>105 Ошибка существования папки /install</b>
                                            <ol>
                                                <li>Удалите папку <code>/install</code>
                                            </ol>
                                    </ul>
                                </div>
                            </div>


                            </div>
                            <footer class="footer">
                                <div class="container">
                                    <p class="text-muted text-center">
                                        Перейти <a href="https://www.phpshop.ru" target="_blank" title="Разработчик"><span class="glyphicon glyphicon-home"></span>домой</a> или воспользоваться <a href="https://help.phpshop.ru" target="_blank" title="Техническая поддержка"><span class="glyphicon glyphicon-user"></span>технической поддержкой</a>
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
                                                <h4 class="modal-title">Лицензия</h4>
                                            </div>
                                            <div class="modal-body">

                                                <!-- Лицензия -->
                                                <div id="step-1" style="overflow-y:scroll;height: 450px;" class="small"> 

                                                    <h4 class="title hide">Лицензия</h4>

                                                    <h4>ЛИЦЕНЗИОННОЕ СОГЛАШЕНИЕ НА ИСПОЛЬЗОВАНИЕ ПРОГРАММНОГО ПРОДУКТА "PHPShop"</h4>
                                                    <p>Настоящее Лицензионное Соглашение заключается между пользователем программного продукта "PHPShop" (далее Пользователь) и ИП Туренко Д.Л. (далее Автор). Перед использованием продукта внимательно ознакомьтесь с условиями данного соглашения. Если вы не согласны с условиями данного соглашения, вы не можете использовать данный продукт. Установка и использование продукта (в том числе просмотр исходного кода) означает ваше полное согласие со всеми пунктами настоящего соглашения. Соглашение относится ко всем коммерчески распространяемым версиям и модификациям программного продукта PHPShop.</p>
                                                    <p>Основные термины настоящего соглашения: ЭКЗЕМПЛЯР ПРОГРАММЫ - копия продукта "PHPShop", включающая в себя код программы Интернет-магазина, воспроизведенный в файлах, включая электронную или распечатанную документацию.</p>
                                                    <p>Лицензионное соглашение вступает в силу с момента приобретения или установки продукта и действует на протяжении всего срока использования продукта. </p>
                                                    <p><b>1.	Предмет лицензионного соглашения</b>
                                                        <br>1.1.	Предметом настоящего лицензионного соглашения является право использования одного экземпляра программного продукта (в дальнейшем "ЭКЗЕМПЛЯР ПРОГРАММЫ", "программа" или "продукт") "PHPShop", предоставляемое Пользователю Автор, в порядке и на условиях, установленных настоящим соглашением.
                                                        <br>1.2.	Все положения настоящего соглашения распространяются как на весь продукт в целом, так и на его отдельные компоненты.
                                                        <br>1.3.	Данное Соглашение дает право Пользователю на использование одной копии Продукта на одном web-сервере в пределах одного домена.
                                                        <br>1.4.	Лицензионное соглашение не предоставляет право собственности на продукт "PHPShop" и его компоненты, а только право использования ЭКЗЕМПЛЯРА ПРОГРАММЫ и его компонентов в соответствии с условиями, которые обозначены в пункте 3 настоящего соглашения.
                                                    <p><b>2.	Авторские права </b>
                                                        <br>2.1.	Все авторские права на Продукт, включая документацию и исходный текст, принадлежат Автору, на основании свидетельств о государственной регистрации программы для ЭВМ "PHPShop" №2006614274.
                                                        <br>2.2. Продукт в целом или по отдельности является объектом авторского права и защищен Законом РФ "О правовой охране программ для электронных вычислительных машин и баз данных" от 23 сентября 1992 года, Законом РФ "Об авторском праве и смежных правах" от 9 июля 1993 года, а также международными Договорами.
                                                        <br>2.3. В случае нарушения авторских прав предусматривается ответственность в соответствии с действующим законодательством РФ.
                                                    <p><b>3. 	Условия использования продукта и ограничения </b>
                                                        <BR>3.1.	Пользователь имеет право бесплатно воспользоваться демо-версией Продукта, скачав с сайта Лицензиара www.phpshop.ru и установив на сервер в течение 30 дней, и без ограничений времени при установке на локальном компьютере. Демо-версии всех версий Продукта PHPShop работают без каких-либо ограничений функциональности, кроме количества выгрузки товаров в обработчике 1С версии PHPShop Pro.
                                                        <br>3.2.	Для каждой новой установки Продукта на другой адрес web-сервера должна быть приобретена отдельная Лицензия. Перевод лицензии на новый домен возможен только при активной технической поддержке.
                                                        <br>3.3.	Автор оставляет за собой право требовать размещения обратной ссылки, с указанием Авторского права на сайте, где используется Продукт. Использование Продукта с нарушением условий данного Соглашения, является нарушением законов об авторском праве, и будет преследоваться в соответствии с действующим законодательством. Отказ от размещения обратной ссылки с указанием Авторского права является нарушением Соглашения и ограничивает Продукт в предоставлении технической поддержки Автором на все сайты Пользователя.
                                                        <br>3.4.	Вид ссылки и размещение строго задается Автором, код ссылки не поддается изменению. В целях сохранения визуализации с персональным дизайном возможно изменение цвета ссылки (задается лицензией).
                                                        <br>3.5.	Для официальных партнеров, после письменного согласования с Автором, ссылка размещается в удобном для них месте, на каждой странице сайта. Лицензия без копирайтов Автора увеличивает стоимость Продукта.

                                                        <br>3.6.	Версии Enterprise и Pro 1С поддерживают размещение в некорневые директории, т.е. вида seamply.ru/market1/. Размещение типа market1.seamply.ru и т.д. требует покупки отдельной Лицензии. Техническая поддержка распространяется только на одну копию Продукта. Для каждого нового экземпляра магазина, а также для магазинов некорневых директорий вида seamply.ru/market1/, требуется покупка новой технической поддержки по тарифам, указанным на сайте Лицензиара.  Допускается возможность создания и использования Пользователем дополнительной копии Продукта исключительно в целях тестирования или внесения изменений в исходный код, при условии, что такая копия не будет доступна третьим лицам.

                                                        <br>3.7.	После покупки товара покупателю предоставляются исходные коды php-приложения за исключением файла index.php, в котором происходит проверка лицензии и защита от несанкционированного распространения программы. Все дополнительные приложения из комплекта EasyControl предоставляются в скомпилированном виде без возможности внесения изменения в код.

                                                        <br>3.8.	Пользователь может изменять, добавлять или удалять открытые файлы приобретенного ЭКЗЕМПЛЯРА ПРОГРАММЫ "PHPShop" в соответствии с Законодательством РФ об авторском праве. Изменение скомпилированных файлов запрещено и влечет нарушение данного Соглашения в соответствии с 273 статьей УК РФ.

                                                    <p><b>4.	Ответственность сторон</b>
                                                        <br>4.1.	Пользователь не может копировать, передавать третьим лицам или распространять, сдавать в аренду/прокат Продукт и его компоненты, в том числе, созданные на базе Продукта сайты, в любой форме, в том числе, в виде исходного текста, каким-либо другим способом.

                                                        <br>4.2.	Любое распространение Продукта без предварительного согласия Автора, включая некоммерческое, является нарушением данного Соглашения, и влечет ответственность согласно действующему законодательству.

                                                        <br>4.3.	Продукт поставляется на условиях "КАК ЕСТЬ" ("AS IS") без предоставления гарантий производительности, покупательной способности, сохранности данных, а также иных явно выраженных или предполагаемых гарантий.

                                                        <br>4.4.	Автор не несет какой-либо ответственности за причинение или возможность причинения вреда Вам, Вашей информации или Вашему бизнесу вследствие использования или невозможности использования Продукта.

                                                        <br>4.5.	Автор не несет ответственность, связанную с привлечением Вас к административной или уголовной ответственности за использование Продукта в противозаконных целях (включая, но не ограничиваясь, продажей через Интернет магазин объектов, изъятых из оборота или добытых преступным путем, предназначенных для разжигания межрасовой или межнациональной вражды; и т.д.).
                                                        <br>4.6.	Автор не несет ответственности за работоспособность Продукта, в случае внесения Вами каких бы то ни было изменений в код программы.
                                                        <br>4.7.	Запрещается любое использование Продукта, противоречащее действующему законодательству РФ.
                                                        <br>4.8.	За нарушение условий настоящего соглашения наступает ответственность, предусмотренная законодательством РФ.
                                                    <p><b>5.	Условия технической поддержки</b>
                                                        <br>5.1.	Приобретая Интернет-магазина PHPShop Enterprise и PHPShop Pro 1C, Пользователь получает бесплатную базовую техническую поддержку в течение 6 месяцев. Для версии PHPShop Basic срок поддержки составляет 3 месяца.

                                                        <br>5.2.	Техническая поддержка предусматривает доступ к обновлениям, технические консультации, устранение ошибок в программном продукте "PHPShop", выявленных в течение гарантийного периода.

                                                        <br>5.3.	В поддержку включены консультации по поводу управления и заполнения магазина. Настройки связи с 1С (подключение к серверу и синхронизация данных). Вопросы по поводу установки продукта на сервер, решения проблем, мешающие установки продукта на сервер. Установка и настройка дополнительных бесплатных утилит из пакета EasyControl.

                                                        <br>5.4.	Консультации проводятся в специальном разделе сайта службы технической поддержки <a target="_blank" href="https://help.phpshop.ru/">help.phpshop.ru</a> Автор в течение гарантийного срока по рабочим дням (за исключением выходных и нерабочих праздничных дней Российской Федерации) с 10 до 18 часов московского времени.

                                                        <br>5.5.	По истечению срока бесплатной технической поддержки, Пользователь может приобрести продление. Срок действия технической поддержки продлевается на один год с момента оплаты продления. Пользователь также получает возможность загрузить и установить все изменения и обновления, которые были выпущены к программному продукту до момента оплаты продления технической поддержки. Действующий прайс-лист для приобретения технической поддержки указан на интернет-сайте <a target="_blank" href="http://www.phpshop.ru/docs/techpod.html">http://www.phpshop.ru/docs/techpod.html</a>.
                                                        <br>5.6.	Проблемы, не относящиеся к базовой технической поддержке, не могут быть бесплатно решены. Для выявления и решения таких проблем следует оплатить дополнительные технические работы. Полный список платных технических работ доступен по ссылке: <a target="_blank" href="https://help.phpshop.ru/knowledgebase.php?article=116">https://help.phpshop.ru/knowledgebase.php?article=116</a>
                                                        <br>5.7.	Персональные доработки и модули, выполненные под заказ, поддерживаются бесплатно в течение 1 месяца. Дальнейшая поддержка по тарифам, указанным по адресу: <a target="_blank" href="http://phpshop.ru/docs/techpod.html">http://phpshop.ru/docs/techpod.html</a>.

                                                    <p><b>6. Политика возврата денежных средств</b>
                                                        <br>6.1.	В связи с тем, что перед приобретением, Автором предоставлено право проверить соответствие Продукта потребностям Пользователя, а именно, установить демо-версию Продукта, согласно п. 3.1. настоящего Соглашения, а также, по причине того, что Автор при продаже передает нематериальный продукт, не подлежащий возврату физически, возврат денежных средств Пользователю возможен, если Продукт явно не соответствует функциям, которые описаны в Руководстве Пользователя по адресу: <a target="_blank" href="http://wiki.phpshop.ru">http://wiki.phpshop.ru</a>
                                                        <br>6.2.	Любые другие потребительские свойства, предполагаемые, но не обнаруженные  пользователем при покупке, кроме описанных в  <a target="_blank" href="http://wiki.phpshop.ru">http://wiki.phpshop.ru</a> и на сайте Автора, не могут являться причиной для возврата денежных средств.
                                                        <br>6.3.	Возврат денежных средств осуществляется без комиссий банков и других платежей, по письменному заявлению Пользователя, не позднее 30 (тридцати) календарных дней с момента приобретения Продукта. В заявлении необходимо указать, что Пользователь гарантирует неиспользование Продукта, а также удаление всех без исключения полученных от Автора файлов, имеющих отношение к Продукту.
                                                        <br>6.4.	По истечение 30 (тридцати) дней с момента приобретения Продукта, претензии Автором не принимаются и денежные средства не возвращаются.
                                                        <br>6.5.	Возврат осуществляется в течение 15 (пятнадцати) календарных дней с момента получения письменного заявления в случае принятия Автором решения о возврате денежных средств Пользователю.
                                                        <br>6.6.	Бесплатные утилиты EasyControl, кроме платной синхронизации с 1С, предоставляются на условиях работы КАК ЕСТЬ" ("AS IS"), и не могут быть причиной возврата денежных средств за Продукт.

                                                    <p><b>7.	Изменение и расторжение соглашения </b>
                                                        <br>7.1.	В случае невыполнения пользователем одного из вышеуказанных положений, Автор имеет право в одностороннем порядке расторгнуть настоящее соглашение, уведомив об этом пользователя.
                                                        <br>7.2.	При расторжении соглашения Пользователь обязан прекратить использование продукта и удалить Лицензия полностью.
                                                        <br>7.3.	Пользователь вправе расторгнуть данное соглашение в любое время, полностью удалив ЭКЗЕМПЛЯР ПРОГРАММЫ "PHPShop", при этом, расторжение Соглашения не обязывает Автора возвращать средства, потраченные Пользователем на приобретение Продукта.
                                                        <br>7.4.	В случае если компетентный суд признает какие-либо положения настоящего соглашения недействительными, Соглашение продолжает действовать в остальной части.
                                                        <br>7.5.	Настоящее лицензионное соглашение также распространяется на все обновления, предоставляемые пользователю в рамках технической поддержки, если только при обновлении программного продукта пользователю не предлагается ознакомиться и принять новое лицензионное соглашение или дополнения к действующему соглашению.
                                                    </p>
                                                </div>

                                                <!-- Обновление -->
                                                <div id="step-3" class="hide">
                                                    <h4 class="title hide">Обновление</h4>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label">Версия</label>
                                                        <div class="col-sm-10">
                                                            <?php echo $update_select; ?>
                                                            <p></p>
                                                            <p class="text-muted"><span class="glyphicon glyphicon-info-sign"></span> Необходимо выбрать свою текущую версию PHPShop (до обновления). Если вашей версии нет в списке, то выбрать версию выше и самую близкую к вашей в большую сторону. Например, ваша старая версия Enterprise 3.6.6.0.1, то следует выбрать в списке 3.6.7.1.3.</p><p class="text-muted">Префиксы <kbd>Start</kbd> и <kbd>CMS</kbd> обозначают одноименные сборки. Версии PHPShop 5 и 6 имеет одинаковую структуру данных у всех коммерческих версий Basic, Enterprise и Pro 1C.</p><p class="text-muted">Для обновлении версий PHPShop 3 и ниже потребуется процедура восстановления пароля администратора по email.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Установка -->
                                                <div id="step-2" class="hide">

                                                    <h4 class="title hide">Установка</h4>

                                                     <div class="form-group">
                                                        <label class="col-sm-4 control-label">Конфигурация</label>
                                                        <div class="col-sm-5">
                                                            <select name="shop_type" size="1" class="form-control">
                                                                <option value="0" selected="">Интернет-магазин</option>
                                                                <option value="1">Каталог продукции</option>
                                                                <option value="2">Сайт компании</option></select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">Имя</label>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="user" required class="form-control" placeholder="Администратор" value="Администратор">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">Пользователь</label>
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
                                                        <label class="col-sm-4 control-label">Пароль</label>
                                                        <div class="col-sm-5">
                                                            <input type="password" name="password" required class="form-control" placeholder="Пароль">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="col-sm-offset-4 col-sm-6">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="send-welcome" checked value="1"> Отправить регистрационные данные на E-mail
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-offset-4 col-sm-5">
                                                            <button type="button" class="btn btn-default" id="generator" data-password="<?php echo "P" . substr(md5(time()), 0, 6) ?>"><span class="glyphicon glyphicon-lock"></span> Генератор паролей</button>
                                                            <div id="password-message">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">

                                                <span class="pull-left"><label><input type="checkbox" id="licence-ok" checked> Я принимаю условия лицензионного соглашения</label>.</span>

                                                <button type="button" class="btn btn-default btn-sm back hide"><span class="glyphicon glyphicon-arrow-left"></span> Назад</button>
                                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Отменить</button>
                                                <button type="button" class="btn btn-primary btn-sm steps" data-step="1" name="install" value="1">Далее <span class="glyphicon glyphicon-arrow-right"></span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--/ Modal-->

                            <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
                            <script>

                                $().ready(function () {

                                    // Ошибка MySQL
                                    $('[data-toggle="error"]').on('click', function (event) {
                                        event.preventDefault();
                                        alert($('.list-group-item-danger').text());
                                    });

                                    // Обновление
                                    $('.update').on('click', function () {
                                        $('#step-2 .title').text($('#step-3 .title').text());
                                        $('#step-2').html($('#step-3').html());
                                    });

                                    // Согласие с лицензией
                                    $('#licence-ok').on('click', function () {
                                        if (!this.checked) {
                                            $('#install').modal('hide');
                                            this.checked = true;
                                        }
                                    });

                                    // Вперед
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

                                    // Назад
                                    $('.back').on('click', function () {
                                        $('.steps').attr('data-step', 1);
                                        $('.modal-title').text($('#step-1 .title').text());
                                        $('#step-1').show();
                                        $('#step-2').hide();
                                        $('#licence-ok').closest('.pull-left').show();
                                        $(this).addClass('hide');
                                        $('.steps').attr('type', 'button');
                                    });

                                    // Генератор паролей
                                    $('#generator').on('click', function () {
                                        var password = $(this).attr('data-password');
                                        $('input[type=password]').val(password);
                                        $('#password-message').html('<div class="alert alert-success" role="alert">Ваш пароль: <b>' + password + '</b></div>');
                                    });

									$('#install').on('hidden.bs.modal', function (e) {
                                       location.reload();
                                    });

                                    // Подсказка
                                    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

                                    $('select').addClass('form-control');
                                });
                            </script>

                            </body>
                            </body>
                            </html>