<?php

function write_hook($obj, $row, $rout) {

    if ($rout == "END") {

        $commonSum = $obj->total;
    
        PHPShopObj::loadClass('parser');
        
        // ��������� ������
        include_once(dirname(__DIR__) . '/admpanel/mod_option.php');
        
        $PHPShopModule = new PHPShopModule();

        $cart = unserialize($row['orders_new']);
        
        // ��������� �������
        $orderTpl      = $PHPShopModule->getTplOrder();
        $orderTplAdmin = $PHPShopModule->getTplAdminOrder();

        if ($PHPShopModule->getCascadeEnabled()) {
            $orderTpl      = $PHPShopModule->getTplOrderViber();
            $orderTplAdmin = $PHPShopModule->getTplAdminOrderViber();
        }

        $PHPShopSystem = new PHPShopSystem();
        $nameShop = $PHPShopSystem->objRow['name'];
        $PHPShopValuta = new PHPShopValuta($PHPShopSystem->objRow['dengi']);
        $currency = $PHPShopValuta->getCode();


        foreach($cart['Cart']['cart'] as $k=>$v){
          $productName[] = $v['name'] . ' - ' . $v['price'] . ' ' . $currency .', ' . $v['num'] . ' ��.';
        }
        
        $product = implode(','."\r\n", $productName);
        
        PHPShopParser::set('OrderDate', date('d.m.Y', $row['datas_new']));
        PHPShopParser::set('ProductNum', $cart['Cart']['num']);
        PHPShopParser::set('ProductName', $product);
        PHPShopParser::set('OrderSum', $cart['Cart']['sum'] . ' ' . $currency);
        PHPShopParser::set('ProductDiscount', $cart['Person']['discount'] . '%');
        PHPShopParser::set('ProductDostavka', $cart['Cart']['dostavka'] . ' ' . $currency);
        PHPShopParser::set('OrderCommonSum', $commonSum . ' ' . $currency);
          
        $order = PHPShopParser::file(dirname(__DIR__).'/lib/templates/order/order.tpl', true);

        // ������ ������ ��� ������� ��� �������� ������
        $datainsertUser = array(
          '@NameShop@'      => $nameShop, 
          '@OrderNum@'      => $row['ouid'],
          '@OrderStatus@'   => '�����������',
          '@Order@'         => $order
         );

        // ������� �� ������� ������������ ���������
        $phone = array($PHPShopModule->true_num($row['tel_new']));

        // ���������
        $msgToUser = $PHPShopModule->parseString($orderTpl, $datainsertUser);

        // �������� ������
        $PHPShopDelivery = new PHPShopDelivery($row['dostavka_metod']);
        
        // ������ ������ ��� ������� ��� �������� ������
        $datainsertAdmin = array(
          '@NameShop@'        => $nameShop, 
          '@OrderNum@'        => $row['ouid'],
          '@UserFio@'         => $row['fio_new'],
          '@UserPhone@'       => $row['tel_new'],
          '@UserMail@'        => $row['mail'],
          '@UserDelivery@'    => $PHPShopDelivery->getCity(),
          '@UserCountry@'     => $row['country_new'],
          '@UserState@'       => $row['state_new'],
          '@UserCity@'        => $row['city_new'],
          '@UserIndex@'       => $row['index_new'],
          '@UserStreet@'      => $row['street_new'],
          '@UserHouse@'       => $row['house_new'],
          '@UserPorch@'       => $row['porch_new'],
          '@UserDoorPhone@'   => $row['door_phone_new'],
          '@UserFlat@'        => $row['flat_new'],
          '@UserDelivtime@'   => $row['delivtime_new'],
          '@UserDopInfo@'     => $row['dop_info'],
          '@CommonSumOrder@'  => $commonSum . ' ' . $currency,
          '@Order@'           => $order
        );
        $msgToAdmin = $PHPShopModule->parseString($orderTplAdmin, $datainsertAdmin);


        if ($PHPShopModule->getCascadeEnabled()) {
            // ��������� ������
            $PHPShopModule->sendSmsAdmin($msgToAdmin, 'order_template_admin_viber');

            // �������� ����������
            $PHPShopModule->sendSms($phone,$msgToUser, 'order_template_viber');
        } else {
            // ��������� ������
            $PHPShopModule->sendSmsAdmin($msgToAdmin);

            // �������� ����������
            $PHPShopModule->sendSms($phone,$msgToUser);
        }

    }
}

$addHandler = array
  (
    'write' => 'write_hook',
  );
?>