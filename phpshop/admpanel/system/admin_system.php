<?php

$TitlePage = __("�������� ���������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ����� �����
function GetLocaleList($skin, $option = 'lang') {
    global $PHPShopGUI;
    $dir = "../locale/";

    if (empty($skin))
        $skin = 'russian';

    $locale_array = array(
        'russian' => '�������',
        'ukrainian' => '���������',
        'belarusian' => '��������',
        'english' => 'English'
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                //$name = $locale_array[$file];
                $name = $file;
                if (empty($name))
                    $name = $file;

                if ($skin == $file)
                    $sel = "selected";
                else
                    $sel = "";

                if ($file != "." and $file != ".." and ! strpos($file, '.'))
                    $value[] = array($name, $file, $sel, 'data-content="<img src=\'' . $dir . $file . '/icon.png\'/> ' . $name . '"');
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[' . $option . ']', $value);
}

// ����� html ���������
function GetEditors($editor) {
    global $PHPShopGUI;

    if ($editor == 'tiny_mce')
        $editor = 'default';

    $dir = "./editors/";
    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if ($editor == $file)
                    $sel = "selected";
                else
                    $sel = "";

                if ($file != "." and $file != ".." and $file != "index.html")
                    $value[] = array($file, $file, $sel);
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[editor]', $value);
}

// ����� ������� �������
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value);
}

// ����� ����� ��������� �������
function GetAceSkinList($skin) {
    global $PHPShopGUI;
    $dir = "./tpleditor/gui/ace/";

    if (empty($skin))
        $skin = 'dawn';

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/^theme-([a-zA-Z0-9_]{1,30}).js$/", $file, $match)) {

                    $file = str_replace(array('.js', 'theme-'), '', $file);

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file == 'dawn')
                        $value[] = array('default', 'dawn', $sel);

                    elseif ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[ace_theme]', $value);
}

// ����� ������� ������ ����������
function GetAdminSkinList($skin) {
    global $PHPShopGUI;
    $dir = "./css/";

    $color = array(
        'default' => '#178ACC',
        'cyborg' => '#000',
        'flatly' => '#D9230F',
        'spacelab' => '#46709D',
        'slate' => '#4E5D6C',
        'yeti' => '#008CBA',
        'simplex' => '#DF691A',
        'sardbirds' => '#45B3AF',
        'wordless' => '#468966',
        'wildspot' => '#564267',
        'loving' => '#FFCAEA',
        'retro' => '#BBBBBB',
        'cake' => '#E3D2BA',
        'dark' => '#3E444C'
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {
                    $icon = $color[$match[1]];

                    $file = str_replace(array('.css', 'bootstrap-theme-'), '', $file);

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel, 'data-content="<span class=\'glyphicon glyphicon-picture\' style=\'color:' . $icon . '\'></span> ' . $file . '"');
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('option[theme]', $value, null, null, false, false, false, 1, false, 'theme_new');
}

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite;

    PHPShopObj::loadClass('valuta');
    PHPShopObj::loadClass('user');

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);
    $bank = unserialize($data['bank']);

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $dengi_value[] = array($val['name'], $val['id'], $data['dengi']);
            $kurs_value[] = array($val['name'], $val['id'], $data['kurs']);
            $kurs_beznal_value[] = array($val['name'], $val['id'], $data['kurs_beznal']);
        }


    // �������
    $PHPShopUserStatusArray = new PHPShopUserStatusArray();
    $userstatus_array = $PHPShopUserStatusArray->getArray();

    $userstatus_value[] = array(__('�������������� ������������'), 0, $option['user_status']);
    if (is_array($userstatus_array))
        foreach ($userstatus_array as $val) {
            $userstatus_value[] = array($val['name'], $val['id'], $option['user_status']);
        }

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    $num_vitrina_value[] = array(1, 1, $data['num_vitrina']);
    $num_vitrina_value[] = array(2, 2, $data['num_vitrina']);
    $num_vitrina_value[] = array(3, 3, $data['num_vitrina']);
    $num_vitrina_value[] = array(4, 4, $data['num_vitrina']);
    $num_vitrina_value[] = array(5, 5, $data['num_vitrina']);
    $num_vitrina_value[] = array(6, 6, $data['num_vitrina']);

    $sklad_status_value[] = array('�� ������������', 1, $option['sklad_status']);
    $sklad_status_value[] = array('����� ��������� � ������', 2, $option['sklad_status']);
    $sklad_status_value[] = array('������ ��� � �������', 3, $option['sklad_status']);

    $search_enabled_value[] = array('������ � �������', 2, $option['search_enabled']);
    $search_enabled_value[] = array('������ � �������', 3, $option['search_enabled']);
    $search_enabled_value[] = array('�� ������������', 1, $option['search_enabled']);

    $new_enabled_value[] = array('������ �� �������� �������', 0, $option['new_enabled']);
    $new_enabled_value[] = array('��������������� ���� ��� �������', 1, $option['new_enabled']);
    $new_enabled_value[] = array('��������� ����������� ������ ���� ��� �������', 2, $option['new_enabled']);

    $search_pole_value[] = array('������������', 1, $option['search_pole']);
    $search_pole_value[] = array('��������� ���', 2, $option['search_pole']);

    $filter_logic_value[] = array('������������', 0, $option['filter_logic']);
    $filter_logic_value[] = array('������������ �  ������������� �������', 1, $option['filter_logic']);
    $filter_logic_value[] = array('������������', 2, $option['filter_logic']);

    $timezone_value[] = array('Europe/Moscow', 'Europe/Moscow', $option['timezone']);
    $timezone_value[] = array('Europe/Kiev', 'Europe/Kiev', $option['timezone']);
    $timezone_value[] = array('Europe/Minsk', 'Europe/Minsk', $option['timezone']);
    $timezone_value[] = array('Asia/Novosibirsk ', 'Asia/Novosibirsk ', $option['timezone']);
    $timezone_value[] = array(__('������������ ��������'), '', $option['timezone']);

    if (empty($data['num_row_adm']))
        $data['num_row_adm'] = 3;
    $num_row_adm_value[] = array('1', 1, $data['num_row_adm']);
    $num_row_adm_value[] = array('2', 2, $data['num_row_adm']);
    $num_row_adm_value[] = array('3', 3, $data['num_row_adm']);
    $num_row_adm_value[] = array('4', 4, $data['num_row_adm']);
    $num_row_adm_value[] = array('5', 5, $data['num_row_adm']);
    $num_row_adm_value[] = array('6', 6, $data['num_row_adm']);

    if (empty($option['catlist_depth'])) {
        $option['catlist_depth'] = 2;
    }

    $shop_type_value[]= array('��������-�������', 0, $data['shop_type']);
    $shop_type_value[]= array('������� ���������', 1, $data['shop_type']);
    $shop_type_value[]= array('���� ��������', 2, $data['shop_type']);
    
    // ���������� �������� 1
    if(empty($hideSite))
    $disp = 
            $PHPShopGUI->setField('������������', $PHPShopGUI->setSelect('shop_type_new', $shop_type_value,false,true)).
            $PHPShopGUI->setField("����� ���������", 
            $PHPShopGUI->setInputText(false, 'num_row_new', $data['num_row'], 50), 1, '���������� ������� �� ����� ��������') .
            $PHPShopGUI->setField("���������� � ����������������", $PHPShopGUI->setInputText(false, 'spec_num_new', $data['spec_num'], 50)) .
            $PHPShopGUI->setField("���������� � ��������", $PHPShopGUI->setInputText(false, 'new_num_new', $data['new_num'], 50)) .
            $PHPShopGUI->setField("�������� ����� �������", $PHPShopGUI->setSelect('num_vitrina_new', $num_vitrina_value, 50), 1, '������� � ����� 
	  ��� ������� ������� ��������. ����� 5 � 6 �������������� �� ����� ���������') .
            $PHPShopGUI->setField("�������� ����� � ��������", $PHPShopGUI->setSelect('num_row_adm_new', $num_row_adm_value, 50) . '&nbsp;' . $PHPShopGUI->setCheckbox('num_row_set', 1, '��������� ������ �� ���� ���������', 0), 1, '������� � ����� 
	  ��� ��������� �� ���������. ����� 5 � 6 �������������� �� ����� ���������') .
            $PHPShopGUI->setField("����� �������", $PHPShopGUI->setSelect('option[new_enabled]', $new_enabled_value, null, true)) .
            $PHPShopGUI->setField("������ ��������", $PHPShopGUI->setCheckbox('option[nowbuy_enabled]',1,null,$option['nowbuy_enabled']),1,false,$hideCatalog) .
            $PHPShopGUI->setField("�������� ������", $PHPShopGUI->setCheckbox('option[digital_product_enabled]', 1, '������� �������� �������', $option['digital_product_enabled']), 1, '������������� � ������ ����� �������� ����� ������ ������ � ������ ��������',$hideCatalog) .
            $PHPShopGUI->setField("����� ������� � ��������", $PHPShopGUI->setCheckbox('option[catlist_enabled]', 1, '�������� ������ � �������� ��������', $option['catlist_enabled']), 1) .
            $PHPShopGUI->setField("������� ����������� ������ �������", $PHPShopGUI->setInputText(false, 'option[catlist_depth]', $option['catlist_depth'], 50), 1) .
            $PHPShopGUI->setField("������������ ���������", $PHPShopGUI->setCheckbox('option[ajax_scroll]', 1, '��������� ��������� � �������', $option['ajax_scroll']), 1, '�������� �� �� ���� ��������') .
            $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setSelect('option[timezone]', $timezone_value)
            );
    else 
        $disp =  
            $PHPShopGUI->setField('������������', $PHPShopGUI->setSelect('shop_type_new', $shop_type_value)).
            $PHPShopGUI->setField("����� ���������", $PHPShopGUI->setInputText(false, 'num_row_new', $data['num_row'], 50), 1, '���������� ������� �� ����� ��������') .
            $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setSelect('option[timezone]', $timezone_value))
            ;
    
    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('�����',$disp);

    $warehouse_enabled = $PHPShopBase->getNumRows('warehouses', "where enabled='1'");

    $price = $PHPShopGUI->setField("������ �� ���������", $PHPShopGUI->setSelect('dengi_new', $dengi_value)) .
            $PHPShopGUI->setField("������ � ������", $PHPShopGUI->setSelect('kurs_new', $kurs_value)) .
            $PHPShopGUI->setField("�������� ����", $PHPShopGUI->setInputText(false, 'percent_new', (int) $data['percent'], 100, '%')) .
            $PHPShopGUI->setField("���", $PHPShopGUI->setCheckbox('nds_enabled_new', 1, '��������� ��� � �����', (int) $data['nds_enabled'])) .
            $PHPShopGUI->setField("�������� ���", $PHPShopGUI->setInputText(false, 'nds_new', (int) $data['nds'], 100, '%')) .
            $PHPShopGUI->setField("�������� ������", $PHPShopGUI->setSelect('option[sklad_status]', $sklad_status_value, null, true), 1, '������������ ��� ���������� ������') .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setCheckbox('option[sklad_enabled]', 1, '���������� �������� ������ � ������', $option['sklad_enabled']));

    if ($warehouse_enabled){
        $price .= $PHPShopGUI->setField("����� �����", $PHPShopGUI->setCheckbox('option[sklad_sum_enabled]', 1, '����������� ������� �� �������', $option['sklad_sum_enabled']), 1, '��������� ���������� ������ � �������� ������� � ����� �����. ����������� �� ������� ������ ��������� � �������� ������');
        $price .= $PHPShopGUI->setField("���������� �� �������", $PHPShopGUI->setCheckbox('option[sklad_sort_enabled]', 1, '���������� ������ ������ �� �������', $option['sklad_sort_enabled']));
        
    }

    $price .= $PHPShopGUI->setField("���������� ���", $PHPShopGUI->setInputText(false, 'option[price_znak]', intval($option['price_znak']), 50), 1, '���������� ������ ����� ������� � ����') .
            $PHPShopGUI->setField("����������� ����� ������", $PHPShopGUI->setInputText(false, 'option[cart_minimum]', intval($option['cart_minimum']), 100)) .
            $PHPShopGUI->setField("�������������� ����", $PHPShopGUI->setCheckbox('option[multi_currency_search]', 1, '���������� �� ���� ����� �������������� �������', $option['multi_currency_search']), false, __('���������������� ����� ������ ������')) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setCheckbox('option[parent_price_enabled]', 1, '��������� �������������� ������ ����������� ���� �������� ������', $option['parent_price_enabled'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox('option[auto_discount_disabled]', 1, '��������� �������������� ������ ������������ ������ ��� �������������� ������', $option['auto_discount_disabled']));

    if (empty($option['search_row']))
        $option['search_row'] = $data['num_row_adm'];

    $search_row_value[] = array(1, 1, $option['search_row']);
    $search_row_value[] = array(2, 2, $option['search_row']);
    $search_row_value[] = array(3, 3, $option['search_row']);
    $search_row_value[] = array(4, 4, $option['search_row']);
    $search_row_value[] = array(5, 5, $option['search_row']);
    $search_row_value[] = array(6, 6, $option['search_row']);

    if (empty($option['search_num']))
        $option['search_num'] = $data['num_row'];

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� ������', $PHPShopGUI->setField('������� ������', $PHPShopGUI->setSelect('option[search_pole]', $search_pole_value, null, true),1,false,$hideSite) .
            $PHPShopGUI->setField("�������� ����� ������", $PHPShopGUI->setSelect('option[search_row]', $search_row_value, 50), 1, '������� � ����� 
	  ��� ������. ����� 5 � 6 �������������� �� ����� ���������') .
            $PHPShopGUI->setField("���������� � ������", $PHPShopGUI->setInputText(false, 'option[search_num]', $option['search_num'], 50)) .
            $PHPShopGUI->setField("������.�������", $PHPShopGUI->setCheckbox('option[yandex_speller_enabled]', 1, '������������ ������.������� ���  �������� ���������� � ������ �� �����', $option['yandex_speller_enabled']))
    );

    if (empty($option['filter_cache_period']))
        $option['filter_cache_period'] = 10;
    
    // ��g �������� �������������
    $sort_value[] = array(__('����������'), 0, $option['update_sort_type']);
    $sort_value[] = array(__('�����'), 1, $option['update_sort_type']);

    $filter = $PHPShopGUI->setField("���������� �������� �������", $PHPShopGUI->setCheckbox('option[filter_cache_enabled]', 1, '���������� ������ ���������� �������, ����� �� ���������� �� � �����������', $option['filter_cache_enabled']), 1) .
            $PHPShopGUI->setField("������ �����������", $PHPShopGUI->setInputText(false, 'option[filter_cache_period]', $option['filter_cache_period'], 100, __('����'), false, false, false, false), 1, '������� ���� ������� ������������ ������') .
            $PHPShopGUI->setField("���������� ���������� ������", $PHPShopGUI->setCheckbox('option[filter_products_count]', 1, '�������� ���������� ������ ����� �� ��������� �������', $option['filter_products_count']), 1) .
            $PHPShopGUI->setField('������ �������', $PHPShopGUI->setSelect('option[filter_logic]', $filter_logic_value, null, true)).
            $PHPShopGUI->setField("������������� ��������������", $PHPShopGUI->setSelect('option[update_sort_type]', $sort_value)
                    );

    if(empty($hideSite))
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� �������', $filter);

    if(empty($hideCatalog))
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� ���', $price);

    if (empty($option['user_phone_mask']))
        $option['user_phone_mask'] = '(999) 999-9999';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� �������', $PHPShopGUI->setField('����', GetLocaleList($option['lang'])) . $PHPShopGUI->setField('������', GetSkinList($data['skin']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_skin]', 1, '������ ��������� ������� Live Edit', $option["user_skin"]), 1, '������ ������ ����� (front-end)') .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setIcon($data['logo'], "logo_new", false), 1, '������������ � ����� ������� � �������� ����������') .
            $PHPShopGUI->setField("Favicon", $PHPShopGUI->setIcon($data['icon'], "icon_new", false, array('load' => false, 'server' => true, 'url' => true, 'multi' => false, 'view' => false)), 1, '������ ����� � �������� � ������') .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(null, "bank[vk]", $bank['vk'], 350, false, false, false, '����� ��������')) .
            $PHPShopGUI->setField("Telegram", $PHPShopGUI->setInputText(null, "bank[telegram]", $bank['telegram'], 350, false, false, false, '����� ��������')) .
            $PHPShopGUI->setField("�������������", $PHPShopGUI->setInputText(null, "bank[odnoklassniki]", $bank['odnoklassniki'], 350, false, false, false, '����� ��������')) .
            $PHPShopGUI->setField("WhatsApp", $PHPShopGUI->setInputText(null, "bank[whatsapp]", $bank['whatsapp'], 350, false, false, false, '����� ��������')) .
            $PHPShopGUI->setField("Rutube", $PHPShopGUI->setInputText(null, "bank[youtube]", $bank['youtube'], 350, false, false, false, '����� ��������')).
            $PHPShopGUI->setField("������ ����� ��������", $PHPShopGUI->setInputText(null, "option[user_phone_mask]", $option['user_phone_mask'], 300) .
            $PHPShopGUI->setCheckbox('option[user_phone_mask_enabled]', 1, '��������� ����� ����� ��������', $option['user_phone_mask_enabled']), 1, '������: (999) 999-9999') .
            $PHPShopGUI->setField("������ �� �����", $PHPShopGUI->setCheckbox('option[user_captcha_enabled]', 1, '��������� ����������� �������� ��� ������ �� �����', $option['user_captcha_enabled'])).
            $PHPShopGUI->setField("������������������", $PHPShopGUI->setCheckbox('option[user_cookie_enabled]', 1, '��������� ��������� �� ������������� cookie-������ �� PC', $option['user_cookie_enabled']).'<br>'. $PHPShopGUI->setCheckbox('option[user_cookie_mobile_enabled]', 1, '��������� ��������� �� ������������� cookie-������ �� ��������� �����������', $option['user_cookie_mobile_enabled']))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� e-mail �����������', $PHPShopGUI->setField("E-mail ����������", $PHPShopGUI->setInputText(null, "adminmail2_new", $data['adminmail2'], 300), 1, '��� ������������� ��������� SMTP �������� ����� ������ ��������� � ������������� SMTP') .
            $PHPShopGUI->setField("SMTP", $PHPShopGUI->setCheckbox('option[mail_smtp_enabled]', 1, '�������� ����� ����� SMTP ��������', $option['mail_smtp_enabled']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_smtp_debug]', 1, '�������� ���������� ��������� (Debug)', $option['mail_smtp_debug']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[mail_smtp_auth]', 1, '��������������� TLS ��� SMTP', $option['mail_smtp_auth'])
            ) .
            $PHPShopGUI->setField("�������� ������ SMTP", $PHPShopGUI->setInputText(null, "option[mail_smtp_host]", $option['mail_smtp_host'], 300, false, false, false, 'smtp.yandex.ru'), 1, '������ ��������� ����� SMTP') .
            $PHPShopGUI->setField("���� �������", $PHPShopGUI->setInputText(null, "option[mail_smtp_port]", $option['mail_smtp_port'], 100, false, false, false, '25'), 1, '���� ��������� SMTP �������') .
            $PHPShopGUI->setField("������������", $PHPShopGUI->setInputText(null, "option[mail_smtp_user]", $option['mail_smtp_user'], 300, false, false, false, 'user@yandex.ru')) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInput('password', "option[mail_smtp_pass]", $option['mail_smtp_pass'], null, 300)) .
            $PHPShopGUI->setField("�������� �����", $PHPShopGUI->setInputText(null, "option[mail_smtp_replyto]", $option['mail_smtp_replyto'], 300), 1, '������ �� �������� ��������� ����� ��������� �� ���� �����')
    );


    if(empty($hideCatalog))
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� �������������', $PHPShopGUI->setField("����������� �������������", $PHPShopGUI->setCheckbox('option[user_mail_activate]', 1, '��������� ����� E-mail', $option['user_mail_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_mail_activate_pre]', 1, '������ ��������� ���������������', $option['user_mail_activate_pre']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_price_activate]', 1, '����������� ��� ��������� ���', $option['user_price_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_items_activate]', 1, '����������� ��� ��������� ��������', $option['user_items_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_servers_control]', 1, '���������� ������������� ��� ������', $option['user_servers_control']))
            . $PHPShopGUI->setField("������ ����� �����������", $PHPShopGUI->setSelect('option[user_status]', $userstatus_value, 300))
            . $PHPShopGUI->setField("������ �� �������:", $PHPShopGUI->setInputText(null, "option[bonus]", intval($option['bonus']), 100, '%'))
            . $PHPShopGUI->setField("������������ ������ ��������:", $PHPShopGUI->setInputText(null, "option[order_bonus]", intval($option['order_bonus']), 100, '%'))
    );
    

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������� ����������', $PHPShopGUI->setField('�������� ����', GetAdminSkinList($option['theme']), 1, '�������� ���� ���������� ������ ���������� (back-end)') .
            $PHPShopGUI->setField('����', GetLocaleList($option['lang_adm'], 'lang_adm')) .
            $PHPShopGUI->setField("HTML-�������� �� ���������", GetEditors($option['editor']), 1, '���������� �������� ��������') .
            $PHPShopGUI->setField("���� ��������� ��������� ����", GetAceSkinList($option['ace_theme']), 1, '������������� ��������� ���������� ��������� HTML ����') .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(null, "option[adm_title]", substr($option['adm_title'],0,70), 300), 1, '��������� ��������� � ����� ������� ���� ������ ����������') .
            $PHPShopGUI->setField("Multi Manager", $PHPShopGUI->setCheckbox('option[rule_enabled]', 1, '���� ���� ���������� �������� ��� ����������', $option['rule_enabled']),null, null, $hideSite) .
            $PHPShopGUI->setField("������ ���������� ������", $PHPShopGUI->setCheckbox('option[odnotip]', 1, '�������� ������������ ���������� ������� ��� ���������� �������', $option['odnotip']),null, null, $hideCatalog) .
            $PHPShopGUI->setField("Yandex Cloud", $PHPShopGUI->setCheckbox('option[yandexcloud_enabled]', 1, '��������� ���������� � Yandex Cloud', $option['yandexcloud_enabled'])) .
            $PHPShopGUI->setField("�������� IP ������", $PHPShopGUI->setCheckbox('option[ip_enabled]', 1, '��������� �������� IP ������ ��� ����������� ����� CloudFlare', $option['ip_enabled'])) .
            $PHPShopGUI->setField("������� ��������", $PHPShopGUI->setCheckbox('option[fast_view]', 1, '��������� ������� �������� ������� � ����', $option['fast_view']),null, null, $hideSite) .
            
            $PHPShopGUI->setField("������� �����", $PHPShopGUI->setSelect('option[search_enabled]', $search_enabled_value, null, true), 1, '����� � ������� ������ ���� ������ ����������',$hideCatalog)
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

    // ������� ��������� � ���������
    unset($option['support_notice']);

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('option.user_calendar', 'option.cloud_enabled', 'option.digital_product_enabled', 'option.parent_price_enabled', 'option.user_skin', 'option.user_mail_activate', 'option.user_mail_activate_pre', 'option.user_price_activate', 'option.mail_smtp_enabled', 'option.mail_smtp_debug', 'option.multi_currency_search', 'option.mail_smtp_auth', 'option.sklad_enabled', 'option.rule_enabled', 'option.catlist_enabled', 'option.filter_cache_enabled', 'option.filter_products_count', 'option.chat_enabled', 'option.new_enabled', 'option.sklad_sum_enabled', 'option.user_servers_control', 'option.user_phone_mask_enabled', 'option.user_items_activate', 'option.ajax_scroll', 'option.ajax_scroll_paginator', 'option.fast_view', 'option.auto_discount_disabled', 'option.chat_support', 'option.user_captcha_enabled', 'option.user_cookie_enabled','option.user_cookie_mobile_enabled','option.odnotip','option.sklad_sort_enabled','option.nowbuy_enabled','option.yandex_speller_enabled','option.yandexcloud_enabled','option.ip_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;
    
    if($option['product_off'] == 1)
        $option['cart_off'] = $option['product_off'];

    // ����� ������� �� front-end
    if ($data['skin'] != $_POST['skin_new'] and PHPShopSecurity::true_skin($_POST['skin_new']))
        $_SESSION['skin'] = $_POST['skin_new'];

    $_POST['admoption_new'] = serialize($option);
    $_POST['nds_enabled_new'] = $_POST['nds_enabled_new'] ? 1 : 0;
    $_POST['nds_enabled_new'] = $_POST['nds_enabled_new'] ? 1 : 0;

    // �������
    $_POST['logo_new'] = iconAdd('logo_new');

    // Favicon
    $_POST['icon_new'] = iconAdd('icon_new');

    // ����� ����� �������������
    if (!empty($_POST['num_row_set'])) {
        $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrmCat->update(array('num_row_new' => $_POST['num_row_adm_new']));
    }

    $bank = unserialize($data['bank']);
    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $_POST['bank_new'] = serialize($bank);

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// ���������� ����������� 
function iconAdd($name = 'icon_new') {
    global $PHPShopSystem;

    // ����� ����������
    $path = '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST[$name];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST[$name])) {
        $file = $_POST[$name];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// ��������� �������
$PHPShopGUI->getAction();
?>