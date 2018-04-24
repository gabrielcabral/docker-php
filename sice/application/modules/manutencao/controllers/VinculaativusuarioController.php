<?php

/**
 * Controller do QuantidadeTurma
 * 
 * @author diego.matos
 * @since 30/03/2012
 */

class Manutencao_VinculaAtivUsuarioController extends Fnde_Sice_Controller_Action {

	protected $_stSistema = 'sice';

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function formAction() {

		$this->_helper->layout()->disableLayout();

		
                $arParam = null;
                
                if(isset($_SESSION['NU_SEQ_ATIVIDADE']) && count($_SESSION['NU_SEQ_ATIVIDADE'])) {
                    $arParam = $this->_getAllParams();
                    $arParam['NU_SEQ_ATIVIDADE'] = $_SESSION['NU_SEQ_ATIVIDADE'];
                    $arParam['DS_ATIVIDADE_ALTERNATIVA'] = $_SESSION['DS_ATIVIDADE_ALTERNATIVA'];
                }

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arParam, $arExtra);
		$form->setName("principal");

		$formLimpo = $this->getForm(null, $arExtra, true);

		$formLimpo->removeElement("adicionar");
		$formLimpo->removeElement("salvar");
		$formLimpo->removeDecorator("dadosAtividades");
		$formLimpo->setName("auxiliar");

		$this->view->form = $form;
		$this->view->formLimpo = $formLimpo;

		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array(), $formSecundario = false ) {
		$form = new VinculaAtivUsuario_Form($arDados, $arExtra, $formSecundario);
		$this->setAtividade($form, $arDados);
		$form->setDecorators(array('FormElements', 'Form'));

		return $form;
	}

	/**
	 * 
	 * @param form $form
	 * @param array $arDados
	 */
	private function setAtividade( $form, $arDados ) {
		$business = new Fnde_Sice_Business_Componentes();
		$arAtividade = $business->getAllByTable("Atividade", array("NU_SEQ_ATIVIDADE", "DS_ATIVIDADE"));

		if ( is_array($arDados['NU_SEQ_ATIVIDADE']) ) {
			for ( $i = 0; $i < count($arDados['NU_SEQ_ATIVIDADE']); $i++ ) {
				$form->setAtividade($arAtividade, 'NU_SEQ_ATIVIDADE' . $i);
			}

		} else {
			$form->setAtividade($arAtividade, 'NU_SEQ_ATIVIDADE');
		}
                
	}

}
