<?php

/**
 * Библиотека восстановления файлов
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 */
class PHPShopRestore extends PHPShopUpdate {

    var $_restore_path = '../../';
    var $_restore_version;

    function __construct() {
        parent::__construct();
    }

    /*
     *  Проверка существования бекапа
     */

    function checkRestore($version) {
        if (file_exists($this->_backup_path . 'backups/' . intval($version) . '/files.zip')) {
            $this->_restore_version = $version;
            return true;
        }
    }

    /**
     *  Восстановление базы
     */
    function restoreBD() {
        global $PHPShopGUI;

        if (file_exists($this->_backup_path . 'backups/' . $this->_restore_version . '/restore.sql')) {

            if (!copy($this->_backup_path . 'backups/' . $this->_restore_version . '/restore.sql', 'dumper/backup/restore.sql')) {
                $this->log("Не удаётся скопировать восстановление базы в backup/backups/" . $this->_restore_version . '/restore.sql', 'warning', 'remove');
                return false;
            }

            $this->_log .= $PHPShopGUI->setProgress(__('Восстановление базы данных...'), 'install-restore-bd');
            $this->log("Восстановление базы данных выполнено", 'success hide install-restore-bd');
            $this->log("Не удается восстановленить базу данных", 'danger hide install-restore-bd-danger');
        }
    }

    /**
     *  Восстановления файла из бекапа
     */
    function restoreFiles() {

        // Права на изменение файла
        $this->chmod("phpshop/inc/config.ini", $this->_user_ftp_chmod);

        $this->installFiles('backups/' . $this->_restore_version . '/files.zip', $status = 'восстановления', $this->_restore_path);

        // Права на изменение файла
        $this->chmod("phpshop/inc/config.ini", $this->_user_ftp_re_chmod);
    }

    /**
     *  Восстановления конфига. Понижение версии.
     */
    function restoreConfig() {
        $config['upload']['version'] = $this->_restore_version;
        $this->installConfig($config);
    }

}