<?php

/**
 * ���������� ������ � ��������� ������
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopClass
 */
class PHPShopArray {

    /**
     * @var string ��� ��
     */
    var $objBase;

    /**
     * ������ ������� �������
     * @var array 
     */
    var $objSQL = false;

    /**
     * ����� 
     * @var int 
     */
    var $limit = 10000;

    /**
     * @var bool ����� �������
     */
    var $debug = false;
    var $cache = true;
    var $ignor_select = false;
    var $objArray = null;

    /**
     * ����� ������ mysql
     * @var bool 
     */
    var $mysql_error = false;

    /**
     * @var int ����������� [1] ���������� ������ [2] ��� [3] ������� ������
     */
    var $objType = 1;

    /**
     * �������������� ���������
     * @var array 
     */
    var $args = array();

    /**
     * ���������� �������
     * @var array
     */
    var $order = array();

    /**
     * ������
     * @var string ��� ������ ������
     */
    var $memory = null;

    /**
     * ����� ������������� ����� � ���������� [NEW]
     * @var bool 
     */
    var $ignor = false;

    function __construct() {

        // ����� �� �������
        if (!empty($GLOBALS['SysValue']['my']['array_limit']))
            $this->limit = $GLOBALS['SysValue']['my']['array_limit'];

        $this->objArg = func_get_args();

        // �������������� ���������
        if (is_array($this->args) and count($this->args) > 0)
            $this->objArg = array_merge($this->objArg, $this->args);

        $this->objArgNum = func_num_args();
        $this->setArray();

        if ($this->memory)
            $_SESSION['Memory'][$this->memory] = $this->objArray;
    }

    /**
     * �������� ������� ��������� ��������� �� ��
     * @param mixed $param ��� ��������� ����� �������
     */
    function setArray() {

        if ($this->objArgNum > 0) {
            foreach ($this->objArg as $v) {
                $select[] = $v;
            }
        } else
            $select[] = "*";

        if ($this->ignor) {
            $this->ignor_select = $select;
            unset($select);
            $select[] = "*";
        }

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->mysql_error = $this->mysql_error;
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $this->cache;
        $data = $PHPShopOrm->select($select, $this->objSQL, $this->order, array('limit' => $this->limit));

        if ($select[0] == "*") {

            if (is_array($data)) {

                if (count($data) == 1)
                    $data_true[] = $data;
                else
                    $data_true = $data;

                foreach ($data_true as $val)
                    $array[$val['id']] = $val;
            }
        } else if (is_array($data))
            foreach ($data as $k => $objRow) {
                switch ($this->objType) {
                    case(1):
                        foreach ($this->objArg as $val)
                            $_array[$val] = $objRow[$val];
                        $array[$objRow[$this->objArg[0]]] = $_array;
                        break;

                    case(2):
                        $array[$objRow[$this->objArg[0]]] = $objRow[$this->objArg[1]];
                        break;

                    case(3):
                        foreach ($this->objArg as $val)
                            $array[$val] = $objRow[$val];
                        break;
                }
            }

        // ������������� �����   
        if (is_array($this->ignor_select) and count($this->ignor_select) > 0) {
            foreach ($array as $k => $v)
                foreach ($v as $key => $val)
                    if (in_array($key, $this->ignor_select)) {
                        unset($array[$k][$key]);
                    }
        }

        if (!empty($array))
            $this->objArray = $array;
    }

    /**
     * ������ ������ �������
     * @return array
     */
    function getArray() {
        return $this->objArray;
    }

    /**
     * �������� ��������
     * @param string $param ��� ���������
     * @param mixed $value �������� ���������
     */
    function setParam($param, $value) {
        if (strstr($param, '.')) {
            $param = explode(".", $param);
            $this->objArray[$param[0]][$param[1]] = $value;
        } else
            $this->objArray[$param] = $value;
    }

    /**
     * ������ �������� �������
     * @param string $param ��� ���������
     * @return string
     */
    function getParam($param) {
        if (strstr($param, '.')) {
            $param = explode(".", $param);
            if (isset($this->objArray[$param[0]][$param[1]]))
                return $this->objArray[$param[0]][$param[1]];
        } else
            return $this->objArray[$param];
    }

    /**
     * �������������� � �������� ������ �� ������� ��������� ��� �������� ������
     * <code>
     * // example:
     * $PHPShopDeliveryArray = new PHPShopDeliveryArray();
     * $PHPShopDeliveryArray -> getKey('PID.name',true);
     * </code>
     * @param string $param ��� ���������
     * @param bool $type ��� ���������� ������ ��������� ����������� ������, ����� ������� FIFO
     * @return array
     */
    function getKey($param, $type = false) {
        $param = explode(".", $param);
        $array = $this->objArray;
        $newArray=null;
        if (is_array($array))
            foreach ($array as $val)
                foreach ($val as $key => $v)
                    if ($key == $param[1]) {
                        if (empty($type)) {
                            $newArray[$val[$param[0]]] = $v;
                        } else {
                            if (empty($newArray[$val[$param[0]]]))
                                $newArray[$val[$param[0]]][] = $v;
                            else
                                $newArray[$val[$param[0]]][] = $v;
                        }
                    }
        return $newArray;
    }

    /**
     * ������� ���������  � �������
     * @return int
     */
    function getNum() {
        return count($this->objArray);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

}

?>