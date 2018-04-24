<?php

/**
 * Controller do Avaliar Bolsas
 * 
 * @author rafael.paiva
 * @since 26/06/2012
 */

class Financeiro_AvaliarBolsasController extends Fnde_Sice_Controller_Action {

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

			$this->setTitle('Bolsas');
			$this->setSubtitle('Avaliar');

			$arSessao = $_SESSION['searchParam']['param'];
			if ( !in_array($arSessao['ST_BOLSA'], array(1, 4, 5, 6)) ) { //1 = Devolvida, 4 = Cancelada, 5 = Avaliada, 6 = Não avaliada
				$this->addMessage(Fnde_Message::MSG_ERROR,
						"A ação Avaliar não pode ser executada, pois a (s) bolsa (s) selecionada (s) está com a situação "
								. Fnde_Sice_Business_Componentes::nomeSituacaoBolsa($arSessao['ST_BOLSA']) . ".");
				$this->_redirect("/financeiro/bolsa/list");
			}

			//monta menu de contexto
			$menu = array($this->getUrl('financeiro', 'bolsa', 'list', ' ') => 'filtrar');
			$this->setActionMenu($menu);

			//Montando a mensagem de orientação
			$msgOrientacao = "Para avaliação:";
			$msgOrientacao .= "<ul><b> ";
			$msgOrientacao .= "	<li type=disc>A: Apto</li> ";
			$msgOrientacao .= "	<li type=disc>I: Inapto</li> ";
			$msgOrientacao .= "	<li type=disc>N: Não avaliado</li> ";
			$msgOrientacao .= "</b></ul> ";

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
			$obPeriodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();
			$businessRegiao = new Fnde_Sice_Business_Regiao();
			$businessBolsa = new Fnde_Sice_Business_Bolsa();
			$businessSituacaoBolsa = new Fnde_Sice_Business_SituacaoBolsa();
			$params = $this->_getAllParams();
			
			$params['identificador_linha'] = $this->verificaIdentificador($params);
			
			if ( $params['identificador_linha'] ) {
				$_SESSION['IDENTIFICADOR_LINHA'] = $params['identificador_linha'];
			} else {
				$params['identificador_linha'] = $_SESSION['IDENTIFICADOR_LINHA'];
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
			
			$form = new AvaliarBolsas_Form($arDados, $arExtra);
			$form->setDecorators(array('FormElements', 'Form'));
			$form->setAction($this->view->baseUrl() . '/index.php/financeiro/avaliarbolsas/salvar-avaliacao')->setMethod(
					'post')->setAttrib('id', 'form');
			//Recupera os dados de pesquisa da tela de filtrar bolsas.
			$arSessao = $_SESSION['searchParam']['param'];
			//Recuperando o período de vinculação.
			$arIdPerVincula = array('NU_SEQ_PERIODO_VINCULACAO' => $arSessao['NU_SEQ_PERIODO_VINCULACAO']);
			$periodoVinculacao = $obPeriodoVinculacao->getDatasPeriodoById($arIdPerVincula);
			
			//Preparando o array de dados do período para montar o HTML.
			$arPeriodo = $arSessao;

			$arPeriodo['DT_INICIAL'] = $periodoVinculacao['DT_INICIAL'];
			$arPeriodo['DT_FINAL'] = $periodoVinculacao['DT_FINAL'];
			$arPeriodo['MES_REFERENCIA'] = substr($arPeriodo['DT_FINAL'], -7);
			
			$situacao = $businessSituacaoBolsa->getSituacaoBolsaById($arPeriodo['ST_BOLSA']);
			$arPeriodo['DS_SITUACAO_BOLSA'] = $situacao['DS_SITUACAO_BOLSA'];
			$params['SITUACAO_BOLSISTAS'] = $arPeriodo['ST_BOLSA'];
			
			$arPeriodo['SG_UF'] = $params['UF_BOLSISTAS'];
			$result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arPeriodo['SG_UF']));
			$arPeriodo['NO_REGIAO'] = $result['NO_REGIAO'];
			
			//Cria HTML com os dados do Período de Vinculação.
			$htmlPeriodo = $form->getElement("htmlPeriodo");
			$strDadosPeriodo = $this->view->retornaHtmlPeriodo($arPeriodo);
			$htmlPeriodo->setValue($strDadosPeriodo);
			
			if($businessBolsa->isBolsaAntiga($arSessao['NU_SEQ_PERIODO_VINCULACAO'])){
				$arBolsistas = $businessBolsa->pesquisarBolsasAvaliacaoAntiga($params['UF_BOLSISTAS'],
					$params['PERFIL_BOLSISTAS'], $arPeriodo);
			}else{
				$arBolsistas = $businessBolsa->pesquisarBolsasAvaliacao($params);
			}
			
			//Cria HTML com os dados dos Bolsistas.
			$htmlBolsas = $form->getElement("htmlBolsas");

			$qtdBolsas = $form->getElement("QTD_BOLSAS");
			$qtdBolsas->setValue(count($arBolsistas));
			$strDadosBolsas = $this->retornaHtmlBolsas($arBolsistas);
			$htmlBolsas->setValue($strDadosBolsas);

			return $form;
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
			return $this->render('form');
		}
	}

	/**
	 * 
	 * @param unknown_type $params
	 */
	private function verificaIdentificador($params){
		if(isset( $params['IDENTIFICADOR_LINHA'] )) {
			return $params['IDENTIFICADOR_LINHA'];
		}else{
			return $params['identificador_linha'];
		}
		
	}
	
	/**
	 * Retorna o HTML da tabela com os dados dos bolsista.
	 * @param array $arBolsistas Dados dos bolsistas.
	 * @return string HTML.
	 */
	public function retornaHtmlBolsas( $arBolsas ) {

		$count = 0;

		$html = "<div class='listagem datatable'>" . "<table id='tbBolsistas'>"
				. "	<caption><i>Listagem dos bolsistas para avaliação</i></caption>" . "	<thead><tr>"
				. "		<th>Perfil</th>" . "		<th>UF</th>" . "		<th>Nome</th>" . "		<th>CPF</th>"
				. "		<th>Situação</th>" . "		<th>Montante por bolsista</th>"
				. "		<th>Quantidade de bolsas</th>" . "		<th>Turmas Avaliadas</th>"
				. "		<th style='text-align: center'>A</th>" . "		<th style='text-align: center'>I</th>"
				. "		<th style='text-align: center'>N</th>" . "		<th style='text-align: center'>Ações</th>"
				. "	</tr></thead>" . "	<tbody>";

		foreach ( $arBolsas as $bolsa ) {
			$html .= "<tr>"
					. "	<td><input type='hidden' name='NU_SEQ_BOLSA[{$count}]' value={$bolsa['NU_SEQ_BOLSA']}/><input type='hidden' name='NU_SEQ_USUARIO[{$count}]' value={$bolsa['NU_SEQ_USUARIO']}/>"
					. $bolsa['DS_TIPO_PERFIL'] . "</td>" . "	<td>" . $bolsa['SG_UF_ATUACAO_PERFIL'] . "</td>"
					. "	<td>" . $bolsa['NO_USUARIO'] . "</td>" . "	<td>"
					. Fnde_Sice_Business_Componentes::formataCpf($bolsa['NU_CPF']) . "</td>" . "	<td>"
					. $bolsa['DS_SITUACAO_BOLSA'] . "</td>" . "	<td>R$ "
					. number_format(( float ) $bolsa['VL_BOLSA'], 2, ',', '.') . "</td>" . "	<td><center>"
					. $bolsa['QTD_BOLSA'] . "</center></td>" . "	<td><center>" . $bolsa['QT_TURMAS_AVALIADAS']
					. "</center></td>";

			$checkedA = $bolsa['ST_APTIDAO'] == 'A' ? "checked" : "";
			$checkedI = $bolsa['ST_APTIDAO'] == 'I' ? "checked" : "";
			$checkedN = $bolsa['ST_APTIDAO'] == 'N' ? "checked" : "";

			$disabled = $this->desabilitaRadioAN($bolsa);
			$desabilitarInapto = $this->desabilitaRadioI($bolsa);
			
			$html .= "	<td><center><input type='radio' name='AVALIACAO[" . $count . "]' id='A" . $count
					. "' value='A' {$checkedA} {$disabled} ></center></td>"
					. "	<td><center><input type='radio' name='AVALIACAO[" . $count . "]' id='I" . $count
					. "' value='I' {$checkedI} {$desabilitarInapto}"
					. "	  onclick=\"motivoInaptidao('/index.php/financeiro/motivoinaptidao/form/NU_SEQ_BOLSA/{$bolsa['NU_SEQ_BOLSA']}/INDICE/$count');\"></center></td>"
					. "	<td><center><input type='radio' name='AVALIACAO[" . $count . "]' id='N" . $count
					. "' value='N' {$checkedN} {$disabled}></center></td>";

			$html .= $this->retornarHtmlAvaliacao($bolsa);

			$html .= "</tr>";

			$count++;
		}
		$html .= "	</tbody>" . "</table>" . "</div>";

		return $html;
	}

	/**
	 * Retorna parte de ações do html de bolsas
	 * @param array $bolsa
	 * @return string
	 */
	private function retornarHtmlAvaliacao( $bolsa ) {
		if ( $bolsa['NU_SEQ_TIPO_PERFIL'] == 6 ) {
			$html = "	<td><center><a href='{$this->view->baseUrl()}/index.php/financeiro/avaliarturmas/form/NU_SEQ_BOLSA/"
					. $bolsa['NU_SEQ_BOLSA'] . "/NU_SEQ_TIPO_PERFIL/" . $bolsa['NU_SEQ_TIPO_PERFIL'] . "' class='icoAvaliar' title='Avaliar turmas'></a></center></td>";
		} else {
			$html = "	<td><center><a href='{$this->view->baseUrl()}/index.php/financeiro/visualizarturmas/form/NU_SEQ_BOLSA/"
					. $bolsa['NU_SEQ_BOLSA'] . "/NU_SEQ_TIPO_PERFIL/" . $bolsa['NU_SEQ_TIPO_PERFIL'] . "'class='icoVisualizar' title='Visualizar turmas'></a></center></td>";
		}

		return $html;
	}

	/**
	 * Desabilita o radio de 
	 * @param array $bolsa
	 * @return string
	 */
	private function desabilitaRadioAN( $bolsa ) {
		$disabled = null;
		//Se tipo perfil = a tutor
		if ( $bolsa['NU_SEQ_TIPO_PERFIL'] == 6 ) {
			if ( $bolsa['QT_TURMAS_AVALIADAS'] < $bolsa['QT_TOTAL'] ) {
				$disabled = "disabled=disabled";
			} else if ( $bolsa['QT_TURMAS_REPROVADAS'] == $bolsa['QT_TOTAL'] ) {
				$disabled = "disabled=disabled";
			}
		}
		return $disabled;
	}

	/**
	 * 
	 * @param array $bolsa
	 * @return string
	 */
	private function desabilitaRadioI( $bolsa ) {
		$desabilitarInapto = null;
		//Se tipo perfil = a tutor
		if ( $bolsa['NU_SEQ_TIPO_PERFIL'] == 6) {
			if ( $bolsa['QT_TURMAS_AVALIADAS'] < $bolsa['QT_TOTAL'] ) {
				$desabilitarInapto = "disabled=disabled";
			}
		}
		return $desabilitarInapto;
	}
	
	/**
	 * 
	 * @param array $arBolsas
	 */
	private function concatUf($arBolsas){
		//Preparando a concatenação das UFs dos Usuários selecionados.
		$arUfs = array();
		foreach ( $arBolsas as $bolsa ) {
			array_push($arUfs, $bolsa['SG_UF_ATUACAO_PERFIL']);
		}
		
		return $arUfs;
	}
	
	/**
	 * Função responsável por gravar as avaliações.
	 */
	public function salvarAvaliacaoAction() {
		$bolsaBusiness = new Fnde_Sice_Business_Bolsa();

		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}

		$arParams = $this->_getAllParams();

		if ( !$arParams['AVALIACAO'] ) {
			$strBolsas = "/";
			$this->addMessage(Fnde_Message::MSG_ERROR, "Realize a avaliação de pelo menos uma bolsa para finalizar.");
			$this->_redirect("/financeiro/avaliarbolsas/form/NU_SEQ_BOLSA" . $strBolsas);
		}

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		for ( $i = 0; $i < $arParams['QTD_BOLSAS']; $i++ ) {
			if ( $arParams['AVALIACAO'][$i] ) {
				$bolsa = substr($arParams['NU_SEQ_BOLSA'][$i], 0, -1);

				$arBolsa = array('NU_SEQ_BOLSA' => $bolsa, 'ST_APTIDAO' => $arParams['AVALIACAO'][$i],
						'NU_SEQ_USUARIO_AVALIADOR' => $arUsuario['NU_SEQ_USUARIO'], 'ST_BOLSA' => 5,);

				if ( $arParams['AVALIACAO'][$i] != "I" ) {
					$arBolsa['NU_SEQ_JUSTIF_INAPTIDAO'] = null;
					$arBolsa['DS_OBSERVACAO_INAPTIDAO'] = null;
					$arBolsa['ST_BOLSA'] = $arParams['AVALIACAO'][$i] == "N" ? 6 : 5;
				}

				try {

					if ( $bolsaBusiness->isJustificado($arBolsa) ) {
						$this->salvar($arBolsa);
					} else {

						$businessUsuario = new Fnde_Sice_Business_Usuario();
						$usuario = $businessUsuario->getUsuarioByIdBolsa($bolsa);

						$estilo = Fnde_Message::MSG_ERROR;
						$msg = "A bolsa de " . $usuario['NO_USUARIO']
								. " foi avaliada como \"Inapto (I)\", portanto o motivo deve ser justificado.";
						break;
					}

				} catch ( Exception $e ) {
					$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
					$this->_redirect("/financeiro/avaliarbolsas/form");
				}
			}

			$estilo = Fnde_Message::MSG_SUCCESS;
			$msg = "Bolsa (s) avaliada (s) com sucesso";
		}

		$this->addMessage($estilo, $msg);
		$this->_redirect("/financeiro/bolsa/list");
	}

	/**
	 * 
	 * @param unknown_type $arBolsa
	 */
	private function salvar( $arBolsa ) {
		$obModelBolsa = new Fnde_Sice_Model_Bolsa();
		$bolsaBusiness = new Fnde_Sice_Business_Bolsa();
		$obModelBolsa->fixDateToBr();
		if ( $bolsaBusiness->avaliarBolsa($arBolsa) ) {
			$arHistorico = array('NU_SEQ_BOLSA' => $arBolsa['NU_SEQ_BOLSA'], 'ST_APTIDAO' => $arBolsa['ST_APTIDAO'],
					'NU_SEQ_USUARIO' => $arBolsa['NU_SEQ_USUARIO_AVALIADOR'], 'ST_BOLSA' => 5,
					'DT_HISTORICO' => date('d/m/Y G:i:s'),
					'NU_SEQ_JUSTIF_INAPTIDAO' => $arBolsa['NU_SEQ_JUSTIF_INAPTIDAO'],
					'DS_OBSERVACAO' => $arBolsa['DS_OBSERVACAO_INAPTIDAO'], 'ST_BOLSA' => $arBolsa['ST_BOLSA']);

			$bolsaBusiness->salvarHistoricoBolsa($arHistorico);
		}
	}
}
