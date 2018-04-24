<?php

/**
 * Controller do AvaliarCursista
 * 
 * @author vinicius.can�ado
 * @since 10/09/2012
 */

class Secretaria_NotificarCursistaController extends Fnde_Sice_Controller_Action {

	// Identificador do valor de turma ativa.
	const TURMA_ATIVA = '4';

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author vinicius.cancado
	 * @since 10/09/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		//Recuperando par�metros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new NotificarCursista_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		//$form->setAction($this->view->baseUrl() . '/index.php/secretaria/notificarcursista/notificar')->setMethod('post')->setAttrib('id', 'form');

		$html = $form->getElement("htmlTurma");
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$html->setValue($str);
		return $form;

	}

	/**
	 * Carrega tela de notificar cursista.
	 */
	public function notificarcursistaAction() {
		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$this->setTitle('Avaliar Curso');
		$this->setSubtitle('Notificar cursistas');

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		if ( isset($arParam['NU_SEQ_TURMA']) ) {
			$business = new Fnde_Sice_Business_VincCursistaTurma();
			$rsRegistros = $business->retornaCursistasTurma($arParam['NU_SEQ_TURMA']);
		} else {
			$rsRegistros = array();
		}

		$arrHeader = array('<center>Matricula</center>', '<center>Nome</center>', '<center>CPF</center>',
				'<center>Sit. da Notifica��o</center>', '<center>Sit. da avalia��o</center>');

		$grid = new Fnde_Sice_View_Helper_DataTables();

		$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
				'Listagem de Cursistas')->setId('NU_MATRICULA')->setRowInput(
				Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'tbCursista'));

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Carrega tela de notificar cursista a partir de Emitir certificado.
	 */
	public function notificarcursistaemitircertAction() {
		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$this->setTitle('Avaliar Curso');
		$this->setSubtitle('Notificar cursistas');

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		if ( isset($arParam['NU_SEQ_TURMA']) ) {
			$business = new Fnde_Sice_Business_VincCursistaTurma();
			$rsRegistros = $business->retornaCursistasTurma($arParam['NU_SEQ_TURMA']);
		} else {
			$rsRegistros = array();
		}

		$arrHeader = array('<center>Matricula</center>', '<center>Nome</center>', '<center>CPF</center>',
				'<center>Sit. da Notifica��o</center>', '<center>Sit. da avalia��o</center>');

		$grid = new Fnde_Sice_View_Helper_DataTables();

		$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
				'Listagem de Cursistas')->setId('NU_MATRICULA')->setRowInput(
				Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'tbCursista'));

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Retorna os cursistas notificados.
	 */
	public function buscarCursistasNotificadoAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$business = new Fnde_Sice_Business_VincCursistaTurma();
		$retorno = $business->retornaCursistasNotificados($arParam['NU_SEQ_TURMA']);

		$this->_helper->json($retorno);

		return $retorno;
	}

	/**
	 * Retorna os cursistas nao notificados.
	 */
	public function buscarCursistasNaoNotificadoAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$business = new Fnde_Sice_Business_VincCursistaTurma();
		$retorno = $business->retornaCursistasNaoNotificados($arParam['NU_SEQ_TURMA']);

		$this->_helper->json($retorno);

		return $retorno;
	}

	/**
	 * Retorna os cursistas que avaliaram o curso de uma turma.
	 */
	public function getCursistaAvaliouCursoTurmaAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$business = new Fnde_Sice_Business_VincCursistaTurma();
		$retorno = $business->getCursistaAvaliouCursoTurma($arParam['NU_SEQ_TURMA']);

		$this->_helper->json($retorno);

		return $retorno;
	}

	/**
	 * Notifica cursista.
	 */
	public function notificarAction() {
		try {

			//monta menu de contexto
			$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
			$this->setActionMenu($menu);

			$this->setTitle('Avaliar Curso');
			$this->setSubtitle('Notificar cursistas');

			$arParam = $this->_getAllParams();

			$arMatriculasSplit = preg_split('/,/', $arParam['NU_MATRICULA']);

			$bussinesTurma = new Fnde_Sice_Business_Turma();

			$turma = $bussinesTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

			$d = explode("/", $turma['DT_INICIO']);
			$data = $d[2] . "-" . $d[1] . "-" . $d[0];
			$dataTurma = strtotime($data);

			$dataAtual = strtotime(date("Y-m-d"));
			// data atual
			// verifica se a data atual � maior que o in�cio da turma
			if ( ( $dataAtual < $dataTurma ) || $turma['ST_TURMA'] != Secretaria_NotificarCursistaController::TURMA_ATIVA ) {
				$this->addMessage(Fnde_Message::MSG_ERROR,
						"N�o � poss�vel notificar cursista(s) para avalia��o da turma que n�o foi iniciada e/ou n�o est� ativa.");
				$this->_redirect(
						"/secretaria/notificarcursista/notificarcursista/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);

			} else {
				$business = new Fnde_Sice_Business_VincCursistaTurma();

				if ( $this->enviarEmail($arMatriculasSplit, $arParam['NU_SEQ_TURMA']) ) {
					$business->atualisarNotificacaoCursista($arMatriculasSplit, $arParam['NU_SEQ_TURMA']);
				}

				$this->addMessage(Fnde_Message::MSG_SUCCESS, "Notifica��o enviada com sucesso.");
				$this->_redirect("/secretaria/turma/list");
			}

		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect("/secretaria/notificarcursista/notificarcursista/NU_SEQ_TURMA/" . $arParam['NU_SEQ_TURMA']);
		}
	}

	/**
	 * Envia email.
	 * @param $arMatriculasSplit
	 * @param $idturma
	 * @throws Exception
	 */
	public function enviarEmail( $arMatriculasSplit, $idturma ) {
		try {

			$business = new Fnde_Sice_Business_VincCursistaTurma();

			$result = $business->getDadosEmailCursista($arMatriculasSplit, $idturma);

			for ( $i = 0; $i < count($result); $i++ ) {

				$textoEmail = "Prezado(a) Cursista,
<br><br>
O Curso Forma��o de Conselheiros Escolares {$result[$i]['DS_NOME_CURSO']} est� chegando ao fim. O seu Tutor, {$result[$i]['TUTOR']}, te acompanhou nessa jornada de aprendizagem. O Curso teve carga hor�ria total de {$result[$i]['VL_CARGA_HORARIA']} horas, sendo {$result[$i]['VL_CARGA_DISTANCIA']} horas em Ambiente Virtual de Aprendizagem � AVA � e {$result[$i]['VL_CARGA_PRESENCIAL']} horas em Encontros de forma��o presencial.
<br><br>
Agora chegou o momento de voc� realizar a avalia��o do curso. Esta avalia��o do curso, <b>que corresponde a 10% de sua nota final</b>, � essencial para que o Minist�rio da Educa��o (MEC) realize continuamente o aperfei�oamento da qualidade do programa. Sem esta avalia��o, n�o saberemos o que deve ser mantido e o que deve ser mudado.
<br><br>
O endere�o para realiza��o da avalia��o do curso �: https://www.fnde.gov.br/sice
<br><br>
A Coordena��o Nacional do Programa Nacional de Fortalecimento dos Conselhos Escolares agradece.";

				Fnde_Sice_Business_Componentes::sendMail($textoEmail, $result[$i]['DS_EMAIL_USUARIO'],
						$result[$i]['CURSISTA'], 'E-mail de notifica��o do cursista');
			}

			return true;
		} catch ( Exception $e ) {
			throw $e;
		}

	}
}
