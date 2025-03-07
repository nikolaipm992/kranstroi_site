<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sphinxsearch.sphinxsearch_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopBase;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["filter_show_counts_new"]))
        $_POST["filter_show_counts_new"] = 0;
    if (empty($_POST["filter_update_new"]))
        $_POST["filter_update_new"] = 0;
    if (empty($_POST["search_show_informer_string_new"]))
        $_POST["search_show_informer_string_new"] = 0;
    if (empty($_POST["ajax_search_categories_new"]))
        $_POST["ajax_search_categories_new"] = 0;
    if (empty($_POST["available_sort_new"]))
        $_POST["available_sort_new"] = 0;
    if (empty($_POST["use_additional_categories_new"]))
        $_POST["use_additional_categories_new"] = 0;
    if (empty($_POST["use_proxy_new"]))
        $_POST["use_proxy_new"] = 0;
    if (empty($_POST["search_uid_first_new"]))
        $_POST["search_uid_first_new"] = 0;
    if (empty($_POST["yandexsearch_new"]))
        $_POST["yandexsearch_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name, $PHPShopBase;

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['��������� � �������']);
    $PHPShopGUI->field_col = 4;

    include_once dirname(__DIR__) . '/class/SphinxSearch.php';
    $SphinxSearch = new SphinxSearch();

    if (!empty($SphinxSearch->link_db))
        $check = '<span class="glyphicon glyphicon-ok text-success"></span>';
    else
        $check = '<span class="glyphicon glyphicon-remove text-danger"></span>';

    // �������
    $data = $PHPShopOrm->select();


    $Tab1 .= $PHPShopGUI->setCollapse('��������� ������', $PHPShopGUI->setField('���� "������� � ����������"', $PHPShopGUI->setSelect('find_in_categories_new', [
                        ['�� ������������', 0, $data['find_in_categories']],
                        ['����������� ��������', 1, $data['find_in_categories']],
                        ['����������� �������', 2, $data['find_in_categories']]
                            ], 250)) .
            $PHPShopGUI->setField('����� ���������� �������', $PHPShopGUI->setInputText($check, 'host_new', $data['host'], 250, false, false, false, '127.0.0.1')) .
            $PHPShopGUI->setField('���� ���������� ������� ��� MySQL', $PHPShopGUI->setInputText($check, 'port_new', $data['port'], 100, false, false, false, '9306')) .
            $PHPShopGUI->setField('������� � ���', $PHPShopGUI->setSelect('search_page_row_new', [
                        [1, 1, $data['search_page_row']],
                        [2, 2, $data['search_page_row']],
                        [3, 3, $data['search_page_row']],
                        [4, 4, $data['search_page_row']],
                        [5, 5, $data['search_page_row']]
                            ], 50)) .
            $PHPShopGUI->setField('������������ ���-�� ��������� � ����� "������� � ����������"', $PHPShopGUI->setInputText(false, 'max_categories_new', $data['max_categories'], 50)) .
            $PHPShopGUI->setField('������� �� ��������', $PHPShopGUI->setInputText(false, 'search_page_size_new', $data['search_page_size'], 50)) .
            $PHPShopGUI->setField('����������� ���������� �������� � ����� ��� ����������', $PHPShopGUI->setInputText(false, 'misprints_ajax_new', $data['misprints_ajax'], 50)) .
            $PHPShopGUI->setField('��������� �������� ��� ����� ���������� ������� ��', $PHPShopGUI->setInputText(false, 'misprints_from_cnt_new', $data['misprints_from_cnt'], 50)) .
            $PHPShopGUI->setField('�������������� ������', $PHPShopGUI->setCheckbox('search_show_informer_string_new', 1, '���������� ������ "������� XX ����������� � XX ����������."', $data['search_show_informer_string'])) .
            $PHPShopGUI->setField('�������������� ���������', $PHPShopGUI->setCheckbox('use_additional_categories_new', 1, '���������� �������������� ��������� �������', $data['use_additional_categories'])) .
            $PHPShopGUI->setField('������� � ������� ������', $PHPShopGUI->setInputText(false, 'ajax_search_products_cnt_new', $data['ajax_search_products_cnt'], 50)) .
            $PHPShopGUI->setField('��������� � ������� ������', $PHPShopGUI->setInputText(false, 'ajax_search_categories_cnt_new', $data['ajax_search_categories_cnt'], 50)) .
            $PHPShopGUI->setField('������� � �������', $PHPShopGUI->setCheckbox('available_sort_new', 1, '�������� ������� ������ � �������', $data['available_sort'])) .
            $PHPShopGUI->setField('������ ������� �� ��������', $PHPShopGUI->setCheckbox('search_uid_first_new', 1, '������� ������ �� ���������� ��������', $data['search_uid_first'])) .
            $PHPShopGUI->setField('������ � �������', $PHPShopGUI->setCheckbox('yandexsearch_new', 1, '������ � ������� ���� ������ �� �������', $data['yandexsearch'], $PHPShopGUI->disabled_yandexcloud))
    );


    $config = "source mainConfSourse
{
	type = mysql
	sql_host = " . $PHPShopBase->getParam("connect.host") . "
	sql_user = " . $PHPShopBase->getParam("connect.user_db") . "
	sql_pass = " . $PHPShopBase->getParam("connect.pass_db") . "
	sql_db = " . $PHPShopBase->getParam("connect.dbase") . "
	sql_port = " . $PHPShopBase->getParam("connect.port") . "	
	sql_query_pre	= SET NAMES utf8
}


source productsSrc : mainConfSourse
{

	sql_query = SELECT id,name,uid,content,category,items \
					FROM phpshop_products where enabled='1' and parent_enabled='0';

	#type of group fields
	sql_field_string = uid
	sql_field_string = name
	sql_field_string = content
	sql_field_string = category
	sql_field_string = items
}

index productsIndex
{
	source	= productsSrc
	path = /var/lib/sphinx/data/productsIndex
	morphology = stem_enru, Soundex, Metaphone
	min_word_len = " . $data['misprints_ajax'] . "
	expand_keywords  = 1
	index_exact_words = 1
	min_infix_len = " . $data['misprints_from_cnt'] . "
	html_strip = 1
}

source categoriesSrc : mainConfSourse
{

	sql_query		= SELECT id,name \
					FROM phpshop_categories where skin_enabled='0';

	#type of group fields
	sql_field_string = name
}

index categoriesIndex
{
	source					= categoriesSrc
	path					= /var/lib/sphinx/data/categoriesIndex
	morphology				= stem_enru, Soundex, Metaphone
	min_word_len		= 1
	expand_keywords		= 1
	index_exact_words	= 1
	min_infix_len		= 3
}

indexer
{
	mem_limit = 240M
}

searchd
{      
        
    log = /var/log/sphinx/searchd.log
	query_log = /var/log/sphinx/query.log
	pid_file = /var/run/sphinx/searchd.pid
	listen = " . $data['port'] . "
}";


    $PHPShopGUI->setEditor('ace', true);
    $oFCKeditor = new Editor('config', true);
    $oFCKeditor->Height = '520';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $config;

    $Tab2 = $PHPShopGUI->setCollapse('sphinx.conf', $oFCKeditor->AddGUI());

    $Tab3 = '<div class="form-group form-group-sm"><div class="col-sm-12" style="padding-left: 20px;padding-right: 20px;">
                ' . $PHPShopGUI->setTextarea('synonyms_new', $data['synonyms'], true, '100%', 300, '����� ������� ����� � �������. ������ ����� ���� � ����� ������. ��������:<br> �����, ������� <br> 
                            ryzen, ������') .
            '</div></div>';

    $info = '
    <h4>����������� Sphinx �� �������� Beget</h4>
    <ol>
        <li>� ������ �������� �������� �������� Beget � ������� <a href="https://cp.beget.com/cloudservices" target="_blank">�������</a> ����������� ������ <kbd>Sphinx</kbd>.</li>
        <li><a href="https://beget.com/ru/kb/how-to/services/ispolzovanie-sphinx#ispolzovanie-sphinx-na-hostinge" target="_blank">���������� �� �������������</a> ������� Sphinx �� �������� Beget.</li>
        <li>� �������� ����������������� ����� <kbd>sphinx.conf</kbd> ��� Sphinx ����������� ������ �� �������� <kbd>������������</kbd> � ���������� ������.</li>
    </ol>   
    
    <h4>����������� Sphinx �� ������ ���������</h4>
    <ol>
        <li>���������� ��� ����������� ��������� ������ <a href="https://sphinxsearch.com/" target="_blank">Sphinx</a>.</li>
        <li>� �������� ������� ����������������� ����� <kbd>sphinx.conf</kbd> ��� Sphinx ����������� ������ �� �������� <kbd>������������</kbd> � ���������� ������.<br>��������� <code>log</code>, <code>query_log</code>, <code>pid_file</code>, <code>path</code> ������� �� �������� � ������������ ������� �� ���. ������������ �������� ���� ���������� ������� �������� � ��������� ��������.</li>
    </ol> 
    
    <h4>����������� Sphinx �� �������� Beget � ������������� ��� �� ������ ���������</h4>
    <ol>
        <li><a href="https://beget.com/p566" target="_blank">������������������</a> �� �������� Beget.</li>
        <li>� ������ �������� �������� �������� Beget � ������� <a href="https://cp.beget.com/cloudservices" target="_blank">�������</a> ����������� ������ <kbd>Sphinx</kbd>.</li>
        <li><a href="https://cp.beget.com/cloudservices" target="_blank">���������� �� �������������</a> ������� Sphinx �� �������� Beget.</li>
        <li>�������� �� ����� �������� ������ � ���� ������ MySQL �� �������� IP-������</li>
        <li>� �������� ����������������� ����� <kbd>sphinx.conf</kbd> ��� Sphinx ����������� ������ �� �������� <kbd>������������</kbd> � ���������� ������. ������ ��������� <code>sql_host=' . $PHPShopBase->getParam("connect.host") . '</code> ������� ���� ���������� IP-����� ���� ������ MySQL <code>sql_host={IP_MYSQL}</code>.</li>
        <li>� ������ �������� �������� �������� Beget � ������� <a href="https://cp.beget.com/cloudservices/sphinx/searchd" target="_blank">������� - Sphinx - ��������� ������</a> ������������ ������� ������ � ������� IP-����� ������ �����.</li>    
    </ol>   

    <h4>��������� ������</h4>
    <ol>
        <li>������� ����� ���������� ������� Sphinx, �� ��������� <code>127.0.0.1</code>. ��� ��������� ������ ������� � Sphinx �� �������� IP-������ ����� Beget, ����� ����� ���� <code>sphinx.{LOGIN}.beget.hosting</code>.</li>
        <li>������� ���� ���������� ������� Sphinx, �� ��������� <code>9306</code>. ��� ��������� ������ ������� � Sphinx �� �������� IP-������ ����� Beget , ���� ����� <code>55408</code>.</li>
        <li>��� ��������� ���������� "����������� ���������� �������� � ����� ��� ����������" � "��������� �������� ��� ����� ���������� ������� ��" ������� �������� ���������������� ���� <kbd>sphinx.conf</kbd> ��� Sphinx �� ��������� ������� � ���������� ���������� ���������� ��������.</li>
        <li>��� ������� �������� �������� <a href="https://docs.phpshop.ru/nastroiky/yandex-cloud" target="_blank">YandexCloud</a> ����� �������� ����� ������ ������� � ������� ���� ������ �� ������� �� ���������� ������.</li>
    </ol>
    
    <h4>����� ������ ������</h4>
    <ol>  
      <li>������ ������������ ������������� ��� ������� �������� ����������� ���������.</li>
      <li>������ �������������� ������������� ��� ���������� �������� ����������� ���������.</li>
      <li>���� ��������� ������ ������ �������� � �������� <kbd>� ������</kbd> - <kbd>��������� ������</kbd>.</li>
      <li>����������� ��������� �� ������ ���������� ������� Sphinx � ��� ��������� �������������� ����������� ������� ��������.</li>
    </ol> 
';

    $Tab4 = $PHPShopGUI->setInfo($info);

    $Tab5 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(["��������", $Tab1, true],["������������", $Tab2], ["��������", $Tab3],  ["����������", $Tab4],  ["� ������", $Tab5]);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>