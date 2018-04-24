<?php

/**
 * Controller do PeriodoVinculacao
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */

class Manutencao_PeriodoVinculacaoController extends Fnde_Sice_Controller_Action {

	/**
	 * A��o de listagem
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function listAction() {

		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Per�odo Vincula��o');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'periodovinculacao', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'periodovinculacao', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//seta novos valores na sess�o
		if ( $this->_request->isPost() ) {
			$arFilter = $this->getRequest()->getParams();
		} else {
			$arFilter = array();
		}

		$form = $this->getFormFilter();
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->isPostValido($this->_request->isPost(), $arFilter) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_PeriodoVinculacao();
				$rsRegistros = $obBusiness->pesquisarPeriodoVinculacaoPorPeril($arFilter['NU_SEQ_TIPO_PERFIL']);
				if ( !count($rsRegistros) ) {
					$this->addInstantMessage(Fnde_Message::MSG_INFO,
							'Nenhum registro localizado para o filtro informado.');
				}
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			}
		}

		//chama filtro form
		$this->view->formFilter = $form;

		//Chamando componente zend.grid dentro do helper
		if ( $rsRegistros ) {

			$rowAction = array(
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(
									array('action' => 'del-periodo-vinculacao', 'NU_SEQ_PERIODO_VINCULACAO' => ''))
									. '%s', 'params' => array('NU_SEQ_PERIODO_VINCULACAO'),
							'attribs' => array('class' => 'icoExcluir excluir',
									'mensagem' => 'Deseja realmente excluir o registro?', 'title' => 'Excluir')));

			$arrHeader = array('<center>ID</center>', '<center>Perfil</center>', '<center>Exerc�cio</center>',
					'<center>Data Inicial</center>', '<center>Data Final</center>', '<center>Data Inclus�o</center>',);

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setHeaderActive(false);
			$grid->setAutoCallJs(true);
			$grid->setTitle("Listagem de per�odos de vincula��o");
			$grid->setActionColumn("<center>A��o</center>");
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setRowAction($rowAction)->setId(
					'NU_SEQ_PERIODO_VINCULACAO')->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Verifica se os parametros retornados para pesquisa s�o v�lido para prosseguir com o filtro
	 * @param unknown_type $post
	 * @param unknown_type $arFilter
	 */
	private function isPostValido( $post, $arFilter ) {
		if ( $post || isset($arFilter['startlist']) || isset($arFilter['start']) || !empty($arFilter) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove um registro de PeriodoVinculacao
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function delPeriodoVinculacaoAction() {
		$arParam = $this->_getAllParams();

		$obPeriodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();

		$listpervincbolsa = $obPeriodoVinculacao->getListPeriodoVincBolsa($arParam['NU_SEQ_PERIODO_VINCULACAO']);
		if ( $listpervincbolsa ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"O per�odo de vincula��o n�o poder� ser exclu�do, pois existem bolsas vinculadas a ele.");
			$this->_redirect("/manutencao/periodovinculacao/list");
		} else {
			try {
				$resposta = $obPeriodoVinculacao->del($arParam['NU_SEQ_PERIODO_VINCULACAO']);
				if ( !$resposta ) {
					$this->addMessage(Fnde_Message::MSG_ERROR,
							"O per�odo de vincula��o n�o poder� ser exclu�do, pois existem bolsas vinculadas a ele.");
					$this->_redirect("/manutencao/periodovinculacao/list");
				} else {
					$this->addMessage(Fnde_Message::MSG_SUCCESS, "Registro exclu�do com sucesso");
					$this->_redirect("/manutencao/periodovinculacao/list");
				}
			} catch ( Exception $e ) {
				$this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao tentar excluir per�odo de vincula��o.");
				$this->_redirect("/manutencao/periodovinculacao/list");
			}

		}
	}

	/**
	 * Monta o formul�rio e renderiza na view
	 *
	 * @access public
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function formAction() {

		$this->setTitle('Per�odo Vincula��o');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'periodovinculacao', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'periodovinculacao', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formul�rio
		$periodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();
		if ( $this->getRequest()->getParam("NU_SEQ_PERIODO_VINCULACAO") )
			$arDados = $periodoVinculacao->getConfiguracaoById(
					$this->getRequest()->getParam("NU_SEQ_PERIODO_VINCULACAO"));

		//Recuperando array de dados extras para setar valores extras no formul�rio
		//$arExtra = $this->getArExtraFormulario();

		//Recupera o objeto de formul�rio para valida��o
		$form = $this->getForm($arDados);

		if ( $arDados['NU_SEQ_PERIODO_VINCULACAO'] ) {
			$this->view->form = $form->populate($arDados);
		} else {
			$this->view->form = $this->getForm();
		}

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarPeriodoVinculacaoAction();
		}

		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$this->setTitles(
				array('dtInclusao', 'nuSeqPeriodoVinculacao', 'dtFinal', 'vlExercicio', 'dtInicial', 'nuSeqTipoPerfil',));
		$this->setNameList(
				array('DT_INCLUSAO', 'NU_SEQ_PERIODO_VINCULACAO', 'DT_FINAL', 'VL_EXERCICIO', 'DT_INICIAL',
						'NU_SEQ_TIPO_PERFIL',));

		$form = new PeriodoVinculacao_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/periodovinculacao/form')->setMethod('post')->setAttrib(
				'id', 'form');

		return $form;
	}

	/**
	 * M�todo acess�rio get de nameList.
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * M�todo acess�rio set de nameList
	 * @param array $arList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * M�todo acess�rio set de titles
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * M�todo acess�rio set de titles
	 * @param array $arTitles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * M�todo acess�rio get do formul�rio de pesquisa de Per�odo de Vincula��o
	 * @param array $arDados
	 * @param array $obGrid
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {

		$rsTipoPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil",
				array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));

		$form = new PeriodoVinculacao_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/periodovinculacao/list')->setMethod('post');
		$this->setTipoPerfilForm($form, $rsTipoPerfil);

		return $form;
	}

	/**
	 * M�todo para setar Tipo de Perfil no formulario
	 */
	public function setTipoPerfilForm( $form, $rsTipoPerfil ) {

		$form->setTipoPerfil($rsTipoPerfil);

	}

	/**
	 * M�todo acess�rio get de arTitlesList
	 */
	public function getArTitlesList() {
		return array('dtInclusao', 'nuSeqPeriodoVinculacao', 'dtFinal', 'vlExercicio', 'dtInicial', 'nuSeqTipoPerfil',);
	}

	/**
	 * M�todo para gravar os per�odos de vincula��o no banco de dados.
	 * 
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function salvarPeriodoVinculacaoAction() {
		$obBusinessPeriodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();
		
		$this->montaCabecalhoFormAoSalvar($this->getRequest()->isPost());

		//Recupera o objeto de formul�rio para valida��o
		$form = $this->getForm($this->_request->getParams());

		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			return $this->render('form');
		}

		//Recupera os par�metros do request
		$arParams = $this->_request->getParams();

		if ( $arParams['VL_EXERCICIO'] == 0 ) {
			$form->getElement('VL_EXERCICIO')->addError("Ano inv�lido!");
			$this->view->form = $form;
			return $this->render('form');
		}
		
		$arPeriodoVinculacao = $this->getPeriodoVinculacao($arParams);

		try {
			if ( $obBusinessPeriodoVinculacao->getPeriodoVinculacaoByAno(
					array('VL_EXERCICIO' => ( int ) $arParams['VL_EXERCICIO'])) ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, "Exerc�cio {$arParams['VL_EXERCICIO']} j� cadastrado");
				$this->view->form = $form;
				return $this->render('form');
			}
			$totalRegistro = $obBusinessPeriodoVinculacao->verificaExistenciaPeriodoVinculacao();
			if ( count($totalRegistro) == 0 ) {
				$obBusinessPeriodoVinculacao->salvarPeriodoVinculacao($arPeriodoVinculacao);
				$this->addMessage(Fnde_Message::MSG_SUCCESS, "Cadastro realizado com sucesso.");
				$this->_redirect("/manutencao/periodovinculacao/list");
			} else {
				$totalRegistro = $obBusinessPeriodoVinculacao->verificaExistenciaPeriodoVinculacaoAnoAnterior(
						( int ) $arParams['VL_EXERCICIO'] - 1);
				if ( count($totalRegistro) == 0 ) {
					$this->addInstantMessage(Fnde_Message::MSG_ERROR,
							"O per�odo de vincula��o para o exerc�cio {$arParams['VL_EXERCICIO']} n�o pode ser cadastrado, n�o consta per�odo para o exerc�cio anterior.");
					$this->view->form = $form;
					return $this->render('form');
				} else {
					$obBusinessPeriodoVinculacao->salvarPeriodoVinculacao($arPeriodoVinculacao);
					$this->addMessage(Fnde_Message::MSG_SUCCESS, "Cadastro realizado com sucesso.");
					$this->_redirect("/manutencao/periodovinculacao/list");
				}
			}
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
			return $this->render('form');
		}
	}
	
	/**
	 * Metodo auxiliar que retorna array com os dados de Periodo Vinculacao.
	 * @param array $arParams
	 */
	private function getPeriodoVinculacao($arParams) {
		$obBusinesPerfil = new Fnde_Sice_Business_TipoPerfil();
		$perfis = $obBusinesPerfil->search(array('NU_SEQ_TIPO_PERFIL', 'DS_TIPO_PERFIL'));
		
		$dataAutal = date('d/m/Y');
		
		$arPeriodoVinculacao = array();
		$i = 0;
		
		for ( $j = 0; $j < count($perfis); $j++ ) {
			if ( $this->isPerfilValido($perfis, $j) ) {
				$k = 1;
				while ( $k < 12 ) {
					$arPeriodoVinculacao[$i]['VL_EXERCICIO'] = $arParams['VL_EXERCICIO'];
					$arPeriodoVinculacao[$i]['DT_INCLUSAO'] = $dataAutal;
					$arPeriodoVinculacao[$i]['NU_SEQ_TIPO_PERFIL'] = $perfis[$j]['NU_SEQ_TIPO_PERFIL'];
					$arPeriodoVinculacao[$i]['DT_INICIAL'] = date('d/m/Y',
							strtotime($arParams['VL_EXERCICIO'] . "-" . ( $k < 10 ? '0' . $k : $k ) . '-01'));
					$k++;
					$numDiasMes = cal_days_in_month(CAL_GREGORIAN, $k, $arParams['VL_EXERCICIO']);
					$arPeriodoVinculacao[$i]['DT_FINAL'] = date('d/m/Y',
							strtotime($arParams['VL_EXERCICIO'] . "-" . ( $k < 10 ? '0' . $k : $k ) . '-' . $numDiasMes));
					$i++;
					$k++;
				}
			}
		}
		
		return $arPeriodoVinculacao;
	}

	/**
	 * Remonta cabe�alho do formul�rio ao salvar usu�rio
	 */
	private function montaCabecalhoFormAoSalvar( $postRequest ) {
		$this->setTitle('Per�odo Vincula��o');
		$this->setSubtitle('Cadastrar');

		$menu = array($this->getUrl('manutencao', 'periodovinculacao', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'periodovinculacao', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Se os dados n�o foram enviados por post retorna para a index
		if ( !$postRequest ) {
			return $this->_forward('index');
		}
	}
	
	/**
	 * Metodo auxiliar que verifica o perfil do usuaio.
	 * @param array $perfis
	 * @param int $j
	 */
	private function isPerfilValido($perfis, $j){
		if($perfis[$j]['DS_TIPO_PERFIL'] == 'Tutor' || $perfis[$j]['DS_TIPO_PERFIL'] == 'Coordenador Executivo Estadual'
					|| $perfis[$j]['DS_TIPO_PERFIL'] == 'Articulador'){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * M�todo para limpar a �ltima pesquisa realizada.
	 */
	public function clearSearchAction() {

		//limpa sess�o
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sess�o
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

}
