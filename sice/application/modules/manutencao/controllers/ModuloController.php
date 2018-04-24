<?php

/**
 * Controller do Modulo
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */

class Manutencao_ModuloController extends Fnde_Sice_Controller_Action {

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

		$this->setTitle('Módulo');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'modulo', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'modulo', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sessão
		$arFilter = $this->getSearchParamModulo();

		$form = $this->getFormFilter();
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->_request->isPost() || !empty($arFilter) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_Modulo();
				$arParams = $this->getParams($arFilter);
				$rsRegistros = $obBusiness->pesquisarMODULO($arParams);

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
							'url' => $this->view->Url(array('action' => 'visualizar-modulo', 'NU_SEQ_MODULO' => ''))
									. '%s', 'params' => array('NU_SEQ_MODULO'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar',)),
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'NU_SEQ_MODULO' => '')) . '%s',
							'params' => array('NU_SEQ_MODULO'),
							'attribs' => array('class' => 'icoEditar', 'title' => 'Editar')),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(array('action' => 'del-modulo', 'NU_SEQ_MODULO' => '')) . '%s',
							'params' => array('NU_SEQ_MODULO'),
							'attribs' => array('class' => 'icoExcluir excluir', 'title' => 'Excluir',
									'mensagem' => 'Deseja realmente excluir o registro?')),);
			$arrHeader = array('<center>ID</center>', '<center>Tipo Curso</center>', '<center>Sigla</center>',
					'<center>Nome</center>', '<center>Carga Horária</center>', '<center>Pré Requisito</center>',
					'<center>Situação</center>');

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setActionColumn("<center>Ação</center>");
			$grid->setHeaderActive(false);
			$grid->setTitle("Listagem de módulos");
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setRowAction($rowAction)->setId(
					'NU_SEQ_MODULO')->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Metodo auxiliar que retorna o filtro para pesquisa.
	 * @param $arFilter
	 */
	private function getParams( $arFilter ) {
		$arParams = array();
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['NU_SEQ_TIPO_CURSO']) ) {
			$arParams['NU_SEQ_TIPO_CURSO'] = $arFilter['NU_SEQ_TIPO_CURSO'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['DS_SIGLA_MODULO']) ) {
			$arParams['DS_SIGLA_MODULO'] = trim($arFilter['DS_SIGLA_MODULO']);
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['DS_NOME_MODULO']) ) {
			$arParams['DS_NOME_MODULO'] = trim($arFilter['DS_NOME_MODULO']);
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['ST_MODULO']) ) {
			$arParams['ST_MODULO'] = $arFilter['ST_MODULO'];
		}

		return $arParams;
	}

	/**
	 * Remove um registro de Modulo
	 *
	 * @author diego.matos
	 * @since 14/05/2012
	 */
	public function delModuloAction() {
		$arParam = $this->_getAllParams();

		$obModulo = new Fnde_Sice_Business_Modulo();
		$resposta = $obModulo->del($arParam['NU_SEQ_MODULO']);

		$resposta = ( string ) $resposta;

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Módulo excluído com sucesso.");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"O módulo não pode ser excluído, pois existe curso vinculado a ele.");
		}

		$this->_redirect("/manutencao/modulo/list");
	}

	/**
	 * Método para visualizar um módulo gravado no sistema.
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function visualizarModuloAction() {
		$this->setTitle('Módulo');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'modulo', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'modulo', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		$modulo = new Fnde_Sice_Business_Modulo();
		$arDados = null;
		if ( $this->getRequest()->getParam("NU_SEQ_MODULO") ) {
			$arDados = $modulo->getModuloById($this->getRequest()->getParam("NU_SEQ_MODULO"));
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados);

		$elementos = $form->getElements();

		foreach ( $elementos as $elemento ) {
			if ( $elemento->getName() != "cancelar" ) {
				$elemento->setAttrib("disabled", true);
			}
		}

		//$form->setElements($elementos);
                $arDados['DS_CONTEUDO_PROGRAMATICO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_CONTEUDO_PROGRAMATICO']);
		$this->view->form = $form->populate($arDados);

		$this->render('form');
	}

	/**
	 * Método para visualizar o certificado em PDF do método
	 *
	 * @author rafael.paiva
	 */
	public function visualizarCertificadoAction() {

		// 		die(var_dump($_POST['VL_CARGA_HORARIA_HIDDEN']));

		$obBusiness = new Fnde_Sice_Business_Parametro();
		$resolucao = $obBusiness->getParametroById('RESOLUCAO');
		$resolucao = $resolucao['DS_PARAMETRO'];

		$pdf = new Zend_Pdf();
		$pdf->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$page = $pdf->pages[0];

		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER);
		$page->setFont($font, 8);

		if ( $_POST['DS_CONTEUDO_PROGRAMATICO'] ) {
			$texto = str_replace("\n", "\\n", $_POST['DS_CONTEUDO_PROGRAMATICO']);
		} else {
			$texto = str_replace("\n", "\\n", $_POST['DS_CONTEUDO_PROGRAMATICO_HIDDEN']);
		}

		$conteudoMinistrado = wordwrap($texto, 91, '\n', 1);

		$texto = "SICE - Sistema de Informação do Programa Nacional de Fortalecimento dos Conselhos Escolares";
		$texto .= "\\nMódulo: " . $_POST['DS_NOME_MODULO'];
		if ( $_POST['VL_CARGA_HORARIA'] ) {
			$texto .= "\\nCarga Horária: " . $_POST['VL_CARGA_HORARIA'];
		} else {
			$texto .= "\\nCarga Horária: " . $_POST['VL_CARGA_HORARIA_HIDDEN'];
		}
		$texto .= "\\nNome do Tutor: " . "NO_TUTOR";
		$texto .= "\\n \\n \\n \\n";
		$texto .= "\\nConteúdo Ministrado:\\n" . $conteudoMinistrado;
		$texto .= "\\n\\n\\nResolução " . $resolucao;

		$d = explode('\n', $texto);

		$stringpos = 780; // posicao x do texto
		$stringdif = 12; // diferença entre cada quebra de linha.

		foreach ( $d as $c ) {
			$page->drawText($c, 10, $stringpos);
			$stringpos = ( $stringpos - $stringdif ); //subtrai para que a linha fique embaixo
		}

		header('Content-Type: application/pdf');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');
		ini_set('zlib.output_compression', '0');

		echo $pdf->render();

		die();

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
		$this->setTitle('Módulo');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'modulo', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'modulo', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		$modulo = new Fnde_Sice_Business_Modulo();
		$arDados = null;
		if ( $this->getRequest()->getParam("NU_SEQ_MODULO") ) {
			$arDados = $modulo->getModuloById($this->getRequest()->getParam("NU_SEQ_MODULO"));
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados, null);

		if ( $arDados['NU_SEQ_MODULO'] ) {
                        $arDados['DS_CONTEUDO_PROGRAMATICO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $arDados['DS_CONTEUDO_PROGRAMATICO']);
			$this->view->form = $form->populate($arDados);
		} else {
			$this->view->form = $this->getForm();
		}

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarModuloAction();
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

		$params = $this->_getAllParams();

		if ( isset($params['NU_SEQ_MODULO']) && $params['NU_SEQ_MODULO'] != '' ) {
			$obBusinessModulo = new Fnde_Sice_Business_Modulo();
			$rsModulo = $obBusinessModulo->getModuloById($params['NU_SEQ_MODULO']);
			$arDados = $rsModulo;
		}

		$this->setTitles(
				array('vlCargaDistancia', 'dsConteudoProgramatico', 'nuSeqTipoCurso', 'vlMaxConclusao', 'stModulo',
						'dsNomeModulo', 'dsSiglaModulo', 'SModulo', 'nuSeqModulo', 'dsPrerequisitoModulo',
						'vlCargaHoraria', 'vlMinConclusao', 'vlCargaPresencial',));
		$this->setNameList(
				array('VL_CARGA_DISTANCIA', 'DS_CONTEUDO_PROGRAMATICO', 'NU_SEQ_TIPO_CURSO', 'VL_MAX_CONCLUSAO',
						'ST_MODULO', 'DS_NOME_MODULO', 'DS_SIGLA_MODULO', 'NU_SEQ_MODULO_PREREQUISITO',
						'NU_SEQ_MODULO', 'DS_PREREQUISITO_MODULO', 'VL_CARGA_HORARIA', 'VL_MIN_CONCLUSAO',
						'VL_CARGA_PRESENCIAL',));

		$form = new Modulo_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/modulo/form')->setMethod('post')->setAttrib(
				'id', 'form');

		$obComponente = new Fnde_Sice_Business_Componentes();

		$this->setTipoCursoForm($form, $obComponente);
		$this->setPreRequisitosForm($form, $arDados, $obComponente);
		$this->setDisabledOrNo($form);

		return $form;
	}

	/**
	 * Método para inserir tipo de curso no select do formulário
	 * @author gustavo.gomes
	 * @param  $form
	 * @param  $obComponente
	 */
	public function setTipoCursoForm( $form, $obComponente ) {

		$rsTipoCurso = $obComponente->getAllByTable("TipoCurso", array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

		$form->setTipoCurso($rsTipoCurso);

	}
	/**
	 * Método para inserir os pre requisitos no select do formulário
	 * @author gustavo.gomes
	 * @param  $form
	 * @param  $arDados
	 * @param  $obComponente
	 */
	public function setPreRequisitosForm( $form, $arDados, $obComponente ) {

		if ( $arDados['NU_SEQ_MODULO'] == null ) {
			$rsModuloPrerequisito = $obComponente->getAllByTable("Modulo", array("NU_SEQ_MODULO", "DS_NOME_MODULO"));
		} else {
			$rsModuloPrerequisito = $obComponente->getAllByTable("Modulo", array("NU_SEQ_MODULO", "DS_NOME_MODULO"),
					array("stWhere" => "NU_SEQ_MODULO <> {$arDados['NU_SEQ_MODULO']}"));
		}

		$form->setPreRequisito($rsModuloPrerequisito);

	}

	/**
	 * Método para validar se os campos serão ou não desabilitados
	 * @author gustavo.gomes
	 * @param $form
	 */
	public function setDisabledOrNo( $form ) {
		$nuModulo = $form->getNuModulo();
		$businessModulo = new Fnde_Sice_Business_Modulo();
		if ( $nuModulo->getValue() != null ) {
			if ( $businessModulo->verificaVinculacao(array('NU_SEQ_MODULO' => $nuModulo->getValue())) != null ) {
				$form->setDisabled();
				$form->setAtribsConfirmar('toDisabled');
			} else {
				$form->setAtribsConfirmar();
			}
		} else {
			$form->setAtribsConfirmar();
		}
	}

	/**
	 * Método acessório get de nameList
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Método acessório set de nameList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Método acessório get de titles
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Método acessório set de titles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Método acessório get de arTitlesList
	 */
	public function getArTitlesList() {
		return array('vlCargaDistancia', 'dsConteudoProgramatico', 'nuSeqTipoCurso', 'vlMaxConclusao', 'stModulo',
				'dsNomeModulo', 'dsSiglaModulo', 'SModulo', 'nuSeqModulo', 'dsPrerequisitoModulo', 'vlCargaHoraria',
				'vlMinConclusao', 'vlCargaPresencial',);
	}

	/**
	 * Método acessório get do formulário de pesquisa da tela de módulo.
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new Modulo_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/modulo/list')->setMethod('post');
		$this->setTipoCursoFormFilter($form);

		return $form;
	}

	/**
	 * Método para setar o tipo de curso no formulario de filtro
	 *
	 * @author gustavo.gomes
	 */
	public function setTipoCursoFormFilter( $form ) {

		$businessComponente = new Fnde_Sice_Business_Componentes();

		$rsTipoCurso = $businessComponente->getAllByTable("TipoCurso", array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

		$form->setTipoCurso($rsTipoCurso);

	}

	/**
	 * Método de validação do formulário de acordo com as regras de apresentação da tela.
	 *
	 * @author diego.matos
	 */
	function validarFormulario( $form, $arParams ) {
		$flagCarga = $this->validaCargaHoraria($form, $arParams);
		$flagConclusao = $this->validaConclusao($form, $arParams);

		$flag = ( $flagCarga && $flagConclusao );

		$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
		$this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));

		return $flag;

	}

	/**
	 * Valida as regras de Carga Horaria.
	 * @param $form
	 * @param $arParams
	 */
	private function validaCargaHoraria( $form, $arParams ) {
		$flag = true;

		//Validando E5
		if ( $arParams['VL_CARGA_HORARIA'] == 0 ) {
			$form->getElement('VL_CARGA_HORARIA')->addError("A carga horária total do módulo não pode ser igual a zero");
			$flag = false;
		}

		//Validando E13
		if ( $arParams['VL_CARGA_HORARIA'] > 0 && $arParams['VL_CARGA_HORARIA'] < 40 ) {
			$form->getElement('VL_CARGA_HORARIA')->addError("O valor do campo não pode ser menor que 40");
			$flag = false;
		}

		//Validando E6
		if ( $arParams['VL_CARGA_HORARIA'] > 100 ) {
			$form->getElement('VL_CARGA_HORARIA')->addError(
					"A carga horária total do módulo não pode ser maior que 100");
			$flag = false;
		}

		//Validando E7
		if ( $arParams['VL_CARGA_DISTANCIA'] == 0 ) {
			$form->getElement('VL_CARGA_DISTANCIA')->addError(
					"A carga horária a distância do módulo não pode ser igual a zero");
			$flag = false;
		}

		//Validando E11 (VL_MAX_CONCLUSAO não pode ser igual a zero)
		if ( ( $arParams['VL_CARGA_HORARIA'] / $arParams['VL_MAX_CONCLUSAO'] ) > 24 ) {
			$form->getElement('VL_MAX_CONCLUSAO')->addError('Quantidade de dias incoerente com a carga horária total.');
			$flag = false;
		}

		//Validando E12
		if ( ( $arParams['VL_CARGA_DISTANCIA'] + $arParams['VL_CARGA_PRESENCIAL'] ) != $arParams['VL_CARGA_HORARIA'] ) {
			$form->getElement('VL_CARGA_HORARIA')->addError(
					'A soma da "Carga horária presencial" e da "Carga horária a distância" deve ser igual a "Carga horária total"');
			$flag = false;
		}

		return $flag;
	}

	/**
	 * Valida as regras de pre requisito e de min e max para conclusao.
	 * @param $form
	 * @param $arParams
	 */
	private function validaConclusao( $form, $arParams ) {
		$flag = true;

		//Validando E8
		if ( $arParams['DS_PREREQUISITO_MODULO'] == 'S' && $arParams['NU_SEQ_MODULO_PREREQUISITO'] == '' ) {
			$form->getElement('NU_SEQ_MODULO_PREREQUISITO')->addError(
					'Se existe um módulo pré-requisito, por favor informe-o ou marque o campo "Pré-requisito" como NÃO.');
			$flag = false;
		}

		//Validando E14
		if ( $arParams['VL_MIN_CONCLUSAO'] == 0 ) {
			$form->getElement('VL_MIN_CONCLUSAO')->addError('O mínimo para conclusão não pode ser igual a zero');
			$flag = false;
		}

		//Validando E15
		if ( $arParams['VL_MAX_CONCLUSAO'] == 0 ) {
			$form->getElement('VL_MAX_CONCLUSAO')->addError('O máximo para conclusão não pode ser igual a zero');
			$flag = false;
		}

		return $flag;
	}

	/**
	 * Método para gravar os dados de um módulo no banco de dados.
	 *
	 * @since 18/04/2012
	 */
	public function salvarModuloAction() {

		$this->setTitle('Módulo');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'modulo', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'modulo', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams());

		if ( $form->getElement('DS_PREREQUISITO_MODULO')->getValue() != 'S' ) {
			$form->getElement('NU_SEQ_MODULO_PREREQUISITO')->setRequired(false);
		}

		if ( !$form->isValid($_POST) ) {
			$form->getElement('NU_SEQ_MODULO_PREREQUISITO')->setRequired(true);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			return $this->render('form');
		} else {
			$form->getElement('NU_SEQ_MODULO_PREREQUISITO')->setRequired(true);
		}

		//Recupera os parâmetros do request
		$arParams = $this->_request->getParams();

		if ( $this->validarFormulario($form, $arParams) ) {

			$arModulo = array();

			if ( $arParams['NU_SEQ_MODULO'] != null ) {
				$arModulo['NU_SEQ_MODULO'] = $arParams['NU_SEQ_MODULO'];
			}
			$arModulo['NU_SEQ_TIPO_CURSO'] = $arParams['NU_SEQ_TIPO_CURSO'];
			$arModulo['DS_SIGLA_MODULO'] = trim($arParams['DS_SIGLA_MODULO']);
			$arModulo['DS_NOME_MODULO'] = trim($arParams['DS_NOME_MODULO']);
			$arModulo['VL_CARGA_HORARIA'] = $arParams['VL_CARGA_HORARIA'];
			$arModulo['VL_CARGA_PRESENCIAL'] = $arParams['VL_CARGA_PRESENCIAL'];
			$arModulo['VL_CARGA_DISTANCIA'] = $arParams['VL_CARGA_DISTANCIA'];
			$arModulo['ST_MODULO'] = $arParams['ST_MODULO'];
			$arModulo['DS_PREREQUISITO_MODULO'] = $arParams['DS_PREREQUISITO_MODULO'];
			$arModulo['NU_SEQ_MODULO_PREREQUISITO'] = $arParams['NU_SEQ_MODULO_PREREQUISITO'];
			$arModulo['VL_MIN_CONCLUSAO'] = $arParams['VL_MIN_CONCLUSAO'];
			$arModulo['VL_MAX_CONCLUSAO'] = $arParams['VL_MAX_CONCLUSAO'];
			$arModulo['DS_CONTEUDO_PROGRAMATICO'] = $arParams['DS_CONTEUDO_PROGRAMATICO'];

			$obModelo = new Fnde_Sice_Model_Modulo();

			try {
				if ( $arModulo['NU_SEQ_MODULO'] == null ) {
					$obModelo->insert($arModulo);
				} else {
					$where = "NU_SEQ_MODULO = " . $arModulo['NU_SEQ_MODULO'];
					$obModelo->update($arModulo, $where);
				}

				$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Ação realizada com sucesso');
				$this->_redirect("/manutencao/modulo/list");

			} catch ( Exception $e ) {
				$this->trataException($form, $e);
			}
		}
		$this->view->form = $form;
		return $this->render('form');
	}

	private function trataException( $form, $e ) {
		if ( strpos($e->getMessage(), 'ORA-00001', 0) ) {
			if ( strpos($e->getMessage(), 'SICE_FNDE.SMDL_UK_01', 0) ) { //Validando E10
				$form->getElement('DS_SIGLA_MODULO')->addError('A sigla do módulo já está cadastrada');
			} elseif ( strpos($e->getMessage(), 'SICE_FNDE.SMDL_UK_02', 0) ) { //Validando E9
				$form->getElement('DS_NOME_MODULO')->addError('O nome do módulo já está cadastrado');
			}

			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
		} else {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}
	}

	/**
	 * Método para limpar uma pesquisa feita anteriormente.
	 */
	public function clearSearchAction() {

		//limpa sessão
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessão
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * Método para recuperar os parâmetros de uma pesquisa.
	 */
	public function getSearchParamModulo() {
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
