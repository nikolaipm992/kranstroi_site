<?php

PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexcart.yandexcart_system"));

// ���������� ���
function actionUpdatePrice() {

    // ������������
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/yandexcart/cron/products.php?s=" . $cron_secure ;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name, $PHPShopSystem;

    $PHPShopGUI->field_col = 5;
    PHPShopObj::loadClass("order");

    // �������
    $data = $PHPShopOrm->select();

    $options = unserialize($data['options']);


    $PHPShopGUI->addJSFiles('../modules/yandexcart/admpanel/gui/yandexcart.gui.js');
    
    if (!empty($data['auth_token_2'])) {
        $PHPShopGUI->action_button['��������� ������'] = [
            'name' => __('��������� ������'),
            'class' => 'btn btn-default btn-sm navbar-btn ozon-export',
            'type' => 'button',
            'icon' => 'glyphicon glyphicon-open'
        ];
        
        switch($data['export']){
            case 0: 
                $export_name = __('��������� ���� � �����');
                break;
            case 1: 
                $export_name = __('��������� ����');
                break;
            case 2: 
                $export_name = __('��������� �����');
                break;
            
        }
        
        
        $PHPShopGUI->action_button['��������� ����'] = [
            'name' => $export_name,
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'exportID',
            'icon' => 'glyphicon glyphicon-export'
        ];
        
        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['��������� ����','��������� � �������']);
    }

    isset($options['statuses']) && is_array($options['statuses']) ? $statuses = $options['statuses'] : $statuses = [];
    isset($options['payments']) && is_array($options['payments']) ? $payments = $options['payments'] : $payments = [];

    // ��������
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));
    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }
            $delivery_value[] = array($delivery['city'], $delivery['id'], $data['delivery_id']);
        }
    }


    $models = [
        ['�� ������������', '', $data['model']],
        ['FBS (������� � ������������ ��������)', 'FBS', $data['model']],
        ['DBS (������� � ��������� ��������)', 'DBS', $data['model']],
        ['FBY (������� � ������������ �������)', 'FBY', $data['model']],
        ['�������� (������� � ��������� �������� �������)', '��������', $data['model']]
    ];

    $models_2 = [
        ['�� ������������', '', $data['model']],
        ['FBS (������� � ������������ ��������)', 'FBS', $data['model_2']],
        ['DBS (������� � ��������� ��������)', 'DBS', $data['model_2']],
        ['FBY (������� � ������������ �������)', 'FBY', $data['model_2']],
        ['�������� (������� � ��������� �������� �������)', '��������', $data['model_2']]
    ];

    $models_3 = [
        ['�� ������������', '', $data['model']],
        ['FBS (������� � ������������ ��������)', 'FBS', $data['model_3']],
        ['DBS (������� � ��������� ��������)', 'DBS', $data['model_3']],
        ['FBY (������� � ������������ �������)', 'FBY', $data['model_3']],
        ['�������� (������� � ��������� �������� �������)', '��������', $data['model_3']]
    ];


    // ������
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    $warehouse_value[] = array('����� �����', 0, $data['warehouse']);
    $warehouse_value_2[] = array('����� �����', 0, $data['warehouse_2']);
    $warehouse_value_3[] = array('����� �����', 0, $data['warehouse_3']);
    if (is_array($dataWarehouse)) {
        foreach ($dataWarehouse as $val) {
            $warehouse_value[] = array($val['name'], $val['id'], $data['warehouse']);
            $warehouse_value_2[] = array($val['name'], $val['id'], $data['warehouse_2']);
            $warehouse_value_3[] = array($val['name'], $val['id'], $data['warehouse_3']);
        }
    }

    // ��������
    $shopOption1 = $PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('model_new', $models, '100%', true));
    $shopOption1 .= $PHPShopGUI->setField('������������� ��������', $PHPShopGUI->setInputText('', 'campaign_id_new', $data['campaign_id']));
    $shopOption1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('warehouse_new', $warehouse_value, '100%', true));
    if (!empty($data['campaign_id']))
        $shopOption1 .= $PHPShopGUI->setField('������ �� ���� � ��������', $PHPShopGUI->setText($PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?pas=' . $data['password'], 'http://' . $_SERVER['SERVER_NAME'] . '/yml/')));

    $shopOption2 = $PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('model_2_new', $models_2, '100%', true));
    $shopOption2 .= $PHPShopGUI->setField('������������� ��������', $PHPShopGUI->setInputText('', 'campaign_id_2_new', $data['campaign_id_2']));
    $shopOption2 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('warehouse_2_new', $warehouse_value_2, '100%', true));
    if (!empty($data['campaign_id_2']))
        $shopOption2 .= $PHPShopGUI->setField('������ �� ���� � ��������', $PHPShopGUI->setText($PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?pas=' . $data['password'] . '&campaign=2', 'http://' . $_SERVER['SERVER_NAME'] . '/yml/?campaign=2')));

    $shopOption3 = $PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('model_3_new', $models_3, '100%', true));
    $shopOption3 .= $PHPShopGUI->setField('������������� ��������', $PHPShopGUI->setInputText('', 'campaign_id_3_new', $data['campaign_id_3']));
    $shopOption3 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('warehouse_3_new', $warehouse_value_3, '100%', true));
    if (!empty($data['campaign_id_3']))
        $shopOption3 .= $PHPShopGUI->setField('������ �� ���� � ��������', $PHPShopGUI->setText($PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?pas=' . $data['password'] . '&campaign=3', 'http://' . $_SERVER['SERVER_NAME'] . '/yml/?campaign=3')));

    $Tab1 .= $PHPShopGUI->setField('������������� ��������', $PHPShopGUI->setInputText(null, 'businesses_id_new', $data['businesses_id']));
    $Tab1 .= $PHPShopGUI->setField('��������������� ����� ��� ��������� �������� �� �������', $PHPShopGUI->setInputText(null, 'auth_token_new', $data['auth_token']));
    $Tab1 .= $PHPShopGUI->setField('��������������� ����� ��� �������� �������� �������', $PHPShopGUI->setInputText(null, 'auth_token_2_new', $data['auth_token_2']));
    //$Tab1 .= $PHPShopGUI->setField('ID ���������� ������.OAuth', $PHPShopGUI->setInputText(null, 'client_id_new', $data['client_id']));
    //$Tab1 .= $PHPShopGUI->setField('OAuth-�����', $PHPShopGUI->setInputText(null, 'client_token_new', $data['client_token'], false, '<a target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&client_id=" id="client_token">' . __('��������') . '</a>'));
    $Tab1 .= $PHPShopGUI->setField('���� ����������', $PHPShopGUI->setRadio("type_new", 1, "ID ������", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "������� ������", $data['type']));
    $Tab1 .= $PHPShopGUI->setField('������ ������ ����� � ��������', $PHPShopGUI->setInputText(null, 'password_new', $data['password']));
    $Tab1 .= $PHPShopGUI->setField('����������� ����� �������', $PHPShopGUI->setCheckbox('stop_new', 1, null, $data['stop']), 1, '��� ������� FBS/DBS');
    //$Tab1 .= $PHPShopGUI->setField('��������� �����������', $PHPShopGUI->setCheckbox('options[block_image]', 1, null, $options['block_image']));
    //$Tab1 .= $PHPShopGUI->setField('��������� ��������', $PHPShopGUI->setCheckbox('options[block_content]', 1, null, $options['block_content']));
    $Tab1 .= $PHPShopGUI->setField('����� �������������', $PHPShopGUI->setCheckbox('use_params_new', 1, '�������� ����� ������������� � YML', $data['use_params']));

    $export_value[] = ['���� � �����', 0, $data['export']];
    $export_value[] = ['����', 1, $data['export']];
    $export_value[] = ['�����', 2, $data['export']];

    $Tab1 .= $PHPShopGUI->setField('���������� ������', $PHPShopGUI->setSelect('export_new', $export_value, '100%', true));
    $Tab1 .= $PHPShopGUI->setField('�������� ��� ������� � �������', $PHPShopGUI->setSelect('delivery_id_new', $delivery_value, 300, null));
    $Tab1 .= $PHPShopGUI->setField('������ ��������', $PHPShopGUI->setCheckbox('log_new', 1, null, $data['log']));
    $Tab1 .= $PHPShopGUI->setField('������ �� �����', $PHPShopGUI->setCheckbox('link_new', 1, '�������� ������ �� ����� � �������', $data['link']));
    $Tab1 .= $PHPShopGUI->setField('��������� �����', $PHPShopGUI->setCheckbox('create_products_new', 1, '��������� ������������� ����� �� ������', $data['create_products']));


    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (is_array($CategoryArray))
        $GLOBALS['count'] = count($CategoryArray);

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // �����������
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);

            // �����������
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';



    if (!empty($data['model']))
        $campaign[] = ['�������� &#8470;1 ' . $data['model'], 0, 0];
    if (!empty($data['model_2']))
        $campaign[] = ['�������� &#8470;2 ' . $data['model_2'], 2, 1];
    if (!empty($data['model_3']))
        $campaign[] = ['�������� &#8470;3 ' . $data['model_3'], 3, 1];

    // ����� ��������
    $catOption = $PHPShopGUI->setField("����������", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("������ ������", $PHPShopGUI->setSelect("campaign", $campaign, '100%'));
    $catOption .= $PHPShopGUI->setField("����� � ������.�������", $PHPShopGUI->setRadio("enabled_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "����.", 1));

    $Tab1 .= $PHPShopGUI->setField('������ ��������� ��������', '<div id="yandexDescriptionShablon">
<textarea class="form-control yandex-shablon" name="description_template_new" rows="3" style="width: 100%;height: 70px;">' . $data['description_template'] . '</textarea>
    <div class="btn-group" role="group" aria-label="...">
    <input  type="button" value="' . __('��������') . '" onclick="yandexShablonAdd(\'@Content@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('������� ��������') . '" onclick="yandexShablonAdd(\'@Description@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('��������������') . '" onclick="yandexShablonAdd(\'@Attributes@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('�������') . '" onclick="yandexShablonAdd(\'@Catalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('����������') . '" onclick="yandexShablonAdd(\'@Subcatalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('�����') . '" onclick="yandexShablonAdd(\'@Product@\',)" class="btn btn-default btn-sm">
    </div>
</div>
<script>function yandexShablonAdd(variable) {
    var shablon = $(".yandex-shablon").val() + " " + variable;
    $(".yandex-shablon").val(shablon);
}</script>', 1, '�������������� � �������� ������� �������������� ��������. ������������� ������������ ������ ��� ������ ���������� ���������� �������.');

    $priceOption1 = $PHPShopGUI->setField('������� ��� ������.������', $PHPShopGUI->setSelect('options[price]', $PHPShopGUI->setSelectValue($options['price'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_fee]', $options['price_fee'], 100, '%')) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setInputText(null, 'options[price_markup]', $options['price_markup'], 100, $PHPShopSystem->getDefaultValutaCode()));

    $priceOption2 = $PHPShopGUI->setField('������� ��� ������.������', $PHPShopGUI->setSelect('options[price_2]', $PHPShopGUI->setSelectValue($options['price_2'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_fee_2]', $options['price_fee_2'], 100, '%')) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setInputText(null, 'options[price_markup_2]', $options['price_markup_2'], 100, $PHPShopSystem->getDefaultValutaCode()));

    $priceOption3 = $PHPShopGUI->setField('������� ��� ������.������', $PHPShopGUI->setSelect('options[price_3]', $PHPShopGUI->setSelectValue($options['price_3'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_fee_3]', $options['price_fee_3'], 100, '%')) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setInputText(null, 'options[price_markup_3]', $options['price_markup_3'], 100, $PHPShopSystem->getDefaultValutaCode()));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1).
            $PHPShopGUI->setCollapse('�������� &#8470;1', $shopOption1 . $priceOption1) .
            $PHPShopGUI->setCollapse('�������� &#8470;2', $shopOption2 . $priceOption2) .
            $PHPShopGUI->setCollapse('�������� &#8470;3', $shopOption3 . $priceOption3);
    $Tab1 .= $PHPShopGUI->setCollapse('������', $catOption);

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $status) {
            $status_delivered_value[] = [
                $status['name'], $status['id'], isset($statuses['delivered']) ? $statuses['delivered'] : null
            ];
            $status_delivery_value[] = [
                $status['name'], $status['id'], isset($statuses['delivery']) ? $statuses['delivery'] : null
            ];
            $status_pickup_value[] = [
                $status['name'], $status['id'], isset($statuses['pickup']) ? $statuses['pickup'] : null
            ];
            $status_unpaid_value[] = [
                $status['name'], $status['id'], isset($statuses['unpaid']) ? $statuses['unpaid'] : null
            ];
            $status_started_value[] = [
                $status['name'], $status['id'], isset($statuses['processing_started']) ? $statuses['processing_started'] : null
            ];
            $status_delivery_service_undelivered_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_delivery_service_undelivered']) ? $statuses['cancelled_delivery_service_undelivered'] : null
            ];
            $status_processing_expired_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_processing_expired']) ? $statuses['cancelled_processing_expired'] : null
            ];
            $status_replacing_order_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_replacing_order']) ? $statuses['cancelled_replacing_order'] : null
            ];
            $status_reservation_expired_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_reservation_expired']) ? $statuses['cancelled_reservation_expired'] : null
            ];
            $status_reservation_failed_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_reservation_failed']) ? $statuses['cancelled_reservation_failed'] : null
            ];
            $status_shop_failed_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_shop_failed']) ? $statuses['cancelled_shop_failed'] : null
            ];
            $status_user_changed_mind_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_user_changed_mind']) ? $statuses['cancelled_user_changed_mind'] : null
            ];
            $status_user_not_paid_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_user_not_paid']) ? $statuses['cancelled_user_not_paid'] : null
            ];
            $status_user_refused_delivery_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_user_refused_delivery']) ? $statuses['cancelled_user_refused_delivery'] : null
            ];
            $status_user_refused_product_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_refused_product']) ? $statuses['cancelled_refused_product'] : null
            ];
            $status_user_refused_quality_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_refused_quality']) ? $statuses['cancelled_refused_quality'] : null
            ];
            $status_user_unreachable_value[] = [
                $status['name'], $status['id'], isset($statuses['cancelled_unreachable']) ? $statuses['cancelled_unreachable'] : null
            ];
        }

    // ������� ������
    $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $paymentsArr = $paymentOrm->getList();

    foreach ($paymentsArr as $payment) {
        $payment_yandex_value[] = [
            $payment['name'], $payment['id'], isset($payments['yandex']) ? $payments['yandex'] : null
        ];
        $payment_apple_pay_value[] = [
            $payment['name'], $payment['id'], isset($payments['apple_pay']) ? $payments['apple_pay'] : null
        ];
        $payment_google_pay_value[] = [
            $payment['name'], $payment['id'], isset($payments['google_pay']) ? $payments['google_pay'] : null
        ];
        $payment_credit_value[] = [
            $payment['name'], $payment['id'], isset($payments['credit']) ? $payments['credit'] : null
        ];
        $payment_certificate_value[] = [
            $payment['name'], $payment['id'], isset($payments['certificate']) ? $payments['certificate'] : null
        ];
        $payment_card_on_delivery_value[] = [
            $payment['name'], $payment['id'], isset($payments['card_on_delivery']) ? $payments['card_on_delivery'] : null
        ];
        $payment_cash_on_delivery_value[] = [
            $payment['name'], $payment['id'], isset($payments['cash_on_delivery']) ? $payments['cash_on_delivery'] : null
        ];
    }

    $Tab1 .= $PHPShopGUI->setCollapse('������� ������ ��� DBS/FBS', $PHPShopGUI->setField('���������� ������ ��� ���������� ������', $PHPShopGUI->setSelect('payments[yandex]', $payment_yandex_value)
            ) .
            $PHPShopGUI->setField('Apple Pay', $PHPShopGUI->setSelect('payments[apple_pay]', $payment_apple_pay_value)
            ) .
            $PHPShopGUI->setField('Google Pay', $PHPShopGUI->setSelect('payments[google_pay]', $payment_google_pay_value)
            ) .
            $PHPShopGUI->setField('� ������', $PHPShopGUI->setSelect('payments[credit]', $payment_credit_value)
            ) .
            $PHPShopGUI->setField('���������� ����������', $PHPShopGUI->setSelect('payments[certificate]', $payment_certificate_value)
            ) .
            $PHPShopGUI->setField('���������� ������ ��� ��������� ������', $PHPShopGUI->setSelect('payments[card_on_delivery]', $payment_card_on_delivery_value)
            ) .
            $PHPShopGUI->setField('���������', $PHPShopGUI->setSelect('payments[cash_on_delivery]', $payment_cash_on_delivery_value)
            ), null);

    // ������ ������
    $Tab1 .= $PHPShopGUI->setCollapse('������� ������ ��� DBS/FBS', 
            
           $PHPShopGUI->setField('����� �����������, ��� ����� ������������ (������������)', $PHPShopGUI->setSelect('statuses[processing_started]', $status_started_value), 1, 'PROCESSING'
            ) .
            $PHPShopGUI->setField('������ ���������', $PHPShopGUI->setSelect('statuses[delivered]', $status_delivered_value), 1, 'DELIVERED'
            ) .
            $PHPShopGUI->setField('������ ������� � ������ ��������', $PHPShopGUI->setSelect('statuses[delivery]', $status_delivery_value), 1, 'DELIVERY'
            ) .
            $PHPShopGUI->setField('����� ��������� � ����� ����������', $PHPShopGUI->setSelect('statuses[pickup]', $status_pickup_value), 1, 'PICKUP'
            ) .
            $PHPShopGUI->setField('����� ��������, �� ��� �� �������', $PHPShopGUI->setSelect('statuses[unpaid]', $status_unpaid_value), 1, 'UNPAID'
            ) .
            
            $PHPShopGUI->setField('������ �������� �� ������ ��������� �����', $PHPShopGUI->setSelect('statuses[cancelled_delivery_service_undelivered]', $status_delivery_service_undelivered_value), 1, 'CANCELLED DELIVERY_SERVICE_UNDELIVERED'
            ) .
            $PHPShopGUI->setField('������� �� ��������� ����� � ������� ���� ����', $PHPShopGUI->setSelect('statuses[cancelled_processing_expired]', $status_processing_expired_value), 1, 'CANCELLED PROCESSING_EXPIRED'
            ) .
            $PHPShopGUI->setField('���������� ����� �������� ����� ������ �� ����������� ����������', $PHPShopGUI->setSelect('statuses[cancelled_replacing_order]', $status_replacing_order_value), 1, 'CANCELLED REPLACING_ORDER'
            ) .
            $PHPShopGUI->setField('���������� �� �������� ���������� ������������������ ������ � ������� 10 �����', $PHPShopGUI->setSelect('statuses[cancelled_reservation_expired]', $status_reservation_expired_value), 1, 'CANCELLED RESERVATION_EXPIRED'
            ) .
            $PHPShopGUI->setField('������� �� ����������, ��� ����� ������� �����', $PHPShopGUI->setSelect('statuses[cancelled_reservation_failed]', $status_reservation_failed_value), 1, 'CANCELLED RESERVATION_FAILED'
            ) .
            $PHPShopGUI->setField('������� �� ����� ��������� �����', $PHPShopGUI->setSelect('statuses[cancelled_shop_failed]', $status_shop_failed_value), 1, 'CANCELLED SHOP_FAILED'
            ) .
            $PHPShopGUI->setField('���������� ������� ����� �� ����������� ��������', $PHPShopGUI->setSelect('statuses[cancelled_user_changed_mind]', $status_user_changed_mind_value), 1, 'CANCELLED USER_CHANGED_MIND'
            ) .
            $PHPShopGUI->setField('���������� �� ������� ����� (��� ���� ������ PREPAID) � ������� ���� �����', $PHPShopGUI->setSelect('statuses[cancelled_user_not_paid]', $status_user_not_paid_value), 1, 'CANCELLED USER_NOT_PAID'
            ) .
            $PHPShopGUI->setField('���������� �� ���������� ������� ��������', $PHPShopGUI->setSelect('statuses[cancelled_user_refused_delivery]', $status_user_refused_delivery_value), 1, 'CANCELLED USER_REFUSED_DELIVERY'
            ) .
            $PHPShopGUI->setField('���������� �� ������� �����', $PHPShopGUI->setSelect('statuses[cancelled_refused_product]', $status_user_refused_product_value), 1, 'CANCELLED USER_REFUSED_PRODUCT'
            ) .
            $PHPShopGUI->setField('���������� �� ���������� �������� ������', $PHPShopGUI->setSelect('statuses[cancelled_refused_quality]', $status_user_refused_quality_value), 1, 'CANCELLED USER_REFUSED_QUALITY'
            ) .
            $PHPShopGUI->setField('�� ������� ��������� � �����������', $PHPShopGUI->setSelect('statuses[cancelled_unreachable]', $status_user_unreachable_value), 1, 'CANCELLED USER_UNREACHABLE'
            ), null);


    // ����������
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "exportID", "���������", "right", 80, "", "but", "actionUpdatePrice.modules.edit").
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('link_new', 'log_new','create_products_new');

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $_POST['options']['statuses'] = $_POST['statuses'];
    $_POST['options']['payments'] = $_POST['payments'];


    $_POST['options_new'] = serialize($_POST['options']);
    $PHPShopOrm->debug = false;
    $_POST['region_data_new'] = 1;
    if (empty($_POST["use_params_new"]))
        $_POST["use_params_new"] = 0;
    if (empty($_POST["stop_new"]))
        $_POST["stop_new"] = 0;

    // ���������
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;

            if (empty($_POST['campaign']))
                $yml = ['yml_new' => intval($_POST['enabled_all'])];
            elseif ($_POST['campaign'] == 2)
                $yml = ['yml_2_new' => intval($_POST['enabled_all'])];
            elseif ($_POST['campaign'] == 3)
                $yml = ['yml_3_new' => intval($_POST['enabled_all'])];

            $PHPShopOrmProducts->update($yml, $where);
        }
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>