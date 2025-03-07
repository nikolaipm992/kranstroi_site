<?php

/**
 * ���������� ����������� Pechka54
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class Pechka54Rest {

    var $option = array();
    var $taxes = array();

    /**
     * �����������
     */
    public function __construct() {
        $this->option();
        $this->nds();
    }

    /**
     * ��������� ������
     */
    public function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_system']);
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ���
     */
    public function nds() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_taxes']);
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 10));
        if (is_array($data))
            foreach ($data as $val)
                $this->taxes[$val['tax_id']] = $val;
    }

    /**
     * �������� ���� � �����54
     * @param array $data ������ ������
     */
    function OFDStart($data) {

        $OrderId = $data['uid'];
        $sum = 0;

        // ������ ������
        $order = @unserialize($data['orders']);

        // ���
        $tax = $this->option['tax_product'];
        $tax_delivery = $this->option['tax_delivery'];

        if (class_exists('Pechka54Rest')) {


            $check['response']['kkm'] = $data['kkm'];
            $check['response']['OrderId'] = $OrderId;

            // �������
            if (empty($data['ofd_type']) or $data['ofd_type'] == 'registration') {
                $typeCheck = 'OpenCheckSell';
                $typeTable = 'registration';
            }
            // �������
            elseif ($data['ofd_type'] == 'return') {
                $typeCheck = 'OpenCheckReturn';
                $typeTable = 'return';
            }

            // �������� ����
            $check['response']['taskTable'][] = array(
                "data" => "",
                "type" => $typeCheck
            );

            // ������ �������
            $check['response']['taskTable'][] = array(
                "data" => $order['Person']['mail'],
                "type" => "ClientContact"
            );

            // �������
            if (is_array($order['Cart']['cart'])) {
                foreach ($order['Cart']['cart'] as $product) {
                    // ������
                    if ($order['Person']['discount'] > 0)
                        $price = $product['price'] - ($product['price'] * $order['Person']['discount'] / 100);
                    else
                        $price = $product['price'];

                    $check['response']['taskTable'][] = array(
                        'data' => $product['name'],
                        'type' => $typeTable,
                        'param' => array(
                            'price' => floatval(number_format($price, 2, '.', '')),
                            'quantity' => floatval(number_format($product['num'], 2, '.', '')),
                            'tax' => $tax
                        )
                    );
                    
                    // ����� �� �������
                    $sum+=floatval(number_format($price*$product['num'], 2, '.', ''));
                    
                }
            }

            // ��������
            if (!empty($order['Cart']['dostavka'])) {

                $check['response']['taskTable'][] = array(
                    'data' => '��������',
                    'type' => 'registration',
                    'param' => array(
                        'price' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                        'quantity' => 1,
                        'tax' => $tax_delivery
                    )
                );

                $sum+=floatval(number_format($order['Cart']['dostavka'], 2, '.', ''));
            }

            // �����
            $check['response']['taskTable'][] = array(
                'data' => '',
                'type' => 'payment',
                'param' => array(
                    'summ' => floatval(number_format($sum, 2, '.', '')),
                    'typeclose' => 1
                )
            );

            // �������� ����
            $check['response']['taskTable'][] = array(
                "data" => "",
                "type" => "CloseCheck"
            );

            $check["resultCode"] = 0;
            
            if($typeTable == 'registration')
            $check["resultInfo"] = "������ ���� ������� ������ �" . $OrderId;
            else $check["resultInfo"] = "������ ���� �������� ������ �" . $OrderId;
            $check["operation"] = $typeTable;
            

            // ������ �� ������
            $ofd_status = 1;

            // ������ ������
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->update(array('ofd_status_new' => $ofd_status, 'ofd_type_new' => $typeTable), array('id' => '="' . $data['id'] . '"'));

            return $check;
        }
    }

    /**
     * ����� JSON
     * @param array $result 
     */
    function compile($result) {
        header("HTTP/1.1 200");
        header("Content-Type: application/json; charset=utf8");

        if (is_array($result))
            echo json_encode(json_fix_cyr($result));
    }

}

?>