<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir('../');

require_once 'app/Mage.php';
umask(0);

Mage::init();

$term = isset($_GET['term']) ? $_GET['term'] : null;

$attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('entity_type_id', 4);

$ret=[];

function transformData($data) {
    return [
        'id' => $data['attribute_id'],
        'label' => $data['attribute_code'],
        'value' => $data['attribute_code']
    ];
}

foreach ($attributes as $attribute) {
    // attribute_code   // frontend_label
    $data = $attribute->getData();

    $content_fields = [
        $data['attribute_code'],
        $data['frontend_label']
    ];

    $content = implode(' ', $content_fields);

    if ($term!=null) {
        if (strstr($content, $term)) {
            $ret[] = transformData($data);
        }
    } else {
        //$ret[] = $data;
        $ret[] = transformData($data);
    }

    //$ret[] = $attribute->getData();
}

echo json_encode($ret);