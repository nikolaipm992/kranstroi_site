<?php

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.grastinwidget.grastinwidget_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    grastinSetDelivery();
    grastinDoZero();

    $_POST['no_partners_new'] = serialize($_POST['no_partners']);
    $_POST['payment_service_new'] = serialize($_POST['service']);
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.grastinwidget.grastinwidget_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    include_once '../modules/grastinwidget/class/GrastinWidget.php';

    $PHPShopGUI->addJSFiles('../modules/grastinwidget/admpanel/gui/script.gui.js');

    // Подсказки
    if ($PHPShopSystem->ifSerilizeParam('admoption.dadata_enabled')) {
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions.min.js', './order/gui/dadata.gui.js');
        $PHPShopGUI->addCSSFiles('./css/suggestions.min.css');
    }

    // Выборка
    $data = $PHPShopOrm->select();

    $status = grastinGetStatuses($data['status']);
    $delivery_value = grastinGetDelivery($data['delivery_id']);
    $no_partners = GrastinWidget::getPartners(unserialize($data['no_partners']));
    $fee_type = GrastinWidget::getFeeType($data['fee_type']);
    $fromCity = GrastinWidget::getFromCity($data['from_city']);
    $payments = grastinGetPayments();
    $services = GrastinWidget::getServices();
    $payment_service = unserialize($data['payment_service']);

    $service = '';
    foreach ($services as $service_key => $service_title) {
        $paymentTypes = array();
        foreach ($payments as $payment) {
            $paymentTypes[] = array(
                $payment['name'],
                $payment['id'],
                @in_array($payment['id'], $payment_service[$service_key])
            );
        }
        $service .= $PHPShopGUI->setField($service_title, $PHPShopGUI->setSelect('service[' . $service_key . '][]', $paymentTypes, '', false, false, false, false, 1, true));
    }

    $Tab1 = $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_id_new[]', $delivery_value, 300, null, false, $search = false, false, $size = 1));
    $Tab1.= $PHPShopGUI->setField('Статус для отправки', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1.= $PHPShopGUI->setField('API-ключ', $PHPShopGUI->setInputText(false, 'api_new', $data['api'], 300));
    $Tab1.= $PHPShopGUI->setField('Режим разработки', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "При работе в тестовом режиме заказы на доставку не создаются.", $data["dev_mode"]));

    $Tab1.= $PHPShopGUI->setCollapse('Города отправления и доставки',
        $PHPShopGUI->setField('Город отправления', $PHPShopGUI->setSelect('from_city_new', $fromCity, 300,true)) .
        $PHPShopGUI->setField('Город доставки по умолчанию', $PHPShopGUI->setInputText(false, 'to_city_new', $data['to_city'], 300))
    );

    $Tab1.= $PHPShopGUI->setCollapse('Параметры подсчета стоимости и наценка',
        $PHPShopGUI->setField('Добавить наценку', '<input class="form-control input-sm " onkeypress="grastinvalidate(event)" type="number" step="1" min="0" value="' . $data['fee'] . '" name="fee_new" style="width:300px;">') .
        $PHPShopGUI->setField('Тип наценки', $PHPShopGUI->setSelect('fee_type_new', $fee_type, 300)) .
        $PHPShopGUI->setField('Вес по умолчанию, кг.', '<input class="form-control input-sm " onkeypress="grastinvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
        $PHPShopGUI->setField('Добавить дни к сроку доставки', '<input class="form-control input-sm " onkeypress="grastinvalidate(event)" type="number" step="1" min="0" value="' . $data['delivery_add'] . '" name="delivery_add_new" style="width:300px;">') .
        $PHPShopGUI->setField('Не показывать партнеров', $PHPShopGUI->setSelect('no_partners[]', $no_partners, 300, false, false, false, false,  1, true))
    );

    $Tab1.= $PHPShopGUI->setCollapse('Дополнительные настройки',
         $PHPShopGUI->setField('', $PHPShopGUI->setCheckbox("city_from_hide_new", 1, "Не показывать город отправления", $data["city_from_hide"])) .
         $PHPShopGUI->setField('', $PHPShopGUI->setCheckbox("city_to_hide_new", 1, "Не показывать город доставки", $data["city_to_hide"])) .
         $PHPShopGUI->setField('', $PHPShopGUI->setCheckbox("duration_hide_new", 1, "Не показывать срок доставки", $data["duration_hide"])) . 
         $PHPShopGUI->setField('', $PHPShopGUI->setCheckbox("weight_hide_new", 1, "Не показывать вес", $data["weight_hide"])) );

    $Tab1.= $PHPShopGUI->setCollapse('Код услуги доставки Грастин', $service);

    $info = grastinGetInfo();

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab4));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

function grastinDoZero ()
{
    if(empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';
    if(empty($_POST['city_from_hide_new']))
        $_POST['city_from_hide_new'] = 0;
    if(empty($_POST['city_to_hide_new']))
        $_POST['city_to_hide_new'] = 0;
    if(empty($_POST['lock_city_to_new']))
        $_POST['lock_city_to_new'] = 0;
    if(empty($_POST['duration_hide_new']))
        $_POST['duration_hide_new'] = 0;
    if(empty($_POST['weight_hide_new']))
        $_POST['weight_hide_new'] = 0;
    if(empty($_POST['dev_mode_new']))
        $_POST['dev_mode_new'] = 0;
}

function grastinSetDelivery ()
{
    if (isset($_POST['delivery_id_new'])) {
        if (is_array($_POST['delivery_id_new'])) {
            foreach ($_POST['delivery_id_new'] as $val) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
                $PHPShopOrm->update(array('is_mod_new' => 2), array('id' => '=' . intval($val)));
            }
            $_POST['delivery_id_new'] = implode(',', $_POST['delivery_id_new']);
        }
    }
}

function grastinGetStatuses ($optionStatus)
{
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    $status[] = array(__('Новый заказ'), 0, $optionStatus);
    if (is_array($OrderStatusArray)) {
        foreach ($OrderStatusArray as $order_status) {
            $status[] = array($order_status['name'], $order_status['id'], $optionStatus);
        }
    }

    return $status;
}

function grastinGetDelivery ($optionStatus)
{
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {

            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if (in_array($delivery['id'], explode(",", $optionStatus)))
                $delivery_id = $delivery['id'];
            else
                $delivery_id = null;

            $delivery_value[] = array($delivery['city'], $delivery['id'], $delivery_id);
        }
    }

    return $delivery_value;
}

function grastinGetInfo ()
{
    return '<h4>Получение учетных данных</h4>
       <ol>
        <li>Заключить договор с <a href="https://grastin.ru/" target="_blank">Grastin</a>.</li>
        <li>Получить API-ключ.</li>
        </ol>
        
       <h4>Настройка модуля</h4>
        <ol>
        <li>Выбрать способ доставки для работы модуля.</li>
        <li>Заполнить "API-ключ".</li>
        <li>Заполнить "Город отправления".</li>
        <li>Заполнить "Город доставки по умолчанию".</li>
        </ol>
        
       <h4>Настройка доставки</h4>
        <ol>
        <li>В карточке редактирования доставки в закладке <kbd>Изменение стоимости доставки</kbd> настроить дополнительный параметр сохранения стоимости доставки для модуля. Опция "Не изменять стоимость" должна быть активна.</li>
        <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Индекс</kbd> "Вкл." и "Обязательное"</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Телефон</kbd> "Вкл." и "Обязательное"</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Город</kbd> "Вкл." и "Обязательное"</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Улица</kbd> "Вкл." и "Обязательное"</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Дом</kbd> "Вкл." и "Обязательное"</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Квартира</kbd> "Вкл." и "Обязательное"</li>
        </ol>';
}

function grastinGetPayments()
{
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $result = $PHPShopOrm->select(array('*'));

    return !empty($result['id']) ? array($result) : $result;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>