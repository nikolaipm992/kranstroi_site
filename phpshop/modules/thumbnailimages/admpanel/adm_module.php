<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

include_once dirname(__DIR__) . '/class/ThumbnailImages.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.thumbnailimages.thumbnailimages_system"));
$PHPShopOrm->debug = false;

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    
    if(empty($_POST['stop_new']))
        $_POST['stop_new']=0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionGenerateOriginal() {
    global $PHPShopOrm;

    $data = $PHPShopOrm->select();
    $thumbnailImages = new ThumbnailImages();
    $result = $thumbnailImages->generateOriginal();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('���������� �����������: � %s �� %s. ��� ��������� ����������� ����������. ��������� ������� ������ �������� �������� � 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    if ('original' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('���������. ���������� �����������: � %s �� %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    echo $message;

    if (count($result['skipped']) > 0) {
        $skipped = '';
        foreach ($result['skipped'] as $file) {
            $skipped .= '�� ������ ����: ' . $file . '<br>';
        }
        echo '<div class="alert alert-warning" id="rules-message"  role="alert">' .
        $skipped
        . '</div>';
    }
}

function actionGenerateThumbnail() {
    global $PHPShopOrm;

    $data = $PHPShopOrm->select();
    $thumbnailImages = new ThumbnailImages();
    $result = $thumbnailImages->generateThumbnail();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('���������� �����������: � %s �� %s. ��� ��������� ����������� ����������. ��������� ������� ������ �������� �������� � 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    if ('thumb' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('���������. ���������� �����������: � %s �� %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    echo $message;

    if (count($result['skipped']) > 0) {
        $skipped = '';
        foreach ($result['skipped'] as $file) {
            $skipped .= '�� ������ ����: ' . $file . '<br>';
        }
        echo '<div class="alert alert-warning" id="rules-message"  role="alert">' .
        $skipped
        . '</div>';
    }
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(['version_new' => $new_version]);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    // �������
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 4;
    
    $PHPShopGUI->action_button['������������� ������'] = [
        'name' => __('������������� ������'),
        'action' => 'saveIDthumb',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    ];

    $PHPShopGUI->action_button['������������� �������'] = [
        'name' => __('������������� �������'),
        'action' => 'saveIDorig',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    ];

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['������������� ������', '������������� �������', '��������� � �������']);

    $Tab1 = '<div class="alert alert-info" role="alert">' .
            __('����������, ������������ � ����������� �� ������� <kbd>��������</kbd> ����� �������������� ������.')
            . '</div>';

    $Tab1 .= $PHPShopGUI->setField('������������ ����������� �� ���', $PHPShopGUI->setInputText(false, 'limit_new', $data['limit'], 150));

    $e_value[] = array('������������', 1, $data['type']);
    $e_value[] = array('JPG', 2, $data['type']);
    $e_value[] = array('WEBP', 3, $data['type']);
    

    $Tab1 .= $PHPShopGUI->setField('������ ����������� ��� ����������', $PHPShopGUI->setSelect('type_new', $e_value, 150, true));
    
    $d_value[] = array('���', 1, $data['delete']);
    $d_value[] = array('��', 2, $data['delete']);
    

    $Tab1 .= $PHPShopGUI->setField('������� ������ ����������� ��� ����� �������', $PHPShopGUI->setSelect('delete_new', $d_value, 150, true));
    
    $Tab1 .= $PHPShopGUI->setField('����������� ��������� ������', $PHPShopGUI->setCheckbox('stop_new', 1, '', $data['stop']));

    $Info = '<p>
        ������ ��������� ������������� ����� �������� �� ��������� � <kbd>���������</kbd> &rarr; <kbd>�����������</kbd> ����������.<br>
        ������ ��� ������� � �������� ������������ �� ������ ��������:
        <ul>
            <li>����������� ��������� <kbd>��������� �������� ����������� ��� ����������</kbd></li>
            <li>���� ��������� �������� - ����������� ������� ����� �������� � ��������� <code>_big</code>, ��� ����������� �������� � ������������ �������, ��� �������� ������ ������������ ���.</li>
            <li>���� ��������� ��������� ��� ����������� � ��������� <code>_big</code> ��� - ��� ��������� ������ ����������� ������������ ������� �������� ������, ���������� �������� ���������� 
                <kbd>����. ������ ���������</kbd> � <kbd>����. ������ ���������</kbd>.
            </li>
            <li>��� ����������� ������� � ��������� <code>_s</code> ����� �������� ������ ���������������� �������������.</li>
            <li>��� �������� �� webp ���� ����������� ������� ��������� ��� ��������� ��������� � ������� ��������.</li>
            <li>��� ������������� �������� ������� �������� ����� ������ � <kbd>Cron</kbd> � ������� ������������ ����� <code>phpshop/modules/thumbnailimages/cron/images.php thumb</code> ��� ��������� ������ � <code>phpshop/modules/thumbnailimages/cron/images.php orig</code> ��� ��������� ������� ��������.</li>
        </ul>
       </p>
       <p>
       ��������� ������� ����������� �������� ������, ���� �������� ��������� <kbd>��������� �������� ����������� ��� ����������</kbd> ��� ��������� ������� 
       <kbd>����. ������ ���������</kbd> � <kbd>����. ������ ���������</kbd> � ���������� ������������� ������� �����������.
        </p>
';

    $Tab2 = $PHPShopGUI->setInfo($Info);


    // ���������� �������� 2
    $Tab3 = $PHPShopGUI->setPay(false, true, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(["��������", $Tab1, true], ["��������", $Tab2], ["� ������", $Tab3]);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveIDthumb", "���������", "right", 80, "", "but", "actionGenerateThumbnail.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveIDorig", "���������", "right", 80, "", "but", "actionGenerateOriginal.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>