<?php

/**
 * ������ ������ � ������
 * @param array $row ������ ������
 * @return string 
 */
function tab_files($row) {
    global $PHPShopInterface;
    $files = unserialize($row['files']);
    $PHPShopInterface->checkbox_action = false;

    if (is_array($files))
        $count = count($files);
    else
        $count = 0;

    $PHPShopInterface->setCaption(array("", "50%"), array("", "1%"), array('<button data-count="' . $count . '" class="btn btn-default btn-sm file-add"><span class="glyphicon glyphicon-plus"></span> ' . __('���������� ����') . '</button>', "50%", array('align' => 'right', 'locale' => false)));

    $key = 0;
    if (is_array($files))
        foreach ($files as $file) {

            $path_parts = pathinfo($file['path']);

            if (empty($file['name']))
                $file['name'] = $path_parts['basename'];

            $PHPShopInterface->setRow(array('name' => urldecode($file['name']), 'link' => $file['path'], 'align' => 'left', 'class' => 'file-edit'), $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'files_new[' . $key . '][path]', 'value' => $file['path'])) . $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'files_new[' . $key . '][name]', 'value' => $file['name'])), array('name' => $path_parts['basename'], 'link' => $file['path'], 'target' => '_blank', 'align' => 'right', 'class' => 'file-edit-path'));
            $key++;
        }



    $disp = '<table class="table table-hover file-list">' . $PHPShopInterface->getContent() . '</table>';

    return $disp;
}

?>