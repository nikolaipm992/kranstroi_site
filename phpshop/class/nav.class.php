<?php

/**
 * ���������� ���������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopNav {

    /**
     * @var array ������ ������ ���������
     */
    var $objNav;

    /**
     * �����������
     */
    function __construct() {
        $url = parse_url("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

        // ��������, ���� � �����
        $path_parts = pathinfo($_SERVER['PHP_SELF']);
        $root = $path_parts['dirname'] . "/";
        if ($root != "//")
            if ($root != "\/")
                $url = str_replace($path_parts['dirname'] . "/", "/", $url);

        $Query = @$url["query"];
        $Path = explode("/", $url["path"]);
        if (is_array($Path)) {
            $File = @explode("_", $Path[2]);
            $Prifix = @explode(".", $File[1]);
            $Name = @explode(".", $File[0]);
            $Page = @explode(".", $File[2]);
        }
        parse_str($Query, $output);
        $longpage = explode(".", str_replace("/page/", "", $url["path"]));

        // �������� ��� index
        if (empty($Path[1]))
            $Path[1] = 'index';

        $this->objNav = array(
            "truepath" => $url["path"],
            "path" => $Path[1],
            "nav" => $File[0],
            "name" => $Name[0],
            "id" => $Prifix[0],
            "page" => $Page[0],
            "querystring" => @$url["query"],
            "query" => $output,
            "longname" => $longpage[0],
            "url" => $url["path"]);
        $GLOBALS['SysValue']['nav'] = $this->objNav;
    }

    /**
     * ������ ���������� ��������� path
     * @return string
     */
    function getPath() {
        return $this->objNav['path'];
    }

    /**
     * �������� ������� �� �������������� �������
     * @param mixed $path ������ ��������
     * @return bool
     */
    function notPath($path) {
        if (is_array($path)) {
            foreach ($path as $val) {
                if ($this->objNav['path'] == $val)
                    return false;
            }
            return true;
        }
        else if ($this->objNav['path'] != $path)
            return true;
    }

    /**
     * ������ ���������� ��������� nav
     * @return string
     */
    function getNav() {
        return $this->objNav['nav'];
    }

    /**
     * ������ ���������� ��������� name
     * @return string
     */
    function getName($longname = false) {
        if (!empty($longname))
            return $this->objNav['longname'];
        else
            return $this->objNav['name'];
    }

    /**
     * ������ ���������� ��������� id
     * @return string
     */
    function getId() {
        
        return $this->objNav['id'];
    }

    /**
     * ������ ���������� ��������� page
     * @return int
     */
    function getPage() {
        if ($this->objNav['page'] > 0 or $this->isPageAll())
            return $this->objNav['page'];
        elseif ($this->objNav['path'] == 'spec' or $this->objNav['path'] == 'newtip')
            return $this->getId();
        else
            return 1;
    }

    function isPageAll() {
        //return strtoupper($this->objNav['page']) === 'ALL';
    }

    /**
     * �������� �� ������� ��������
     * @return bool
     */
    function index($check = true) {
        if ($this->objNav['path'] == 'index' or $check)
            return true;
    }

}

?>