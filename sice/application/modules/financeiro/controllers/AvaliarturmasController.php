<?php

/**
 * Controller do Avaliar Turmas
 * 
 * @author rafael.paiva
 * @since 27/06/2012
 */

class Financeiro_AvaliarTurmasController extends Fnde_Sice_Controller_Action {

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 27/06/2012
	 */
	public function formAction() {
		$this->_helper->layout()->disableLayout();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();

		$this->view->form = $form;

		return $this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 27/06/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$obPeriodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();
		$obUsuario = new Fnde_Sice_Business_Usuario();
		$obTurma = new Fnde_Sice_Business_Turma();
		$obBolsa = new Fnde_Sice_Business_Bolsa();
		$params = $this->_getAllParams();

		$form = new AvaliarTurmas_Form($params);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/financeiro/avaliarturmas/salvar-avaliacao-turma')->setMethod(
				'post')->setAttrib('id', 'form');
		$form->setErrors(array("Justificativa inserida com sucesso."));
		//Recuperando o mês de referência da sessão de pesquisa da tela anterior.
		$arIdPerVincula = array(
				'NU_SEQ_PERIODO_VINCULACAO' => $_SESSION['searchParam']['param']['NU_SEQ_PERIODO_VINCULACAO']);
		$periodoVinculacao = $obPeriodoVinculacao->getDatasPeriodoById($arIdPerVincula);
		$mesReferencia = substr($periodoVinculacao['DT_FINAL'], -7);

		$getBolsa = $obBolsa->getBolsaById($params['NU_SEQ_BOLSA']);

		//Montando a mensagem de orientação
		$msgOrientacao .= "<div id='mensagens'>";
		$msgOrientacao .= "<div class='msgOrientacao'>";
		$msgOrientacao .= "	  	<h3>Orientação</h3>";
		$msgOrientacao .= "		<p>";
		$msgOrientacao .= "			Legenda:";
		$msgOrientacao .= "			<ul> ";
		$msgOrientacao .= "				<li type=disc>AD: quantidade de cursistas aprovados com destaque</li> ";
		$msgOrientacao .= "				<li type=disc>A: quantidade de cursistas aprovados</li> ";
		$msgOrientacao .= "				<li type=disc>R: quantidade de cursistas reprovados</li> ";
		$msgOrientacao .= "				<li type=disc>D: quantidade de cursistas desistentes</li> ";
		$msgOrientacao .= "				<li type=disc>CM: cursistas matriculados</li> ";
		$msgOrientacao .= "			</ul> ";
		$msgOrientacao .= "	  	</p>";
		$msgOrientacao .= "</div> ";
		$msgOrientacao .= "</div> ";

		$htmlOrientacao = $form->getElement("htmlOrientacao");
		$htmlOrientacao->setValue($msgOrientacao);

		//Cria HTML com os dados do Período de Vinculação.
		$tutor = $obUsuario->getUsuarioById($getBolsa['NU_SEQ_USUARIO']);
		$htmlPeriodo = $form->getElement("htmlTutor");
		$strDadosTutor = $this->retornaHtmlTutor($tutor, $mesReferencia);
		$htmlPeriodo->setValue($strDadosTutor);

		//Recupera os dados de pesquisa da tela de filtrar bolsas.
		$arSessao = $_SESSION['searchParam']['param'];
		$arSessao['NU_SEQ_TIPO_PERFIL'] = $params['NU_SEQ_TIPO_PERFIL'];
		
		//Cria HTML com os dados das turmas.
		if($obBolsa->isBolsaAntiga($arSessao['NU_SEQ_PERIODO_VINCULACAO'])){
			$arTurmas = $obTurma->getDadosAvaliarTurmasAntigo($arSessao, $params['NU_SEQ_BOLSA']);
		}else{
			$arTurmas = $obTurma->getDadosAvaliarTurmas($arSessao, $params['NU_SEQ_BOLSA']);
		}
		
		$htmlTurmas = $form->getElement("htmlTurmas");
		$qtTurmas = $form->getElement("QT_TURMAS");
		$qtTurmas->setValue(count($arTurmas));
		$strDadosTurmas = $this->retornaHtmlTurmas($arTurmas);
		$htmlTurmas->setValue($strDadosTurmas);

		return $form;
	}

	/**
	 * Salva a avaliação da turma, no caso de ser aprovada.
	 * Se a turma não for aprovada irá salvar no Motivonaoaprovacao.
	 */
	public function salvarAvaliacaoTurmaAction() {
		$obModelAvaliacaoTurma = new Fnde_Sice_Model_AvaliacaoTurma();
		$obBusinessAvaliacaoTurma = new Fnde_Sice_Business_AvaliacaoTurma();

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		//Recupera os parâmetros do request
		$arParams = $this->_request->getParams();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();

		//Recupera ID do usuario logado no sistema.
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		//Verificando se todas as turmas foram avaliadas. Qtde de turmas no grid deve ser iqual à qtde de turmas avaliadas.
		if ( count($arParams['AVALIACAO']) < $arParams['QT_TURMAS'] ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, "Avalie todas as turmas antes de Confirmar.");

			$this->view->form = $form;
			return $this->render('form');
		}

		$obModelAvaliacaoTurma->getAdapter()->beginTransaction();

		foreach ( $arParams['AVALIACAO'] as $turma => $avaliacao ) {
			//Verificando se a turma 'não' foi aprovada, pois já foi salvo no Motivonaoaprovacao.
			if ( $avaliacao != 'N' ) {
				$arDadosAprovacao = array('NU_SEQ_TURMA' => $turma, 'NU_SEQ_BOLSA' => $arParams['NU_SEQ_BOLSA'],
						'NU_SEQ_USUARIO_AVALIADOR' => $arUsuario['NU_SEQ_USUARIO'], 'ST_APROVACAO' => $avaliacao,
						'NU_SEQ_JUSTIF_REPROV' => null, 'DS_OBSERVACAO' => null, 'DT_AVALIACAO' => date('d/m/Y G:i:s'),);

				try {
					//Verificando se a turma já foi avaliada para atualizar os dados.
					if ( $obBusinessAvaliacaoTurma->getAvaliacaoTurmaById($turma) ) {
						$obModelAvaliacaoTurma->update($arDadosAprovacao, "NU_SEQ_TURMA=" . $turma);
					} else {
						$obModelAvaliacaoTurma->insert($arDadosAprovacao);
					}
				} catch ( Exception $e ) {
					$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
					$obModelAvaliacaoTurma->getAdapter()->rollBack();

					$this->view->form = $form;
					return $this->render('form');
				}
			} else {
				//Verificando se foi informado o motivo de não aprovação da turma avaliada como 'não'.
				if ( !$obBusinessAvaliacaoTurma->getAvaliacaoTurmaById($turma) ) {
					$msg = "A turma $turma foi avaliada como \"Não (N)\", portanto o motivo deve ser justificado.";
					$this->addInstantMessage(Fnde_Message::MSG_ERROR, $msg);
					$obModelAvaliacaoTurma->getAdapter()->rollBack();

					$this->view->form = $form;
					return $this->render('form');
				}
			}
		}
		$obModelAvaliacaoTurma->getAdapter()->commit();

		$this->addMessage(Fnde_Message::MSG_SUCCESS, "Turma(s) avaliada(s) com sucesso.");
		$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Retorna o HTML da tabela com os dados do período de vinculação.
	 * @return string HTML
	 */
	public function retornaHtmlTutor( $tutor, $mesReferencia ) {

		$html .= "<div class='listagem' style='display:inline'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption><i>Dados do Tutor Avaliado</i></caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width:25%;'>";
		$html .= "CPF";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>" . Fnde_Sice_Business_Componentes::formataCpf($tutor['NU_CPF']) . "</b>";
		$html .= "</td>";

		//Formatando a Situação do Usuário
		switch ( $tutor['ST_USUARIO'] ) {
			case 'A': {
					$stUsuario = 'Ativo';
					break;
				}
			case 'D': {
					$stUsuario = 'Inativo';
					break;
				}
			case 'L': {
					$stUsuario = 'Liberação Pendente';
					break;
				}
		}

		$html .= "<td style='background-color:#E6EFF4; width:25%;' >";
		$html .= "Situação do bolsista";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$stUsuario}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Nome";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$tutor['NO_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Mês Referência";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$mesReferencia}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Código do bolsista";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$tutor['NU_SEQ_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4' >";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$tutor['SG_UF_ATUACAO_PERFIL']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div>";

		return $html;
	}

	/**
	 * Retorna o HTML da tabela com os dados dos bolsista.
	 * @param array $arBolsistas Dados dos bolsistas.
	 * @return string HTML.
	 */
	public function retornaHtmlTurmas( $arTurmas ) {

		$count = 0;

		$html = "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "	<caption><i>Listagem das turmas</i></caption>";
		$html .= "	<thead><tr>";
		$html .= "		<th>Curso</th>";
		$html .= "		<th>Qtd módulos</th>";
		$html .= "		<th>Dt. de Início</th>";
		$html .= "		<th>Dt. fim prevista</th>";
		$html .= "		<th>Dt. Finalizada</th>";
		$html .= "		<th>Dias atraso</th>";
		$html .= "		<th style='text-align: center'>AD</th>";
		$html .= "		<th style='text-align: center'>A</th>";
		$html .= "		<th style='text-align: center'>R</th>";
		$html .= "		<th style='text-align: center'>D</th>";
		$html .= "		<th style='text-align: center'>CM</th>";
		$html .= "		<th style='text-align: center'>Aprova?</th>";
		$html .= "	</tr></thead>";
		$html .= "	<tbody>";
		foreach ( $arTurmas as $turma ) {
			if ( $turma['DT_FINALIZACAO'] && $turma['DT_FIM'] ) {
				$dataFinalizacao = new Zend_Date($turma['DT_FINALIZACAO'], 'D/M/Y');
				$dataFimPrevista = new Zend_Date($turma['DT_FIM'], 'D/M/Y');

				// calcula a diferença de dias entre as datas de finalização e fim prevista.
				$diferenca = ( int ) floor(
 ( $dataFinalizacao->getTimestamp() - $dataFimPrevista->getTimestamp() ) / ( 3600 * 24 ));
			} else {
				$diferenca = null;
			}

			$html .= "<tr id='teste'>";
			$html .= "	<td>" . $turma['DS_NOME_CURSO'] . "</td>";
			$html .= "	<td>" . $turma['QTD_MODULO'] . "</td>";
			$html .= "	<td>" . $turma['DT_INICIO'] . "</td>";
			$html .= "	<td>" . $turma['DT_FIM'] . "</td>";
			$html .= "	<td>" . $turma['DT_FINALIZACAO'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $diferenca . "</td>";
			$html .= "	<td style='text-align: center'>" . $turma['AD'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $turma['A'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $turma['R'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $turma['D'] . "</td>";
			$html .= "	<td style='text-align: center'>" . $turma['CM'] . "</td>";
			$html .= "	<td><center>";
			$html .= "	  <input type='radio' name='AVALIACAO[{$turma['NU_SEQ_TURMA']}]' id='AVALIACAO[{$turma['NU_SEQ_TURMA']}]' value='S' "
					. ( ( $turma['ST_APROVACAO'] == 'S' ) ? 'checked' : '' ) . ">S";
			$html .= "	  <input type='radio' name='AVALIACAO[{$turma['NU_SEQ_TURMA']}]' id='AVALIACAO[{$turma['NU_SEQ_TURMA']}]' value='N' "
					. ( ( $turma['ST_APROVACAO'] == 'N' ) ? 'checked' : '' );
			$html .= "	    onclick=\"motivoNaoAprovacao('/index.php/financeiro/motivonaoaprovacao/form/NU_SEQ_TURMA/{$turma['NU_SEQ_TURMA']}/NU_SEQ_BOLSA/{$turma['NU_SEQ_BOLSA']}');\">N";
			$html .= "	</center></td>";
			$html .= "</tr>";

			$count++;
		}

		$html .= "	</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}
}
