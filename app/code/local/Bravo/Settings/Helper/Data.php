<?php

class Bravo_Settings_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isMobile()
    {
        return Zend_Http_UserAgent_Mobile::match(
            Mage::helper('core/http')->getHttpUserAgent(),
            $_SERVER
        );
    }
}