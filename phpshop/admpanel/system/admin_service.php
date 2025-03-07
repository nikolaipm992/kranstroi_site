<?php

$TitlePage = __("Обслуживание");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite, $PHPShopSystem;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('service_content');
    $oFCKeditor->Height = '350';
    $oFCKeditor->Value = $option['service_content'];

    // Режим обслуживания
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Основное', $PHPShopGUI->setField("Режим обслуживания", $PHPShopGUI->setCheckbox('option[service_enabled]', 1, 'Включить вывод сообщения о проведении технических работ на сайте', $option['service_enabled'])) .
            $PHPShopGUI->setField('Служебные IP адреса', $PHPShopGUI->setTextarea('option[service_ip]', $option['service_ip'], false, $width = false, 50), 1, 'Укажите IP адреса через запятую') .
            $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(null, 'option[service_title]', $option['service_title'])) .        
            $PHPShopGUI->setField('Сообщение', $oFCKeditor->AddGUI())
    );
    
     $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Защита', $PHPShopGUI->setField('Заблокированные IP адреса', $PHPShopGUI->setTextarea('option[block_ip]', $option['block_ip'], false, $width = false, 100), 1, 'Укажите IP адреса через запятую') 
    );
    
    // Robots.txt
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/robots.txt'))
        $robots = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt');
    else $robots;

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Индексирование', $PHPShopGUI->setField('Robots.txt', $PHPShopGUI->setTextarea('service_robots', $robots, false, $width = false, 500))
    );



    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('option.service_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $option['service_content'] = $_POST['service_content'];
    
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt',$_POST['service_robots']);

    $_POST['admoption_new'] = serialize($option);


    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>