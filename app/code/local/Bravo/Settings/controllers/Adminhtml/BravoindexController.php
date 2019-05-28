<?php

class Bravo_Settings_Adminhtml_BravoindexController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveattributeAction() {
        $err_fields = $this->validateSaveAttribute();

        if (count($err_fields)>0) {
            Mage::getSingleton('adminhtml/session')->addError('Uno o più campi mancanti: ' . implode(', ', $err_fields));
            $this->_redirect('*/*');
            return;
        }

        $setId          = $_POST['set_id'];
        $group_id       = $_POST['group_id'];
        $attribute_id   = $_POST['attribute_id'];

        try {
            $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
            $installer->startSetup();

            $entityTypeId   = $installer->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);

            $installer->addAttributeToGroup($entityTypeId, $setId, $group_id, $attribute_id);
            $installer->endSetup();
            
            Mage::getSingleton('adminhtml/session')->addSuccess('Aggiornamento riuscito');

        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError('Errore' . $ex->getMessage());
            $this->_redirect('*/*');
        }

        $this->_redirect('*/*', [
            'setid'=>$setId,
            'groupid'=>$group_id
            ]
        );
    }

    public function savelistAction() {
        $params     = Mage::app()->getRequest()->getParams();
        $setid      = Mage::app()->getRequest()->getParam('setid');
        $groupid    = Mage::app()->getRequest()->getParam('groupid');

        $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
        $installer->startSetup();

        if (isset($params['sort'])) {
            foreach ($params['sort'] as $attr_entity_id=>$attr) {
                    $attribute_id = $attr['attribute_id'];
                    $attribute = Mage::getModel('eav/entity_attribute')->load($attribute_id);
                    $code = $attribute->getData('attribute_code');

                    try {
                        $installer->updateTableRow('eav/entity_attribute',
                            'entity_attribute_id', $attr_entity_id,
                            'sort_order', $attr['position']
                        );
                    } catch (Exception $ex) {
                        Mage::getSingleton('adminhtml/session')->addError('Errore' . $ex->getMessage());
                        $this->_redirect('*/*');
                    }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess('Aggiornamento riuscito');
        }

        $installer->endSetup();

        $this->_redirect('*/*', [
            'setid'=>$setid,
            'groupid'=>$groupid
            ]
        );

    }

    public function removeAttributeAction() {
        $entity_id  = Mage::app()->getRequest()->getParam('remove_attribute_entity_id');
        $setid      = Mage::app()->getRequest()->getParam('setid');
        $groupid    = Mage::app()->getRequest()->getParam('groupid');

        if (is_null($entity_id) || $entity_id=='') {
            Mage::getSingleton('adminhtml/session')->addError("Errore rimozioni attributo");
            $this->_redirectReferer();
        }

        try {
            $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
            $installer->startSetup();
            $installer->deleteTableRow('eav/entity_attribute', 'entity_attribute_id', $entity_id);
            $installer->endSetup();
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError('Errore' . $ex->getMessage());
            $this->_redirect('*/*');
        }

        Mage::getSingleton('adminhtml/session')->addSuccess("Rimosso attributo $entity_id");

        $this->_redirect('*/*', [
            'setid'=>$setid,
            'groupid'=>$groupid
            ]
        );
    }

    
    public function ajaxGetAttributesAction()
    {
        echo json_encode([
            ['id'=>1]
        ]);
    }



    private function validateSaveAttribute() {
        $errs = [];

        $requiredFields = [
            'set_id',
            'group_id',
            'attribute_id'
        ];
        $translationFields = [
            'set_id' => 'set',
            'group_id' => 'gruppo',
            'attribute_id' => 'attributo'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field])=='') {
                $errs[] = $translationFields[$field];
            }
        }

        return $errs;
    }

}
