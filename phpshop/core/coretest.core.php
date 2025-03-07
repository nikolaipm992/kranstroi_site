<?php
/**
 * ���������� �������� ��������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopTest
 */
class PHPShopCoretest extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * ����� �� ���������
     */
    function index() {

        $disp='
<h1>����������� PHP ������ ����� PHPShop Core</h1>
<p>
�������� ����� ����� ���������� �� ������: phpshop/core/coretest.php<br>
��� �����������  HTML ������ ����������� ����� � ����� /pageHTML/
</p>

<h1>��� ������ �����: "'.$this->PHPShopSystem->getValue('name').'"</h1>
�������� ������ CoreTest:

<ul>
<li> C������ ���� � �������� ������
<p>
C������ ���� � �������� ������ � ����� phpshop/core/, ���������� ������������� ����, ��������, ���� ���� ����������
<b>coretest.class.php</b> � �������������� ��� ������ ������
http://'.$_SERVER['SERVER_NAME'].'/coretest/
 </p>

<li>������� ����� ��������� �������<br>
<p>
��� ������ ������ ��������� ������������� ���� � ��������� �
������ �����, ��������, ���� ����� ���������� <b>PHPShopCoretest</b>


<pre>
class PHPShopCoretest extends PHPShopCore {

    function __construct() {
        parent::__construct();
    }

function index() {

 // ����
 $this->title="����������� PHP ������ ����� API - ".$this->PHPShopSystem->getValue("name");
 $this->description=\'����������� PHP ������\';
 $this->keywords=\'php\';

 // ���������� ����������
 $this->set(\'pageContent\',\'PHPShop Core ��������!\');
 $this->set(\'pageTitle\', \'����������� PHP ������ ����� API\');

  // ���������� ������
  $this->parseTemplate($this->getValue(\'templates. page_page_list\'));
    }
}
</pre>
   <li>� ����� �������� ����� ��������� "PHPShop Core �������!" � ����� ������� �����.
</ul>
</p>
';

        // ����
        $this->title='����������� PHP ������ ����� API - '.$this->PHPShopSystem->getValue("name");
        $this->description='����������� PHP ������';
        $this->keywords='php';

        // ���������� ���������
        $this->set('pageContent',$disp);
        $this->set('pageTitle','����������� PHP ������ ����� API');


        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_page_list'));

    }
}

?>
