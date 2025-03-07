<?php

// ��������� ������
PHPShopObj::loadClass("array");

class PHPShopAlfaCreditArray extends PHPShopArray {
    var $xml = null;
    var $statuses = array (
        'appNew' => '����� ������',
        'appDraft' => '�������� ������. ������ ������� ���������� ������, ������ �������.',
        'appCallCenter' => '�������� ������. ������� ������, � �������� �������� ��������� ����� ��� ������������.',
        'appPrelimAccept' => '������ �������������� ��������.',
        'appDecline' => '������ �������� ���������.',
        'appSmsSignFail' => '���-������. ��������� ���������� ��������� ���������� �� ��� (������ ���� �������� ��� ���������� ��������� ���������� �� ���).',
        'appContractSignedBySms' => '���-������. ��������� ��������� ���������. ������� ������������.',
        'appContractRejectedByClient' => '���-������. ������ ��������� �� ���������� ��������� ����������.',
        'appFinalComplete' => '�������������. �������� �������� ����������� � ��������-�������.',
        'appOperatorReject' => '������ �������� �������������� ��������� ��� ���������� �� ����� ����������.',
        'appComplete' => '������������� �������. ��������� ��������� ��������� ��������.',
        'appOverdue' => '������ ����������. ����� ���� ����� ������.',
        'appTechAutomaticProcess' => '�������������� �������� � ���� ����� ������ �� �����.',
        'appScoring' => '�������� ���� ������ ������ � ��������������. ������ ���������� �� ��������������� �������.',
        'appSmsCreditEnabled' => '���-������. ���-������ �������� ��� ������� �� ������ ������. ������ �����������.',      
    );
    
    function __construct($construct = true) {
        if ($construct) {
            $this->objType = 3;
            $this->objBase = $GLOBALS['SysValue']['base']['alfacredit']['alfacredit_system'];
            parent::__construct('inn', 'category_name', 'action_name', 'min_sum_cre', 'cre_name', 'min_sum_ras', 'ras_name', 'prod_mode');
        }
    }
    
    function log($reference, $cart) {
        foreach ($cart as $k => $c) {
            $cart[$k]['name'] = iconv("utf-8", "windows-1251", $c['name']);
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['alfacredit']['alfacredit_log']);
        $PHPShopOrm->debug = false;
        $_insert = array(
            'reference_new' => $reference,
            'date_new' => time(), 
            'status_new' => serialize(array('currentStatus' => 'appNew', 'currentStatusDescription' => '����� ������')),
            'cart_new' => serialize($cart)
        );
        $PHPShopOrm->insert($_insert);
    }
    
    function get_type($sum) {
        $min_sum_cre = $this->getParam('min_sum_cre');
        $min_sum_ras = $this->getParam('min_sum_ras');
        
        $type = '';
        if ( is_numeric($min_sum_cre) || is_numeric($min_sum_ras) ) {
            $min_sum_cre = intval($min_sum_cre);
            $min_sum_ras = intval($min_sum_ras);
            
            if ($sum > $min_sum_cre && $min_sum_cre > 0) 
                $type = 'cre';

            if ($sum >= $min_sum_ras && $min_sum_ras > 0)
                $type = 'ras';
        }
        
        return $type;        
    }
    
    function get_xml($reference, $goods) {
        if (is_array($goods)) {
            $this->xml = '<inParams>
<companyInfo>
<inn>' . $this->getParam('inn') . '</inn>
</companyInfo>
<creditInfo>
<reference>' . $reference . '</reference>
</creditInfo>';

            if (isset($_SESSION['UsersMail']) && isset($_SESSION['UsersName'])) {
            $this->xml .= '
<clientInfo>
<firstname>' . iconv("windows-1251", "utf-8", htmlspecialchars($_SESSION['UsersName'], ENT_COMPAT, 'cp1251', true)) . '</firstname>
<email>' . $_SESSION['UsersMail'] . '</email>
</clientInfo>';
            }

            $this->xml .= '
<specificationList>';
            
            foreach ($goods as $good) {
                $this->xml .= '
<specificationListRow>
<category>' . $this->getParam('category_name') . '</category>
<code>' . $good['id'] . '</code>
<description>' . $good['name'] . '</description>
<amount>' . $good['num'] . '</amount>
<price>' . $good['price'] . '</price>';
            
                if ($this->getParam('action_name'))
                    $this->xml .= '
<action>' . $this->getParam('action_name') . '</action>';
                
                $this->xml .= '
<image>http://' . $good['pic_small'] . '</image>
</specificationListRow>';
            }
        $this->xml .= '
</specificationList>
</inParams>
';        
        }
        return $this->xml;
    }
}

?>