<?php

/**
 *
 * @author diego.matos
 */
class Relatorios_EmitirRelatoriosController extends Fnde_Sice_Controller_Action {

	/**
	 * Instancia o objeto desta tela.
	 * @see Zend_Controller_Action::init()
	 */
	public function init() {
		$this->setTitle('Relat�rios');
		$this->setSubtitle('Filtrar relat�rios');
		parent::init();
	}

	/**
	 * Indica a��o da tela.
	 */
	public function indexAction() {
		$this->_forward('list');
	}

	/**
	 * Pesquisa da tela.
	 */
	public function listAction() {

		$form = $this->getFormFilter();
		//recupera valores da sess�o
		$arFilter = $this->_getAllParams();
		$form->populate($arFilter);

		//chama filtro form
		$this->view->formFilter = $form;

		$arrData = array();
		$businessSisrel = new Fnde_Sice_Business_Sisrel();
		$cfgAmbiente = $businessSisrel->getConfigAmbiente();
		$perfil = $businessSisrel->getPerfil();
		$httpxforwarded = "sice.fnde.gov.br";
		
		if(!$this->getRequest()->isPost()){
			setcookie('perfil_cookie', $perfil['perfil'], time() + 3600, '/', '.fnde.gov.br',false,false);
			setcookie('co_usuario', $perfil['co_usuario'], time() + 3600, '/', '.fnde.gov.br',false,false);
			setcookie('http_x_forwarded_host', $httpxforwarded, time() + 3600, '/', '.fnde.gov.br',false,false);
			
		}else{
		
			try {
				
				$dadosRel = $businessSisrel->getRelatorios($cfgAmbiente);
				
				$dadosRel = json_decode($dadosRel, true);
				
				$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
				$perfisUsuarioLogado = $usuarioLogado->credentials;
				
				$uf = "";
				
				if( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado)
						|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ){
					$businessUsuario = new Fnde_Sice_Business_Usuario();
					
					$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
					if ( $cpfUsuarioLogado ) {
						$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
					}
					
					if(!$form->getElement("SG_UF")->getValue()){
						$uf = $arUsuario['SG_UF_ATUACAO_PERFIL'];
					}else{
						$uf = $form->getElement("SG_UF")->getValue();
					}
				}else{
					$uf = $form->getElement("SG_UF")->getValue();
				}
				
				if ( count($dadosRel) ) {
					
					foreach ( $dadosRel['consulta'] as $out ) {
						$arrData[] = array('Nome' => utf8_decode($out['no_consulta']),
								'A��es' => $this->view->link('Gera PDF',
										$cfgAmbiente['urlPath'] . $out['urlpdf'] . "&"
												. $out['ds_post_consulta'][0]['value'] . "="
												. $uf,
										array('class' => 'icoPDF', 'target' => '_blank')) . ' '
										. $this->view->link('Exportar excel',
												$cfgAmbiente['urlPath'] . $out['urlxls'] . "&"
														. $out['ds_post_consulta'][0]['value'] . "="
														. $uf,
												array('class' => 'icoExcel', 'target' => '_blank')) . ' '
										. $this->view->link('Visualizar',
												$cfgAmbiente['urlPath'] . $out['urlhtml'] . "&"
														. $out['ds_post_consulta'][0]['value'] . "="
														. $uf,
												array('class' => 'icoVisualizar', 'target' => '_blank')));
					}
				}
	
				$this->view->arrData = $arrData;
				$headerGrid = array('Nome');
				if ( count($dadosRel) ) {
	
					$grid = new Fnde_View_Helper_DataTables();
	
					$grid->setAutoCallJs(true);
					$grid->setActionColumn("<center>A��es</center>");
					$this->view->grid = $grid->setData($arrData)->setRowAction(array())->setTitle("Listagem de Relat�rios")->setHeader(
							$headerGrid);
				} else {
					$this->addInstantMessage(Fnde_Message::MSG_INFO, "Nenhum modelo de relat�rio encontrado");
				}
				
				//HABILITA O BOT�O PARA CRIAR RELAT�RIOS DIN�MICOS (CASO O USU�RIO TENHA PERMISS�O)
	 			
			} catch ( Exception $e ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			}
		}
		
		$dadosMenu = array($cfgAmbiente['urlPath'] . "?module=relatorio&controller=index&action=index" => 'cadastrar');
		$this->setActionMenu($dadosMenu);
		
	}

	/**
	 * M�todo acess�rio get para recuperar o formul�rio de pesquisa da tela.
	 * @param array $arDados
	 * @param grid $obGrid
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new EmitirRelatorios_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/relatorios/emitirrelatorios/list')->setMethod('post');
		$this->setUf($form);

		return $form;
	}

	/**
	 * M�todo para inserir as UFs no select
	 * @author gustavo.gomes
	 * @param EmitirRelatorios_FormFilter $form
	 */
	public function setUf( $form ) {

		$businessUf = new Fnde_Sice_Business_Uf();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ) {
			$result = $businessUf->search(array('SG_UF' => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		} else {
			$result = $businessUf->search(array('SG_UF'));
		}

		$form->setUf($result);
	}

	/**
	 * M�todo para limpar os dados da �ltima pesquisa realizada.
	 */
	public function clearSearchAction() {

		//limpa sess�o
		Zend_Session::namespaceUnset('searchParam');
		
		unset($_COOKIE['perfil_cookie']);
		unset($_COOKIE['co_usuario']);
		unset($_COOKIE['http_x_forwarded_host']);
		
		//redireciona para pagina de listagem da ultima sess�o
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * M�todo para recuperar par�metros de pesquisa.
	 */
	public function getSearchParamRelatorio() {
		$arFilter = array();

		$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');

		$arSession = $this->urlFilterNamespace->param;

		$stUrlAtual = $this->_getParam('module') . '/' . $this->_getParam('controller') . '/'
				. $this->_getParam('action');
		$stUrlSession = $arSession['module'] . '/' . $arSession['controller'] . '/' . $arSession['action'];

		if ( $stUrlAtual == $stUrlSession ) {
			$arFilter = $arSession;
		}
		return $arFilter;
	}
}
