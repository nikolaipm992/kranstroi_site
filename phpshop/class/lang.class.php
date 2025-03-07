<?php

/**
 * ���������� �����������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopGUI
 */
class PHPShopLang {

    var $langFile;
    var $doLang = true;
    var $UndefinedLangValue = array();

    /**
     * ��������� ������ �������� ����� ����������� (��������� ���������� ����� �� ������ ��� ����� �����)
     * @var bool
     */
    var $createUndefined = false;
    var $charset = "windows-1251";

    /**
     * �����������
     */
    function __construct($option = array('locale' => 'russian', 'path' => 'shop')) {

        $this->option = $option;
        if (empty($this->option['locale']))
            $_SESSION['lang'] = $this->option['locale'] = 'russian';

        // ������ ������ �����
        $this->langFile = $GLOBALS['_classPath'] . 'locale/' . $this->option['locale'] . '/' . $this->option['path'] . '.ini';
        if (is_file($this->langFile)) {
            if ($langArray = parse_ini_file_true($this->langFile, 1)) {
                $this->doLang = $this->check($langArray);
                $this->charset = $langArray['charset']['html'];
                $this->code = $langArray['charset']['code'];
            } else
                echo "Error parsing locale " . $this->langFile;
        }
    }

    /**
     * ������ ������
     * @param array $langArray
     * @return bool
     */
    function check($langArray) {
        $GLOBALS['SysValue']['lang'] = $langArray['lang'];

        $this->LangValue['lang'] = $langArray['locale'];

        if (!empty($langArray['charset']['html'])) {
            $this->charset = $langArray['charset']['html'];
            $this->lang_name = $langArray['charset']['code'];
        }

        return true;
    }

    /**
     * ������� ������
     * @param string $value ������
     * @return string
     */
    function gettext($value,$utf_check=true) {

        if ($this->doLang and ! empty($value)) {

            $sourceValue = $value;
            $value = md5($value);

            if (isset($this->LangValue['lang'][$value]))
                $locValue = $this->LangValue['lang'][$value];
            else {
                $locValue = strip_tags($sourceValue, '<kbd><p><h4><p><a><code><ol><li><br><span><pre>');

                if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
                    $locValue = PHPShopString::win_utf8($locValue, $utf_check);

                $this->UndefinedLangValue[$value] = strip_tags($sourceValue);
            }
        } else
            $locValue = $value;

        if (!empty($locValue))
            return $locValue;
        else
            return $value;
    }

    /**
     * ������ � ���� ������ ����� ������
     */
    function write() {
        $updateLang = '';

        if (is_array($this->UndefinedLangValue)) {

            // ������ ������������ ��������
            foreach ($this->UndefinedLangValue as $key => $val)
                $updateLang .= $key . '="' . str_replace('"', '', $val) . '";
';
            if ($this->doLang) {
                if (is_writable($this->langFile)) {

                    $fp = fopen($this->langFile, "a");
                    if ($fp) {
                        fputs($fp, $updateLang);
                        fclose($fp);
                    }
                } else
                    echo '��� ����� ' . $this->langFile;
            }
        }
    }

}

/**
 * �����������
 * @param string $value ��������
 * @return string
 */
function __($value,$utf_check=true) {
    global $PHPShopLang;
    if ($PHPShopLang)
        return $PHPShopLang->gettext($value,$utf_check);
}

/**
 * ����������� �����
 * @param string $value ��������
 */
function _e($value) {
    echo __($value);
}

/**
 * ������ ����������� ������ � �����������
 */
function writeLangFile() {
    global $PHPShopLang;
    if ($PHPShopLang->createUndefined) {
        $PHPShopLang->write();
    }
}

?>