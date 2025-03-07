<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seopult.seopult_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;
    

    $params = array(
        'login' => PHPShopString::win_utf8($_POST['login_new']),
        'url' => $_SERVER['SERVER_NAME'],
        'email' => $_POST['email_new'],
        'hash' => md5($_SERVER['SERVER_NAME'] . time()),
        'partner' => '7a52518f2d1b22983a51a2fbf2a8ec75'
    );

    $request = http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://app01.promopult.org/iframe/getCryptKeyWithUserReg?" . $request);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CRLF, true);

    $json = curl_exec($ch);

    $result = json_decode($json, true);

    // Регистрация нового пользователя
    if ($result['status']['code'] == 0) {

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seopult.seopult_system"));
        $PHPShopOrm->debug = false;
        $params['cryptkey'] = $result['data']['cryptKey'];
        $PHPShopOrm->update($params, false, '');
    }


    if (curl_error($ch) != '' || $json == false) {
        echo "Error: " . curl_error($ch);
        curl_close($ch);
        die;
    }

    curl_close($ch);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules.dir.seopult');
    return true;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm,$select_name,$TitlePage;
    
        $PHPShopGUI->action_button['Пульт'] = array(
        'name' => __('Пульт управления'),
        'action' => 'modules.dir.seopult',
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-th'
    );

    
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('Пульт', 'Сохранить и закрыть'));

    // Выборка
    $data = $PHPShopOrm->select();


    if (empty($data['login']))
        $data['login'] = $_SERVER['SERVER_NAME'] . '_' . rand(0, 100);
    if (empty($data['email']))
        $data['email'] = $PHPShopSystem->getParam('adminmail2');

    $Tab1 = $PHPShopGUI->setField('Пользователь', $PHPShopGUI->setInputText(false, 'login_new', $data['login'],300));
    $Tab1.=$PHPShopGUI->setField('E-mail', $PHPShopGUI->setInputText(false, 'email_new', $data['email'],300));

    // Форма регистрации
    $Tab2 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true), array("О Модуле", $Tab2));

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