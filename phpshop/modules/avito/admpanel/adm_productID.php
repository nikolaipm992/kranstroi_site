<?php

include_once dirname(__FILE__) . '/../class/Avito.php';

function addAvitoProductTab($data) {
    global $PHPShopGUI;

    // ������ �������� ����
    $PHPShopGUI->field_col = 5;

    // �������� �� ���������

    $PHPShopCategory = new PHPShopCategory((int) $data['category']);
    if ($PHPShopCategory) {
        //$data['export_avito'] = $PHPShopCategory->getParam('export_cat_avito');
        //$data['condition_avito'] = $PHPShopCategory->getParam('condition_cat_avito');
        $type_avito = $PHPShopCategory->getParam('type_avito');
        $subtype_avito = $PHPShopCategory->getParam('subtype_avito');
    }

        // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab = $PHPShopGUI->setField('������� � �����', $PHPShopGUI->setCheckbox('export_avito_new', 1, '', $data['export_avito']));
    $tab .= $PHPShopGUI->setField('���� �����', $PHPShopGUI->setInputText(null, 'price_avito_new', $data['price_avito'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField("�������� ������:", $PHPShopGUI->setInput('text', 'name_avito_new', $data['name_avito']));
    $tab .= $PHPShopGUI->setField("����� ID:", $PHPShopGUI->setInput('text', 'export_avito_id_new', $data['export_avito_id']));
    $tab .= $PHPShopGUI->setField('��������� ������', $PHPShopGUI->setSelect('condition_avito_new', Avito::getConditions($data['condition_avito'])), 1, '��� <condition>');
    $tab .= $PHPShopGUI->setField('������� �������� ����������', $PHPShopGUI->setSelect('listing_fee_avito_new', Avito::getListingFee($data['listing_fee_avito'])), 1, '��� <ListingFee>');
    $tab .= $PHPShopGUI->setField('������� ������', $PHPShopGUI->setSelect('ad_status_avito_new', Avito::getAdStatuses($data['ad_status_avito'])), 1, '��� <AdStatus>');
    $tab .= $PHPShopGUI->setField('��� ����������', $PHPShopGUI->setSelect('ad_type_avito_new', Avito::getAdTypes($data['ad_type_avito'])), 1, '��� <AdType>');

    // �������� � ����������
    if ($type_avito <= 200 and $type_avito >= 145) {
        $tab .= $PHPShopGUI->setField("����� ������ OEM:", $PHPShopGUI->setInput('text', 'oem_avito_new', $data['oem_avito']), 1, '��� �����-����� "�������� � ����������"');
    }

    // �������������
    if ($subtype_avito == 14) {
        $tiers = unserialize($data['tiers_avito']);
        if (!is_array($tiers)) {
            $tiers = [];
        }
        $tab .= $PHPShopGUI->setField("�������������", $PHPShopGUI->setInput('text', 'tiers[brand]', isset($tiers['brand']) ? $tiers['brand'] : null));
        $tab .= $PHPShopGUI->setField("����������", $PHPShopGUI->setSelect('tiers[Size]', Avito::SpareAudioSize(isset($tiers['Size']) ? $tiers['Size'] : null)));
        $tab .= $PHPShopGUI->setField("������������ ������� Android", $PHPShopGUI->setSelect('tiers[AndroidOS]', Avito::SpareAudioAndroidOS(isset($tiers['AndroidOS']) ? $tiers['AndroidOS'] : null)));
        $tab .= $PHPShopGUI->setField("����������� ������, ��", $PHPShopGUI->setSelect('tiers[RAM]', Avito::SpareAudioRAM(isset($tiers['RAM']) ? $tiers['RAM'] : null)));
        $tab .= $PHPShopGUI->setField("���������� ������, ��", $PHPShopGUI->setSelect('tiers[ROM]', Avito::SpareAudioROM(isset($tiers['ROM']) ? $tiers['ROM'] : null)));
        $tab .= $PHPShopGUI->setField("���� ���������", $PHPShopGUI->setSelect('tiers[CPU]', Avito::SpareAudioCPU(isset($tiers['CPU']) ? $tiers['CPU'] : null)));
    }

    // ������������
    if ($subtype_avito == 15) {
        $tiers = unserialize($data['tiers_avito']);
        if (!is_array($tiers)) {
            $tiers = [];
        }
        $tab .= $PHPShopGUI->setField("�������������", $PHPShopGUI->setInput('text', 'tiers[brand]', isset($tiers['brand']) ? $tiers['brand'] : null));
        $tab .= $PHPShopGUI->setField("����������", $PHPShopGUI->setSelect('tiers[Size]', Avito::SpareAudioSizeAkust(isset($tiers['Size']) ? $tiers['Size'] : null)));
        $tab .= $PHPShopGUI->setField("��� ������������", $PHPShopGUI->setSelect('tiers[AudioType]', Avito::SpareAudioAudioType(isset($tiers['AudioType']) ? $tiers['AudioType'] : null)));
        $tab .= $PHPShopGUI->setField("���������� �����", $PHPShopGUI->setSelect('tiers[VoiceCoil]', Avito::SpareAudioVoiceCoil(isset($tiers['VoiceCoil']) ? $tiers['VoiceCoil'] : null)));
        $tab .= $PHPShopGUI->setField("����������� ��������, ��", $PHPShopGUI->setInput('text', 'tiers[RMS]', isset($tiers['RMS']) ? $tiers['RMS'] : null, false, 100));
        $tab .= $PHPShopGUI->setField("��������, ��", $PHPShopGUI->setSelect('tiers[Impedance]', Avito::SpareAudioImpedance(isset($tiers['Impedance']) ? $tiers['Impedance'] : null)));
    }

    // �����������������
    if ($subtype_avito == 16) {
        $tiers = unserialize($data['tiers_avito']);
        if (!is_array($tiers)) {
            $tiers = [];
        }
        $tab .= $PHPShopGUI->setField("�������������", $PHPShopGUI->setInput('text', 'tiers[brand]', isset($tiers['brand']) ? $tiers['brand'] : null));
        $tab .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setSelect('tiers[Design]', Avito::SpareAudioDesign(isset($tiers['Design']) ? $tiers['Design'] : null)));
        $tab .= $PHPShopGUI->setField("���������� �����", $PHPShopGUI->setSelect('tiers[CamsNumber]', Avito::SpareAudioCamsNumber(isset($tiers['CamsNumber']) ? $tiers['CamsNumber'] : null)));
        $tab .= $PHPShopGUI->setField("������������ ���������� �����������", $PHPShopGUI->setSelect('tiers[Resolution]', Avito::SpareAudioResolution(isset($tiers['Resolution']) ? $tiers['Resolution'] : null)));
    }

    // ���������
    if ($subtype_avito == 17) {
        $tiers = unserialize($data['tiers_avito']);
        if (!is_array($tiers)) {
            $tiers = [];
        }
        $tab .= $PHPShopGUI->setField("�������������", $PHPShopGUI->setInput('text', 'tiers[brand]', isset($tiers['brand']) ? $tiers['brand'] : null));
        $tab .= $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setSelect('tiers[AmplifierType]', Avito::SpareAudioAmplifierType(isset($tiers['AmplifierType']) ? $tiers['AmplifierType'] : null)));
        $tab .= $PHPShopGUI->setField("���������� �������", $PHPShopGUI->setSelect('tiers[ChannelsNumber]', Avito::SpareAudioChannelsNumber(isset($tiers['ChannelsNumber']) ? $tiers['ChannelsNumber'] : null)));
        $tab .= $PHPShopGUI->setField("����������� �������� �� ����� (4 ��), ��", $PHPShopGUI->setInput('text', 'tiers[RMSfour]', isset($tiers['RMSfour']) ? $tiers['RMSfour'] : null, false, 100));
        $tab .= $PHPShopGUI->setField("����������� �������� �� ����� (2 ��), ��", $PHPShopGUI->setInput('text', 'tiers[RMStwo]', isset($tiers['RMStwo']) ? $tiers['RMStwo'] : null, false, 100));
    }

    // ����, ����� � �����
    if ($type_avito <= 216 and $type_avito >= 212) {
        $tiers = unserialize($data['tiers_avito']);
        if (!is_array($tiers)) {
            $tiers = [];
        }

        $tab .= $PHPShopGUI->setField("�������������:", $PHPShopGUI->setInput('text', 'tiers[brand]', isset($tiers['brand']) ? $tiers['brand'] : null)) .
                $PHPShopGUI->setField("������� �����:", $PHPShopGUI->setInput('text', 'tiers[diameter]', isset($tiers['diameter']) ? $tiers['diameter'] : null)) .
                $PHPShopGUI->setField("������ �����, ������:", $PHPShopGUI->setInput('text', 'tiers[rim-width]', isset($tiers['rim-width']) ? $tiers['rim-width'] : null)) .
                $PHPShopGUI->setField("���������� ��������� ��� �����:", $PHPShopGUI->setInput('text', 'tiers[rim-bolts]', isset($tiers['rim-bolts']) ? $tiers['rim-bolts'] : null)) .
                $PHPShopGUI->setField("������� ������������ ��������� ��� �����:", $PHPShopGUI->setInput('text', 'tiers[rim-bolts-diameter]', isset($tiers['rim-bolts-diameter']) ? $tiers['rim-bolts-diameter'] : null)) .
                $PHPShopGUI->setField("����� (ET):", $PHPShopGUI->setInput('text', 'tiers[rim-offset]', isset($tiers['rim-offset']) ? $tiers['rim-offset'] : null)) .
                $PHPShopGUI->setField("���������� ��� ��� �����:", $PHPShopGUI->setSelect('tiers[tier-type]', Avito::getTierTypes(isset($tiers['tier-type']) ? $tiers['tier-type'] : null))) .
                $PHPShopGUI->setField("��� ��������:", $PHPShopGUI->setSelect('tiers[wheel-axle]', Avito::getWheelAxle(isset($tiers['wheel-axle']) ? $tiers['wheel-axle'] : null))) .
                $PHPShopGUI->setField("��� �����:", $PHPShopGUI->setSelect('tiers[rim-type]', Avito::getRimTypes(isset($tiers['rim-type']) ? $tiers['rim-type'] : null))) .
                $PHPShopGUI->setField("������ ������� ����:", $PHPShopGUI->setSelect('tiers[tire-section-width]', Avito::getTireSectionWidth(isset($tiers['tire-section-width']) ? $tiers['tire-section-width'] : null))) .
                $PHPShopGUI->setField("������ ������� ����:", $PHPShopGUI->setSelect('tiers[tire-aspect-ratio]', Avito::getTireAspectRatio(isset($tiers['tire-aspect-ratio']) ? $tiers['tire-aspect-ratio'] : null))
        );
    }

    $building = unserialize($data['building_avito']);
    if (!is_array($building)) {
        $building = [];
    }

    // �������������� - �������� ���������
    if ($subtype_avito == 4) {

        $tab .= $PHPShopGUI->setField("��� ������", $PHPShopGUI->setSelect('building[SheetMaterialsSubType]', Avito::SheetMaterialsType(isset($building['SheetMaterialsSubType']) ? $building['SheetMaterialsSubType'] : null)));
        $tab .= $PHPShopGUI->setField("��������", $PHPShopGUI->setSelect('building[SheetMaterialsType]', Avito::SheetMaterialsSubType(isset($building['SheetMaterialsType']) ? $building['SheetMaterialsType'] : null)));
    }

    // �������������� - ������������� ����
    elseif ($subtype_avito == 10) {

        $tab .= $PHPShopGUI->setField("��� ������", $PHPShopGUI->setSelect('building[Walltype]', Avito::Walltype(isset($building['Walltype']) ? $building['Walltype'] : null)));

        // ����� ��� �������������
        if ($building['Walltype'] == '����� ��� �������������') {

            $tab .= $PHPShopGUI->setField("��� ������������� �����", $PHPShopGUI->setSelect('building[ConstructionBlocksType]', Avito::ConstructionBlocksType(isset($building['ConstructionBlocksType']) ? $building['ConstructionBlocksType'] : null)));

            $tab .= $PHPShopGUI->setField("������ ��������������� �����", $PHPShopGUI->setSelect('building[Size]', Avito::SizeGazosilikat(isset($building['Size']) ? $building['Size'] : null)));
            $tab .= $PHPShopGUI->setField("������������� ������������", $PHPShopGUI->setSelect('building[Brand]', Avito::BrandGazosilikat(isset($building['Size']) ? $building['Brand'] : null)));
        }
        // ������
        elseif ($building['Walltype'] == '������') {

            $tab .= $PHPShopGUI->setField("��� �������", $PHPShopGUI->setSelect('building[TypeBrick]', Avito::TypeBrick(isset($building['TypeBrick']) ? $building['TypeBrick'] : null)));

            $tab .= $PHPShopGUI->setField("���������� �������", $PHPShopGUI->setSelect('building[PurposeBrick]', Avito::PurposeBrick(isset($building['PurposeBrick']) ? $building['PurposeBrick'] : null)));

            $tab .= $PHPShopGUI->setField("����", $PHPShopGUI->setSelect('building[Color]', Avito::BrickColor(isset($building['Color']) ? $building['Color'] : null)));
            $tab .= $PHPShopGUI->setField("������", $PHPShopGUI->setSelect('building[Size]', Avito::BrickSize(isset($building['Size']) ? $building['Size'] : null)));
            $tab .= $PHPShopGUI->setField("����������� ������", $PHPShopGUI->setSelect('building[HollownessBrick]', Avito::HollownessBrick(isset($building['HollownessBrick']) ? $building['HollownessBrick'] : null)));
        }
    }
    // �������������� - ������������ �����
    elseif ($subtype_avito == 9) {
        $tab .= $PHPShopGUI->setField("��� �����", $PHPShopGUI->setSelect('building[MixesType]', Avito::MixesType(isset($building['MixesType']) ? $building['MixesType'] : null)));
        $tab .= $PHPShopGUI->setField("�����", $PHPShopGUI->setSelect('building[ConcreteGrade]', Avito::ConcreteGrade(isset($building['ConcreteGrade']) ? $building['ConcreteGrade'] : null)));
        $tab .= $PHPShopGUI->setField("��� ���������", $PHPShopGUI->setSelect('building[ProductKind]', Avito::ProductKind(isset($building['ProductKind']) ? $building['ProductKind'] : null)));
    }

    $PHPShopGUI->addTab(array("�����", $tab, true));
}

function avitoUpdate() {

    if (is_array($_POST['tiers']))
        $_POST['tiers_avito_new'] = serialize($_POST['tiers']);

    if (is_array($_POST['building']))
        $_POST['building_avito_new'] = serialize($_POST['building']);

    if (empty($_POST['export_avito_new']) and ! isset($_REQUEST['ajax'])) {
        $_POST['export_avito_new'] = 0;
    }
}

function avitoSave() {
    global $PHPShopOrm;

    // ���������� ��� � ��������
    include_once dirname(__FILE__) . '/../class/Avito.php';
    $Avito = new Avito();
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    $products = $PHPShopOrm->getOne(['*'], ['export_avito' => "='1'", 'id' => '=' . $_POST['rowID']]);
    if (is_array($products) and count($products) > 0) {
        $Avito->updateStocks([$products]);
        $Avito->updatePrices($products);
    }
}

$addHandler = array(
    'actionStart' => 'addAvitoProductTab',
    'actionDelete' => false,
    'actionSave' => 'avitoSave',
    'actionUpdate' => 'avitoUpdate'
);
