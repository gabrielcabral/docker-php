<?php

/**
 * Controller do Curso
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */

class Manutencao_CursoController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function listAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Curso');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'curso', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'curso', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sessão
		$arFilter = $this->getSearchParamCurso();

		$form = $this->getFormFilter();
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->_request->isPost() || !empty($arFilter) ) {
			if ( $form->isValid($arFilter) ) {
				$obBusiness = new Fnde_Sice_Business_Curso();

				//Prepara os parametros de pesquisa.
				$arParams = $this->getParamsPesquisa($arFilter);

				$rsRegistros = $obBusiness->pesquisarCurso($arParams);

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
					'visualizar' => array('label' => 'Visualisar',
							'url' => $this->view->Url(array('action' => 'visualizar-curso', 'NU_SEQ_CURSO' => ''))
									. '%s', 'params' => array('NU_SEQ_CURSO'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'NU_SEQ_CURSO' => '')) . '%s',
							'params' => array('NU_SEQ_CURSO'),
							'attribs' => array('class' => 'icoEditar', 'title' => 'Editar',
									'mensagem' => 'Deseja realmente alterar dados do curso?',)),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(array('action' => 'del-curso', 'NU_SEQ_CURSO' => '')) . '%s',
							'params' => array('NU_SEQ_CURSO'),
							'attribs' => array('class' => 'icoExcluir excluir',
									'mensagem' => 'Deseja realmente excluir o registro?', 'title' => 'Excluir')),);

			$arrHeader = array('<center>ID</center>', '<center>Tipo Curso</center>', '<center>Sigla</center>',
					'<center>Nome</center>', '<center>Carga Horária</center>', '<center>Pré-Requisito</center>',
					'<center>Situação</center>');

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setHeaderActive(false);
			$grid->setActionColumn("<center>Ação</center>");
			$grid->setAutoCallJs(true);
			$grid->setTitle("Listagem de cursos");
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setRowAction($rowAction)->setId(
					'NU_SEQ_CURSO')->setTableAttribs(array('id' => 'edit'));
		}
	}

	private function getParamsPesquisa( $arFilter ) {
		$arParams = array();

		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['NU_SEQ_TIPO_CURSO']) ) {
			$arParams['NU_SEQ_TIPO_CURSO'] = $arFilter['NU_SEQ_TIPO_CURSO'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['DS_SIGLA_CURSO']) ) {
			$arParams['DS_SIGLA_CURSO'] = trim($arFilter['DS_SIGLA_CURSO']);
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['DS_NOME_CURSO']) ) {
			$arParams['DS_NOME_CURSO'] = trim($arFilter['DS_NOME_CURSO']);
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['ST_CURSO']) ) {
			$arParams['ST_CURSO'] = $arFilter['ST_CURSO'];
		}

		return $arParams;
	}

	/**
	 * Remove um registro de Curso
	 *
	 * @author diego.matos
	 * @since 15/05/2012
	 */
	public function delCursoAction() {
		$arParam = $this->_getAllParams();

		$obCurso = new Fnde_Sice_Business_Curso();

		try {
			$obCurso->removerCurso($arParam['NU_SEQ_CURSO']);
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"O curso não pode ser excluído, pois existe(m) turma(s) vinculada(s) a ele.");
			$this->_redirect("/manutencao/curso/list");
		}
		$this->addMessage(Fnde_Message::MSG_SUCCESS, "Exclusão realizada com sucesso.");
		$this->_redirect("/manutencao/curso/list");
	}

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function formAction() {

		$this->setTitle('Curso');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'curso', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'curso', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		if ( isset($params['NU_SEQ_CURSO']) && $params['NU_SEQ_CURSO'] != '' ) {
			$obBusinessCurso = new Fnde_Sice_Business_Curso();
			$rsModulo = $obBusinessCurso->getCursoById($params['NU_SEQ_CURSO']);
			$arDados = $rsModulo;

			$obBusinessVinculoModuloCurso = new Fnde_Sice_Business_VincCursoModulo();
			$vinculos = $obBusinessVinculoModuloCurso->buscarVinculoPorCurso($arDados['NU_SEQ_CURSO']);

			$arDados['qtn'] = count($vinculos);
			$arDados['qtn'] = ( $arDados['qtn'] == 0 ? 1 : $arDados['qtn'] );
			$arExtra = $vinculos;
		}

		//Recupera o objeto de formulário para validação
		if ( !$arDados['NU_SEQ_CURSO'] ) {
			$arDados = $this->_getAllParams();
                        

			$obBusinessCurso = new Fnde_Sice_Business_Curso();
			$rsModulo = $obBusinessCurso->getCursoById($arDados['NU_SEQ_CURSO']);
			$arDados = $rsModulo->toArray();

			$obBusinessVinculoModuloCurso = new Fnde_Sice_Business_VincCursoModulo();

			if ( $arDados['NU_SEQ_CURSO'] ) {
				$vinculos = $obBusinessVinculoModuloCurso->buscarVinculoPorCurso($arDados['NU_SEQ_CURSO']);
			}

			$arDados['qtn'] = count($vinculos);
			$arDados['qtn'] = ( $arDados['qtn'] == 0 ? 1 : $arDados['qtn'] );
			$arExtra = $vinculos;

		}

		$form = $this->getForm($arDados, $arExtra);
		$this->recarregaComboModulo($form);

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarCursoAction();
		}
                
                $arDados['DS_OBJETIVO_CURSO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_OBJETIVO_CURSO']);
		$this->view->form = $form->populate($arDados);
		$this->view->formlimpo = ( $this->getFormComboModulo($arDados, $arExtra) );

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
				array('nuSeqTipoCurso', 'qtModulos', 'stCurso', 'SCurso', 'dsPrerequisitoCurso', 'nuSeqCurso',
						'vlCargaHoraria', 'dsNomeCurso', 'dsSiglaCurso',));
		$this->setNameList(
				array('NU_SEQ_TIPO_CURSO', 'QT_MODULOS', 'ST_CURSO', 'NU_SEQ_CURSO_PREREQUISITO',
						'DS_PREREQUISITO_CURSO', 'NU_SEQ_CURSO', 'VL_CARGA_HORARIA', 'DS_NOME_CURSO', 'DS_SIGLA_CURSO',));

		$form = new Curso_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/curso/form')->setMethod('post')->setAttrib(
				'id', 'form');
		$obBusinessCompentes = new Fnde_Sice_Business_Componentes();
		$this->setTipoCurso($form, $obBusinessCompentes);
		$this->setCursoPreRequisito($form, $arDados, $obBusinessCompentes);
		$this->setModuloPreRequisito($form, $obBusinessCompentes);
		$this->bloqueiaCampos($form, $arDados);

		return $form;
	}

	/**
	 * Método para inserir tipoCurso no select
	 * @author gustavo.gomes
	 * @param object $form
	 * @param object $obBusinessCompentes
	 */
	public function setTipoCurso( $form, $obBusinessCompentes ) {
		$rsTipoCurso = $obBusinessCompentes->getAllByTable("TipoCurso", array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

		$form->setTipoCurso($rsTipoCurso);
	}

	/**
	 * Método para inserir preRequisito de curso no select
	 * @author gustavo.gomes
	 * @param object $form
	 * @param array $arDados
	 * @param object $obBusinessCompentes
	 */
	public function setCursoPreRequisito( $form, $arDados, $obBusinessCompentes ) {

		if ( $arDados['NU_SEQ_CURSO'] == null ) {
			$rsCursoPrerequisito = $obBusinessCompentes->getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"));
		} else {
			$rsCursoPrerequisito = $obBusinessCompentes->getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"),
					array("stWhere" => "NU_SEQ_CURSO<> {$arDados['NU_SEQ_CURSO']}"));
		}

		$form->setCursoPreRequisito($rsCursoPrerequisito);

	}

	/**
	 * Método para bloquear os campos do formulário caso tenha vinculo de curso com turma
	 * @author gustavo.gomes
	 * @param object $form
	 * @param array $arDados
	 */
	public function bloqueiaCampos( $form, $arDados ) {
		$businessTurma = new Fnde_Sice_Business_Turma();
		if ( $arDados['NU_SEQ_CURSO'] && $businessTurma->verificarVinculoCursoTurma($arDados['NU_SEQ_CURSO']) ) {
			$form->bloqueiaCampos();
		}

	}

	/**
	 * Método para inserir preRequisito de módulo
	 * @author gustavo.gomes
	 * @param object $form
	 * @param object $obBusinessCompentes
	 */
	public function setModuloPreRequisito( $form, $obBusinessCompentes ) {

		$rsModuloPrerequisito = $obBusinessCompentes->getAllByTable("Modulo", array("NU_SEQ_MODULO", "DS_NOME_MODULO"));

		$form->setModuloPreRequisito($rsModuloPrerequisito);
	}

	/**
	 * Método acessório get do form do combo de módulos.
	 *
	 * @author vinicius.cancado
	 */
	public function getFormComboModulo( $arDados = array(), $arExtra = array() ) {
		$obBusinessModuloPrerequisito = new Fnde_Sice_Business_Modulo();
		$resultSet = $obBusinessModuloPrerequisito->search(array());

		$form = new Curso_FormComboModulo($arDados, $arExtra, $resultSet);
		$this->setComboModulo($form);
		return $form;
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setComboModulo( $form ) {
		$obBusinessComponentes = new Fnde_Sice_Business_Componentes();

		$rsModuloPrerequisito = $obBusinessComponentes->getAllByTable("Modulo",
				array("NU_SEQ_MODULO", "DS_NOME_MODULO"));

		if ( $form ) {
			$form->setComboModulo($rsModuloPrerequisito);
		} else {
			$arRetorno = array(null => "Selecione");
			$arRetorno += $rsModuloPrerequisito;
			return $arRetorno;
		}
	}

	/**
	 * 
	 * @param form $form
	 */
	private function recarregaComboModulo( $form ) {
		for ( $i = 0; $i < $form->getElement('qtn')->getValue(); $i++ ) {
			$form->getElement('NU_SEQ_MODULO' . $i)->setMultiOptions($this->setComboModulo(null));
		}
	}

	/**
	 * Método acessório get de nameList.
	 *
	 * @author vinicius.cancado
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Método acessório set de nameList.
	 *
	 * @author vinicius.cancado
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Método acessório get de titles.
	 *
	 * @author vinicius.cancado
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Método acessório set de nameList.
	 *
	 * @author vinicius.cancado
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Método acessório get para o formulário de Pesquisa.
	 * @param array $arDados
	 * @param array $obGrid
	 * 
	 * @since 18/04/2012
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new Curso_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/curso/list')->setMethod('post');
		$this->setTipoCursoFormFilter($form);

		return $form;
	}

	public function setTipoCursoFormFilter( $form ) {
		$businessComponente = new Fnde_Sice_Business_Componentes();

		$rsTipoCurso = $businessComponente->getAllByTable("TipoCurso", array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

		$form->setTipoCurso($rsTipoCurso);

	}

	/**
	 * Método para validar se os dados estão de acordo com as regras de apresentação da tela.
	 *
	 * @author diego.matos
	 * @since 18/04/2012
	 */
	function validarFormulario( $form, $arParams ) {

		$flag = true;

		//Validando E5
		if ( $arParams['VL_CARGA_HORARIA'] > 100 ) {
			$form->getElement('VL_CARGA_HORARIA')->addError("A carga horária total do curso não pode ser maior que 100");
			$flag = false;
		}

		//Validando E6
		if ( $arParams['VL_CARGA_HORARIA'] < 40 ) {
			$form->getElement('VL_CARGA_HORARIA')->addError("A carga horária total do curso não pode ser menor que 40");
			$flag = false;
		}

		//Validando E8
		if ( $arParams['QT_MODULOS'] != $form->getElement('qtn')->getValue() ) {
			$form->getElement('QT_MODULOS')->addError(
					"Quantidade de módulos diferente da quantidade de módulos adicionados");
			$flag = false;
		}

		//Validando E10
		$arModulosCurso = $this->validaModulos($form, $arParams);

		$obModelo = new Fnde_Sice_Business_Modulo();
		$qtHorasModulos = $obModelo->retornarTotalHoras($arModulosCurso);

		//Validando E7
		if ( $arParams['VL_CARGA_HORARIA'] != $qtHorasModulos ) {
			$form->getElement('TOTAL_HORAS')->addError(
					"A carga horária total dos módulos deve ser igual ao total do curso");
			$flag = false;
		}

		$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
		$this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));

		return $flag;
	}

	private function validaModulos( $form, $arParams ) {
		$arModulosCurso = array();
		//Validando E10
		for ( $i = 0; $i < $form->getElement('qtn')->getValue(); $i++ ) {
			$modulo = array();
			$modulo['NU_SEQ_MODULO'] = $arParams['NU_SEQ_MODULO' . $i];
			$arModulosCurso[] = $modulo;
		}

		$contMod1 = 0;

		foreach ( $arModulosCurso as $modulo ) {
			if ( $sair ) {
				break;
			}
			$contMod2 = 0;
			foreach ( $arModulosCurso as $mod ) {
				if ( $contMod1 != $contMod2 ) {
					if ( $modulo['NU_SEQ_MODULO'] == $mod['NU_SEQ_MODULO'] ) {
						$form->getElement('NU_SEQ_MODULO' . $contMod1)->addError(
								'Os módulos adicionados não podem ser iguais');
						$form->getElement('NU_SEQ_MODULO' . $contMod2)->addError(
								'Os módulos adicionados não podem ser iguais');
						$sair = true;
					}
				}
				$contMod2++;
			}
			$contMod1++;
		}

		return $arModulosCurso;
	}

	/**
	 * Método para gravar os dados do curso no banco de dados.
	 *
	 * @author diego.matos
	 * @since 18/04/2012
	 */
	public function salvarCursoAction() {
		$this->setTitle('Curso');
		$this->setSubtitle('Cadastrar');
		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'curso', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'curso', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams());
		$this->recarregaComboModulo($form);
		if ( $form->getElement('DS_PREREQUISITO_CURSO')->getValue() != 'S' ) {
			$form->getElement('NU_SEQ_CURSO_PREREQUISITO')->setRequired(false);
		}
		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$form->getElement('NU_SEQ_CURSO_PREREQUISITO')->setRequired(true);
			$this->view->form = $form;
			$this->view->formlimpo = ( $this->getFormComboModulo() );
			return $this->render('form');
		}
		//Recupera os parâmetros do request
		$arParams = $this->_request->getParams();

		if ( $this->validarFormulario($form, $arParams) ) {
			$arCurso = array();
			$msgSucesso = 'Cadastro realizado com sucesso';
			if ( $arParams['NU_SEQ_CURSO'] != null ) {
				$arCurso['NU_SEQ_CURSO'] = $arParams['NU_SEQ_CURSO'];

				$obModeloVincCursoModulo = new Fnde_Sice_Business_VincCursoModulo();
				$obModeloVincCursoModulo->excluirVinculoCursoModulo($arCurso['NU_SEQ_CURSO']);

				$msgSucesso = 'Alteração realizada com sucesso';
			}

			$arCurso['NU_SEQ_TIPO_CURSO'] = $arParams['NU_SEQ_TIPO_CURSO'];
			$arCurso['DS_SIGLA_CURSO'] = trim($arParams['DS_SIGLA_CURSO']);
			$arCurso['DS_NOME_CURSO'] = trim($arParams['DS_NOME_CURSO']);
			$arCurso['VL_CARGA_HORARIA'] = $arParams['VL_CARGA_HORARIA'];
			$arCurso['QT_MODULOS'] = $arParams['QT_MODULOS'];
			$arCurso['ST_CURSO'] = $arParams['ST_CURSO'];
			$arCurso['NU_SEQ_CURSO_PREREQUISITO'] = $arParams['NU_SEQ_CURSO_PREREQUISITO'];
			$arCurso['DS_PREREQUISITO_CURSO'] = $arParams['DS_PREREQUISITO_CURSO'];
			$arCurso['DS_OBJETIVO_CURSO'] = $arParams['DS_OBJETIVO_CURSO'];

			$arModulosCurso = array();
			for ( $i = 0; $i < $form->getElement('qtn')->getValue(); $i++ ) {
				$modulo = array();
				$modulo['NU_SEQ_MODULO'] = $arParams['NU_SEQ_MODULO' . $i];
				$arModulosCurso[] = $modulo;
			}
			try {
				$obModelo = new Fnde_Sice_Business_Curso();
				$obModelo->salvarCurso($arCurso, $arModulosCurso);
				$this->addMessage(Fnde_Message::MSG_SUCCESS, $msgSucesso);
				$this->_redirect("/manutencao/curso/list");
			} catch ( Exception $e ) {
				$this->trataErro($e, $arCurso, $form);
			}
		} else {
			$this->view->form = $form;
			$this->view->formlimpo = ( $this->getFormComboModulo() );
			return $this->render('form');
		}
	}

	/**
	 * Trata o erro de salvar curso
	 * @param $e
	 * @param $arCurso
	 * @param $form
	 */
	private function trataErro( $e, $arCurso, $form ) {
		if ( strpos($e->getMessage(), 'ORA-00001', 0) ) {
			if ( strpos($e->getMessage(), 'SCRS_UK_01', 0) ) { //Validando E03
				$form->getElement('DS_SIGLA_CURSO')->addError(
						'A sigla ' . $arCurso['DS_SIGLA_CURSO'] . ' do curso já está cadastrada');
			} elseif ( strpos($e->getMessage(), 'SCRS_UK_02', 0) ) { //Validando E4
				$form->getElement('DS_NOME_CURSO')->addError(
						'O curso ' . $arCurso['DS_NOME_CURSO'] . ' já está cadastrado');
			}
			//$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			$this->view->formlimpo = ( $this->getFormComboModulo() );
			return $this->render('form');
		} else if ( strpos($e->getMessage(), 'ORA-02291', 0) ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Curso já removido.");
			$this->_redirect("/manutencao/curso/list");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect("/manutencao/curso/list");
		}
	}

	/**
	 * Método para visualizar um curso gravado no sistema.
	 *
	 * @author diego.matos
	 * @since 18/04/2012
	 */
	public function visualizarCursoAction() {

		$this->setTitle('Curso');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'curso', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'curso', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		if ( isset($params['NU_SEQ_CURSO']) && $params['NU_SEQ_CURSO'] != '' ) {
			$obBusinessCurso = new Fnde_Sice_Business_Curso();
			$rsModulo = $obBusinessCurso->getCursoById($params['NU_SEQ_CURSO']);
			$arDados = $rsModulo;

			$obBusinessVinculoModuloCurso = new Fnde_Sice_Business_VincCursoModulo();
			$vinculos = $obBusinessVinculoModuloCurso->buscarVinculoPorCurso($arDados['NU_SEQ_CURSO']);

			$arDados['qtn'] = count($vinculos);
			$arDados['qtn'] = ( $arDados['qtn'] == 0 ? 1 : $arDados['qtn'] );
			$arExtra = $vinculos;
		}

		//Recupera o objeto de formulário para validação
		if ( !$arDados['NU_SEQ_CURSO'] ) {
			$arDados = $this->_getAllParams();

			$obBusinessCurso = new Fnde_Sice_Business_Curso();
			$rsModulo = $obBusinessCurso->getCursoById($arDados['NU_SEQ_CURSO']);
			$arDados = $rsModulo->toArray();

			$obBusinessVinculoModuloCurso = new Fnde_Sice_Business_VincCursoModulo();
			$vinculos = $obBusinessVinculoModuloCurso->buscarVinculoPorCurso($arDados['NU_SEQ_CURSO']);

			$arDados['qtn'] = count($vinculos);
			$arDados['qtn'] = ( $arDados['qtn'] == 0 ? 1 : $arDados['qtn'] );
			$arExtra = $vinculos;

		}

		$arDados['readonly'] = 1;
		$form = $this->getForm($arDados, $arExtra);

		$elementos = $form->getElements();

		foreach ( $elementos as $elemento ) {
			if ( $elemento->getName() != "cancelar" ) {
				$elemento->setAttrib("disabled", true);
			}
		}

                
                $arDados['DS_OBJETIVO_CURSO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_OBJETIVO_CURSO']);
		$this->view->form = $form->populate($arDados);
		$this->view->formlimpo = ( $this->getFormComboModulo($arDados, $arExtra) );

		$this->render('form');

	}

	/**
	 * Método para limpar os dados de uma pesquisa feita anteriormente.
	 *
	 * @author diego.matos
	 * @since 18/04/2012
	 */
	public function clearSearchAction() {

		//limpa sessão
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessão
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * Método para limpar os obter os dados de uma pesquisa.
	 *
	 * @author diego.matos
	 * @since 18/04/2012
	 */
	public function getSearchParamCurso() {
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
