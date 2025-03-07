<?php

$TitlePage = __("��������� ���������� � ���������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    // ����-�����
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $option['metrica_token'] = $option['telegram_token'] = '';
    }

    // ������.�������
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('���������� ��������� ������.�������', $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'option[metrica_token]', $option['metrica_token'], 375, '<a target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&client_id=78246cbd13f74fbd9cb2b48d8bff2559">' . __('��������') . '</a>')) .
            $PHPShopGUI->setField('ID �����', $PHPShopGUI->setInputText(null, 'option[metrica_id]', $option['metrica_id'], 300, false, false, false, 'XXXXXXXX') .
                    $PHPShopGUI->setHelp('������ �������� � ������� <a href="?path=metrica">���������� ���������</a>')) .
            $PHPShopGUI->setField("��� ��������", $PHPShopGUI->setCheckbox('option[metrica_enabled]', 1, '�������� ���� ���������� � ���������� ��� ��������', $option['metrica_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[metrica_ecommerce]', 1, '�������� ���� ������ ����������� ���������', $option['metrica_ecommerce']) . '<br>' . $PHPShopGUI->setCheckbox('option[metrica_webvizor]', 1, '�������� ��������, ����� ���������� � ��������� ����', $option['metrica_webvizor'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox('option[metrica_widget]', 1, '�������� ������ ���������� � ������ ������������', $option['metrica_widget']))
            , 'in', false
    );

    // ������.�����
    if (empty($hideCatalog))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('����� �������� ������.�����', $PHPShopGUI->setField('API-����', $PHPShopGUI->setInputText(false, 'option[yandex_apikey]', $option['yandex_apikey'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://developer.tech.yandex.ru" target="_blank">������� ������������</a>')) .
                $PHPShopGUI->setField("����� �������� ������", $PHPShopGUI->setCheckbox('option[yandexmap_enabled]', 1, '����� ������ �������� ������ �� ������.�����', $option['yandexmap_enabled']))
                , 'in', true
        );

    // ������.�����
    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������.�����', $PHPShopGUI->setField('API-����', $PHPShopGUI->setInputText(false, 'option[yandex_search_apikey]', $option['yandex_search_apikey'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://developer.tech.yandex.ru" target="_blank">������� ������������</a>')) .
                $PHPShopGUI->setField('������������� ������', $PHPShopGUI->setInputText(false, 'option[yandex_search_id]', $option['yandex_search_id'], 300)) .
                $PHPShopGUI->setField("�������� ������.�����", $PHPShopGUI->setCheckbox('option[yandex_search_enabled]', 1, '������������ ������.����� �� �����, ������ ������������ ������', $option['yandex_search_enabled'])), 'in', true
        );

    // ������ ID
    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ID', $PHPShopGUI->setField('ClientID', $PHPShopGUI->setInputText(false, 'option[yandex_id_apikey]', $option['yandex_id_apikey'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://oauth.yandex.ru/client/new/id/" target="_blank">������� ������������</a>')) .
                $PHPShopGUI->setField("�������� ������ ID", $PHPShopGUI->setCheckbox('option[yandex_id_enabled]', 1, '������������ OAuth ����������� � ������� ������ ID �� �����', $option['yandex_id_enabled'])), 'in', true
        );

    // VK ID
    if (empty($hideSite)){
        $get_token='https://oauth.vk.com/authorize?client_id='.$option['vk_id'].'&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=video,offline&response_type=token&v=5.52';
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('VK ID', $PHPShopGUI->setField('ID ����������', $PHPShopGUI->setInputText(false, 'option[vk_id]', $option['vk_id'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://id.vk.com/about/business/go" target="_blank">������� ������������</a>')) .
                $PHPShopGUI->setField('����� �������', $PHPShopGUI->setTextarea('option[vk_id_token]', $option['vk_id_token'], false, 300, '100') . $PHPShopGUI->setHelp('������������ ��� �������� ������� �� ������ VK. �������� <a href="'.$get_token.'" id="client_token" target="_blank">������������ �����</a>')).
                $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setInputText(false, 'option[vk_id_apikey]', $option['vk_id_apikey'], 300)) .
                $PHPShopGUI->setField("�������� VK ID", $PHPShopGUI->setCheckbox('option[vk_id_enabled]', 1, '������������ OAuth ����������� � ������� VK ID �� �����', $option['vk_id_enabled'])), 'in', true
        );
    }


    // Google Analitiks
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('���������� ��������� Google', $PHPShopGUI->setField('������������� ������������', $PHPShopGUI->setInputText('UA-', 'option[google_id]', $option['google_id'], 300, false, false, false, 'XXXXX-Y') .
                    $PHPShopGUI->setHelp('������ �������� � ������� <a href="https://analytics.google.com/analytics/web/" target="_blank">Google ���������</a>')) .
            $PHPShopGUI->setField("��� ��������", $PHPShopGUI->setCheckbox('option[google_enabled]', 1, '�������� ���� ���������� � ���������� ��� ��������', $option['google_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[google_analitics]', 1, '�������� ���� ������ ����������� ���������', $option['google_analitics']))
            , 'in', true
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������������ Google reCAPTCHA', $PHPShopGUI->setField("reCAPTCHA", $PHPShopGUI->setCheckbox('option[recaptcha_enabled]', 1, '�������� ����� ��������� �������� �� �����', $option['recaptcha_enabled'])) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[recaptcha_pkey]", $option['recaptcha_pkey'], 300)) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[recaptcha_skey]", $option['recaptcha_skey'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://www.google.com/recaptcha" target="_blank">Google.com</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������������ hCaptcha', $PHPShopGUI->setField("hCaptcha", $PHPShopGUI->setCheckbox('option[hcaptcha_enabled]', 1, '�������� �������������� ����� ��������� �������� �� �����', $option['hcaptcha_enabled'])) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[hcaptcha_pkey]", $option['hcaptcha_pkey'], 300)) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[hcaptcha_skey]", $option['hcaptcha_skey'], 300) . $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://hCaptcha.com/?r=235b1c9fa5a4" target="_blank">hCaptcha.com</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� DaData.ru', $PHPShopGUI->setField("���������", $PHPShopGUI->setCheckbox('option[dadata_enabled]', 1, '�������� ��������� DaData.ru', $option['dadata_enabled'])) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[dadata_token]", $option['dadata_token'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://dadata.ru" target="_blank">DaData.ru</a>'))
    );

    if (empty($hideCatalog)) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('SMS ����������� Targetsms.ru', $PHPShopGUI->setField("SMS ����������", $PHPShopGUI->setCheckbox('option[sms_enabled]', 1, '����������� � ������ ��������������', $option['sms_enabled']) . '<br>' .
                        $PHPShopGUI->setCheckbox('option[notice_enabled]', 1, '����������� � ������� ������ �������������', $option['notice_enabled']) . '<br>' .
                        $PHPShopGUI->setCheckbox('option[sms_login]', 1, '����������� �� ��������', $option['sms_login']) . $PHPShopGUI->setHelp('��� ���������� �����, � ������� ���������� ���� �������, ������������ ��� ����������')
                ) .
                $PHPShopGUI->setField("��������� �������", $PHPShopGUI->setInputText(null, "option[sms_phone]", $option['sms_phone'], 300, false, false, false, '79261234567'), 1, '������� ��� SMS ����������� ������� 79261234567') .
                $PHPShopGUI->setField("������������", $PHPShopGUI->setInputText(null, "option[sms_user]", $option['sms_user'], 300), 1, '������������ � ������� Targetsms.ru') .
                $PHPShopGUI->setField("������", $PHPShopGUI->setInput('password', "option[sms_pass]", $option['sms_pass'], null, 300), 1, '������ � ������� Targetsms.ru') .
                $PHPShopGUI->setField("������� �����������", $PHPShopGUI->setInputText(null, "option[sms_name]", $option['sms_name'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href=" https://sms.targetsms.ru/ru/reg.html?ref=phpshop" target="_blank">Targetsms.ru</a>'))
        );

        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('PUSH �����������', $PHPShopGUI->setField("PUSH ����������", $PHPShopGUI->setCheckbox('option[push_enabled]', 1, '����������� � ������ ��������������. ��������� SSL ����������.', $option['push_enabled'])) .
                $PHPShopGUI->setField("���� �������", $PHPShopGUI->setInputText(null, "option[push_token]", $option['push_token'], 300)) .
                $PHPShopGUI->setField("������������� �����������", $PHPShopGUI->setInputText(null, "option[push_id]", $option['push_id'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://console.firebase.google.com/" target="_blank">Firebase.google.com</a>'))
        );
    }


    // Telegram
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Telegram', $PHPShopGUI->setField("��������� ���", $PHPShopGUI->setCheckbox('option[telegram_news_enabled]', 1, '�������� �������� �������� �� ������', $option['telegram_news_enabled'])) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText("������", "option[telegram_news_delim]", $option['telegram_news_delim'], 200, '��������')) .
            $PHPShopGUI->setField("API-����", $PHPShopGUI->setInputText(null, "option[telegram_news_token]", $option['telegram_news_token'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://docs.phpshop.ru/stranicy/novosti#zagruzka-novostei-iz-telegram" target="_blank">����������</a>')));

    // VK Reviews
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ���������',
    $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox('option[vk_reviews_enabled]', 1, '�������� �������� ������� �� ������', $option['vk_reviews_enabled'])).
    $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setInputText(null, "option[vk_reviews_confirmation]", $option['vk_reviews_confirmation'], 300)) .
    $PHPShopGUI->setField("���� �������������", $PHPShopGUI->setInputText(null, "option[vk_reviews_secret]", $option['vk_reviews_secret'], 300)) .
    $PHPShopGUI->setField("API-����", $PHPShopGUI->setInputText(null, "option[vk_reviews_token]", $option['vk_reviews_token'], 300) . $PHPShopGUI->setHelp('���������� � �������, �����������, ��������� ������ <a href="https://docs.phpshop.ru/nastroiky/dialog#vkontakte" target="_blank">����������</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� �����', $PHPShopGUI->setField("RSS", $PHPShopGUI->setCheckbox('option[rss_graber_enabled]', 1, '��������� ������� �� ������� RSS �������', $option['rss_graber_enabled']) . $PHPShopGUI->setHelp('��������� ������ ����������� �  ������� <a href="?path=news.rss">RSS ������</a>'))
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
    $PHPShopOrm->updateZeroVars('option.recaptcha_enabled', 'option.dadata_enabled', 'option.sms_enabled', 'option.sms_status_order_enabled', 'option.notice_enabled', 'option.metrica_enabled', 'option.metrica_widget', 'option.metrica_ecommerce', 'option.google_enabled', 'option.google_analitics', 'option.rss_graber_enabled', 'option.yandexmap_enabled', 'option.push_enabled', 'option.metrica_webvizor', 'option.yandex_search_enabled', 'option.sms_login', 'option.hcaptcha_enabled', 'option.yandex_speller_enabled', 'option.yandex_id_enabled', 'option.telegram_news_enabled', 'option.vk_id_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $_POST['admoption_new'] = serialize($option);

    // �������� PUSH-�������
    if (empty($option['push_enabled'])) {
        $PHPShopPush = new PHPShopPush();
        $PHPShopPush->clean();
    }

    // Telegram ����������� �������
    if (!empty($option['telegram_news_enabled']) and ! empty($option['telegram_news_token'])) {

        $url = 'https://api.telegram.org/bot' . $option['telegram_news_token'] . '/setWebhook?url=https://' . $_SERVER['SERVER_NAME'] . '/bot/telegram-news.php/' . md5($option['telegram_news_token']);
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