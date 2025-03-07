<?php

if (empty($argv[0]))
    exit('Only PHP-CLI command line!');


if (empty($argv[0])) {
    // Test
    $argv[1] = $_GET['com'];
    $argv[2] = $_GET['v'];
} else {
    $_SERVER['DOCUMENT_ROOT'] = "..";
}


$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
include($_classPath . 'lib/zip/pclzip.lib.php');
include($_classPath . 'lib/phpass/passwordhash.php');
PHPShopObj::loadClass(array("base", "file", "orm"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

$License = parse_ini_file_true("../license/" . PHPShopFile::searchFile("../license/", 'getLicense'), 1);


switch ($argv[1]) {

    case "mysql":
        exit("done");
        break;

    case "user":

        $hasher = new PasswordHash(8, false);
        $PHPShopOrm = new PHPShopOrm($PHPShopBase->getParam('base.users'));

        $insert = array(
            'status' => 'a:24:{s:5:"gbook";s:5:"1-1-1";s:4:"news";s:5:"1-1-1";s:5:"order";s:7:"1-1-1-1";s:5:"users";s:7:"1-1-1-1";s:9:"shopusers";s:5:"1-1-1";s:7:"catalog";s:11:"1-1-1-0-0-0";s:6:"report";s:5:"1-1-1";s:4:"page";s:5:"1-1-1";s:4:"menu";s:5:"1-1-1";s:6:"banner";s:5:"1-1-1";s:6:"slider";s:5:"1-1-1";s:5:"links";s:5:"1-1-1";s:3:"csv";s:5:"1-1-1";s:5:"opros";s:5:"1-1-1";s:6:"rating";s:5:"1-1-1";s:8:"exchange";s:5:"1-1-0";s:6:"system";s:3:"1-1";s:8:"discount";s:5:"1-1-1";s:6:"valuta";s:5:"1-1-1";s:8:"delivery";s:5:"1-1-1";s:7:"servers";s:5:"1-1-1";s:10:"rsschanels";s:5:"0-0-0";s:6:"update";i:1;s:7:"modules";s:9:"1-1-1-0-0";}',
            'login' => $argv[2],
            'password' => $hasher->HashPassword($argv[3]),
            'mail' => $argv[4],
            'enabled' => 1,
            'name' => 'Администратор'
        );

        if ($PHPShopOrm->insert($insert, ''))
            exit("done");
        else
            exit("error");


        break;


    case "backup":

        if (!empty($argv[2]))
            $GLOBALS['SysValue']['upload']['version'] = intval($argv[2]);


        // Создание папки
        $_backup_path = '../backup/backups/' . $GLOBALS['SysValue']['upload']['version'];
        //mkdir($_backup_path);

        @copy("../backup/temp/restore.sql", $_backup_path . '/restore.sql');


        $archive = new PclZip($_backup_path . '/files.zip');
        $map = parse_ini_file_true("../backup/temp/upd_conf.txt", 1);
        $zip_files = null;

        if (is_array($map)) {
            foreach ($map as $k => $v) {

                if (!empty($v['files'])) {

                    if (strstr($v['files'], ';')) {
                        $files = explode(";", $v['files']);

                        if (is_array($files)) {
                            foreach ($files as $file) {
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $file))
                                    $zip_files.= $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $file . ',';
                            }
                        }
                    }
                    elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $v['files']))
                        $zip_files.= $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $v['files'] . ',';
                }
            }
        }

        if (!empty($zip_files)) {
            $v_list = $archive->create($zip_files, PCLZIP_OPT_REMOVE_PATH, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/');
            if ($v_list == 0) {
                //echo("error : " . $archive->errorInfo(true));
                exit("error");
                return false;
            }

            exit("done");
        }

        break;


    // Текущая версия
    case "version":

        exit($GLOBALS['SysValue']['upload']['version']);

        break;

    // Cсылка на обновление
    case "link":

        $link = "http://www.phpshop.ru/update/update5.php?from=" . $License['License']['DomenLocked'] . "&version=" . $GLOBALS['SysValue']['upload']['version'] . "&support=" . $License['License']['SupportExpires'] . '&serial=' . $License['License']['Serial'] . '&sh=true';

        exit($link);

        break;

    // Обновление MySQL
    case "sql":

        // Выполнение команд из update.sql
        if (file_exists("../backup/temp/update.sql")) {
            $sql = file_get_contents("../backup/temp/update.sql");


            if (!empty($sql)) {
                $sql_query = explode(";\r", trim($sql));

                foreach ($sql_query as $v)
                    @mysqli_query($PHPShopBase->link_db, trim($v));

                exit("done");
            }
        }
        else
            exit("done");

        break;

    // Обновление config.ini
    case "ini":

        $config = parse_ini_file_true("../backup/temp/config_update.txt", 1);

        $SysValue = parse_ini_file_true($PHPShopBase->iniPath, 1);

        // Новый config.ini
        if (is_array($config)) {
            foreach ($config as $k => $v) {
                if (is_array($config[$k])) {
                    foreach ($config[$k] as $key => $value) {
                        $SysValue[$k][$key] = $value;
                    }
                }
            }
        }


        $s = null;

        if (is_array($SysValue))
            foreach ($SysValue as $k => $v) {

                $s .="[$k]\n";
                foreach ($v as $key => $val) {
                    if (!is_array($val))
                        $s .= "$key = \"$val\";\n";
                }

                $s .= "\n";
            }

        if (!empty($s)) {
            if ($f = fopen($_classPath . "inc/config.ini", "w")) {

                if (!empty($s) and strstr($s, 'phpshop')) {
                    fwrite($f, $s);
                    exit("done");
                }

                fclose($f);
            }
            else
                exit("Сonfiguration file can not be updated. No permission to modify the file phpshop/inc/config.ini!");
        }
        else
            exit("Сonfiguration file can not be updated. Error parsing the file!");
        break;

    default: exit('no command [link / sql / ini / mysql / version / backup / user]');
}
?>