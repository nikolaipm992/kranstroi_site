<?php

$TitlePage = __('�������� ��������� ����� ����');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
PHPShopObj::loadClass('user');

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;

    $PHPShopGUI->action_button['�������'] = array(
        'name' => __('���������'),
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-download-alt'
    );


    // ������ �������� ����
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('�������'));

    $structure_value[] = array('��������� � ������', '0', 'selected');
    $structure_value[] = array('������ ���������', '1', '');

    // ������ ������
    foreach ($GLOBALS['SysValue']['base'] as $val) {
        if (is_array($val)) {
            foreach ($val as $mod_base)
                $baseArray[$mod_base] = $mod_base;
        }
        else
            $baseArray[$val] = $val;
    }

    $table=null;
    foreach ($baseArray as $val) {
        $table.='<option value="' . $val . '" selected class="">' . $val . '</option>';
    }

    // ���������� �������� 1
    $PHPShopGUI->_CODE.= $PHPShopGUI->setCollapse('���������', $PHPShopGUI->setField('�������', '
        <table >
        <tr>
        <td>
        <select id="pattern_table" style="height:400px;width:500px" name="pattern_table[]" multiple class="form-control" required>' . $table . '</select>
        </td>
        <td>&nbsp;</td>
        <td class="text-center"><a class="btn btn-default btn-sm" href="#" id="select-all" data-toggle="tooltip" data-placement="top" title="' . __('������� ���') . '"><span class="glyphicon glyphicon-chevron-left"></span></a><br><br>
        <a class="btn btn-default btn-sm" id="select-none" href="#" data-toggle="tooltip" data-placement="top" title="' . __('������ ��������� �� ����') . '"><span class="glyphicon glyphicon-chevron-right"></span></a></td>
        </tr>
   </table>
            
' . $PHPShopGUI->setHelp('��� ������ ����� ����� ������ ������� ����� ������� ���� �� ������, ��������� ������� CTRL')) .
            $PHPShopGUI->setField('GZIP ������', $PHPShopGUI->setCheckbox('export_gzip', 1, null, 1), 1, '��������� ������ ������������ �����') .
            $PHPShopGUI->setField('�����������', $PHPShopGUI->setInputText(false, 'export_comment', '', 300)) .
            $PHPShopGUI->setField('�������� �����������', $PHPShopGUI->setSelect('export_structure', $structure_value, 300, true)), 'in', false);

    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionCreate.exchange.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionCreate() {
    global $PHPShopModules, $PHPShopGUI;

    

    // ����������
    if (!empty($_REQUEST['update']))
        $file = 'upload_dump.sql';
    else if (!empty($_POST['export_comment']))
        $file = substr(PHPShopString::toLatin($_POST['export_comment']), 0, 25) . '.sql';
    else
        $file = 'base_' . date("Y_m_d_His") . '.sql';


    $file = "./dumper/backup/" . $file;
    include_once('./dumper/dumper.php');
    $result = mysqlbackup($GLOBALS['SysValue']['connect']['dbase'], $file, $_POST['export_structure'], $_POST['pattern_table']);

    // Gzip
    if (!empty($_REQUEST['export_gzip'])) {
        $result = PHPShopFile::gzcompressfile($file);
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    if (empty($_REQUEST['update'])) {

        if ($result)
            header('Location: ?path=' . $_GET['path']);
        else
            echo $PHPShopGUI->setAlert(__('��� ���� �� ������ �����') . ' ' . $file, 'danger');
    }
    else
        return array('success' => true);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>