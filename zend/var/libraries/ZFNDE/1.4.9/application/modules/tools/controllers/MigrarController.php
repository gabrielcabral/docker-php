<?php
/**
 *
 */
class Tools_MigrarController extends Fnde_Controller_Action {

    public function init(){
        $this->setTitle('Ferramentas');
    }

    public function indexAction() {
        $this->setSubtitle('Migração de ACL');
        $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Área disponível apenas em ambiente de desenvolvimento.');

        $form = new Tools_Migrar_ConfirmarForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (!$form->isValid($formData)) {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
            }
        }

        $form->setAction($this->getUrl('tools', 'migrar', 'confirm'))
             ->setMethod('post');
        $this->view->form = $form;
    }


    public function confirmAction() {
        $this->setSubtitle('Migração de ACL');

        $options = Zend_Registry::get('config');
        $acl = new Fnde_Acl(new Zend_Config_Ini($options['security']['acl']['rules']), $options['security']['acl']['module_controller_separator'], $options['security']['acl']['privileges_separator']);
        $acl->migraAcl();
    }
}