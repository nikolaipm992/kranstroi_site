<?php

$TitlePage = __("Настройка интеграций с сервисами");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить'));

    // Демо-режим
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $option['metrica_token'] = $option['telegram_token'] = '';
    }

    // Яндекс.Метрика
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Статистика посещений Яндекс.Метрика', $PHPShopGUI->setField('Токен', $PHPShopGUI->setInputText(false, 'option[metrica_token]', $option['metrica_token'], 375, '<a target="_blank" href="https://oauth.yandex.ru/authorize?response_type=token&client_id=78246cbd13f74fbd9cb2b48d8bff2559">' . __('Получить') . '</a>')) .
            $PHPShopGUI->setField('ID сайта', $PHPShopGUI->setInputText(null, 'option[metrica_id]', $option['metrica_id'], 300, false, false, false, 'XXXXXXXX') .
                    $PHPShopGUI->setHelp('Отчеты доступны в разделе <a href="?path=metrica">Статистика посещений</a>')) .
            $PHPShopGUI->setField("Код счетчика", $PHPShopGUI->setCheckbox('option[metrica_enabled]', 1, 'Включить сбор статистики и разместить код счетчика', $option['metrica_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[metrica_ecommerce]', 1, 'Включить сбор данных электронной коммерции', $option['metrica_ecommerce']) . '<br>' . $PHPShopGUI->setCheckbox('option[metrica_webvizor]', 1, 'Включить вебвизор, карту скроллинга и аналитику форм', $option['metrica_webvizor'])) .
            $PHPShopGUI->setField("Виджет", $PHPShopGUI->setCheckbox('option[metrica_widget]', 1, 'Включить виджет статистики в панель инструментов', $option['metrica_widget']))
            , 'in', false
    );

    // Яндекс.Карты
    if (empty($hideCatalog))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Карта доставки Яндекс.Карты', $PHPShopGUI->setField('API-ключ', $PHPShopGUI->setInputText(false, 'option[yandex_apikey]', $option['yandex_apikey'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://developer.tech.yandex.ru" target="_blank">Кабинет разработчика</a>')) .
                $PHPShopGUI->setField("Карта доставки заказа", $PHPShopGUI->setCheckbox('option[yandexmap_enabled]', 1, 'Вывод адреса доставки заказа на Яндекс.Карте', $option['yandexmap_enabled']))
                , 'in', true
        );

    // Яндекс.Поиск
    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Яндекс.Поиск', $PHPShopGUI->setField('API-ключ', $PHPShopGUI->setInputText(false, 'option[yandex_search_apikey]', $option['yandex_search_apikey'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://developer.tech.yandex.ru" target="_blank">Кабинет разработчика</a>')) .
                $PHPShopGUI->setField('Идентификатор поиска', $PHPShopGUI->setInputText(false, 'option[yandex_search_id]', $option['yandex_search_id'], 300)) .
                $PHPShopGUI->setField("Включить Яндекс.Поиск", $PHPShopGUI->setCheckbox('option[yandex_search_enabled]', 1, 'Использовать Яндекс.Поиск на сайте, вместо стандартного поиска', $option['yandex_search_enabled'])), 'in', true
        );

    // Яндекс ID
    if (empty($hideSite))
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Яндекс ID', $PHPShopGUI->setField('ClientID', $PHPShopGUI->setInputText(false, 'option[yandex_id_apikey]', $option['yandex_id_apikey'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://oauth.yandex.ru/client/new/id/" target="_blank">Кабинет разработчика</a>')) .
                $PHPShopGUI->setField("Включить Яндекс ID", $PHPShopGUI->setCheckbox('option[yandex_id_enabled]', 1, 'Использовать OAuth авторизацию с помощью Яндекс ID на сайте', $option['yandex_id_enabled'])), 'in', true
        );

    // VK ID
    if (empty($hideSite)){
        $get_token='https://oauth.vk.com/authorize?client_id='.$option['vk_id'].'&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=video,offline&response_type=token&v=5.52';
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('VK ID', $PHPShopGUI->setField('ID приложения', $PHPShopGUI->setInputText(false, 'option[vk_id]', $option['vk_id'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://id.vk.com/about/business/go" target="_blank">Кабинет разработчика</a>')) .
                $PHPShopGUI->setField('Токен доступа', $PHPShopGUI->setTextarea('option[vk_id_token]', $option['vk_id_token'], false, 300, '100') . $PHPShopGUI->setHelp('Используется для передачи отзывов из группы VK. Получить <a href="'.$get_token.'" id="client_token" target="_blank">Персональный токен</a>')).
                $PHPShopGUI->setField('Сервисный ключ', $PHPShopGUI->setInputText(false, 'option[vk_id_apikey]', $option['vk_id_apikey'], 300)) .
                $PHPShopGUI->setField("Включить VK ID", $PHPShopGUI->setCheckbox('option[vk_id_enabled]', 1, 'Использовать OAuth авторизацию с помощью VK ID на сайте', $option['vk_id_enabled'])), 'in', true
        );
    }


    // Google Analitiks
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Статистика посещений Google', $PHPShopGUI->setField('Идентификатор отслеживания', $PHPShopGUI->setInputText('UA-', 'option[google_id]', $option['google_id'], 300, false, false, false, 'XXXXX-Y') .
                    $PHPShopGUI->setHelp('Отчеты доступны в разделе <a href="https://analytics.google.com/analytics/web/" target="_blank">Google Аналитика</a>')) .
            $PHPShopGUI->setField("Код счетчика", $PHPShopGUI->setCheckbox('option[google_enabled]', 1, 'Включить сбор статистики и разместить код счетчика', $option['google_enabled']) . '<br>' . $PHPShopGUI->setCheckbox('option[google_analitics]', 1, 'Включить сбор данных электронной коммерции', $option['google_analitics']))
            , 'in', true
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Безопасность Google reCAPTCHA', $PHPShopGUI->setField("reCAPTCHA", $PHPShopGUI->setCheckbox('option[recaptcha_enabled]', 1, 'Включить режим усиленной проверки от ботов', $option['recaptcha_enabled'])) .
            $PHPShopGUI->setField("Публичный ключ", $PHPShopGUI->setInputText(null, "option[recaptcha_pkey]", $option['recaptcha_pkey'], 300)) .
            $PHPShopGUI->setField("Секретный ключ", $PHPShopGUI->setInputText(null, "option[recaptcha_skey]", $option['recaptcha_skey'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://www.google.com/recaptcha" target="_blank">Google.com</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Безопасность hCaptcha', $PHPShopGUI->setField("hCaptcha", $PHPShopGUI->setCheckbox('option[hcaptcha_enabled]', 1, 'Включить альтернативный режим усиленной проверки от ботов', $option['hcaptcha_enabled'])) .
            $PHPShopGUI->setField("Публичный ключ", $PHPShopGUI->setInputText(null, "option[hcaptcha_pkey]", $option['hcaptcha_pkey'], 300)) .
            $PHPShopGUI->setField("Секретный ключ", $PHPShopGUI->setInputText(null, "option[hcaptcha_skey]", $option['hcaptcha_skey'], 300) . $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://hCaptcha.com/?r=235b1c9fa5a4" target="_blank">hCaptcha.com</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Подсказки DaData.ru', $PHPShopGUI->setField("Подсказки", $PHPShopGUI->setCheckbox('option[dadata_enabled]', 1, 'Включить подсказки DaData.ru', $option['dadata_enabled'])) .
            $PHPShopGUI->setField("Публичный ключ", $PHPShopGUI->setInputText(null, "option[dadata_token]", $option['dadata_token'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://dadata.ru" target="_blank">DaData.ru</a>'))
    );

    if (empty($hideCatalog)) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('SMS уведомления Targetsms.ru', $PHPShopGUI->setField("SMS оповещение", $PHPShopGUI->setCheckbox('option[sms_enabled]', 1, 'Уведомление о заказе администратору', $option['sms_enabled']) . '<br>' .
                        $PHPShopGUI->setCheckbox('option[notice_enabled]', 1, 'Уведомление о наличии товара пользователям', $option['notice_enabled']) . '<br>' .
                        $PHPShopGUI->setCheckbox('option[sms_login]', 1, 'Авторизация по телефону', $option['sms_login']) . $PHPShopGUI->setHelp('При включенной опции, в корзине появляется поле Телефон, обязательное для заполнения')
                ) .
                $PHPShopGUI->setField("Мобильный телефон", $PHPShopGUI->setInputText(null, "option[sms_phone]", $option['sms_phone'], 300, false, false, false, '79261234567'), 1, 'Телефон для SMS уведомлений формата 79261234567') .
                $PHPShopGUI->setField("Пользователь", $PHPShopGUI->setInputText(null, "option[sms_user]", $option['sms_user'], 300), 1, 'Пользователь в системе Targetsms.ru') .
                $PHPShopGUI->setField("Пароль", $PHPShopGUI->setInput('password', "option[sms_pass]", $option['sms_pass'], null, 300), 1, 'Пароль в системе Targetsms.ru') .
                $PHPShopGUI->setField("Подпись отправителя", $PHPShopGUI->setInputText(null, "option[sms_name]", $option['sms_name'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href=" https://sms.targetsms.ru/ru/reg.html?ref=phpshop" target="_blank">Targetsms.ru</a>'))
        );

        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('PUSH Уведомления', $PHPShopGUI->setField("PUSH оповещение", $PHPShopGUI->setCheckbox('option[push_enabled]', 1, 'Уведомление о заказе администратору. Требуется SSL сертификат.', $option['push_enabled'])) .
                $PHPShopGUI->setField("Ключ сервера", $PHPShopGUI->setInputText(null, "option[push_token]", $option['push_token'], 300)) .
                $PHPShopGUI->setField("Идентификатор отправителя", $PHPShopGUI->setInputText(null, "option[push_id]", $option['push_id'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://console.firebase.google.com/" target="_blank">Firebase.google.com</a>'))
        );
    }


    // Telegram
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Telegram', $PHPShopGUI->setField("Новостной бот", $PHPShopGUI->setCheckbox('option[telegram_news_enabled]', 1, 'Включить передачу новостей из группы', $option['telegram_news_enabled'])) .
            $PHPShopGUI->setField("Анонс", $PHPShopGUI->setInputText("первые", "option[telegram_news_delim]", $option['telegram_news_delim'], 200, 'символов')) .
            $PHPShopGUI->setField("API-ключ", $PHPShopGUI->setInputText(null, "option[telegram_news_token]", $option['telegram_news_token'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://docs.phpshop.ru/stranicy/novosti#zagruzka-novostei-iz-telegram" target="_blank">Инструкция</a>')));

    // VK Reviews
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Отзывы Вконтакте',
    $PHPShopGUI->setField("Отзывы", $PHPShopGUI->setCheckbox('option[vk_reviews_enabled]', 1, 'Включить передачу отзывов из группы', $option['vk_reviews_enabled'])).
    $PHPShopGUI->setField("Код подтверждения", $PHPShopGUI->setInputText(null, "option[vk_reviews_confirmation]", $option['vk_reviews_confirmation'], 300)) .
    $PHPShopGUI->setField("Ключ подтверждения", $PHPShopGUI->setInputText(null, "option[vk_reviews_secret]", $option['vk_reviews_secret'], 300)) .
    $PHPShopGUI->setField("API-ключ", $PHPShopGUI->setInputText(null, "option[vk_reviews_token]", $option['vk_reviews_token'], 300) . $PHPShopGUI->setHelp('Информация о сервисе, регистрация, получение ключей <a href="https://docs.phpshop.ru/nastroiky/dialog#vkontakte" target="_blank">Инструкция</a>'))
    );

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Новостная лента', $PHPShopGUI->setField("RSS", $PHPShopGUI->setCheckbox('option[rss_graber_enabled]', 1, 'Загружать новости из внешних RSS каналов', $option['rss_graber_enabled']) . $PHPShopGUI->setHelp('Новостные каналы управляются в  разделе <a href="?path=news.rss">RSS каналы</a>'))
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
    $PHPShopOrm->updateZeroVars('option.recaptcha_enabled', 'option.dadata_enabled', 'option.sms_enabled', 'option.sms_status_order_enabled', 'option.notice_enabled', 'option.metrica_enabled', 'option.metrica_widget', 'option.metrica_ecommerce', 'option.google_enabled', 'option.google_analitics', 'option.rss_graber_enabled', 'option.yandexmap_enabled', 'option.push_enabled', 'option.metrica_webvizor', 'option.yandex_search_enabled', 'option.sms_login', 'option.hcaptcha_enabled', 'option.yandex_speller_enabled', 'option.yandex_id_enabled', 'option.telegram_news_enabled', 'option.vk_id_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $_POST['admoption_new'] = serialize($option);

    // Удаление PUSH-токенов
    if (empty($option['push_enabled'])) {
        $PHPShopPush = new PHPShopPush();
        $PHPShopPush->clean();
    }

    // Telegram регистрация вебхука
    if (!empty($option['telegram_news_enabled']) and ! empty($option['telegram_news_token'])) {

        $url = 'https://api.telegram.org/bot' . $option['telegram_news_token'] . '/setWebhook?url=https://' . $_SERVER['SERVER_NAME'] . '/bot/telegram-news.php/' . md5($option['telegram_news_token']);
        $сurl = curl_init();
        curl_setopt_array($сurl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ));
        $result = curl_exec($сurl);
        curl_close($сurl);
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>