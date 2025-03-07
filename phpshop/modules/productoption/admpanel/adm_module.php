<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $vendor = array(
        'option_1_name' => $_POST['option_1_name'],
        'option_1_format' => $_POST['option_1_format'],
        'option_2_name' => $_POST['option_2_name'],
        'option_2_format' => $_POST['option_2_format'],
        'option_3_name' => $_POST['option_3_name'],
        'option_3_format' => $_POST['option_3_format'],
        'option_4_name' => $_POST['option_4_name'],
        'option_4_format' => $_POST['option_4_format'],
        'option_5_name' => $_POST['option_5_name'],
        'option_5_format' => $_POST['option_5_format'],
        'option_6_name' => $_POST['option_6_name'],
        'option_6_format' => $_POST['option_6_format'],
        'option_7_name' => $_POST['option_7_name'],
        'option_7_format' => $_POST['option_7_format'],
        'option_8_name' => $_POST['option_8_name'],
        'option_8_format' => $_POST['option_8_format'],
        'option_9_name' => $_POST['option_9_name'],
        'option_9_format' => $_POST['option_9_format'],
        'option_10_name' => $_POST['option_10_name'],
        'option_10_format' => $_POST['option_10_format'],
    );

    $_POST['option_new'] = serialize($vendor);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function checkSelect($val) {
    $value[] = array('text', 'text', $val);
    $value[] = array('textarea', 'textarea', $val);
    //$value[] = array('checkbox', 'checkbox', $val);
    $value[] = array('hidden', 'hidden', $val);
    $value[] = array('editor', 'editor', $val);
    $value[] = array('radio', 'radio', $val);
    return $value;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->field_col = 1;

    // �������
    $data = $PHPShopOrm->select();
    $vendor = unserialize($data['option']);

    $Tab1 = $PHPShopGUI->setField('����� A', $PHPShopGUI->setInputText('���:', 'option_1_name', $vendor['option_1_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_1_format', checkSelect($vendor['option_1_format']), 100). '&nbsp;<span class="text-muted">@productOption1@</span>');

    $Tab1.= $PHPShopGUI->setField('����� B', $PHPShopGUI->setInputText('���:', 'option_2_name', $vendor['option_2_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_2_format', checkSelect($vendor['option_2_format']), 100). '&nbsp;<span class="text-muted">@productOption2@</span>');

    $Tab1.= $PHPShopGUI->setField('����� C', $PHPShopGUI->setInputText('���:', 'option_3_name', $vendor['option_3_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_3_format', checkSelect($vendor['option_3_format']), 100). '&nbsp;<span class="text-muted">@productOption3@</span>');

    $Tab1.= $PHPShopGUI->setField('����� D', $PHPShopGUI->setInputText('���:', 'option_4_name', $vendor['option_4_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_4_format', checkSelect($vendor['option_4_format']), 100). '&nbsp;<span class="text-muted">@productOption4@</span>');

    $Tab1.= $PHPShopGUI->setField('����� E', $PHPShopGUI->setInputText('���:', 'option_5_name', $vendor['option_5_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_5_format', checkSelect($vendor['option_5_format']), 100). '&nbsp;<span class="text-muted">@productOption5@</span>');


    $Tab2 = $PHPShopGUI->setField('����� A', $PHPShopGUI->setInputText('���:', 'option_6_name', $vendor['option_6_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_6_format', checkSelect($vendor['option_6_format']), 100). '&nbsp;<span class="text-muted">@catalogOption1@</span>');

    $Tab2.= $PHPShopGUI->setField('����� B', $PHPShopGUI->setInputText('���:', 'option_7_name', $vendor['option_7_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_7_format', checkSelect($vendor['option_7_format']), 100). '&nbsp;<span class="text-muted">@catalogOption2@</span>');

    $Tab2.= $PHPShopGUI->setField('����� C', $PHPShopGUI->setInputText('���:', 'option_8_name', $vendor['option_8_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_8_format', checkSelect($vendor['option_8_format']), 100). '&nbsp;<span class="text-muted">@catalogOption3@</span>');

    $Tab2.= $PHPShopGUI->setField('����� D', $PHPShopGUI->setInputText('���:', 'option_9_name', $vendor['option_9_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_9_format', checkSelect($vendor['option_9_format']), 100). '&nbsp;<span class="text-muted">@catalogOption4@</span>');

    $Tab2.= $PHPShopGUI->setField('����� E', $PHPShopGUI->setInputText('���:', 'option_10_name', $vendor['option_10_name'], 180, false, 'left') . '&nbsp;' . $PHPShopGUI->setSelect('option_10_format', checkSelect($vendor['option_10_format']), 100). '&nbsp;<span class="text-muted">@catalogOption5@</span>');

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1, 'in', false);
    $Tab1.= $PHPShopGUI->setCollapse('��������', $Tab2);

    $info = '������ ��������� �������� �������������� ���� ��� ����������� � �������� �������� �� ����� � ��� �������������� � �������� ������ ����� �������� "�������������". 
<p>        
��� ������ ������ �� ������� �� ����� ������������ ���������� <kbd>@productOption1@</kbd>, <kbd>@productOption2@</kbd>, <kbd>@productOption3@</kbd>, <kbd>@productOption4@</kbd>, <kbd>@productOption5@</kbd>, ��� ��������� ������������ ���������� <kbd>@catalogOption1@</kbd>, <kbd>@catalogOption2@</kbd>, <kbd>@catalogOption3@</kbd>, <kbd>@catalogOption4@</kbd>, <kbd>@catalogOption5@</kbd>.  ���������� ������������ ����������� ���������� ������ ���������� � �������� �������������� ������ ����. ���������� �������� � ����� ����� �������� ��������� <code>phpshop/templates/��� �������/product/</code> � ������� ��������� <code>phpshop/templates/��� �������/catalog/</code>.</p>  

��� ������� � ��������� ������� ����� php ������� ������������ �����������:<br><br>
<code>
$PHPShopProduct = new PHPShopProduct(�� ������);<br>
echo $PHPShopProduct->getParam("option1");<br>
echo $PHPShopProduct->getParam("option2");<br>
echo $PHPShopProduct->getParam("option3");<br>
echo $PHPShopProduct->getParam("option4");<br>
echo $PHPShopProduct->getParam("option5");<br>
</code>

��� ������� � ��������� ��������� ����� php ������� ������������ �����������:<br><br>
<code>
$PHPShopCategory = new PHPShopCategory(�� ��������);<br>
echo $PHPShopCategory->getParam("option6");<br>
echo $PHPShopCategory->getParam("option7");<br>
echo $PHPShopCategory->getParam("option8");<br>
echo $PHPShopCategory->getParam("option9");<br>
echo $PHPShopCategory->getParam("option10");<br>
</code>
';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, $pay = false, $data['version'], $update = true);


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("��������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� ������� 
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>