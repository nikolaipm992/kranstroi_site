<?

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.smspircompany.smspircompany_message"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules;
    $PHPShopModules->getUpdate();
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (!isset($_POST['cascade_enabled_new'])) {
        $_POST['cascade_enabled_new'] = 0;
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=smspircompany');
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;

    $PHPShopGUI->includeJava.='
    <style>
      .module_section .tab-pane > .btn {
        margin: 3px;
        color: #fff;
      }
      
      .module_section textarea {
        margin-top: 10px;
      }
      
    </style>
    <script type="text/javascript">

        $.fn.extend({
            insertAtCaret: function(myValue){
                return this.each(function(i) {
                    if (document.selection) {
                        // ��� ��������� ���� Internet Explorer
                        this.focus();
                        var sel = document.selection.createRange();
                        sel.text = myValue;
                        this.focus();
                    }
                    else if (this.selectionStart || this.selectionStart == "0") {
                        // ��� ��������� ���� Firefox � ������ Webkit-��
                        var startPos = this.selectionStart;
                        var endPos = this.selectionEnd;
                        var scrollTop = this.scrollTop;
                        this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                        this.focus();
                        this.selectionStart = startPos + myValue.length;
                        this.selectionEnd = startPos + myValue.length;
                        this.scrollTop = scrollTop;
                    } else {
                        this.value += myValue;
                        this.focus();
                    }
                })
            }
        });
        
        
        function snippetAdd(area,snippet){
          $("#"+area).insertAtCaret(snippet);
        }    

    </script>';

    // �������
    $data = $PHPShopOrm->select();
    extract($data);

    // ���������� �������� 1
    $Tab1 =$PHPShopGUI->setText("������ API (�� ��������� phpshop.pir.company): ", false)
        .$PHPShopGUI->setInput('text','domen_api_new',$domen_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("����� API:", false)
        .$PHPShopGUI->setInput('text','login_api_new',$login_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("������ API:", false)
        .$PHPShopGUI->setInput('password','password_api_new',$password_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("������� �������������� �������� ��� ��������� ����������� (����� ������� ��������� ���������, ���������� �� ����� �������):", false)
        .$PHPShopGUI->setInput('text','admin_phone_new',$admin_phone,false,'','','','','(� �������: 71234567890)');

    $Tab1.=$PHPShopGUI->setText("��� ����������� (�� ��������� PirCompany). ��� ������������� � ��� ����������� ����� ����������� ���������� ������������ � ����� ����������.:", false)
        .$PHPShopGUI->setInput('text','sender_new',$sender,false,'','','','','');

    $Tab1.=$PHPShopGUI->setLink("https://phpshop.pir.company/ru/reg.html", '������� �� ������ � ������������������', '_blank', 'margin:5px;display:inline-block;');

    $Tab1.=$PHPShopGUI->setLink("https://phpshop.pir.company/ru/cabinet.html", '������� �� ������ �  ��������������', '_blank', 'margin:5px;display:inline-block;');

    // ���������� �������� 3
    $Info = '<div>
        <div class="panel panel-default">
          <div class="panel-heading"><b>������</b></div>
          <div class="panel-body">
              <p>��� ������ ������ � ������������� ������� "PIR.Company: SMS-�����������" ���������� ������ <a href="https://phpshop.pir.company/ru/reg.html" target="_blank">�����������</a>
              �� ������� PIR.Company � ��������� ��������� ��������, ������� ���������� � ������������ �� ������� � 9.00 �� 18.00 ��
              ����������� �������.</p>
              <p>��������� ��� � ������ ����������� �������������� - 2,40 ���. ��������� ��� ��� ����� ����������� - 2,60 ���.</p>
              <p>��� ������������� � ��� ����������� ����� ����������� ���������� ������������ � ����� ���������� ������� PIR.Company.</p>
              <p>��� �������� SMS-����������� ������������ ������ <a href="http://pir.company" target="_blank">PIR.Company.</a></p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>������ "����� �����" ��� ��������������</b></div>
          <div class="panel-body">
              <p>������������� ��� �������������� �����. ������ ��������� ����� ��������� ����� ���������� ������ ������.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>������ "����� �����" ��� ���������</b></div>
          <div class="panel-body">
              <p>������������� ��� ����������� ��������� � ����������� ������.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>������ "��������� ������� ������"</b></div>
          <div class="panel-body">
              <p>������������� ��� ����������� ��������� �� ��������� ������� ������.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>������ � �������� ����� ��������� ����� - <button type="button" class="btn btn-sm btn-primary">������</button></b></div>
          <div class="panel-body">
              <p>������ ������ ��������� � ��������� ���� ����������� ���������� ��� ������������ ��������� ��������� ����� ��������� �����������. ����� �������� ���������� � ��������� ����, ������ ��������� ������ � ������ ����� ���� � �������� �� ������ � ������ ���������.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>��������� ��������</b></div>
          <div class="panel-body">
               <p>�������� ������:</p>
                <ol>
                    <li>����� �������� ����������� �� ������� ����������� Viber.</li>
                    <li>���� ���������� Viber �������, ������������ Viber ���������.</li>
                    <li>���� ���������� �� ���������, �������� ������������ sms-���������.</li>
                </ol>
                <p>����� Viber-��������� �� 1000 ��������.<br>���� ����������� ������������� �������� � ������ ����������.</p>
          </div>
        </div>
        <div class="well"><b>��������� Viber-��������� � ������ ����������� - 1,50 ���.<br>��������� ��� � ������ ����������� - 2,40 ���.</b></div>
      </div>
    ';
    
    $Tab3 = $PHPShopGUI->setInfo($Info, '', '95%');

    // ���������� �������� 4
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);
    
    // ���������� �������� 5
    $Tab5.= $PHPShopGUI->setInfo('<div><p>������ <a href="http://www.webvk.ru" target="_blank">WEBVK</a></p><p>E-mail: <a href="mailto:mail@webvk.ru">mail@webvk.ru</a></p></div>', '', '99%');

    
    // ���������� �������� 7
    $Tab7 = $PHPShopGUI->setInfo('
      <div>
      <p><b>������ ���������</b></p>

      <p>������ ������: � 10:00 �� 19:00 (���������, ����� ��������)</p>
      
      <p>���.: <a href="tel:8-800-550-17-89">8-800-550-13-86</a></p>
      <p><a href="mailto:support@pir.company">support@pir.company</a></p>
      </div>
    ', '', '95%');

    $TabCascade = $PHPShopGUI->setText("������ API ���������� ������ (�� ��������� phpshop.pir.company):", false)
        .$PHPShopGUI->setInput('text','cascade_domen_api_new',$cascade_domen_api,false,'','');

    $TabCascade .= $PHPShopGUI->setText("��� ����������� (�� ��������� media-gorod). ��� ������������� ����������� ����� ����������� ���������� ������������ � ����� ����������.:", false)
        .$PHPShopGUI->setInput('text','cascade_sender_new',$cascade_sender,false,'','','','','');

    $TabCascade .= $PHPShopGUI->setCheckbox('cascade_enabled_new', '1', '�������� ��������� �����', $cascade_enabled);

    $TabCascadeInfo = $PHPShopGUI->setPanel('<b>��������</b>', '
        <p>��������� ��������.</p>
        <p>�������� ������:</p>
        <ol>
            <li>����� �������� ����������� �� ������� ����������� Viber.</li>
            <li>���� ���������� Viber �������, ������������ Viber ���������.</li>
            <li>���� ���������� �� ���������, �������� ������������ sms-���������.</li>
        </ol>
        <p>����� Viber-��������� �� 1000 ��������.<br>���� ����������� ������������� �������� � ������ ����������.</p>
        <br>
        <p>��������� Viber-��������� � ������ ����������� - 1.50 ���.<br>��������� ��� � ������ ����������� - 2,40 ���.</p>
    ');

    $TabCascade .= $TabCascadeInfo;

    $bodyTabTemplateMessage  = '<ul class="nav nav-tabs">';
    $bodyTabTemplateMessage .=      '<li class="active"><a href="#sms-templates" data-toggle="tab">SMS</a></li>';
    $bodyTabTemplateMessage .=      '<li><a href="#viber-template" data-toggle="tab">VIBER</a></li>';
    $bodyTabTemplateMessage .= '</ul>';

    $bodyTabTemplateMessage .= '<div class="tab-content">';
    $bodyTabTemplateMessage .=      '<div class="tab-pane fade in active" id="sms-templates">';
    $bodyTabTemplateMessage .=          templates('sms', $order_template_admin_sms, $order_template_sms, $change_status_order_template_sms);
    $bodyTabTemplateMessage .=      '</div>';
    $bodyTabTemplateMessage .=      '<div class="tab-pane" id="viber-template">';
    $bodyTabTemplateMessage .=          templates('viber', $order_template_admin_viber, $order_template_viber, $change_status_order_template_viber);
    $bodyTabTemplateMessage .=      '</div>';
    $bodyTabTemplateMessage .= '</div>';

    $tabTemplateMessage = $PHPShopGUI->setPanel('<b>������� ���������</b>', $bodyTabTemplateMessage);
    
    // ����� ����� ��������
    $PHPShopGUI->setTab(
        array("��������", $Tab1, true),
        array("��������� �����", $TabCascade),
        array("������� ���������", '<div class="module_section">' . $tabTemplateMessage . '</div>', true),
        array("����������", $Tab3),
        array("� ������", $Tab4),
        array("�����������", $Tab5),
        array("���������", $Tab7)
    );

    // ����� ������ ��������� � ����� � �����
    $ContentFooter=
            $PHPShopGUI->setInput("hidden","newsID",$id,"right",70,"","but").
            $PHPShopGUI->setInput("submit","saveID","��","right",70,"","but","actionUpdate");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������ '����� �����' ��� ��������������
function templates($provider, $templateAdminOrder_value, $templateClientOrder_value, $templateClientOrderChangeStatus_value) {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();
    extract($data);

    $template = $PHPShopGUI->setText("<h3>������ '����� �����' ��� ��������������: </h3>",false)
        .$PHPShopGUI->setInput("button","","�������� ��������-��������","","","snippetAdd('order_template_admin_".$provider."_new','��������-������� - @NameShop@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������������� ������","","","snippetAdd('order_template_admin_".$provider."_new','�������� ����� ����� �@OrderNum@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","��� ���������","","","snippetAdd('order_template_admin_".$provider."_new','��� - @UserFio@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������� ���������","","","snippetAdd('order_template_admin_".$provider."_new','������� - @UserPhone@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","E-mail ���������","","","snippetAdd('order_template_admin_".$provider."_new','E-mail - @UserMail@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","��������","","","snippetAdd('order_template_admin_".$provider."_new','������ �������� - @UserDelivery@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������","","","snippetAdd('order_template_admin_".$provider."_new','������ - @UserCountry@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������","","","snippetAdd('order_template_admin_".$provider."_new','������ - @UserState@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","�����","","","snippetAdd('order_template_admin_".$provider."_new','����� - @UserCity@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","�������� ������","","","snippetAdd('order_template_admin_".$provider."_new','�������� ������ - @UserIndex@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","�����","","","snippetAdd('order_template_admin_".$provider."_new','����� - @UserStreet@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","���","","","snippetAdd('order_template_admin_".$provider."_new','��� - @UserHouse@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","�������","","","snippetAdd('order_template_admin_".$provider."_new','������� - @UserPorch@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","��� ��������","","","snippetAdd('order_template_admin_".$provider."_new','��� �������� - @UserDoorPhone@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","��������","","","snippetAdd('order_template_admin_".$provider."_new','�������� - @UserFlat@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","����� ��������","","","snippetAdd('order_template_admin_".$provider."_new','����� �������� - @UserDelivtime@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","���. ����","","","snippetAdd('order_template_admin_".$provider."_new','���. ���� - @UserDopInfo@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������ ������","","","snippetAdd('order_template_admin_".$provider."_new','������ ������:".'\r\n'."@Order@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","�������� ����� ����� ������","","","snippetAdd('order_template_admin_".$provider."_new','����� ����� ������: @CommonSumOrder@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('order_template_admin_'.$provider.'_new',$templateAdminOrder_value,false,'',500);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>����� ������. �� ����� 19 ��������. ��� ����������, ����������� ���� ���� ������� ������ ������ � ��������� "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_admin_viber_button_text_new',$order_template_admin_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ ������. ��� ����������, ����������� ���� ���� ������� �������� ������ � ��������� "����� ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_admin_viber_button_url_new',$order_template_admin_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ �� �����������. � ������ ���������� �����������, ����� ���������� ��������� ��������� "����� ������" � "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setFile($order_template_admin_viber_image_url, 'order_template_admin_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    // ���������� �������� 2
    $template .= $PHPShopGUI->setText("<h3>������ '����� �����' ��� ���������: </h3>",false)
        .$PHPShopGUI->setInput("button","","�������� ��������-��������","","","snippetAdd('order_template_".$provider."_new','��������-������� - @NameShop@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������������� ������","","","snippetAdd('order_template_".$provider."_new','��� ����� ������ - @OrderNum@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������ ������","","","snippetAdd('order_template_".$provider."_new','������ ������ ������ - @OrderStatus@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","������ ������","","","snippetAdd('order_template_".$provider."_new','������ ������:".'\r\n'."@Order@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('order_template_'.$provider.'_new',$templateClientOrder_value,false,'',150);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>����� ������. �� ����� 19 ��������. ��� ����������, ����������� ���� ���� ������� ������ ������ � ��������� "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_viber_button_text_new',$order_template_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ ������. ��� ����������, ����������� ���� ���� ������� �������� ������ � ��������� "����� ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_viber_button_url_new',$order_template_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ �� �����������. � ������ ���������� �����������, ����� ���������� ��������� ��������� "����� ������" � "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setFile($order_template_viber_image_url, 'order_template_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    $template .= $PHPShopGUI->setText("<h3>������ '��������� ������� ������' ��� ���������: </h3>",false)
        .$PHPShopGUI->setInput("button","","�������� ��������-��������","","","snippetAdd('change_status_order_template_".$provider."_new','��������-������� - @NameShop@')","btn-sm btn-primary","","","")
        .$PHPShopGUI->setInput("button","","������������� ������","","","snippetAdd('change_status_order_template_".$provider."_new','��� ����� ������ - @OrderNum@')","btn-sm btn-primary","","","")
        .$PHPShopGUI->setInput("button","","������ ������","","","snippetAdd('change_status_order_template_".$provider."_new','������ ������ ������ ������� �� - @OrderStatus@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('change_status_order_template_'.$provider.'_new',$templateClientOrderChangeStatus_value,false,'',150);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>����� ������. �� ����� 19 ��������. ��� ����������, ����������� ���� ���� ������� ������ ������ � ��������� "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','change_status_order_template_viber_button_text_new',$change_status_order_template_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ ������. ��� ����������, ����������� ���� ���� ������� �������� ������ � ��������� "����� ������"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','change_status_order_template_viber_button_url_new',$change_status_order_template_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>������ �� �����������. � ������ ���������� �����������, ����� ���������� ��������� ��������� "����� ������" � "������ ������"</h5>',false);
        $template .= $PHPShopGUI->setFile($change_status_order_template_viber_image_url, 'change_status_order_template_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    return $template;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'],'actionStart');


?>


