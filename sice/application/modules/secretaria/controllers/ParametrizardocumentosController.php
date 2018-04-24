<?php

/**
 * Controller do Parametrizar Documentos
 *
 * @author rafael.paiva
 * @since 12/04/2013
 */

class Secretaria_ParametrizarDocumentosController extends Fnde_Sice_Controller_Action {
	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 12/04/2013
	 */
	public function formAction() {
		try {
			if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
				$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
				$this->_redirect('index');
			}

			$this->setTitle('Parametrizar Emissão de Documentos');
			$this->setSubtitle('Parametrizar');

			//Recupera o objeto de formulário para validação
			$form = $this->getForm();

			if ( $this->getRequest()->isPost() ) {
                            if($this->getRequest()->getParam('presalvar'))
                                $this->preSalvarAction();
                            else
                                $this->salvarAction();
			}
                        
			$this->view->form = $form;
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}
	}

	/**
	 * Retorna os dados do formulario
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 11/09/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$form = new ParametrizarDocumentos_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/parametrizardocumentos/form?presalvar=true')->setMethod('post');
             
		$this->setParametros($form);
		return $form;
	}
        
        
	public function preSalvarAction(){
		
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}
		$formParam = $this->_getAllParams();
                
		//Recupera o objeto de formulário para validação
		$form = $this->getForm($formParam);
                $form->getElement("DS_LOGIN")->setValue($formParam['DS_LOGIN']);
		
		try {
			$busParametro = new Fnde_Sice_Business_ParametroCertificado();
                        
                        $login = $busParametro->isUserSegWeb($formParam['DS_LOGIN']);
                    
                        if(!$login) {
                            $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'O Login informado não foi encontrado.');
                        }
                        else {
                            $this->addInstantMessage(Fnde_Message::MSG_INFO, "O Login informado está relacionado ao usuário " . $login['NO_USUARIO'] . ". Confirme novamente para continuar.");
                            $form->setAction($this->view->baseUrl() . '/index.php/secretaria/parametrizardocumentos/form')->setMethod('post');
                        }
			
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Não foi possivel realizar a alteração devido a um erro inesperado.');
		}
		
		$this->view->form = $form;
		$this->render("form");
	}
        
	
	/**
	 * Salva os dados do Formulário.
	 */
	public function salvarAction(){
		
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}
		
		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_getAllParams());
		
		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			return $this->render('form');
		}
		//Recupera os parâmetros do request
		$formParam = $this->_getAllParams();
		
		try {
			$busParametro = new Fnde_Sice_Business_ParametroCertificado();
                        
                        $login = $busParametro->isUserSegWeb($formParam['DS_LOGIN']);
                    
                        if(!$login) {
                            throw new Exception('O Login informado não foi encontrado.');
                        }
			
			$arDados['NO_SECRETARIO'] = $formParam['NO_SECRETARIO'];
			$arDados['NO_CARGO'] = $formParam['NO_CARGO'];
			$arDados['NO_LOCAL_ATUACAO'] = $formParam['NO_LOCAL_ATUACAO'];
			$arDados['DS_LOGIN'] = strtoupper($formParam['DS_LOGIN']);
			
			$busParametro->setAtualizaResponsavel($arDados);
			
			$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "Parametrização Realizada com Sucesso");
			
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Não foi possivel realizar a alteração devido a um erro inesperado.');
		}
		
		$this->view->form = $form;
		$this->render("form");
	}
	
	/**
	 * Retorna os campos preenchidos do formulário
	 * 
	 * @param unknown_type $form
	 */
	public function setParametros($form){
		$busParam = new Fnde_Sice_Business_ParametroCertificado();
		$arParametros = $busParam->getResponsavelCertificado();
                
		$form->setParametros($arParametros['NO_SECRETARIO'], $arParametros['NO_CARGO'], $arParametros['NO_LOCAL_ATUACAO'], $arParametros['DS_LOGIN']);
	}
}
