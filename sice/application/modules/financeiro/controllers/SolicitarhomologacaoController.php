<?php

/**
 * Controller do Solicitar Homologação
 * 
 * @author diego.matos
 * @since 04/07/2012
 */

class Financeiro_SolicitarHomologacaoController extends Fnde_Sice_Controller_Action {

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 26/06/2012
	 */
	public function formAction() {
		try {
			$arSessao = $_SESSION['searchParam']['param'];
	
			//5 - Avaliada;
			if ( $arSessao['ST_BOLSA'] != "5" ) {
				$this->addMessage(Fnde_Message::MSG_ERROR,
						"A ação Solicitar Homologação não pode ser executada, pois a (s) bolsa (s) selecionada (s) está com a situação "
								. Fnde_Sice_Business_Componentes::nomeSituacaoBolsa($arSessao['ST_BOLSA']) . ".");
				$this->_redirect("/financeiro/bolsa/list");
			}
	
			$this->setTitle('Bolsas');
			$this->setSubtitle('Solicitar homologação');
	
			//monta menu de contexto
			$menu = array($this->getUrl('financeiro', 'bolsa', 'list', ' ') => 'filtrar');
			$this->setActionMenu($menu);
	
			//Montando a mensagem de orientação
			$msgOrientacao = "Para solicitar homologação das bolsas, selecione a opção ao lado do perfil.";
	
			$this->addInstantMessage(Fnde_Message::MSG_INFO, $msgOrientacao);
	
			//Recupera o objeto de formulário para validação
			$form = $this->getForm();
	
			$this->view->form = $form;
			return $this->render('form');
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
			return $this->render('form');
		}
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 26/06/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		try {
			$businessRegiao = new Fnde_Sice_Business_Regiao();
			$businessBolsa = new Fnde_Sice_Business_Bolsa();
			$businessSituacaoBolsa = new Fnde_Sice_Business_SituacaoBolsa();
	
			$params = $this->_getAllParams();
			
			if(isset( $params['IDENTIFICADOR_LINHA'] )) {
				$params['identificador_linha'] =  $params['IDENTIFICADOR_LINHA'];
			}
			
			if ( $params['identificador_linha'] ) {
				$_SESSION['IDENTIFICADOR_LINHA_SOLI_HOMOL'] = $params['identificador_linha'];
			} else {
				$params['identificador_linha'] = $_SESSION['IDENTIFICADOR_LINHA_SOLI_HOMOL'];
			}
			
			if ( !is_array($params['identificador_linha']) ) {
				$identificador = explode("-", $params['identificador_linha']);
				$params['UF_BOLSISTAS'] = $identificador[0];
				$params['PERFIL_BOLSISTAS'] = $identificador[1];
				$params['PERIODO_BOLSISTAS'] = $identificador[2];
			} else {
				for ( $i = 0; $i < count($params['identificador_linha']); $i++ ) {
					$identificador = explode("-", $params['identificador_linha'][$i]);
					$params['UF_BOLSISTAS'] = $identificador[0];
					$params['PERFIL_BOLSISTAS'][$i] = $identificador[1];
					$params['PERIODO_BOLSISTAS'][$i] = $identificador[2];
					$identificador = null;
				}
			}
			
			$form = new SolicitarHomologacao_Form($arDados);
			$form->setDecorators(array('FormElements', 'Form'));
			$form->setAction($this->view->baseUrl() . '/index.php/financeiro/solicitarhomologacao/solicitar-homologacao')->setMethod(
					'post');
			
			//Recupera os dados de pesquisa da tela de filtrar bolsas para montar o HTML de Período.
			$arPeriodo = $_SESSION['searchParam']['param'];
	
			$businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
			$periodo = $businessPeriodoVinc->getDatasPeriodoById(
					array("NU_SEQ_PERIODO_VINCULACAO" => $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']));
			$arPeriodo['DT_INICIAL'] = $periodo['DT_INICIAL'];
			$arPeriodo['DT_FINAL'] = $periodo['DT_FINAL'];
			$situacao = $businessSituacaoBolsa->getSituacaoBolsaById($arPeriodo['ST_BOLSA']);
			$arPeriodo['DS_SITUACAO_BOLSA'] = $situacao['DS_SITUACAO_BOLSA'];
			$arPeriodo['MES_REFERENCIA'] = substr($arPeriodo['DT_FINAL'], -7);
			$params['SITUACAO_BOLSISTAS'] = $arPeriodo['ST_BOLSA'];
			
			//Preparando a concatenação das UFs dos Usuários.
			$arPeriodo['SG_UF'] = $params['UF_BOLSISTAS'];
			$result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arPeriodo['SG_UF']));
			$arPeriodo['NO_REGIAO'] = $result['NO_REGIAO'];
			
			//Cria HTML com os dados do Período de Vinculação.
			$htmlPeriodo = $form->getElement("htmlPeriodo");
			$strDadosPeriodo = $this->view->retornaHtmlPeriodo($arPeriodo);
			$htmlPeriodo->setValue($strDadosPeriodo);
			
			if($businessBolsa->isBolsaAntiga($arPeriodo['NU_SEQ_PERIODO_VINCULACAO'])){
				$arBolsistas = $businessBolsa->pesquisarBolsasSolicitHomol($params['UF_BOLSISTAS'],
						$params['PERFIL_BOLSISTAS'], $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']);
			}else{
				$arBolsistas = $businessBolsa->pesquisarBolsasAvaliacao($params);
			}
			
			$htmlBolsistas = $form->getElement("htmlBolsistas");
			$strDadosBolsistas = $this->retornaHtmlBolsistas($arBolsistas);
			$htmlBolsistas->setValue($strDadosBolsistas);
	
			return $form;
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
			return $this->render('form');
		}
	}

	/**
	 * Retorna o HTML da tabela com os dados dos bolsista.
	 * @param array $arBolsistas Dados dos bolsistas.
	 * @return string HTML.
	 */
	public function retornaHtmlBolsistas( $arBolsistas ) {
		$html = "<div class='listagem datatable'>";
		$html .= "<table id='tbBolsistas'>";
		$html .= "	<caption><i>Listagem dos bolsistas para homologação</i></caption>";
		$html .= "	<thead><tr>";
		$html .= "		<th style='text-align: center'></th>";
		$html .= "		<th>Perfil</th>";
		$html .= "		<th>UF</th>";
		$html .= "		<th>Nome</th>";
		$html .= "		<th>CPF</th>";
		$html .= "		<th>Montante por bolsista</th>";
		$html .= "		<th>Quantidade de bolsas</th>";
		$html .= "		<th style='text-align: center' >Situação</th>";
		$html .= "		<th style='text-align: center' >Ações</th>";
		$html .= "	</tr></thead>";
		$html .= "	<tbody>";
		foreach ( $arBolsistas as $bolsista ) {
			$html .= "<tr>";
			$html .= "	<td><center><input type='checkbox' " . ( $bolsista['ST_APTIDAO'] == "I" ? 'disabled' : "" )
					. " name='NU_SEQ_BOLSA[]' value='" . $bolsista['NU_SEQ_BOLSA'] . "' /></center></td>";
			$html .= "	<td>" . $bolsista['DS_TIPO_PERFIL'] . "</td>";
			$html .= "	<td>" . $bolsista['SG_UF_ATUACAO_PERFIL'] . "</td>";
			$html .= "	<td>" . $bolsista['NO_USUARIO'] . "</td>";
			$html .= "	<td>" . Fnde_Sice_Business_Componentes::formataCpf($bolsista['NU_CPF']) . "</td>";
			$html .= "	<td style='text-align: right'>" . 'R$ '
					. number_format(( float ) $bolsista['VL_BOLSA'], 2, ',', '.') . "</td>";
			$html .= "	<td style='text-align: center' >" . $bolsista['QTD_BOLSA'] . "</td>";
			$html .= "	<td><center>" . $bolsista['DS_SITUACAO_BOLSA'] . "</center></td>";
			$html .= "	<td style='text-align: center'>";
			$html .= "	<a title='Detalhes' class='icoVisualizar' id='detalhesHomologar' href='"
					. $this->view->baseUrl() . "/index.php/financeiro/detalhesbolsista/form/NU_SEQ_BOLSA/"
					. $bolsista['NU_SEQ_BOLSA'] . "/NU_SEQ_TIPO_PERFIL/" . $bolsista['NU_SEQ_TIPO_PERFIL'] . "'/>";
			$html .= "	<a title='Devolver p/ Avaliação' class='icoReceber' id='devolverHomologar' href='"
					. $this->view->baseUrl() . "/index.php/financeiro/motivodevolucao/form/NU_SEQ_BOLSA/"
					. $bolsista['NU_SEQ_BOLSA'] . "'/>";
			$html .= "	</td>";
			$html .= "</tr>";
		}
		$html .= "	</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Método para efetuar solicitação de homologação da Bolsa.
	 */
	public function solicitarHomologacaoAction() {

		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessBolsa = new Fnde_Sice_Business_Bolsa();

		$arParams = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$resultUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		if ( !$arParams['NU_SEQ_BOLSA'] ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"Selecione pelo menos uma bolsa para realizar a solicitação de homologação.");
			$this->_redirect("/financeiro/solicitarhomologacao/form{$arParams['NU_SEQ_BOLSA_URL']}");
		} else {
			$arParams['ST_BOLSA'] = 8;
			try {
				for ( $i = 0; $i < count($arParams['NU_SEQ_BOLSA']); $i++ ) {
					$arParamsProvisorio['ST_BOLSA'] = $arParams['ST_BOLSA'];
					if ( is_array($arParams['NU_SEQ_BOLSA']) ) {
						$arParamsProvisorio['NU_SEQ_BOLSA'] = $arParams['NU_SEQ_BOLSA'][$i];
					} else {
						$arParamsProvisorio['NU_SEQ_BOLSA'] = $arParams['NU_SEQ_BOLSA'];
					}
					//Alterar status da bolsa
					$businessBolsa->alterarStatusBolsa($arParamsProvisorio);
					//Monta o array de histórico
					$arHistorico = array('NU_SEQ_BOLSA' => $arParamsProvisorio['NU_SEQ_BOLSA'],
							'NU_SEQ_USUARIO' => $resultUsuario['NU_SEQ_USUARIO'],
							'DT_HISTORICO' => date('d/m/Y G:i:s'), 'ST_BOLSA' => $arParamsProvisorio['ST_BOLSA']);
					//Salvar histórico
					$businessBolsa->salvarHistoricoBolsa($arHistorico);
					$arParamsProvisorio = array();
				}

			} catch ( Exception $e ) {
				$this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao tentar solicitar homologação.");
				$this->_redirect("/financeiro/solicitarhomologacao/form{$arParams['NU_SEQ_BOLSA_URL']}");
			}
			$this->addMessage(Fnde_Message::MSG_SUCCESS,
					"Solicitação de homologação(s) da(s) bolsa(s) executada com sucesso.");
			$this->_redirect("/financeiro/bolsa/list");
		}
	}
}
