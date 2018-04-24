<?php

/**
 * Form de cadastro ParametrizarDocumentos
 * 
 * @author rafael.paiva
 * @since 12/04/2013~
 */
class ParametrizarDocumentos_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Emitir Certificado.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author rafael.paiva
	 * @since 12/04/2013
	 */
	public function __construct( $arDados, $arExtra ) {
		$this->setAttrib('class', 'labelLongo');
		//Criando os elementos.
		$nome = $this->createElement('text', 'NO_SECRETARIO',
				array("label" => "Nome do Secretário: ", "maxlength" => "100", "size" => "50"))->setRequired(true);
						
		$cargo = $this->createElement('text', 'NO_CARGO',
				array("label" => "Cargo: ", "maxlength" => "100", "size" => "50"))->setRequired(true);

		$local = $this->createElement('text', 'NO_LOCAL_ATUACAO',
				array("label" => "Local de Atuação: ", "maxlength" => "100", "size" => "50"))->setRequired(true);

		$ds_login = $this->createElement('text', 'DS_LOGIN',
				array("label" => "Login no SEGWEB: ", "maxlength" => "100", "size" => "50"))->setRequired(true);

		
		$this->addElements(
				array($nome, $cargo,$local, $ds_login));
		
		$this->addDisplayGroup(
				array('NO_SECRETARIO', 'NO_CARGO', 'NO_LOCAL_ATUACAO', 'DS_LOGIN'), 'filtroUsuario', array("legend" => ""));
		
		
		//Adicionando os elementos ao formulário.
		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));
                
////		$btConfirmar = $this->createElement('button', 'confirmar',
////				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar", 
//                                    "data-href" => $this->getView()->baseUrl() . "/index.php/secretaria/parametrizardocumentos/form"));

//		$btCancelar = $this->createElement('button', 'cancelar',
//				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
//						"onclick" => "window.location='" . $this->getView()->baseUrl()
//								. "/index.php/secretaria/parametrizardocumentos/form"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}
	
	/**
	 * Seta os valores do formulário
	 * @param unknown_type $secretario
	 * @param unknown_type $cargo
	 * @param unknown_type $local
	 * @param unknown_type $ds_login
	 */
	public function setParametros($secretario, $cargo, $local, $ds_login){
		$this->getElement("NO_SECRETARIO")->setValue($secretario);
		$this->getElement("NO_CARGO")->setValue($cargo);
		$this->getElement("NO_LOCAL_ATUACAO")->setValue($local);
		$this->getElement("DS_LOGIN")->setValue($ds_login);
	}

}
