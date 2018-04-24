<?php

class seguranca_PerfilController extends Fnde_Sice_Controller_Action {

	public function init() {
		parent::init();
	}

	public function indexAction() {

	}

	public function listarAction() {
		//$this->getName();
	}

	public function formularioAction() {

	}

	public function gravarAction() {
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}
		$form = $this->getForm();
		if ( !$form->isValid($_POST) ) {
			$this->view->form = $form;
			return $this->render('form');
		}
	}
}

