<?php

/**
 * ���������� �������������� ������
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
     *  �������� ������������� ������
     */

    function checkRestore($version) {
        if (file_exists($this->_backup_path . 'backups/' . intval($version) . '/files.zip')) {
            $this->_restore_version = $version;
            return true;
        }
    }

    /**
     *  �������������� ����
     */
    function restoreBD() {
        global $PHPShopGUI;

        if (file_exists($this->_backup_path . 'backups/' . $this->_restore_version . '/restore.sql')) {

            if (!copy($this->_backup_path . 'backups/' . $this->_restore_version . '/restore.sql', 'dumper/backup/restore.sql')) {
                $this->log("�� ������ ����������� �������������� ���� � backup/backups/" . $this->_restore_version . '/restore.sql', 'warning', 'remove');
                return false;
            }

            $this->_log .= $PHPShopGUI->setProgress(__('�������������� ���� ������...'), 'install-restore-bd');
            $this->log("�������������� ���� ������ ���������", 'success hide install-restore-bd');
            $this->log("�� ������� ��������������� ���� ������", 'danger hide install-restore-bd-danger');
        }
    }

    /**
     *  �������������� ����� �� ������
     */
    function restoreFiles() {

        // ����� �� ��������� �����
        $this->chmod("phpshop/inc/config.ini", $this->_user_ftp_chmod);

        $this->installFiles('backups/' . $this->_restore_version . '/files.zip', $status = '��������������', $this->_restore_path);

        // ����� �� ��������� �����
        $this->chmod("phpshop/inc/config.ini", $this->_user_ftp_re_chmod);
    }

    /**
     *  �������������� �������. ��������� ������.
     */
    function restoreConfig() {
        $config['upload']['version'] = $this->_restore_version;
        $this->installConfig($config);
    }

}