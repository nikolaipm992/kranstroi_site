<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

/**
 * ���������� ������ ������ �����
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopYandexkassa extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        // ������ �������
        parent::__construct();
    }

    /**
     * ����� �� ���������
     */
    function index() {
        if(!isset($_REQUEST['order']) || empty($_REQUEST['order'])) {
            $this->setError404();
        }

        try {
            $YandexKassa = new YandexKassa();
            $logOrder = $YandexKassa->getLogDataByOrderId((int) base64_decode($_REQUEST['order']));
            $order = $YandexKassa->getOrderStatus($logOrder['yandex_id']);

            if(isset($order['paid']) && $order['paid']) {
                $this->parseTemplate($GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_success_forma'], true);
            } else {
                $this->parseTemplate($GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_fail_forma'], true);
            }
        } catch (\Exception $exception) {
            $this->parseTemplate($GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_fail_forma'], true);
        }
    }
}

?>