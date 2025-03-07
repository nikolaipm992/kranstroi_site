<?php

class Bitrix24 {

    /*
     * ������ ��������� ��� ��������
     */
    private $categories = array();

    /*
     * ������ ������� ��� ��������
     */
    private $products = array();

    /*
     * ID ������ �������� � �������24
     */
    private $delivery_id;

    public function __construct($order = array())
    {

        $this->PHPShopOrm = new PHPShopOrm();
        $PHPShopSystem = new PHPShopSystem();

        /*
         * ����� ������
         */
        $this->PHPShopOrm->objBase = 'phpshop_modules_bitrix24_system';
        $this->option = $this->PHPShopOrm->select();

        /*
         * ��� ������
         */
        $this->iso = $PHPShopSystem->getDefaultValutaIso();

        /*
         * �������� �����������
         */
        $this->image_source = $PHPShopSystem->ifSerilizeParam('admoption.image_save_source');

        /*
         * �����
         */
        if(isset($order['orders']) and !empty($order['orders']))
            $order['orders'] = unserialize($order['orders']);
        $this->order = $order;

        /*
         * ��������� ������
         */
        if($this->order['orders']['Person']['discount'] > 0) {
            foreach ($this->order['orders']['Cart']['cart'] as $product)
                $this->order['orders']['Cart']['cart'][$product['id']]['price'] = $this->order['orders']['Cart']['cart'][$product['id']]['price']  -
                    $this->order['orders']['Cart']['cart'][$product['id']]['price']  * ($this->order['orders']['Person']['discount']  / 100);
        }
    }

    public function init()
    {
        $this->getProducts();

        $this->categories();

        $this->products();

        $user = $this->customer();

        $this->delivery();

        $this->deal($user);
    }

    /*
     * ������������� ���������
     *
     * @param array $cart ������ ������� � ������
     * @return void
     */
    public function categories()
    {
        // ��������� ����� ��������� ��� � �������24 � ��������� �� � ������
        foreach ($this->categories as $category_id => $category) {
            if(empty($category['bitrix24_category_id'])) {
                $result = $this->request('crm.productsection.add',
                    array(
                        'fields' => array(
                            'NAME' => PHPShopString::win_utf8($category['name'])
                        )
                    )
                );

                if(is_int($result['result'])) {
                    $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['categories'];
                    $this->PHPShopOrm->_SQL = '';
                    $this->PHPShopOrm->update(array('bitrix24_category_id_new' => "$result[result]"), array('id=' => "$category_id"));
                    $this->categories[$category_id]['bitrix24_category_id'] = $result['result'];
                } else {
                    $this->log(array('parameters' => array(
                        'fields' => array(
                            'NAME' => PHPShopString::win_utf8($category['name'])
                        )
                    ), 'response' => $result), $this->order['uid'], '������ �������� ���������', 'createCategory', 'error');
                }
            }
        }
        // ��������� �����������
        foreach ($this->categories as $category) {
            if($category['parent_to'] > 0 and $this->categories[$category['parent_to']]['bitrix24_category_id'] > 0)
                $this->request('crm.productsection.update',
                    array(
                        'id' => $category['bitrix24_category_id'],
                        'fields' => array(
                            'SECTION_ID' => $this->categories[$category['parent_to']]['bitrix24_category_id']
                        )
                    )
                );
        }
    }

    /*
     * ������������� �������.
     *
     * @return void
     */
    public function products()
    {
        foreach ($this->products as $product) {
            $fields = array(
                'fields' => array(
                    'NAME' => PHPShopString::win_utf8($product['name']),
                    'CURRENCY_ID' => $this->iso,
                    'PRICE' => $this->order['orders']['Cart']['cart'][$product['id']]['price'],
                    'SECTION_ID' => $this->categories[$product['category']]['bitrix24_category_id'],
                    'DESCRIPTION' => PHPShopString::win_utf8($product['content'])
                )
            );

            // ����������� ������������� ������ ������� (���� � ������� ��� �����������)
            if (empty($product['pic_small'])) {
                $objProductParent = new PHPShopProduct($this->order['orders']['Cart']['cart'][$product['id']]['parent']);
                $product['pic_small'] = $objProductParent->objRow['pic_small'];
                $product['pic_big'] = $objProductParent->objRow['pic_big'];
            }

            $fields['fields']['PREVIEW_PICTURE'] = $this->getPicture($product['pic_small']);
            $fields['fields']['DETAIL_PICTURE']  = $this->getPicture($product['pic_big'], $this->image_source);

            // ���� ����� ��� �� ���������� � CRM - ���������
            if(empty($product['bitrix24_product_id'])) {
                $result = $this->request('crm.product.add', $fields);

                if(is_int($result['result'])) {
                    $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['products'];
                    $this->PHPShopOrm->_SQL = '';
                    $this->PHPShopOrm->update(array('bitrix24_product_id_new' => "$result[result]"), array('id=' => "$product[id]"));
                    $this->products[$product['id']]['bitrix24_product_id'] = $result['result'];
                } else {
                    $this->log(array('parameters' => array(
                        'fields' => array(
                            'NAME' => PHPShopString::win_utf8($product['name']),
                            'CURRENCY_ID' => $this->iso,
                            'PRICE' => $this->order['orders']['Cart']['cart'][$product['id']]['price'],
                            'SECTION_ID' => $this->categories[$product['category']]['bitrix24_category_id'],
                            'DESCRIPTION' => PHPShopString::win_utf8($product['content'])
                        )
                    ), 'response' => $result), $this->order['uid'], '������ �������� ������', 'createProduct', 'error');
                }
            }
            // ��������� ����
            else {
                $this->request('crm.product.update',
                    array(
                        'id' => $product['bitrix24_product_id'],
                        'fields' => array(
                            'CURRENCY_ID' => $this->iso,
                            'PRICE' => $this->order['orders']['Cart']['cart'][$product['id']]['price'],
                        )
                    )
                );
            }
        }
    }

    /*
     * ������������� ����������.
     *
     * @return array
     */
    public function customer()
    {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        $this->PHPShopOrm->_SQL = '';
        $user = $this->PHPShopOrm->select(array('bitrix24_client_id', 'bitrix24_company_id'), array('id=' => '"' . $this->order['user'] . '"'));

        if((int) $user['bitrix24_client_id'] === 0) {
            $user['bitrix24_client_id'] = $this->addContact();
        }

        if((int) $user['bitrix24_company_id'] === 0 && !empty($this->order['org_name'])) {
            $user['bitrix24_company_id'] = $this->addCompany();
        }

        return $user;
    }

    /*
    * ���������� ��������.
    *
    * @return void
    */
    public function addCompany()
    {
        $fields = array(
            'fields' => array(
                'TITLE'        => PHPShopString::win_utf8($this->order['org_name']),
                'COMPANY_TYPE' => 'CUSTOMER'
            )
        );

        if(!empty($this->order['tel']))
        {
            $fields['fields']['PHONE'] = array(
                array(
                    'VALUE' => $this->order['tel'],
                    'VALUE_TYPE' => 'WORK'
                )
            );
        }

        if(!empty($this->order['orders']['Person']['mail']))
        {
            $fields['fields']['EMAIL'] = array(
                array(
                    'VALUE' => $this->order['orders']['Person']['mail'],
                    'VALUE_TYPE' => 'WORK'
                )
            );
        }
        $result = $this->request('crm.company.add', $fields);

        if(is_int($result['result'])) {
            $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
            $this->PHPShopOrm->_SQL = '';
            $this->PHPShopOrm->update(array('bitrix24_company_id_new' => "$result[result]"), array('id=' => '"' . $this->order['user'] . '"'));

            return $result['result'];
        }

        $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], '������ �������� ��������', 'createCompany', 'error');
    }

    /*
    * ���������� ��������.
    *
    * @return void
    */
    public function addContact()
    {
        if(empty($this->order['fio']))
            $name = $this->order['orders']['Person']['name_person'];
        else
            $name = $this->order['fio'];
        $names = explode(' ', $name);

        $fields = array(
            'fields' => array(
                'LAST_NAME' => PHPShopString::win_utf8($names[0]),
                'TYPE_ID'   => 'CLIENT',
                'SOURCE_ID' => 'WEB'
            )
        );

        if(isset($names[1]) and !empty($names[1]))
            $fields['fields']['NAME'] = PHPShopString::win_utf8($names[1]);

        if(isset($names[2]) and !empty($names[2]))
            $fields['fields']['SECOND_NAME'] = PHPShopString::win_utf8($names[2]);

        if(!empty($this->order['tel']))
        {
            $fields['fields']['PHONE'] = array(
                array(
                    'VALUE' => $this->order['tel'],
                    'VALUE_TYPE' => 'WORK'
                )
            );
        }

        if(!empty($this->order['orders']['Person']['mail'])) {
            $fields['fields']['EMAIL'] = array(
                array(
                    'VALUE' => $this->order['orders']['Person']['mail'],
                    'VALUE_TYPE' => 'WORK'
                )
            );
        }

        $result = $this->request('crm.contact.add', $fields);

        if(is_int($result['result'])) {
            $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['shopusers'];
            $this->PHPShopOrm->_SQL = '';
            $this->PHPShopOrm->update(array('bitrix24_client_id_new' => "$result[result]"), array('id=' => '"' . $this->order['user'] . '"'));

            return $result['result'];
        }

        $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], '������ �������� ��������', 'createContact', 'error');
    }

    /*
     * ���������� ������ ��������.
     *
     * @return void
     */
    public function delivery()
    {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['delivery'];
        $this->PHPShopOrm->_SQL = '';
        $delivery = $this->PHPShopOrm->select(array('*'), array('id=' => '"' . $this->order['orders']['Person']['dostavka_metod'] . '"'));

        // ��������� ���� ������ ��������
        if($delivery['bitrix24_delivery_id'] > 0) {
            $this->delivery_id = $delivery['bitrix24_delivery_id'];

            $this->request('crm.product.update',
                array(
                    'id' => $this->delivery_id,
                    'fields' => array(
                        'CURRENCY_ID' => $this->iso,
                        'PRICE' => $this->order['orders']['Cart']['dostavka'],
                    )
                )
            );
        }
        // ��������� ����� ��������
        else {
            $fields = array(
                'fields' => array(
                    'NAME' => PHPShopString::win_utf8($delivery['city']),
                    'CURRENCY_ID' => $this->iso,
                    'PRICE' => $this->order['orders']['Cart']['dostavka'],
                    'SECTION_ID' => 0
                )
            );
            $result = $this->request('crm.product.add', $fields);

            if(is_int($result['result'])) {
                $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['delivery'];
                $this->PHPShopOrm->_SQL = '';
                $this->PHPShopOrm->update(array('bitrix24_delivery_id_new' => "$result[result]"), array('id=' => '"' . $this->order['orders']['Person']['dostavka_metod'] . '"'));
                $this->delivery_id = $result['result'];
            } else {
                $this->log(array('parameters' => $fields, 'response' => $result), $this->order['uid'], '������ �������� ������ ��������', 'createDelivery', 'error');
            }
        }

    }

    /*
     * ���������� ������.
     *
     * @return void
     */
    public function deal($user)
    {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['payment_systems'];
        $this->PHPShopOrm->_SQL = '';
        $payment_method = $this->PHPShopOrm->select(array('name'), array('id=' => '"' . $this->order['orders']['Person']['order_metod'] . '"'));

        if(!empty($this->order['city']))
            $city = $this->order['city'] . ', ';
        else
            $city = ' ';

        if(!empty($this->order['street']))
            $adress = '. ����� ��������:' . $city . $this->order['street'];
        else
            $adress = '.';
        if(!empty($this->order['house']))
            $adress .= ' ' . $this->order['house'];

        if(!empty($this->order['flat']))
            $adress .= ', ��. ' . $this->order['flat'];

        $urdata = '';
        if(!empty($this->order['org_inn'])) {
            $urdata .= '��� ' . $this->order['org_inn'];
        }
        if(!empty($this->order['org_ras'])) {
            $urdata .= '<br>�/� ' . $this->order['org_ras'];
        }
        if(!empty($this->order['org_kpp'])) {
            $urdata .= '<br>��� ' . $this->order['org_kpp'];
        }

        if(!empty($this->order['dop_info']))
            $comment = '�����������: ' . $this->order['dop_info'];
        else
            $comment = '.';

        $fields = array(
            'fields' => array(
                'TITLE'       => PHPShopString::win_utf8('����� �' . $this->order['uid']),
                'TYPE_ID'     => 'GOODS',
                'STAGE_ID'    => 'NEW',
                'CURRENCY_ID' => $this->iso,
                'OPPORTUNITY' => $this->order['sum'],
                'COMMENTS'    => PHPShopString::win_utf8('������ ������: ' . $payment_method['name'] . '<br>' . $urdata . '<br>' . $adress . '<br>' . $comment),
            )
        );

        if(!empty($user['bitrix24_company_id'])) {
            $fields['fields']['COMPANY_ID'] = $user['bitrix24_company_id'];
        }

        $fields['fields']['CONTACT_ID'] = $user['bitrix24_client_id'];

        $result = $this->request('crm.deal.add', $fields);

        if(is_int($result['result'])) {

            $rows = array();
            foreach ($this->products as $product){
                $rows[] = array(
                    'PRODUCT_ID' => $product['bitrix24_product_id'],
                    'PRICE'      => $this->order['orders']['Cart']['cart'][$product['id']]['price'],
                    'QUANTITY'   => $this->order['orders']['Cart']['cart'][$product['id']]['num']
                );
            }

            $rows[] = array(
                'PRODUCT_ID' => $this->delivery_id,
                'PRICE'      => $this->order['orders']['Cart']['dostavka'],
                'QUANTITY'   => 1
            );

            $productsResult = $this->request('crm.deal.productrows.set', array(
                'id'   => $result['result'],
                'rows' => $rows
            ));

            $this->log(array(
                'deal' => array(
                    'parameters' => $fields,
                    'response'   => $result
                ),
                'products' => array(
                    'parameters' => array(
                        'id'   => $result['result'],
                        'rows' => $rows
                    ),
                    'response'   => $productsResult
                )
            ), $this->order['id'], '�������� �������� ������', 'createDeal', 'success');

            $orm = new PHPShopOrm('phpshop_orders');
            $orm->update(array('bitrix24_deal_id_new' => $result['result']), array('uid' => "='" . $this->order['uid'] . "'"));

        } else {
            $this->log(array('parameters' => $fields, 'response' => $result), $this->order['id'], '������ �������� ������', 'createDeal', 'error');
        }
    }

    public function getProducts()
    {
        $product_id = array();
        foreach ($this->order['orders']['Cart']['cart'] as $cart)
            $product_id[] = $cart['id'];

        $this->PHPShopOrm->_SQL = '';
        $query = $this->PHPShopOrm->query("SELECT * FROM " . $GLOBALS['SysValue']['base']['products'] . " WHERE `id` IN ('" . implode("', '", $product_id) . "')");

        while($row = $query->fetch_assoc()){
            $this->products[$row['id']] = $row;
            // �������� ��������� ������, ��� ��� ��������� �� ��������� (���� ��������� ��� �� ��������� � �������24)
            $this->getCategory($row['category']);
        }
    }

    public function getCategory($id)
    {
        $this->PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['categories'];
        $this->PHPShopOrm->_SQL = '';
        $this->categories[$id] = $this->PHPShopOrm->select(array('id', 'name', 'parent_to', 'bitrix24_category_id'), array('id=' => "$id"));

        if(empty($this->categories[$id]['bitrix24_category_id']) and $this->categories[$id]['parent_to'] > 0)
            $this->getCategory($this->categories[$id]['parent_to']);
    }

    public function getPicture($pictureLink, $sourceSetting = false)
    {
        $picture = null;

        if (!empty($pictureLink)) {
            if (strpos('http:', $pictureLink) === false or strpos('https:', $pictureLink) === false) {

                if (!empty($sourceSetting))
                    $pictureLink = str_replace(".", "_big.", $pictureLink);

                $link = 'http://' . $_SERVER['SERVER_NAME'] . $pictureLink;
            }
            else
                $link = $pictureLink;

            $pictureLinkParts = explode('/', $link);

            return array(
                'fileData' => array(
                    array_pop($pictureLinkParts),
                    base64_encode(file_get_contents($link))
                )
            );
        }
    }

    public function getDealStages()
    {
        return $this->request('crm.status.entity.items', array(
            'entityId' => 'DEAL_STAGE'
        ));
    }

    /*
     * ������� � API �������24
     */
    public function request($operation, $parameters) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->option['webhook_url'] . '/' . $operation);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return json_decode(curl_exec($ch),1);
    }

    /**
     * ������ ����
     * @param array $message ���������� ������� � �� ��� ���� �������
     * @param string $order_id ����� ������
     * @param string $status ������ ��������
     * @param string $type request
     */
    public function log($message, $order_id, $status, $type, $status_code = 'succes')
    {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_bitrix24_log');
        $id = explode("-", $order_id);

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $id[0],
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'status_code_new' => $status_code
        );
        $PHPShopOrm->insert($log);
    }
}
