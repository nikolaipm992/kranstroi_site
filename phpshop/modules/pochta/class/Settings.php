<?php

include_once dirname(__DIR__) . '/class/include.php';

class Settings
{
    /** @var array */
    private $options;

    /** @var int */
    public $format;

    /**
     * Settings constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;

        $system = new PHPShopSystem();
        $this->format = (int) $system->getSerilizeParam('admoption.price_znak');
    }

    public function get($key)
    {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }

        return null;
    }

    public function getFromOrderOrSettings($key, $orderSettings, $defaultValue)
    {
        if(isset($orderSettings[$key])) {
            return $orderSettings[$key];
        }

        if(isset($this->options[$key])) {
            return $this->options[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $field
     * @param string $value
     * @param int $orderId
     * @throws Exception
     */
    public function changeSettings($field, $value, $orderId)
    {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($orderId);

        $pochta = unserialize($order['pochta_settings']);
        $pochta[$field] = $value;

        $orm->update(array('pochta_settings_new' => serialize($pochta)), array('id' => "='" . $order['id'] . "'"));
    }

    /**
     * @param int $orderId
     * @return array
     * @throws Exception
     */
    public function getOrderById($orderId)
    {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if(!$order) {
            throw new \Exception('����� �� ������');
        }

        return $order;
    }

    public static function getMailTypeVariants($current)
    {
        return array(
            array(__('������� "�������������"'), 'POSTAL_PARCEL', $current),
            array(__('������� "������"'), 'ONLINE_PARCEL', $current),
            array(__('������ "������"'), 'ONLINE_COURIER', $current),
            array(__('����������� EMS'), 'EMS', $current),
            array(__('EMS �����������'), 'EMS_OPTIMAL', $current),
            array(__('EMS ��'), 'EMS_RT', $current),
            array(__('EMS ������'), 'EMS_TENDER', $current),
            array(__('������'), 'LETTER', $current),
            array(__('������ 1-�� ������'), 'LETTER_CLASS_1', $current),
            array(__('���������'), 'BANDEROL', $current),
            array(__('������ ������'), 'BUSINESS_COURIER', $current),
            array(__('������ ������ �������'), 'BUSINESS_COURIER_ES', $current),
            array(__('������� 1-�� ������'), 'PARCEL_CLASS_1', $current),
            array(__('��������� 1-�� ������'), 'BANDEROL_CLASS_1', $current),
            array(__('���� 1-�� ������'), 'VGPO_CLASS_1', $current),
            array(__('������ �����'), 'SMALL_PACKET', $current),
            array(__('������ �������'), 'EASY_RETURN', $current),
            array(__('����������� ���'), 'VSD', $current),
            array(__('����'), 'ECOM', $current),
        );
    }

    public static function getMailCategoryVariants($current)
    {
        return array(
            array(__('�������'), 'SIMPLE', $current),
            array(__('��������'), 'ORDERED', $current),
            array(__('������������'), 'ORDINARY', $current),
            array(__('� ����������� ���������'), 'WITH_DECLARED_VALUE', $current),
            array(__('� ����������� ��������� � ���������� ��������'), 'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY', $current),
            array(__('� ����������� ��������� � ������������ ��������'), 'WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT', $current),
            array(__('� ������������ ��������'), 'WITH_COMPULSORY_PAYMENT', $current),
            array(__('�������� � ��������'), 'COMBINED_ORDINARY', $current),
            array(__('�������� � �������� � ����������� ���������'), 'COMBINED_WITH_DECLARED_VALUE', $current)
        );
    }

    public static function getDimensionVariants($current)
    {
        return array(
            array(__('�� 260�170�80 ��'), 'S', $current),
            array(__('�� 300�200�150 ��'), 'M', $current),
            array(__('�� 400�270�180 ��'), 'L', $current),
            array(__('�� 530�260�220 ��'), 'XL', $current),
            array(__('���������'), 'OVERSIZED', $current)
        );
    }

    public static function getStatusesVariants($current)
    {
        $statusesObj = new PHPShopOrderStatusArray();
        $statuses = $statusesObj->getArray();

        $result[] = array(__('����� �����'), 0, $current);
        if (is_array($statuses)) {
            foreach ($statuses as $status) {
                $result[] = array($status['name'], $status['id'], $current);
            }
        }

        return $result;
    }

    public static function getDeliveryVariants($currentDelivery)
    {
        $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

        $DeliveryArray = $PHPShopDeliveryArray->getArray();
        $deliveries = array(
            array(__('�� �������'), 0, $currentDelivery)
        );
        if (is_array($DeliveryArray)) {
            foreach ($DeliveryArray as $delivery) {

                if (strpos($delivery['city'], '.')) {
                    $name = explode(".", $delivery['city']);
                    $delivery['city'] = $name[0];
                }

                $deliveries[] = array($delivery['city'], $delivery['id'], $currentDelivery);
            }
        }

        return $deliveries;
    }
}