<?php

$TitlePage = __("��������� ���������� � Yandex Cloud");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['ai']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');
    
    $PHPShopGUI->action_select['������ AI'] = array(
        'name' => '�����-����� YandexGPT',
        'action' => 'yandexcloudModal',
        'icon' => ''
    );
    
    $PHPShopGUI->action_select['�������'] = array(
        'name' => '����������',
        'url' => 'https://docs.phpshop.ru/nastroiky/yandex-cloud',
        'target' => '_blank'
    );


    if (empty($_SESSION['yandexcloud']) or $_SESSION['yandexcloud'] < time()) {

        $PHPShopGUI->action_button['��������'] = array(
            'name' => __('������ ��������'),
            'action' => 'https://www.phpshop.ru/order/order.html?from=' . $_SERVER['SERVER_NAME'] . '#subscription',
            'class' => 'btn btn-primary btn-sm navbar-btn btn-info btn-action-panel-blank',
            'type' => 'submit',
        );

        $PHPShopGUI->setActionPanel($TitlePage, false, ['��������']);

        $PHPShopGUI->_CODE .= $PHPShopGUI->setAlert('���������� � ������������� ����������� <a href="https://docs.phpshop.ru/nastroiky/yandex-cloud" target="_blank">YandexGPT</a> � <a href="https://docs.phpshop.ru/nastroiky/yandex-cloud#poisk" target="_blank">Yandex Search API</a> �������� ������ �� <b>������� ��������</b>', 'info', true);

        $option['yandexgpt_seo'] = 0;
        $option['yandexgpt_seo_import'] = 0;
        $option['yandexgpt_chat_enabled'] = 0;
        $option['yandexsearch_site_enabled'] = 0;
        $PHPShopOrm->update(['ai_new' => serialize($option)]);
    } else
        $PHPShopGUI->setActionPanel($TitlePage, ['������ AI','|','�������'], ['���������']);



    $yandexgpt_model_value[] = array('YandexGPT Lite', 'yandexgpt-lite/latest', $option['yandexgpt_model']);
    $yandexgpt_model_value[] = array('YandexGPT Pro', 'yandexgpt/latest', $option['yandexgpt_model']);

    // ���������
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������', $PHPShopGUI->setField('�������������', $PHPShopGUI->setInputText(null, 'option[yandexgpt_id]', $option['yandexgpt_id'], 375))
    );

    // AI
    $yandexgpt_temperature_value[] = array('0', '0', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.1', '0.1', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.2', '0.2', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.3', '0.3', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.4', '0.4', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.5', '0.5', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.6', '0.6', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.7', '0.7', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.8', '0.8', $option['yandexgpt_temperature']);
    $yandexgpt_temperature_value[] = array('0.9', '0.9', $option['yandexgpt_temperature']);

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������������� ���������', $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'option[yandexgpt_token]', $option['yandexgpt_token'], 375, '<a target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&client_id=1a6990aa636648e9b2ef855fa7bec2fb">' . __('��������') . '</a>')) .
            $PHPShopGUI->setField('������������ ������', $PHPShopGUI->setSelect('option[yandexgpt_temperature]', $yandexgpt_temperature_value, 100)) .
            $PHPShopGUI->setField('������������', $PHPShopGUI->setSelect('option[yandexgpt_model]', $yandexgpt_model_value, 200))
    );

    if (empty($option['yandexgpt_chat_role']))
        $option['yandexgpt_chat_role'] = '�� - ����������� �� �������� �� ����� ' . $_SERVER['SERVER_NAME'] . '. ������ ����� � ������ ���� ������ � �������� ����.';

    if (empty($option['yandexgpt_avatar_dialog']))
        $option['yandexgpt_avatar_dialog'] = '/phpshop/lib/templates/chat/ai.png';


    if (empty($option['yandexgpt_day_dialog']))
        $option['yandexgpt_day_dialog'] = 1;

    if (empty($option['yandexgpt_time_from_dialog']) and empty($option['yandexgpt_time_until_dialog'])) {
        $option['yandexgpt_time_until_dialog'] = 8;
        $option['yandexgpt_time_from_dialog'] = 20;
    }

    // ���
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('���', $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('option[yandexgpt_chat_enabled]', 1, '�������� AI ��� ���� � ��������� �����', $option['yandexgpt_chat_enabled'])) .
            $PHPShopGUI->setField("��������� ����", $PHPShopGUI->setInputText(null, "option[yandexgpt_title_dialog]", $option['yandexgpt_title_dialog'], 375)) .
            $PHPShopGUI->setField("������ AI � ����", $PHPShopGUI->setIcon($option['yandexgpt_avatar_dialog'], "yandexgpt_avatar_dialog", false, array('load' => false, 'server' => true))) .
            $PHPShopGUI->setField('������ ��� ������', $PHPShopGUI->setTextarea('option[yandexgpt_chat_role]', $option['yandexgpt_chat_role'], false, false, 100))
    );

    // ������ �����
    if(empty($option['yandexsearch_image_num']))
        $option['yandexsearch_image_num']=1;
    
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('�����', $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText(null, "option[yandexsearch_token]", $option['yandexsearch_token'], 375)) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('option[yandexsearch_enabled]', 1, '�������� ����� ������� ��� ���� �� ����� ����� ������', $option['yandexsearch_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[yandexsearch_site_enabled]', 1, '������������ ����� ����� ������ �� �����, ������ ������������ ������', $option['yandexsearch_site_enabled'])).
            $PHPShopGUI->setField("����������� � ������", $PHPShopGUI->setInputText(null, "option[yandexsearch_image_num]", (int)$option['yandexsearch_image_num'], 50)) 
    );

    // SEO ����
    if (empty($option['yandexgpt_site_descrip_role']))
        $option['yandexgpt_site_descrip_role'] = '�� - seo �����������. ������ �������� ����� ��� Meta Description. ����� ������ �����.';

    if (empty($option['yandexgpt_site_title_role']))
        $option['yandexgpt_site_title_role'] = '�� - seo �����������. ������ �������� ����� ��� Meta Title. ����� ������ �����.';


    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('����', $PHPShopGUI->setField('������ ��� �������� Meta Title', $PHPShopGUI->setTextarea('option[yandexgpt_site_title_role]', $option['yandexgpt_site_title_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� Meta Description', $PHPShopGUI->setTextarea('option[yandexgpt_site_descrip_role]', $option['yandexgpt_site_descrip_role'], false, false, 100))
    );

    // SEO ��������
    if (empty($option['yandexgpt_catalog_description_role']))
        $option['yandexgpt_catalog_description_role'] = '�� - seo �����������. ������ �������� �������� ������� ��� Meta Description. ����� ������ �����.';

    if (empty($option['yandexgpt_catalog_title_role']))
        $option['yandexgpt_catalog_title_role'] = '�� - seo �����������. ������ �������� �������� ������� ��� Meta Title. ����� ������ �����.';

    if (empty($option['yandexgpt_catalog_content_role']))
        $option['yandexgpt_catalog_content_role'] = '�� - seo �����������. ������ �������� �������� �������.';


    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������', $PHPShopGUI->setField('������ ��� �������� Meta Title', $PHPShopGUI->setTextarea('option[yandexgpt_catalog_title_role]', $option['yandexgpt_catalog_title_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� Meta Description', $PHPShopGUI->setTextarea('option[yandexgpt_catalog_description_role]', $option['yandexgpt_catalog_description_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ��������', $PHPShopGUI->setTextarea('option[yandexgpt_catalog_content_role]', $option['yandexgpt_catalog_content_role'], false, false, 100))
    );

    // SEO �������
    if (empty($option['yandexgpt_product_descrip_role']))
        $option['yandexgpt_product_descrip_role'] = '�� - seo �����������. ������ �������� ������ ��� Meta Description. ����� ������ �����.';

    if (empty($option['yandexgpt_product_title_role']))
        $option['yandexgpt_product_title_role'] = '�� - seo �����������. ������ �������� ������ ��� Meta Title. ����� ������ �����.';

    if (empty($option['yandexgpt_product_content_role']))
        $option['yandexgpt_product_content_role'] = '�� - seo �����������. ������ ��������� �������� ������.';

    if (empty($option['yandexgpt_product_description_role']))
        $option['yandexgpt_product_description_role'] = '�� - seo �����������. ������ ������� �������� ������.';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������', $PHPShopGUI->setField('������ ��� �������� Meta Title', $PHPShopGUI->setTextarea('option[yandexgpt_product_title_role]', $option['yandexgpt_product_title_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� Meta Description', $PHPShopGUI->setTextarea('option[yandexgpt_product_descrip_role]', $option['yandexgpt_product_descrip_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ���������� ��������', $PHPShopGUI->setTextarea('option[yandexgpt_product_content_role]', $option['yandexgpt_product_content_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� �������� ��������', $PHPShopGUI->setTextarea('option[yandexgpt_product_description_role]', $option['yandexgpt_product_description_role'], false, false, 100))
    );


    // SEO �������
    if (empty($option['yandexgpt_news_content_role']))
        $option['yandexgpt_news_content_role'] = '�� - seo �����������. ������ �������.';

    if (empty($option['yandexgpt_news_description_role']))
        $option['yandexgpt_news_description_role'] = '�� - seo �����������. ������ ����� �������.';

    if (empty($option['yandexgpt_news_sendmail_role']))
        $option['yandexgpt_news_sendmail_role'] = '�� - seo �����������. ������ ����� ��������.';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������� � ��������', $PHPShopGUI->setField('������ ��� �������� �������', $PHPShopGUI->setTextarea('option[yandexgpt_news_content_role]', $option['yandexgpt_news_content_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ������', $PHPShopGUI->setTextarea('option[yandexgpt_news_description_role]', $option['yandexgpt_news_description_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ��������', $PHPShopGUI->setTextarea('option[yandexgpt_news_sendmail_role]', $option['yandexgpt_news_sendmail_role'], false, false, 100))
    );

    // SEO ��������
    if (empty($option['yandexgpt_page_descrip_role']))
        $option['yandexgpt_page_descrip_role'] = '�� - seo �����������. ������ �������� ������ ��� Meta Description. ����� ������ �����.';

    if (empty($option['yandexgpt_page_title_role']))
        $option['yandexgpt_page_title_role'] = '�� - seo �����������. ������ �������� ������ ��� Meta Title. ����� ������ �����.';

    if (empty($option['yandexgpt_page_content_role']))
        $option['yandexgpt_page_content_role'] = '�� - seo �����������. ������ ������.';

    if (empty($option['yandexgpt_page_description_role']))
        $option['yandexgpt_page_description_role'] = '�� - seo �����������. ������ ����� ������.';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������', $PHPShopGUI->setField('������ ��� �������� Meta Title', $PHPShopGUI->setTextarea('option[yandexgpt_page_title_role]', $option['yandexgpt_page_title_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� Meta Description', $PHPShopGUI->setTextarea('option[yandexgpt_page_descrip_role]', $option['yandexgpt_page_descrip_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ��������', $PHPShopGUI->setTextarea('option[yandexgpt_page_content_role]', $option['yandexgpt_page_content_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� �������� ������', $PHPShopGUI->setTextarea('option[yandexgpt_page_description_role]', $option['yandexgpt_page_description_role'], false, false, 100))
    );

    // ������
    if (empty($option['yandexgpt_gbook_review_role']))
        $option['yandexgpt_gbook_review_role'] = '�� - seo �����������. ������ ����� � ������ �����.';

    if (empty($option['yandexgpt_gbook_answer_role']))
        $option['yandexgpt_gbook_answer_role'] = '�� - seo �����������. ������ ����� �� ����� � ������ �����.';

    if (empty($option['yandexgpt_product_comment_role']))
        $option['yandexgpt_product_comment_role'] = '�� - seo �����������. ������ ����� � ������.';


    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ � �����������', $PHPShopGUI->setField('������ ��� �������� ������ � �����', $PHPShopGUI->setTextarea('option[yandexgpt_gbook_review_role]', $option['yandexgpt_gbook_review_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� ������ �� ����� � �����', $PHPShopGUI->setTextarea('option[yandexgpt_gbook_answer_role]', $option['yandexgpt_gbook_answer_role'], false, false, 100)) .
            $PHPShopGUI->setField('������ ��� ������ �� ����������� � ������', $PHPShopGUI->setTextarea('option[yandexgpt_product_comment_role]', $option['yandexgpt_product_comment_role'], false, false, 100))
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

    // ������
    $_POST['option']['yandexgpt_avatar_dialog'] = $_POST['yandexgpt_avatar_dialog'];

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['ai']);

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('option.yandexgpt_chat_enabled', 'option.yandexsearch', 'option.yandexsearch_site_enabled', 'option.yandexgpt_seo_import', 'option.yandexsearch_image');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $_POST['ai_new'] = serialize($option);


    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();
?>