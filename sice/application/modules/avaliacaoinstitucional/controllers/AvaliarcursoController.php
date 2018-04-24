<?php

/**
 * Controller do Avaliar curso
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class AvaliacaoInstitucional_AvaliarCursoController extends Fnde_Sice_Controller_Action {

	protected $_stSistema = 'sice';

    public function indexAction(){
        $this->_redirect('/avaliacaoinstitucional/avaliarcurso/form');
    }

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 22/06/2012
	 */
	public function formAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Avaliar curso');
		$this->setSubtitle('Avaliar');

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();
		$this->view->form = $form;

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarAvaliacaoAction();
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

		$params = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();

		/*
		 * Verifica o perfil do usuário logado...
		 * se ele for Coordenador Nacional Administrador, Coordenador Nacional Equipe ou  Coordenador Executivo Estadual
		 * é permitida a edição a qualquer momento
		 */

		$objUsuario = new Fnde_Sice_Business_Usuario();
		$edicaoPermitida = false;
        
		if
		(
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $usuarioLogado->credentials) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $usuarioLogado->credentials) ||
		 	in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $usuarioLogado->credentials)
		){
            $arUsuario = $objUsuario->getUsuarioById($params['NU_SEQ_USUARIO']);
			$edicaoPermitida = true;
		}else if(
		    in_array(Fnde_Sice_Business_Componentes::CURSISTA, $usuarioLogado->credentials)
        ){
            // se for cursista
    		$arUsuario = $objUsuario->getUsuarioByCpf($usuarioLogado->cpf);
        }


		$objAvaliaCurso = new Fnde_Sice_Business_AvaliacaoCurso();

		$objTurma = new Fnde_Sice_Business_Turma();

		if ( $params['NU_SEQ_TURMA'] ) {
			$infoTurma = $objTurma->pesquisaTurma(array('NU_SEQ_TURMA' => $params['NU_SEQ_TURMA']), false);
			$infoTurma = $infoTurma[0];

			$arAux = $objTurma->getTurmaById($params['NU_SEQ_TURMA']);
			$infoTurma['NU_SEQ_CURSO'] = $arAux['NU_SEQ_CURSO'];

			$objPeriodo = new Fnde_Sice_Business_PeriodoVinculacao();
			$arPeriodo = $objPeriodo->getDatasPeriodoById(
					array('NU_SEQ_PERIODO_VINCULACAO' => $arAux['NU_SEQ_PERIODO_VINCULACAO']));
			$p = explode("/", $arPeriodo['DT_FINAL']);
			$infoTurma['MES_REFERENCIA'] = $p[1] . "/" . $p[2];

		} else {

			if($arUsuario['NU_SEQ_USUARIO']){
				$infoTurma = $objTurma->getTurmaAtivaByIdCursista($arUsuario['NU_SEQ_USUARIO']);
			}else {
				$infoTurma = $objTurma->getTurmaAtivaByIdCursista($params['NU_SEQ_USUARIO']);
			}

			if(!$infoTurma){
				$this->addMessage(Fnde_Message::MSG_ERROR,
					"O cursista não esta vinculado a turma alguma.");
				$this->_redirect('index');
			}
		}

//		Permissão para efetuar avalização - Negada por Status da Turma
		if ( !$infoTurma && !$edicaoPermitida) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"A avaliação do curso deverá ser realizada quando o status da turma for: Ativa, Em Avaliação, Finalizada Atrasada ou Finalizada.");

			if ( $params['NU_SEQ_TURMA'] ) {
				$this->_redirect('/secretaria/emitircertificado/list');
			} else {
				$this->_redirect('index');
			}

//		Permissão para efetuar avalização - Negada Fora do Período
		}else{
			$data_fim = $infoTurma['DT_FIM'];
			list($dia, $mes, $ano) = explode('/', $data_fim);
			$data_en = sprintf('%d/%d/%d', $mes, $dia, $ano);
			$timeInicioAvaliacao = strtotime($data_en . ' -15 day');

			$data_Atual = date('d/m/y');
			list($dia, $mes, $ano) = explode('/', $data_Atual);
			$data_en = sprintf('%d/%d/%d', $mes, $dia, $ano);
			$timeDataAtual = strtotime($data_en);
			if($timeDataAtual < $timeInicioAvaliacao && !$edicaoPermitida) {
				$this->addMessage(Fnde_Message::MSG_ERROR,
					"A avaliação do curso deverá ser realizada a partir de 15 dias antes da data fim prevista da turma.");

				$this->_redirect('index');
			}
		}

		if ( $objAvaliaCurso->isCursoAvaliadoCursista($infoTurma['NU_SEQ_TURMA'], $arUsuario['NU_SEQ_USUARIO']) && !$edicaoPermitida) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "A avaliação para este curso já foi realizada.");

			if ( $params['NU_SEQ_TURMA'] ) {
				$this->_redirect('/secretaria/emitircertificado/list');
			} else {
				$this->_redirect('index');
			}
		}

		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($infoTurma['NU_SEQ_TURMA']);

		$arDados['NU_SEQ_TURMA'] = $infoTurma['NU_SEQ_TURMA'];
		$arDados['NU_SEQ_USUARIO'] = $arUsuario['NU_SEQ_USUARIO'];

		/*
		 * verifica se a avaliação já foi efetuada
		 */
		if($arUsuario['NU_SEQ_USUARIO']){
			$idUser = $arUsuario['NU_SEQ_USUARIO'];
		}else {
			$idUser = $params['NU_SEQ_USUARIO'];
		}
		if($objAvaliaCurso->avaliacaoCursoExistentePorTurmaECursista($arDados['NU_SEQ_TURMA'], $idUser)){
			$this->addMessage(Fnde_Message::MSG_ERROR, "A avaliação para este curso já foi realizada.");
			$this->_redirect('/manutencao/usuario/list');
		}

		if ( $params['NU_SEQ_TURMA'] ) {
			$arExtra['NU_SEQ_TURMA'] = $params['NU_SEQ_TURMA'];
		}

		$form = new AvaliarCurso_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/avaliacaoinstitucional/avaliarcurso/form/' . ($edicaoPermitida ? "NU_SEQ_USUARIO/{$arUsuario['NU_SEQ_USUARIO']}" : ''))->setMethod(
				'post');

		$htmlTurma = $form->getElement("htmlDadosTurma");
		$strTurma = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$htmlTurma->setValue($strTurma);

		$arUsuario['MES_REFERENCIA'] = $infoTurma['MES_REFERENCIA'];
		$htmlAavaliador = $form->getElement("htmlDadosAvaliador");
		$strAvaliador = $this->retornaHTMLAvaliador($arUsuario);
		$htmlAavaliador->setValue($strAvaliador);

		return $form;
	}

	/**
	 * Retorna HTML com os dados do avaliador.
	 * @param array $arUsuario Parametros do usuario.
	 */
	private function retornaHTMLAvaliador( $arUsuario ) {

		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption>Dados do Avaliador</caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width: 25%;'>";
		$html .= "CPF";
		$html .= "</td>";
		$html .= "<td style='width: 25%;' >";
		$html .= "<b>" . Fnde_Sice_Business_Componentes::formataCpf($arUsuario['NU_CPF']) . "</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4; width: 25%;'>";
		$html .= "Situação do avaliador";
		$html .= "</td>";
		$html .= "<td style='width: 25%;'>";
		$html .= "<b>"
				. ( $arUsuario['ST_USUARIO'] == "A" ? "ATIVO"
						: ( $arUsuario['ST_USUARIO'] == "D" ? "DESATIVADO" : "LIBERAÇÃO PENDENTE" ) ) . "</b>";

		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Nome";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arUsuario['NO_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Mês referência";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arUsuario['MES_REFERENCIA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "Código";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arUsuario['NU_SEQ_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arUsuario['SG_UF_ATUACAO_PERFIL']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div>";

		return $html;
	}

	/**
	 * Salva a avaliacao do curso.
	 */
	public function salvarAvaliacaoAction() {

		$form = $this->getForm($this->_request->getParams());

		$htmlTurma = $form->getElement('htmlDadosTurma')->getValue();
		$htmlAavaliador = $form->getElement('htmlDadosAvaliador')->getValue();

		if ( !$form->isValid($_POST) ) {
			$form->getElement('htmlDadosTurma')->setValue($htmlTurma);
			$form->getElement('htmlDadosAvaliador')->setValue($htmlAavaliador);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			return;
		}

		$objAvaliaCurso = new Fnde_Sice_Business_AvaliacaoCurso();

		$db = Zend_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try {

			$arParam = $this->_request->getParams();

			$arInsert['NU_SEQ_TURMA'] = $arParam['NU_SEQ_TURMA'];
			$arInsert['NU_SEQ_USUARIO'] = $arParam['NU_SEQ_USUARIO'];
			$arInsert['NU_QUESTAO_1'] = $arParam['NU_QUESTAO_1'];
			$arInsert['NU_QUESTAO_2'] = $arParam['NU_QUESTAO_2'];
			$arInsert['NU_QUESTAO_3'] = $arParam['NU_QUESTAO_3'];
			$arInsert['NU_QUESTAO_4'] = $arParam['NU_QUESTAO_4'];
			$arInsert['NU_QUESTAO_5'] = $arParam['NU_QUESTAO_5'];
			$arInsert['NU_QUESTAO_6'] = $arParam['NU_QUESTAO_6'];
			$arInsert['NU_QUESTAO_7'] = $arParam['NU_QUESTAO_7'];
			$arInsert['NU_QUESTAO_8'] = $arParam['NU_QUESTAO_8'];
			$arInsert['NU_QUESTAO_9'] = $arParam['NU_QUESTAO_9'];
			$arInsert['NU_QUESTAO_10'] = $arParam['NU_QUESTAO_10'];

			$obTurma = new Fnde_Sice_Business_Turma();
			$turma = $obTurma->getTurmaById($arParam['NU_SEQ_TURMA']);
			$cursistaTurma = new Fnde_Sice_Business_VincCursistaTurma();

			$arInsert2 = array();
			foreach($arInsert as $k => $v ){
				$arInsert2[$k] = (int) $v;
			}

			$objAvaliaCurso->salvarAvaliacaoCurso($arInsert);

			//Turma com situação finalizada
			if ( in_array($turma['ST_TURMA'], array(
				Fnde_Sice_Business_Turma::ATIVA,
				Fnde_Sice_Business_Turma::EM_AVALIACAO,
				Fnde_Sice_Business_Turma::FINALIZACAO_ATRASADA,
			)) ) {
				$cursistaTurma->setNotaCursista($arParam['NU_SEQ_USUARIO'], $turma['NU_SEQ_TURMA'], Fnde_Sice_Business_AvaliacaoCurso::NOTA_BONUS_AVALIACAO_INSTITUCIONAL, false);
			} else {
				$cursistaTurma->setNotaCursista($arParam['NU_SEQ_USUARIO'], $turma['NU_SEQ_TURMA'], 0, false);
			}

			$db->commit();

			$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Curso avaliado com sucesso.');

			if ( $arParam['NU_SEQ_TURMA_URL'] ) {
				$this->_redirect('/secretaria/emitircertificado/list');
			} else {
				$this->_redirect('index');
			}

		} catch ( Exception $e ) {

			$db->rollBack();

			$form->getElement('htmlDadosTurma')->setValue($htmlTurma);
			$form->getElement('htmlDadosAvaliador')->setValue($htmlAavaliador);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
		}

	}
}
