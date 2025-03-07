<?php

/**
 * ���������� ������ � CommerceML
 * @version 1.8
 * @package PHPShopClass
 * https://v8.1c.ru/tekhnologii/obmen-dannymi-i-integratsiya/standarty-i-formaty/protokol-obmena-s-saytom/
 * https://dev.1c-bitrix.ru/api_help/sale/xml/contragents.php
 */
class PHPShopCommerceML {

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopSystem;

        $this->exchange_key = $PHPShopSystem->getSerilizeParam("1c_option.exchange_key");
    }

    /**
     * ���������
     * @param array $where ������� ������
     * @return array
     */
    function category($where) {
        $Catalog = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

        // �� �������� ������� ��������
        $where['skin_enabled'] = "!='1'";

        $data = $PHPShopOrm->select(array('id,name,parent_to'), $where, false, array('limit' => 10000));
        if (is_array($data))
            foreach ($data as $row) {
                if ($row['id'] != $row['parent_to']) {
                    $Catalog[$row['id']]['id'] = $row['id'];
                    $Catalog[$row['id']]['name'] = $row['name'];
                    $Catalog[$row['id']]['parent_to'] = $row['parent_to'];
                }
            }

        return $Catalog;
    }

    /**
     * ���������
     * @param integer $id �� ���������
     * @return string
     */
    function setCategories($id) {
        $xml = '<������>';
        $category = $this->category(array('parent_to' => '=' . $id));
        foreach ($category as $val) {
            $xml .= '<������>
                <��>' . $val['id'] . '</��>
		<������������>' . str_replace(['&', '<', '>'], '', $val['name']) . '</������������>';
            $parent = $this->setCategories($val['id']);
            if (!empty($parent))
                $xml .= $parent;
            else
                $xml .= '<������/>';
            $xml .= '</������>';
        }

        $xml .= '</������>';

        return $xml;
    }

    /**
     * ����������� ������
     * @param array $product_row
     * @return string
     */
    function getImages($product_row) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . $product_row['id']), false, array('limit' => 10000));
        $xml = null;
        if (is_array($data))
            foreach ($data as $row) {
                $xml .= '<��������>http://' . $_SERVER['SERVER_NAME'] . $row['name'] . '</��������>';
            }

        if (empty($xml))
            $xml = '<��������>http://' . $_SERVER['SERVER_NAME'] . $product_row['pic_big'] . '</��������>';

        return $xml;
    }

    /**
     * ��������� CommerceML ��� �������
     * @param array $data
     * @return string
     */
    function getProducts($data) {
        global $PHPShopSystem;

        $xml = null;

        // ��������
        $category = $this->setCategories(0);

        // ������
        foreach ($data as $row)
            if (is_array($row)) {

                // ������� �������
                if ($row['parent_enabled'] == 1)
                    continue;

                if ($this->exchange_key == 'code') {
                    $code = $row['uid'];
                    $uid = null;
                    $id = $row['external_code'];
                }

                if ($this->exchange_key == 'uid') {
                    $code = null;
                    $uid = $row['uid'];
                    $id = $row['external_code'];
                }

                if ($this->exchange_key == 'external') {
                    $code = null;
                    $uid = null;
                    $id = $row['uid'];
                }

                $item .= '
                        <�����>
			<��>' . $id . '</��>
                        <���>' . $code . '</���>
			<�������>' . $uid . '</�������>
			<������������>' . str_replace(['&', '<', '>'], '', $row['name']) . '</������������>
                        <�������������� ���="796 " ������������������="�����" �����������������������="PCE">' . $row['ed_izm'] . '</��������������>
                        <������������������><![CDATA[' . $row['description'] . ']]></������������������>
			<������>
				<��>' . $row['category'] . '</��>
			</������>
                        <��������><![CDATA[' . $row['content'] . ']]></��������>
			<�������������>
				<������������>
					<������������>���</������������>
					<������>' . $PHPShopSystem->getParam('nds') . '</������>
				</������������>
			</�������������>
			<������������������>
				<�����������������>
					<������������>���������������</������������>
					<��������>�����</��������>
				</�����������������>
				<�����������������>
					<������������>���</������������>
					<��������>' . $row['weight'] . '</��������>
				</�����������������>
			</������������������>
                        ' . $this->getImages($row) . '
		</�����>
                ';
            }

        $items = ' <������� �����������������������="false">
	<��>1</��>
        <����������������>1</����������������>
	<������������>�������� ������� �������</������������>
		<������>
' . $item . '
		</������>
	</�������>';

        $xml = '<?xml version="1.0" encoding="windows-1251"?>
<���������������������� �����������="2.04" ����������������="' . PHPShopDate::get(time(), false, true) . 'T' . date("H:i:s") . '">
    <�������������>
    <��>1</��>
    <������������>������������� (�������� ������� �������)</������������>
    <��������>
       <��>1</��>
       <������������>' . $PHPShopSystem->getParam('name') . '</������������>
       <�����������������������>' . $PHPShopSystem->getParam('company') . '</�����������������������>
       <���>' . $PHPShopSystem->getParam('nds') . '</���>
       <���>' . $PHPShopSystem->getParam('kpp') . '</���>
    </��������>
	' . $category . '
    </�������������>
    ' . $items . '
</����������������������>';
        return $xml;
    }

    /**
     * ��������� CommerceML ��� ������
     * @param array $data
     * @return string
     */
    function getOrders($data) {
        global $PHPShopSystem;

        $xml = null;
        if (is_array($data))
            foreach ($data as $row)
                if (is_array($row)) {
                    
                    $PHPShopOrder = new PHPShopOrderFunction($row['id']);
                    $this->update_status[] = $row['id'];

                    $num = 0;
                    $id = $row['id'];
                    $uid = $row['uid'];

                    if (!empty($row['user']))
                        $user = $row['user'];
                    elseif (!empty($row['ozonseller_order_data']))
                        $user= 'Ozon';
                    elseif (!empty($row['wbseller_order_data']))
                        $user = 'WB';
                    elseif (!empty($row['yandex_order_id']))
                        $user = '������.������';
                    elseif (!empty($row['megamarket_order_id']))
                        $user= '����������';

                    $order = unserialize($row['orders']);
                    $status = unserialize($row['status']);
                    $sum = $PHPShopOrder->returnSumma($order['Cart']['sum'], $order['Person']['discount']);

                    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
                    $delivery = str_replace(['&', '<', '>'], '', $PHPShopDelivery->getCity());
                    $delivery_id = $PHPShopDelivery->getParam('external_code');

                    if (empty($delivery))
                        $delivery = '��������';

                    if (empty($delivery_id))
                        $delivery_id = 'ORDER_DELIVERY';

                    $item = '<�����>
		                   <��>' . $delivery_id . '</��>
		                   <������������>' . $delivery . '</������������>
				   <�������������>' . $order['Cart']['dostavka'] . '</�������������>
				   <����������>1</����������>
				   <�����>' . $order['Cart']['dostavka'] . '</�����>
				   <�������>��</�������>
                                   <������������������>
                                     <�����������������>
                                         <������������>���������������</������������>
                                         <��������>������</��������>
                                     </�����������������>
                                     <�����������������>
                                        <������������>���������������</������������>
                                       <��������>������</��������>
                                     </�����������������>
                                   </������������������>
			     </�����>';

                    if (is_array($order['Cart']['cart']))
                        foreach ($order['Cart']['cart'] as $val) {

                            $num = $val['num'];
                            $sum = $PHPShopOrder->returnSumma($val['price'] * $num, $order['Person']['discount']);

                            if ($this->exchange_key == 'code') {
                                $code = $val['uid'];
                                $uid = null;
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            if ($this->exchange_key == 'uid') {
                                $code = null;
                                $uid = $val['uid'];
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            if ($this->exchange_key == 'external') {
                                $code = null;
                                $uid = null;
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            // ������
                            if (!empty($val['parent'])) {
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['parent']])['external_code'] . '#' . $id;
                            }

                            $item .= '<�����>
				<��>' . $id . '</��>
                                <���>' . $code . '</���>
				<��������></��������>
				<�������>' . $uid . '</�������>
				<������������>' . str_replace(['&', '<', '>'], '', $val['name']) . '</������������>
				<�������������>' . $val['price'] . '</�������������>
				<����������>' . $val['num'] . '</����������>
				<�����>' . $sum . '</�����>
				<�������>��</�������>
			</�����>';
                        }

                    if (empty($row['fio']))
                        $row['fio'] = $row['org_name'];

                    // ����� ��������
                    $adr_info = null;
                    if ($row['city'])
                        $adr_info .= "�����: " . $row['city'];
                    if ($row['index'])
                        $adr_info .= ", ������: " . $row['index'];
                    if ($row['street'])
                        $adr_info .= ", �����: " . $row['street'];
                    if ($row['house'])
                        $adr_info .= ", ���: " . $row['house'];
                    if ($row['porch'])
                        $adr_info .= ", �������: " . $row['porch'];
                    if ($row['door_phone'])
                        $adr_info .= ", ��� ��������: " . $row['door_phone'];
                    if ($row['flat'])
                        $adr_info .= ", ��������: " . $row['flat'];
                    if ($row['delivtime'])
                        $adr_info .= ", ����� ��������: " . $row['delivtime'];
                    if ($row['dop_info'])
                        $adr_info .= ', ' . str_replace(['&', '<', '>'], '', $row['dop_info']);


                    $xml .= '
	<��������>
                <��>' . $row['id'] . '</��>
		<�����>' . $row['uid'] . '</�����>
		<����>' . PHPShopDate::get($row['datas'], false, true) . 'T' . date("H:i:s") . '</����>
                <�����>' . date("H:i:s") . '</�����>
                <�����������>' . html_entity_decode($status['maneger']) . '[����� ��������� �� �����: ' . $row['uid'] . ']</�����������>
		<�����������>����� ������</�����������>
		<����>��������</����>
		<������>' . $PHPShopSystem->getDefaultValutaIso() . '</������>
		<�����>' . $row['sum'] . '</�����>
                <�����������>
		   <����������>
                     <��>' . $user . '</��>
		      <������������>' . html_entity_decode($row['fio']) . '</������������>
		      <������������������>' . html_entity_decode($row['org_name']) . '</������������������>
		      <���>' . $row['org_inn'] . '</���>
		      <���>' . $row['org_kpp'] . '</���>
		      <����>����������</����>
                      <����������������>
                        <�������������>' . html_entity_decode($adr_info) . '</�������������>
                        <������������>
                          <���>�����</���>
                          <��������>' . html_entity_decode($row['city']) . '</��������>
                        </������������>
                        <������������>
                          <���>�����</���>
                          <��������>' . html_entity_decode($row['street']) . '</��������>
                        </������������>
                        <������������>
                          <���>���</���>
                          <��������>' . html_entity_decode($row['house']) . '</��������>
                        </������������>
                        <������������>
                          <���>��������</���>
                          <��������>' . html_entity_decode($row['flat']) . '</��������>
                        </������������>
                      </����������������>
                        <��������>
			       <�������>
					<���>����������� �����</���>
					<��������>' . $PHPShopOrder->getMail() . '</��������>
				</�������>
				<�������>
					<���>������� �������</���>
					<��������>' . $row['tel'] . '</��������>
				</�������>
			</��������>
		   </����������>
                </�����������>
		<������>
                  ' . $item . '
		</������>
                <������������������>
                  <�����������������>
                     <������������>����� ������</������������>
                     <��������>' . $PHPShopOrder->getOplataMetodName() . '</��������>
                  </�����������������>
                  <�����������������>
		      <������������>����� ��������</������������>
		      <��������>' . html_entity_decode($adr_info) . '</��������>
		  </�����������������>
        </������������������>
	</��������>';
                }

        $xml = '<?xml version="1.0" encoding="windows-1251"?>
<���������������������� �����������="2.04" ����������������="' . PHPShopDate::get(time(), false, true) . 'T' . date("H:i:s") . '">
	' . $xml . '
</����������������������>';

        return $xml;
    }

}

?>