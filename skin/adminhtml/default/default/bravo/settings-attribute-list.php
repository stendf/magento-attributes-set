<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

chdir('../../../../../');

require_once 'app/Mage.php';
umask(0);
Mage::init();

$frontName = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
$murl_name = $frontName . "/bravoindex";
$fullModuleUrl = Mage::helper("adminhtml")->getUrl($murl_name);
$moduleurl = preg_replace("/key(.)*/", '', $fullModuleUrl);
$moduleurl = str_replace('/bravoindex/index/', '/bravoindex', $moduleurl);

$fullRemoveUrl = Mage::helper("adminhtml")->getUrl($moduleurl . 'removeattribute');
$removeUrl = preg_replace("/key(.)*/", '', $fullRemoveUrl);

$key = preg_replace("/(.)*key/", '', $fullRemoveUrl);

?>
var csrf_key  = '<?=$key?>';
var moduleUrl = '<?=$moduleurl?>';
var removeUrl = '<?=$removeUrl?>';