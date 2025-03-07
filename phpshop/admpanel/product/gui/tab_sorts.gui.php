<?php

/**
 * ������ ������ �������������
 * @param int $value �������� ��������������
 * @param int $n �� ��������������
 * @param int $title ��������� ��������������
 * @param array $vendor ������ �������������
 */
function sorttemplate($value, $n, $title, $vendor, $help) {
    global $PHPShopGUI;
    $i = 1;
    
    

    if (is_array($value)) {
        sort($value);
        foreach ($value as $p) {
            $sel = null;
            if (is_array($vendor[$n])) {
                foreach ($vendor[$n] as $value) {

                    if ($value == $p[1])
                        $sel = "selected";
                }
            }elseif ($vendor[$n] == $p[1])
                $sel = "selected";

            $value_new[$i] = array($p[0], $p[1], $sel);
            $i++;
        }
    }

    // ��������
    if (!empty($help))
        $help = '<div class="text-muted">' . $help . ':</div>';

    $value = $PHPShopGUI->setSelect('vendor_array_new[' . $n . '][]', $value_new, 500, null, false, $search = true, false, $size = 1, $multiple = true);

    $disp = $PHPShopGUI->setField('<a href="?path=sort&id=' . $n . '" target="_blank">' . $title . '</a>'.$help, $value, 1, null, null, 'control-label', false) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setInputArg(array('type' => 'text', 'placeholder' => '������ ������ ����� ����������� #', 'size' => '500', 'name' => 'vendor_array_add[' . $n . ']', 'class' => 'vendor_add')));

    return $disp;
}

/**
 * ������ ������������� ������
 * @param array $data ������ ������
 * @return string 
 */
function tab_sorts($data) {
    global $PHPShopGUI;
    PHPShopObj::loadClass("sort");
    $PHPShopSort = new PHPShopSort($data['category'], false, false, 'sorttemplate', unserialize($data['vendor_array']), false, true, false,null,true);

    $sort = $PHPShopSort->disp;

    if (empty($sort))
        $sort =
                '<p class="text-muted">' . __('��� ����������� ������������� � ������� ���������� ���������� <a href="?path=sort" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-share-alt"></span> �������������� � ������</a> � ������� ��� ������ � <a href="?path=catalog&id=' . intval($data['category']) . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-share-alt"></span> ��������� �������</a>. �������������� �� ��������� ����� �������� � ������� ��������� ���������') . '.</p>';


    return $PHPShopGUI->setCollapse('��������������', $sort, $collapse = 'none', true, false);
}

?>
