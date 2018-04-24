<?php

/**
 * Controller do Avaliar Bolsas
 * 
 * @author rafael.paiva
 * @since 26/06/2012
 */

class Financeiro_VerificarPendenciasController extends Fnde_Sice_Controller_Action {

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

			//9 - Pendência;
			if ( $arSessao['ST_BOLSA'] != "9" && $arSessao['ST_BOLSA'] != "3" ) {
				$this->addMessage(Fnde_Message::MSG_ERROR,
						"A ação Verificar Pendências não pode ser executada, pois a (s) bolsa (s) selecionada (s) está com a situação "
								. Fnde_Sice_Business_Componentes::nomeSituacaoBolsa($arSessao['ST_BOLSA']) . ".");
				$this->_redirect("/financeiro/bolsa/list");
			}

			$this->setTitle('Bolsas');
			$this->setSubtitle('Verificar pendências');

			//monta menu de contexto
			$menu = array($this->getUrl('financeiro', 'bolsa', 'list', ' ') => 'filtrar');
			$this->setActionMenu($menu);

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
			$businessBolsa = new Fnde_Sice_Business_Bolsa();
			$businessRegiao = new Fnde_Sice_Business_Regiao();
			$businessSituacaoBolsa = new Fnde_Sice_Business_SituacaoBolsa();

			$arParam = $this->_getAllParams();

			if ( isset($arParam['IDENTIFICADOR_LINHA']) ) {
				$arParam['identificador_linha'] = $arParam['IDENTIFICADOR_LINHA'];
			}

			if ( $arParam['identificador_linha'] ) {
				$_SESSION['IDENTIFICADOR_LINHA_VERIF_PEND'] = $arParam['identificador_linha'];
			} else {
				$arParam['identificador_linha'] = $_SESSION['IDENTIFICADOR_LINHA_VERIF_PEND'];
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

			$form = new VerificarPendencias_Form($arDados, $arExtra);
			$form->setDecorators(array('FormElements', 'Form'));
			$form->setAction($this->view->baseUrl() . '/index.php/financeiro/bolsa/list')->setMethod('post')->setAttrib(
					'id', 'form');

			//Recupera os dados para montar o HTML do período.
			//Recupera os dados de pesquisa da tela de filtrar bolsas.
			$arPeriodo = $_SESSION['searchParam']['param'];

			//Preparando o array de dados do período para montar o HTML.
			$businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
			$periodo = $businessPeriodoVinc->getDatasPeriodoById(
					array("NU_SEQ_PERIODO_VINCULACAO" => $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']));
			$arPeriodo['DT_INICIAL'] = $periodo['DT_INICIAL'];
			$arPeriodo['DT_FINAL'] = $periodo['DT_FINAL'];

			$situacao = $businessSituacaoBolsa->getSituacaoBolsaById($arPeriodo['ST_BOLSA']);
			$arPeriodo['DS_SITUACAO_BOLSA'] = $situacao['DS_SITUACAO_BOLSA'];
			$arPeriodo['MES_REFERENCIA'] = substr($arPeriodo['DT_FINAL'], -7);
			$arParam['SITUACAO_BOLSISTAS'] = $arPeriodo['ST_BOLSA'];

			//Preparando a concatenação das UFs dos Usuários.
			$arPeriodo['SG_UF'] = $arParam['UF_BOLSISTAS'];
			//Preparando a concatenacao das Regioes, caso seja selecionado a opcao todos
			$result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arPeriodo['SG_UF']));
			$arPeriodo['NO_REGIAO'] = $result['NO_REGIAO'];
			//Cria HTML com os dados do Período de Vinculação.

			if($businessBolsa->isBolsaAntiga($arPeriodo['NU_SEQ_PERIODO_VINCULACAO'])){
				$arBolsistas = $businessBolsa->pesquisarBolsasVerifPend($arParam['UF_BOLSISTAS'],
						$arParam['PERFIL_BOLSISTAS'], $arPeriodo["ST_BOLSA"], $arPeriodo["NU_SEQ_PERIODO_VINCULACAO"]);
			}else{
				$arBolsistas = $businessBolsa->pesquisarBolsasAvaliacao($arParam);
			}
			
			$this->view->gridPeriodo = $this->view->retornaHtmlPeriodo($arPeriodo);

			$arrHeader = array('<center>Perfil</center>', '<center>UF</center>', '<center>Nome</center>',
					'<center>CPF</center>', '<center>Valor da bolsa</center>', '<center>Situação</center>',
					'<center>Motivo</center>',);

			$arrHidden = array('NU_SEQ_BOLSA', 'QTD_BOLSA', 'NU_SEQ_PERIODO_VINCULACAO', 'NU_SEQ_TIPO_PERFIL',
					'QT_TOTAL', 'QT_TURMAS_AVALIADAS', 'QT_TURMAS_REPROVADAS', 'NO_USUARIO_AVALIADOR',
					'DS_TIPO_PERFIL_AVALIADOR');

			for ( $i = 0; $i < count($arBolsistas); $i++ ) {
				$temp = 'R$ ' . number_format(( float ) $arBolsistas[$i]["VL_BOLSA"], 2, ',', '.');
				$arBolsistas[$i]["VL_BOLSA"] = $temp;
				$tempCpf = Fnde_Sice_Business_Componentes::formataCpf($arBolsistas[$i]["NU_CPF"]);
				$arBolsistas[$i]["NU_CPF"] = $tempCpf;
			}

			$grid = new Fnde_Sice_View_Helper_DataTables();

			if ( $arPeriodo["ST_BOLSA"] == "9" ) {
				$arrayMaisAcoes = array(
						"Cancelar bolsa" => $this->getUrl('financeiro', 'motivocancelamento', 'form', true),
						"Devolver para avaliação" => $this->getUrl('financeiro', 'motivodevolucaoavaliacao', 'form', true),
						"Reenviar para SGB" => $this->getUrl('financeiro', 'enviarsgb', 'enviar-sgb-verif-pend', true));
			} else {
				$arrayMaisAcoes = array();
			}

			$grid->setMainAction($arrayMaisAcoes);
			$grid->setAutoCallJs(true);
			$grid->setColumnsHidden($arrHidden);
			$this->view->grid = $grid->setData($arBolsistas)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
					'<i>Listagem dos bolsistas com pendências</i>')->setId('NU_SEQ_BOLSA')->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'edit'));

			return $form;
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());

			$this->view->form = $form;
			return $this->render('form');
		}
	}
}
