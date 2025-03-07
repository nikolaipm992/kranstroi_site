<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_sberbankrf_system");

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
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->debug = false;

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;
    if (empty($_POST["notification_new"]))
        $_POST["notification_new"] = 0;
    if (empty($_POST["force_payment_new"]))
        $_POST["force_payment_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab2 = $PHPShopGUI->setField('Логин магазина', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('Пароль магазина', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Token авторизации', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 300), 1,
        'Может использоваться вместо логина и пароля магазина. Можно получить у менеджера Сбербанка.'
    );

    // Система налогообложения
    $tax_system = array (
        array("Общая система налогообложения", 0, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход)", 1, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход минус Расход)", 2, $data["taxationSystem"]),
        array("Единый налог на вмененный доход", 3, $data["taxationSystem"]),
        array("Единый сельскохозяйственный налог", 4, $data["taxationSystem"]),
        array("Патентная система налогообложения", 5, $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('Cистема налогообложения', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Режим разработки', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "Отправка данных на тестовую среду Сбербанка РФ", $data["dev_mode"]));
    $Tab2 .= $PHPShopGUI->setField('Переходить к оплате без подтверждения', $PHPShopGUI->setCheckbox("force_payment_new", 1, "После оформления заказа открывать страницу оплаты", $data["force_payment"]));
    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'], true, 300));
    $Tab2 .= $PHPShopGUI->setField('Уведомление об оплате', $PHPShopGUI->setCheckbox("notification_new", 1, "Уведомление об оплате на Email администратора", $data["notification"]));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор со Сбербанком РФ</li>
<li>На закладке настройки ввести предоставленные Сбербанком РФ Логин API магазина (*********-api) и Пароль магазина.</li>
<li>Указать сотрудникам Сбербанка URL Callback-уведомлений <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/sberbankrf/payment/check.php</code></li>
<li>Вместо логина и пароля магазина можно использовать token авторизации, его необходимо запросить у менеджера Сбербанка РФ.</li>
<li>Во время тестирования включить "Режим разработки", данные будут отправляться на тестовую среду Сбербанка РФ</li>
<li>Для перевода модуля в рабочий режим, выключить "Режим разработки"</a></li>
</ol>
';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab2, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], false)));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>