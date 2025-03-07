<?php

include_once dirname(__DIR__) . '/class/Shiptor.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.shiptor.shiptor_system"));

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
    
    // Доставки
    if (isset($_POST['delivery_id_new'])) {
        if (is_array($_POST['delivery_id_new'])) {
            foreach ($_POST['delivery_id_new'] as $val) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
                $PHPShopOrm->update(array('is_mod_new' => 2), array('id' => '=' . intval($val)));
            }
            $_POST['delivery_id_new'] = @implode(',', $_POST['delivery_id_new']);
        }
    }
    if(empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';

    $_POST['companies_new'] = serialize(array_unique($_POST['companies']));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.shiptor.shiptor_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->addJSFiles('../modules/shiptor/admpanel/gui/script.gui.js?v=1.0');

    // Выборка
    $data = $PHPShopOrm->select();

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    $status[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $status[] = array($order_status['name'], $order_status['id'], $data['status']);
        }

    // Доставка
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {

            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if (in_array($delivery['id'], @explode(",", $data['delivery_id'])))
                $delivery_id = $delivery['id'];
            else
                $delivery_id = null;

            $delivery_value[] = array($delivery['city'], $delivery['id'], $delivery_id);
        }
    }

    $Tab1 = $PHPShopGUI->setField('Публичный ключ API Shiptor', $PHPShopGUI->setInputText(null, 'api_key_new', $data['api_key'], 300));
    $Tab1.= $PHPShopGUI->setField('Приватный ключ API Shiptor', $PHPShopGUI->setInputText(null, 'private_api_key_new', $data['private_api_key'], 300));
    $Tab1.= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_id_new', $delivery_value, 300));
    $Tab1.= $PHPShopGUI->setField('Статус для отправки', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1.= $PHPShopGUI->setField('Службы доставки', $PHPShopGUI->setSelect('companies[]', Shiptor::getCompanyVariants($data['companies']), 300, false, false, false, false,  1, true));
    $Tab1.= $PHPShopGUI->setField('Наложенный платеж', $PHPShopGUI->setSelect('cod_new', [['Да', 1, $data['cod']], ['Нет', 0, $data['cod']]], 300));
    $Tab1.= $PHPShopGUI->setField('Объявленная ценность', $PHPShopGUI->setInputText('От суммы корзины', 'declared_percent_new', $data['declared_percent'], 300,'%'));
    $Tab1.= $PHPShopGUI->setField('Добавить наценку', $PHPShopGUI->setInputText(null, 'fee_new', $data['fee'], 300,'%'));
    $Tab1.= $PHPShopGUI->setField('Увеличение сроков доставки', $PHPShopGUI->setInputText(null, 'add_days_new', $data['add_days'], 300,'дней'));
    $Tab1.= $PHPShopGUI->setField('Округлять стоимость', $PHPShopGUI->setSelect('round_new', Shiptor::getRoundVariants($data['round']), 300));
    $Tab1.= $PHPShopGUI->setCollapse('Вес и габариты по умолчанию',
        $PHPShopGUI->setField('Вес, гр.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
        $PHPShopGUI->setField('Ширина, см.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['width'] . '" name="width_new" style="width:300px;">') .
        $PHPShopGUI->setField('Высота, см.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['height'] . '" name="height_new" style="width:300px;">') .
        $PHPShopGUI->setField('Длина, см.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['length'] . '" name="length_new" style="width:300px;">')
    );

    $info = '<h4>Настройка модуля</h4>
        <ol>
        <li>Зарегистрироваться в <a href="https://shiptor.ru/" target="_blank">Shiptor</a>, заключить договор.</li>
        <li>Выбрать способ доставки для работы модуля.</li>
        <li>Ввести <b>Публичный ключ API Shiptor</b>.</li>
        <li>Ввести <b>Приватный ключ API Shiptor</b>.</li>
        <li>Выбрать статус для передачи заказа в личный кабинет Shiptor.</li>
        </ol>
        
       <h4>Настройка доставки</h4>
        <ol>
        <li>В карточке редактирования доставки в закладке <kbd>Изменение стоимости доставки</kbd> настроить дополнительный параметр сохранения стоимости доставки для модуля. Опция "Не изменять стоимость" должна быть активна.</li>
        <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Телефон</kbd> "Вкл." и "Обязательное"</li>
        <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> включить поля адреса доставки перечисленные ниже. 
            Они будут использоваться для курьерской доставки. При выборе доставки в Пункт выдачи эти поля будут автоматически заблокированы.</li>
        <li>Отметить <kbd>Индекс</kbd> "Вкл." и "Обязательное"</li>
        <li>Отметить <kbd>Регион/штат</kbd> "Вкл." и "Обязательное"</li>
        <li>Отметить <kbd>Город</kbd> "Вкл." и "Обязательное"</li>
        <li>Отметить <kbd>Улица</kbd> "Вкл." и "Обязательное"</li>
        <li>Отметить <kbd>Дом</kbd> "Вкл." и "Обязательное"</li>
        <li>Отметить <kbd>Квартира</kbd> "Вкл."</li>
        </ol>';

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

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>