<?php
/**
 *
 */

/**
 * Description of IndexController
 *
 * @version $Id: IndexController.php 2 2010-08-25 16:12:54Z WalkerAlencar $
 */
class Tools_IndexController extends Fnde_Controller_Action {

    public function init(){
        $this->setTitle('Ferramentas');
    }

    public function indexAction() {
        $this->setSubtitle('Início');
        $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Área disponível apenas em ambiente de desenvolvimento.');
    }
}