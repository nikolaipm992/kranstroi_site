<?php

/**
 * ���������� �������� ������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopCore
 */
class PHPShopSuccess extends PHPShopCore {

    /**
     * �������
     * @var bool 
     */
    var $debug = false;

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['orders'];

        parent::__construct();

        // ����
        $this->title = "������ - " . $this->PHPShopSystem->getValue("name");
    }

    /**
     * ����� ������ �������
     */
    function error() {
        $this->set('orderNum', $this->inv_id);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__);

        $this->parseTemplate("error/error_payment.tpl");
    }

    /**
     * �������������� ������ ������
     * @param Int $uid ����� ������
     * @return string
     */
    function true_num($uid) {
        $order_prefix_format = $this->getValue('my.order_prefix_format');
        if (empty($order_prefix_format))
            $order_prefix_format = 2;
        $last_num = substr($uid, -$order_prefix_format);
        $total = strlen($uid);
        $ferst_num = substr($uid, 0, ($total - $order_prefix_format));

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $uid);
        if ($hook)
            return $hook;

        return $ferst_num . "-" . $last_num;
    }

    /**
     * ��������� ������������� ������
     */
    function true_order() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('uid'), array('uid' => '="' . $this->true_num($this->inv_id) . '"'), false, array('limit' => 1));
        if (is_array($data))
            if (!empty($data['uid']))
                return true;
    }

    /**
     *  ��������� �� �������� �������
     */
    function message() {

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.payment_systems'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('path' => '="' . $this->order_metod . '"'), false, array('limit' => 1));
        if (is_array($data)) {

            // ��������� ������������ �� �������� �������
            $text = PHPShopText::h3($data['message_header'], 'text-success') . $data['message'];
            $this->set('mesageText', $text);
            $this->set('orderMesage', ParseTemplateReturn($this->getValue('templates.order_forma_mesage')));

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $data);

            // ���������� ������
            $this->parseTemplate($this->getValue('templates.order_forma_mesage_main'));
        }
        else
            $this->error();
    }

    /**
     * �������� ������ � ���
     */
    function ofd() {
        global $_classPath,$PHPShopModules,$PHPShopSystem;
        
        // �������� ������� � OFD
        $ofd = $PHPShopSystem->getParam('ofd');
        if (empty($ofd))
            $ofd = 'atol';

        if (!empty($PHPShopModules->ModValue['base'][$ofd])) {
            include_once($_classPath . 'modules/' . substr($ofd, 0, 15) . '/api.php');

            if (function_exists('OFDStart')) {

                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $PHPShopOrm->debug = $this->debug;
                $data = $PHPShopOrm->select(array('*'), array('uid' => '="' . $this->true_num($this->inv_id) . '"'), false, array('limit' => 1));
                if (is_array($data))
                    OFDStart($data);
            }
        }
    }

    /**
     * �������� ������� ����������� ������ ����� ��������� �����
     */
    function set_order_status_101() {

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.order_status'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('id'), array('id' => '=101'), false, array('limit' => 1));

        if (!is_array($data)) {
            $PHPShopOrm->clean();
            $PHPShopOrm->insert(array('id_new' => 101, 'name_new' => __('�������� ���������� ���������'), 'color_new' => '#ccff00'));
        }
        return 101;
    }

    /**
     *  ������ ������������ �������
     */
    function write_payment() {
        if ($this->out_summ > 0) {
            $PHPShopOrm = new PHPShopOrm($this->getValue('base.payment'));
            $PHPShopOrm->debug = $this->debug;
            if ($this->order_metod_name)
                $order_metod_name = $this->order_metod_name;
            else
                $order_metod_name = $this->order_metod;

            $PHPShopOrm->insert(array('uid_new' => $this->inv_id, 'name_new' => $order_metod_name, 'sum_new' => $this->out_summ, 'datas_new' => time()));
        }
    }

    /**
     *  ��������� ������� ������
     */
    function update_order_status() {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.orders'));
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->update(
            array('statusi_new' => $this->set_order_status_101(), 'paid_new' => 1),
            array('uid' => '="' . $this->true_num($this->inv_id) . '"')
        );
    }

    /**
     * �������������� ��������� �������
     * @return array 
     */
    function get_payment() {
        $payment_arrray = array();
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.payment_systems'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('path'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($data))
            foreach ($data as $val)
                $payment_arrray[] = $val['path'];

        return $payment_arrray;
    }

    /**
     * ����� �� ���������
     */
    function index() {
        global $SysValue;

        // ���������� ����������� success.php �� /payment/
        $path = "payment/";

        // �������������� ��������� �������
        $payment_arrray = $this->get_payment();

        if (@$dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..")
                    if (is_dir($path . $file) and in_array($file, $payment_arrray))
                        if (file_exists($path . $file . "/success.php"))
                            include_once($path . $file . "/success.php");
            }
            closedir($dh);
        }

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $_REQUEST);
        if (is_array($hook)) {
            extract($hook);
        } else if ($hook)
            return $hook;

        if (!empty($inv_id)) {

            // ����� ������
            $this->inv_id = PHPShopSecurity::TotalClean($inv_id, 4);

            // ������
            if (strtoupper($my_crc) != strtoupper($crc)) {
                $this->error();
            } else {

                $this->order_metod = $order_metod;

                // ��� ��������� ������� ��� �������
                if (!empty($order_metod_name))
                    $this->order_metod_name = $order_metod_name;

                $this->out_summ = $out_summ;
                $orderId = $inv_id;

                // ����� ���� � ��
                if ($this->true_order()) {

                    // ��������� ��������� ������� �� ��
                    $this->message();

                    // ������� ������� ������ � ��������
                    // $success_function ������� �� ����� ����������� ������� �����
                    if ($success_function == true) {

                        // ������ � �� ������ �� ����������� �������
                        $this->write_payment();

                        // ���������� ������� ������ �� 101
                        $this->update_order_status();

                        // ���
                        $this->ofd();
                    }

                    // ������� �������
                    $_SESSION['cart'] = null;
                    unset($_SESSION['cart']);
                } else {

                    $this->error();
                }
            }
        }
        else
            $this->error();
    }

}

?>