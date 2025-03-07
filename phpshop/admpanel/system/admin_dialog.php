<?php

$TitlePage = __("��������� ��������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js','./js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    // ����-�����
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $option['telegram_token'] = '';
    }

    // �������
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('�������', $PHPShopGUI->setField("���������� � ��������", $PHPShopGUI->setCheckbox('option[telegram_dialog]', 1, '�������� ���������� � Telegram', $option['telegram_dialog']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[vk_dialog]', 1, '�������� ���������� � VK', $option['vk_dialog']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_dialog]', 1, '�������� ���������� �� E-mail', $option['mail_dialog']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[push_dialog]', 1, '�������� PUSH ����������', $option['push_dialog'])) .
            $PHPShopGUI->setField("���������� ��������", $PHPShopGUI->setInputText($GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/', "option[image_dialog_path]", $option['image_dialog_path'], 300), 1, '���� ���������� ������������ ������')
            , 'in', false);
    
    // ���
    if (empty($option['avatar_dialog']))
        $option['avatar_dialog'] = '/phpshop/lib/templates/chat/avatar.png';
    
    if(empty($option['color_dialog']))
        $option['color_dialog']='#42a5f5';
    
    if(empty($option['day_dialog']))
        $option['day_dialog']=1;
    
    $value_day[] = array('5 ������� ����', 1, $option['day_dialog']);
    $value_day[] = array('6 ������� ����', 2, $option['day_dialog']);
    $value_day[] = array('7 ������� ����', 3, $option['day_dialog']);
    
    if(empty($option['margin_dialog']))
        $option['margin_dialog']=0;
    
    if(empty($option['size_dialog']))
        $option['size_dialog']=56;
    
    if(empty($option['sizem_dialog']))
        $option['sizem_dialog']=56;
    
    if(empty($option['time_from_dialog']) and empty($option['time_until_dialog'])){
        $option['time_until_dialog'] = 20;
        $option['time_from_dialog'] = 8;
    }
    
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ����',
            $PHPShopGUI->setField("���", $PHPShopGUI->setCheckbox('option[chat_dialog]', 1, '�������� ������ ����', $option['chat_dialog']).'<br>'.
            $PHPShopGUI->setCheckbox('option[tel_dialog]', 1, '�������� ������������ ���� ������� ��� ����', $option['tel_dialog'])).
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[title_dialog]", $option['title_dialog'], 300)) .
            $PHPShopGUI->setField("����������� � ����", $PHPShopGUI->setTextarea('option[text_dialog]',$option['text_dialog'],false,300)).
            $PHPShopGUI->setField("������ ������ ����", $PHPShopGUI->setInputText('c'.'&nbsp;&nbsp;', "option[time_from_dialog]", (int) $option['time_from_dialog'], 150,__('�.')).'<br>'.
                    $PHPShopGUI->setInputText('��', "option[time_until_dialog]", (int) $option['time_until_dialog'], 150,__('�.')) .'<br>'.
                    $PHPShopGUI->setSelect('option[day_dialog]', $value_day,150,true).'<br>'.
                    $PHPShopGUI->setCheckbox('option[time_off_dialog]', 1, '������ ������ ���� � ��������� �����', $option['time_off_dialog'])
                    ) .
            $PHPShopGUI->setField("������ ���������� � ����", $PHPShopGUI->setIcon($option['avatar_dialog'], "avatar_dialog", false, array('load' => false, 'server' => true))).
            $PHPShopGUI->setField('���� ����', $PHPShopGUI->setInputColor('option[color_dialog]', $option['color_dialog'],150)).
            $PHPShopGUI->setField("������ �����", $PHPShopGUI->setInputText(null, "option[margin_dialog]", $option['margin_dialog'], 150,'px')).
            $PHPShopGUI->setField("������ ������", $PHPShopGUI->setInputText(null, "option[size_dialog]", $option['size_dialog'], 150,'px')).
            $PHPShopGUI->setField("������ ������ ��� ���������", $PHPShopGUI->setInputText(null, "option[sizem_dialog]", $option['sizem_dialog'], 150,'px'))
            );
    
    // Telegram
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Telegram', $PHPShopGUI->setField("��� ���", $PHPShopGUI->setCheckbox('option[telegram_enabled]', 1, '�������� ��� ��� Telegram', $option['telegram_enabled']).'<br>'.
            $PHPShopGUI->setCheckbox('option[telegram_order]', 1, '�������� ���������� � ������� ��������������', $option['telegram_order']) ) .
            $PHPShopGUI->setField("��� ����", $PHPShopGUI->setInputText('@', "option[telegram_bot]", $option['telegram_bot'], 300)) .
            $PHPShopGUI->setField("Chat IDS", $PHPShopGUI->setInputText(null, "option[telegram_admin]", $option['telegram_admin'], 300), 1, '����������� � ������ � �������� ���������������. ��������� �������� ����������� ����� �������.') .
            $PHPShopGUI->setField("API-����", $PHPShopGUI->setInputText(null, "option[telegram_token]", $option['telegram_token'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://docs.phpshop.ru/nastroiky/dialog#telegram" target="_blank">����������</a>')));

    // VK
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('���������', $PHPShopGUI->setField("��� ���", $PHPShopGUI->setCheckbox('option[vk_enabled]', 1, '�������� ��� ��� ���������', $option['vk_enabled']) .'<br>'.
            $PHPShopGUI->setCheckbox('option[vk_order]', 1, '�������� ���������� � ������� ��������������', $option['vk_order'])) .
            $PHPShopGUI->setField("��� ����������", $PHPShopGUI->setInputText(null, "option[vk_bot]", $option['vk_bot'], 300)) .
            $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setInputText(null, "option[vk_confirmation]", $option['vk_confirmation'], 300)) .
            $PHPShopGUI->setField("���� �������������", $PHPShopGUI->setInputText(null, "option[vk_secret]", $option['vk_secret'], 300)) .
            $PHPShopGUI->setField("Chat IDS", $PHPShopGUI->setInputText(null, "option[vk_admin]", $option['vk_admin'], 300), 1, '����������� � ������ � �������� ���������������. ��������� �������� ����������� ����� �������.') .
            $PHPShopGUI->setField("API-����", $PHPShopGUI->setInputText(null, "option[vk_token]", $option['vk_token'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://docs.phpshop.ru/nastroiky/dialog#vkontakte" target="_blank">����������</a>'))
    );


    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // �����
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('option.telegram_enabled', 'option.vk_enabled', 'option.telegram_dialog', 'option.vk_dialog', 'option.mail_dialog', 'option.push_dialog', 'option.telegram_order', 'option.vk_order','option.chat_dialog','option.mobil_dialog','option.tel_dialog','option.time_off_dialog');

    // ������
    $_POST['option']['avatar_dialog'] = $_POST['avatar_dialog'];

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    // ������� �����
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path']))
        @mkdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path'], 0777, true);

    // �������� ���� ���������� �����������
    if (stristr($option['image_dialog_path'], '..') or ! is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['image_dialog_path']))
        $option['image_dialog_path'] = null;

    if (substr($option['image_dialog_path'], -1) != '/' and ! empty($option['image_dialog_path']))
        $option['image_dialog_path'] .= '/';

    $_POST['admoption_new'] = serialize($option);

    // Telegram ����������� �������
    if (!empty($option['telegram_enabled']) and ! empty($option['telegram_token'])) {

        $url = 'https://api.telegram.org/bot' . $option['telegram_token'] . '/setWebhook?url=https://' . $_SERVER['SERVER_NAME'] . '/bot/telegram.php';
        $�url = curl_init();
        curl_setopt_array($�url, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ));
        $result = curl_exec($�url);
        curl_close($�url);
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();
?>