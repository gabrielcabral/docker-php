		<?php

/**
 * Controller do VincCursistaTurma
 * 
 * @author rafael.paiva
 * @since 07/05/2012
 */

class Secretaria_VincCursistaTurmaController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function listAction() {
		$this->setTitle('VincCursistaTurma');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'vinccursistaturma', 'list', ' ') => 'filtrar',
				$this->getUrl('secretaria', 'vinccursistaturma', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			parent::setSearchParam();
		}

		//recupera valores da sessão
		$arFilter = parent::getSearchParam();

		$form = $this->getFormFilter();
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->_request->isPost() || isset($arFilter['startlist']) || isset($arFilter['start'])
				|| !empty($arFilter) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_VincCursistaTurma();
				$rsRegistros = $obBusiness->search($form->getValues());
				if ( !count($rsRegistros) ) {
					$this->addInstantMessage(Fnde_Message::MSG_INFO,
							'Não foram encontrados registros para os filtros informados!');
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
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'id' => '')) . '%s',
							'params' => array('NU_MATRICULA'), 'attribs' => array('class' => 'icoEditar')),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(array('action' => 'del-vinccursistaturma', 'NU_MATRICULA' => ''))
									. '%s', 'params' => array('NU_MATRICULA'),
							'attribs' => array('class' => 'icoExcluir excluir',
									'mensagem' => 'Confirma a exclusão das informações a regional selecionada?')));

			$arrHeader = array('nuSeqTurma', 'nuSeqUsuarioCursista',);

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setTitle('VincCursistaTurma')->setRowAction(
					$rowAction)->setId('NU_MATRICULA')->setColumnsHidden(array('NU_MATRICULA'))->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Remove um registro de VincCursistaTurma
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function delVincCursistaTurmaAction() {
		$arParam = $this->_getAllParams();

		$obVincCursistaTurma = new Fnde_Sice_Business_VincCursistaTurma();
		$resposta = $obVincCursistaTurma->del($arParam['NU_MATRICULA']);

		$resposta = ( string ) $resposta;

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Operação realizada com sucesso!");
		} elseif ( $resposta == '0' ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Exclusão do registro já realizada.");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"VincCursistaTurma não pode ser excluído, pois o mesmo está associado." . $resposta);
		}

		$this->_redirect("/secretaria/vinccursistaturma/list");
	}

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function formAction() {
		$this->setTitle('VincCursistaTurma');
		$this->setSubtitle('Cadastro');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'vinccursistaturma', 'list', ' ') => 'filtrar',
				$this->getUrl('secretaria', 'vinccursistaturma', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		$arDados = $this->getArDadosFormulario();

		//Recuperando array de dados extras para setar valores extras no formulário
		$arExtra = $this->getArExtraFormulario();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados, $arExtra);

		if ( $arDados['NU_MATRICULA'] ) {
			$this->view->form = $form->populate($arDados);
		} else {
			$this->view->form = $this->getForm();
		}
		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		//Recuperando parâmetros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new VincCursistaTurma_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/vinccursistaturma/salvar')->setMethod('post')->setAttrib(
				'id', 'form');

		$html = $form->getElement("htmlTurma");
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$html->setValue($str);

		$htmlAlunosMatriculados = $form->getElement("htmlAlunosMatriculados");
		$strAlunosMatriculados = $this->retornaHtmlAlunosMatriculados($params['NU_SEQ_TURMA']);
		$htmlAlunosMatriculados->setValue($strAlunosMatriculados);

		return $form;
	}

	/**
	 * Retorna HTML de alunos matriculados na turma.
	 * @param $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();

		$arCursistaMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$arCursistasAdicionados = $_SESSION['rsCursista'];
		$html .= "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th style='text-align:center' >Contagem</th><th style='text-align:center' >Matrícula</th><th style='text-align:center' >Nome</th><th style='text-align:center' >CPF</th><th style='text-align:center' >Ações</th></tr></thead>";
		$html .= "<tbody>";

		$count = 0;

		//Preenche os alunos já matriculados na turma.
		if ( isset($arCursistaMatriculados) ) {
			foreach ( $arCursistaMatriculados as $cursista ) {
				$html .= "<tr><td>" . ++$count . "</td><td>" . $cursista['NU_MATRICULA'] . "</td><td>"
						. $cursista['NO_USUARIO'] . "</td><td>"
						. Fnde_Sice_Business_Componentes::formataCpf($cursista['NU_CPF']) . "</td>"
						. "<td class='icons'><a href='" . $this->view->baseUrl()
						. "/index.php/secretaria/vinccursistaturma/remover-cursista-matriculado/"
						. "NU_MATRICULA/{$cursista['NU_MATRICULA']}/NU_SEQ_TURMA/$codTurma'"
						. "class='icoExcluir excluir' mensagem='Tem certeza que deseja remover o cursista?' >"
						. "</a></td></tr>";
			}
		}

		//Preenche os alunos adicionados à turma, porém ainda não confirmados. Salvos na sessão.
		if ( isset($arCursistasAdicionados) ) {
			foreach ( $arCursistasAdicionados as $cursista ) {
				$html .= "<tr><td>" . ++$count . "</td><td>" . $cursista['NU_MATRICULA'] . "</td><td>"
						. $cursista['NO_USUARIO'] . "</td><td>"
						. Fnde_Sice_Business_Componentes::formataCpf($cursista['NU_CPF']) . "</td>"
						. "<td class='icons'><a href='" . $this->view->baseUrl()
						. "/index.php/secretaria/vinccursistaturma/remover-cursista-adicionado/"
						. "NU_CPF/{$cursista['NU_CPF']}/NU_SEQ_TURMA/$codTurma'"
						. "class='icoExcluir excluir' mensagem='Tem certeza que deseja remover o cursista?'>"
						. "</a></td></tr>";
			}
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Metodo acessorio get de namelist.
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Metodo acessorio set de namelist.
	 * @param array $arList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Metodo acessorio get de titles.
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Metodo acessrio set de titles.
	 * @param array $arTitles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Retorna o formulario de filtro.
	 * @param array $arDados
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new VincCursistaTurma_FormFilter();
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/vinccursistaturma/list')->setMethod('post');

		return $form;
	}

	/**
	 * Carrega tela principal.
	 */
	public function carregarTurmaAction() {

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$this->setTitle('Matricular');
		$this->setSubtitle('Filtrar');

		$arParam = $this->_getAllParams();

		$obBusinessTurma = new Fnde_Sice_Business_Turma();
		$turma = $obBusinessTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		if(
			in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
			) {
			//não verifica situação da turma... em atendimento a demanda FNDE-1832-REQ000000014908
		}else {
			if ($turma['ST_TURMA'] != "1") {
				$this->addMessage(Fnde_Message::MSG_ERROR,
					"A turma selecionada com a situação {$turma['DS_ST_TURMA']} não pode ser executada a ação Matricular Cursista.");
				$this->_redirect("/secretaria/turma/list");
			}
		}

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Salva registro no banco.
	 */
	public function salvarAction() {

		$this->setTitle('Matricular');
		$this->setSubtitle('Filtrar');

		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$arCursista = $_SESSION['rsCursista'];

		try {
			if ( isset($arCursista) ) {
				foreach ( $arCursista as $cursista ) {
					$obj = array("NU_SEQ_TURMA" => $cursista['NU_SEQ_TURMA'],
							"NU_SEQ_USUARIO_CURSISTA" => $cursista['NU_SEQ_USUARIO_CURSISTA'], "ST_NOTIFICADO" => "N",);
					$obModelo->insert($obj);
				}
			}
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Matrícula efetuada com sucesso");
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}

		$_SESSION['rsCursista'] = null;
		$this->_redirect("/secretaria/turma/list");
	}

	/**
	 * Remove cursista matriculado na turma.
	 */
	public function removerCursistaMatriculadoAction() {
		$arParam = $this->_getAllParams();
		$obModelo = new Fnde_Sice_Business_VincCursistaTurma();

		try {
			$obModelo->del($arParam['NU_MATRICULA']);
			$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Cursista removido com sucesso.');
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}
		$this->_redirect("/secretaria/vinccursistaturma/carregar-turma/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
	}

	/**
	 * Remove cursista adicionado na turma.
	 */
	public function removerCursistaAdicionadoAction() {
		$arParam = $this->_getAllParams();
		$arCursistasAdicionados = $_SESSION['rsCursista'];

		if ( isset($arCursistasAdicionados) ) {
			foreach ( $arCursistasAdicionados as $key => $cursista ) {
				if ( $cursista['NU_CPF'] == $arParam['NU_CPF'] ) {
					unset($arCursistasAdicionados[$key]);
					$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Cursista removido com sucesso.');
				}
			}
		}
		$_SESSION['rsCursista'] = $arCursistasAdicionados;
		$this->_redirect("/secretaria/vinccursistaturma/carregar-turma/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
	}
}
