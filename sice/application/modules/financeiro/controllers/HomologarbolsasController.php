<?php

/**
 * Controller do Solicitar Homologação
 * 
 * @author diego.matos
 * @since 04/07/2012
 */

class Financeiro_HomologarBolsasController extends Fnde_Sice_Controller_Action {

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

			//8 - Solicita homologação
			if ( $arSessao['ST_BOLSA'] != "8" ) {
				$this->addMessage(Fnde_Message::MSG_ERROR,
						"A ação Homologar não pode ser executada, pois a (s) bolsa (s) selecionada (s) está com a situação "
								. Fnde_Sice_Business_Componentes::nomeSituacaoBolsa($arSessao['ST_BOLSA']) . ".");
				$this->_redirect("/financeiro/bolsa/list");
			}

			$this->setTitle('Bolsas');
			$this->setSubtitle('Homologar');

			//monta menu de contexto
			$menu = array($this->getUrl('financeiro', 'bolsa', 'list', ' ') => 'filtrar');
			$this->setActionMenu($menu);

			//Montando a mensagem de orientação
			$msgOrientacao = "Para homologar as bolsas, selecione a opção ao lado do perfil.";

			$this->addInstantMessage(Fnde_Message::MSG_INFO, $msgOrientacao);

			//Recupera o objeto de formulário para validação
			$form = $this->getForm();

			$this->view->form = $form;
			return $this->render('form');
		} catch ( Exception $e ) {
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

			$arParam = $this->_getAllParams();

			if ( isset($arParam['IDENTIFICADOR_LINHA']) ) {
				$arParam['identificador_linha'] = $arParam['IDENTIFICADOR_LINHA'];
			}

			if ( $arParam['identificador_linha'] ) {
				$_SESSION['IDENTIFICADOR_LINHA_HOMOL_BOLS'] = $arParam['identificador_linha'];
			} else {
				$arParam['identificador_linha'] = $_SESSION['IDENTIFICADOR_LINHA_HOMOL_BOLS'];
			}

			if ( !is_array($arParam['identificador_linha']) ) {
				$identificador = explode("-", $arParam['identificador_linha']);
				$arParam['UF_BOLSISTAS'] = $identificador[0];
				$arParam['PERFIL_BOLSISTAS'] = $identificador[1];
				$arParam['PERIODO_BOLSISTAS'] = $identificador[2];
			} else {
				for ( $i = 0; $i < count($arParam['identificador_linha']); $i++ ) {
					$identificador = explode("-", $arParam['identificador_linha'][$i]);
					$arParam['UF_BOLSISTAS'] = $identificador[0];
					$arParam['PERFIL_BOLSISTAS'][$i] = $identificador[1];
					$arParam['PERIODO_BOLSISTAS'][$i] = $identificador[2];
					$identificador = null;
				}
			}

			$form = new HomologarBolsas_Form($arDados, $arExtra);
			$form->setDecorators(array('FormElements', 'Form'));
			$form->setAction($this->view->baseUrl() . '/index.php/financeiro/homologarbolsas/homologar-bolsas')->setMethod(
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
			$arParam['SITUACAO_BOLSISTAS'] = $arPeriodo['ST_BOLSA'];

			//Preparando a UF dos Usuários selecionados
			$arPeriodo['SG_UF'] = $arParam['UF_BOLSISTAS'];
			//Preparando a Regiao
			$result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arPeriodo['SG_UF']));
			$arPeriodo['NO_REGIAO'] = $result['NO_REGIAO'];

			//Cria HTML com os dados do Período de Vinculação.
			$htmlPeriodo = $form->getElement("htmlPeriodo");
			$strDadosPeriodo = $this->view->retornaHtmlPeriodo($arPeriodo);
			$htmlPeriodo->setValue($strDadosPeriodo);

			if ( $businessBolsa->isBolsaAntiga($arPeriodo['NU_SEQ_PERIODO_VINCULACAO']) ) {
				$arBolsistas = $businessBolsa->pesquisarBolsasHomologacao($arParam['UF_BOLSISTAS'],
						$arParam['PERFIL_BOLSISTAS'], $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']);
			} else {
				$arBolsistas = $businessBolsa->pesquisarBolsasAvaliacao($arParam);
			}

			$htmlBolsistas = $form->getElement("htmlBolsistas");
			$strDadosBolsistas = $this->retornaHtmlBolsistas($arBolsistas);
			$htmlBolsistas->setValue($strDadosBolsistas);

			return $form;
		} catch ( Exception $e ) {
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

		$count = 0;

		$html = "<div class='listagem datatable'>";
		$html .= "<table id='tbBolsistas'>";
		$html .= "	<caption><i>Listagem dos bolsistas para homologação</i></caption>";
		$html .= "	<thead><tr>";
		$html .= "		<th width='2%' style='text-align: center'></th>";
		$html .= "		<th width='14%' >Perfil</th>";
		$html .= "		<th width='5%' >UF</th>";
		$html .= "		<th width='25%'>Nome</th>";
		$html .= "		<th width='10%'>CPF</th>";
		$html .= "		<th width='13%'>Montante por bolsista</th>";
		$html .= "		<th width='13%'>Quantidade de bolsas</th>";
		$html .= "		<th width='10%' style='text-align: center' >Situação</th>";
		$html .= "		<th width='8%' style='text-align: center' >Ações</th>";
		$html .= "	</tr></thead>";
		$html .= "	<tbody>";
		foreach ( $arBolsistas as $bolsista ) {
			$html .= "<tr>";
			$html .= "	<td><center><input type='checkbox' name='NU_SEQ_BOLSA[]' value='" . $bolsista['NU_SEQ_BOLSA']
					. "' /></center></td>";
			$html .= "	<td>" . $bolsista['DS_TIPO_PERFIL'] . "</td>";
			$html .= "	<td>" . $bolsista['SG_UF_ATUACAO_PERFIL'] . "</td>";
			$html .= "	<td>" . $bolsista['NO_USUARIO'] . "</td>";
			$html .= "	<td>" . Fnde_Sice_Business_Componentes::formataCpf($bolsista['NU_CPF']) . "</td>";
			$html .= "	<td style='text-align: right'>" . 'R$ '
					. number_format(( float ) $bolsista['VL_BOLSA'], 2, ',', '.') . "</td>";
			$html .= "	<td style='text-align: center' >" . $bolsista['QTD_BOLSA'] . "</td>";
			$html .= "	<td>" . $bolsista['DS_SITUACAO_BOLSA'] . "</td>";
			$html .= "	<td style='text-align: center'>";
			$html .= "	<a title='Detalhes' class='icoVisualizar' id='detalhesHomologar' href='"
					. $this->view->baseUrl() . "/index.php/financeiro/detalhesbolsista/form/NU_SEQ_BOLSA/"
					. $bolsista['NU_SEQ_BOLSA'] . "/NU_SEQ_TIPO_PERFIL/" . $bolsista['NU_SEQ_TIPO_PERFIL'] . "'/>";
			$html .= "	<a title='Devolver p/ Avaliação' class='icoReceber' id='devolverHomologar' href='"
					. $this->view->baseUrl() . "/index.php/financeiro/motivodevolucao/form/NU_SEQ_BOLSA/"
					. $bolsista['NU_SEQ_BOLSA'] . "'/>";
			$html .= "	</td>";
			$html .= "</tr>";

			$count++;
		}
		$html .= "	</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Método para executar a homologação de uma bolsa e gravar os resultados no banco de dados.
	 */
	public function homologarBolsasAction() {

		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessBolsa = new Fnde_Sice_Business_Bolsa();

		$arParams = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$resultUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		if ( !$arParams['NU_SEQ_BOLSA'] ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Selecione pelo menos uma bolsa para realizar a homologação.");
			$this->_redirect("/financeiro/homologarbolsas/form{$arParams['NU_SEQ_BOLSA_URL']}");
		} else {
			$arParams['ST_BOLSA'] = 2;
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
				$this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao tentar homologar.");
				$this->_redirect("/financeiro/homologarbolsas/form{$arParams['NU_SEQ_BOLSA_URL']}");
			}
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Bolsista (s) homologado (s) com sucesso.");
			$this->_redirect("/financeiro/bolsa/list/");
		}
	}
}
