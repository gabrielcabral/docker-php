<?php

/**
 * Controller do AvaliarCursista
 * 
 * @author rafael.paiva
 * @since 28/05/2012
 */

class Secretaria_AvaliarCursistaController extends Fnde_Sice_Controller_Action {

	//Nota máxima do cursista
	const NOTA_MAX = 10;

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 28/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		//Recupera os parametros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new AvaliarCursista_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/avaliarcursista/avaliar-cursista')->setMethod(
				'post')->setAttrib('id', 'form');

		$html = $form->getElement("htmlTurma");
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$html->setValue($str);

		$htmlCriteriosSugeridos = $form->getElement("htmlCriteriosSugeridos");
		$str = Fnde_Sice_Business_Componentes::retornaHtmlCriteriosSugeridos();
		$htmlCriteriosSugeridos->setValue($str);

		$htmlAlunosMatriculados = $form->getElement("htmlAlunosMatriculados");
		$strAlunosMatriculados = $this->retornaHtmlAlunosMatriculados($params['NU_SEQ_TURMA']);
		$htmlAlunosMatriculados->setValue($strAlunosMatriculados);

		$nuMinAlunos = $form->getElement("NU_MIN_ALUNOS");
		$nuMinAlunos->setValue($infoComplementarTurma['NU_MIN_ALUNOS']);

		$nuAlunosMatriculados = $form->getElement("NU_ALUNOS_MATRICULADOS");
		$nuAlunosMatriculados->setValue($quantCursistas['QUANT_CURSISTAS']);

		return $form;
	}

	/**
	 * Salva as notas de avaliação do cursista feita pelo tutor.
	 */
	public function avaliarCursistaAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Avaliar');

		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$form = $this->getForm($this->_request->getParams());
               
		$arParam = $this->_getAllParams();
		$form->populate($arParam);
                
		if ( !$this->validarFormulario($arParam) ) {
                    
			$this->view->form = $form;
			return $this->render('form');
		}

		for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
			$arNotaTutor[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
					Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TUTOR'][$i]);
		}

		for ( $i = 0; $i < count($arParam['NU_NOTA_TOTAL']); $i++ ) {
			$arNotaTotal[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
					Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TOTAL'][$i]);
		}

		$arDesistente = $arParam['DESISTENTE'];

		if($_POST) {
			try {
				Fnde_Sice_Business_Usuario::validaNota($arNotaTutor, $arDesistente);

				$obHistorico = new Fnde_Sice_Business_HistoricoTurma();
				$obTurma = new Fnde_Sice_Business_Turma();
				$arAlunosMatriculados = $obHistorico->getAlunosMatriculadosPorTurma($arParam['NU_SEQ_TURMA']);

				$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
				$i = 0;

				$obModelo->getAdapter()->beginTransaction();
				foreach ($arAlunosMatriculados as $aluno) {

					$arNotaTotal[$i] = $aluno['NU_NOTA_CURSISTA'] + $arNotaTutor[$i];

					$arDados['NU_SEQ_CRITERIO_AVAL'] = $this->obterSituacaoCursista($arNotaTotal[$i], $arDesistente[$i], $arParam['NU_SEQ_TURMA']);
					$arDados['NU_NOTA_TUTOR'] = $arNotaTutor[$i];

					$where = "NU_MATRICULA = " . $aluno['NU_MATRICULA'];

					$obModelo->update($arDados, $where);
					$i++;
				}

				//adicionando a situação finalizada (11), conforma solicitação enviada por email e anexada ao SVN de documentação
				$arrSituacao = array(12,11);
				$situacao = 12;
				$codTurma = $arParam['NU_SEQ_TURMA'];
				$arTurma = $obTurma->getTurmaPorId($codTurma);

				$obModelo->getAdapter()->commit();

				//Se a turma já estiver na situação "Em Avaliação" não altera a situação.
				//adicionando a situação finalizada (11), conforma solicitação enviada por email e anexada ao SVN de documentação
				if (in_array($arTurma['ST_TURMA'], $arrSituacao)) {
					//Alterando a situação da turma e inserindo no histórico turma.
					$obTurma->alteraSituacaoTurma($arParam, $situacao);
					// 			    $obHistorico->preSalvar($codTurma, $situacao);
				}

				if (!empty($_POST)) {
					//Sucesso
					$this->addMessage(Fnde_Message::MSG_SUCCESS, "Avaliação realizada com sucesso.");
					$this->_redirect("/secretaria/avaliacaopedagogica/list");
				}
			} catch (Exception $e) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			}
		}
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Obtem a situação do cursista de acordo com a configuração e nota lançada pelo tutor.
	 * @param int $nota
	 */
	public function obterSituacaoCursista( $nota, $desistente, $codTurma ) {

		if ( $nota == "" && !$desistente ) {
			return null;
		}

		//Verifica situação do cursista de acordo com a configuração
		$obTurma = new Fnde_Sice_Business_Turma();
		$obCriterioAval = new Fnde_Sice_Business_CriterioAvaliacao();
		
		$turma = $obTurma->getTurmaById($codTurma);
		$arCriterioAval = $obCriterioAval->getCriterioAvaliacaoByIdConfiguracao($turma['NU_SEQ_CONFIGURACAO']);

		//Calcula a porcentagem da nota do aluno conforme nota máxima.
		$porcentagemNota = ( int ) ( $nota * 100 ) / self::NOTA_MAX;

		foreach ( $arCriterioAval as $criterio ) {
			if ( $desistente ) {
				if ( $criterio['DS_SITUACAO'] == 'Desistente' ) {
					return $criterio['NU_SEQ_CRITERIO_AVAL'];
				}

			} else if ( $criterio['DS_SITUACAO'] != 'Desistente' ) {
				if ( $porcentagemNota >= $criterio['NU_MINIMO'] && $porcentagemNota <= $criterio['NU_MAXIMO'] ) {
					return $criterio['NU_SEQ_CRITERIO_AVAL'];
				}
			}
		}
	}

	/**
	 * Retorna HTML de alunos matriculados
	 * @param int $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table id='tbCursista'>";
		$html .= "<thead><tr><th style='text-align: center'>Contagem</th><th style='text-align: center'>Matrícula</th>";
		$html .= "<th style='text-align: center'>Nome</th><th style='text-align: center'>CPF</th><th style='text-align: center'>Nota tutor</th>";
		$html .= "<th style='text-align: center'>Nota cursista</th><th style='text-align: center'>Nota total</th>";
		$html .= "<th style='text-align: center'>Situação</th><th style='text-align: center'>Desistente</th></tr></thead>";
		$html .= "<tbody>";

		$countLinha = 0;
		$countId = 0;

		foreach ( $arAlunosMatriculados as $aluno ) {
			//Formatando valores
			if ( $aluno['NU_NOTA_TUTOR'] != null ) {
				$notaTutor = str_replace(',', '.', $aluno['NU_NOTA_TUTOR']);
			} else {
				$notaTutor = null;
			}
			if ( $aluno['NU_NOTA_CURSISTA'] != null ) {
				$notaCursista = str_replace(',', '.', $aluno['NU_NOTA_CURSISTA']);
			} else {
				$notaCursista = null;
			}
                        
			$html .= "<tr><td style='text-align: center'>" . ++$countLinha . "</td><td style='text-align: center'>";
			$html .= $aluno['NU_MATRICULA'] . "</td><td>" . $aluno['NO_USUARIO']
					. "</td><td style='text-align: center'>";
			$html .= Fnde_Sice_Business_Componentes::formataCpf($aluno['NU_CPF']) . "</td> ";
			$html .= "<td><center><input type='text' name='NU_NOTA_TUTOR[$countId]'id='NU_NOTA_TUTOR[$countId]' value='"
					. ( $notaTutor != null ? number_format($notaTutor, 1, ',', '.') : "" ) . "' ";
			$html .= "class='decimal4' ></center></td> ";
			if ( $notaCursista != null ) {
				$html .= "<td><center><input type='text' name='NU_NOTA_CURSISTA[$countId]'id='NU_NOTA_CURSISTA[$countId]' ";
				$html .= "value='" . number_format($notaCursista, 1, ',', '.')
						. "' readonly='readonly' class='decimal4'></center></td> ";
			} else {
				$html .= "<td style='text-align: center'>Não avaliou</td>";
			}

			//correção de nota total
			$notaTotal = $notaCursista + $notaTutor;
			$notaTotal = $notaTotal * 100;
			$html .= "<td><center><input type='text' name='NU_NOTA_TOTAL[$countId]'id='NU_NOTA_TOTAL[$countId]' value='$notaTotal' ";
			$html .= "readonly='readonly' class='decimal'></center></td> ";

			$checked = false;
			if ( $aluno['DS_SITUACAO'] == 'Desistente' ) {
				$checked = true;
			} else {
				$checked = false;
			}

			$html .= "<td style='text-align: center'>" . $aluno['DS_SITUACAO'] . "</td> ";

			$html .= "<td><center><input type='checkbox' name='DESISTENTE[$countId]'id='DESISTENTE[$countId]'";
			if ( $checked ) {
				$html .= " checked ";
			}
			$html .= "></center></td> ";
			$html .= "</tr>";

			$countId++;
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Monta a tela de pesquisa.
	 */
	public function listAction() {
            try {
                

		$arParam = $this->_getAllParams();
                $obTurma = new Fnde_Sice_Business_Turma();
                $codTurma = $arParam['NU_SEQ_TURMA'];
                $arTurma = $obTurma->getTurmaPorId($codTurma);
                
                $arStTurma = array(
                    Fnde_Sice_Business_Turma::PRE_TURMA,
                    Fnde_Sice_Business_Turma::SOLICITADO_AUTORIZACAO,
                    Fnde_Sice_Business_Turma::AGUARDANDO_AUTORIZACAO,
                    Fnde_Sice_Business_Turma::NAO_AUTORIZADA,
                    Fnde_Sice_Business_Turma::SOLICITADO_CANCELAMENTO,
                    Fnde_Sice_Business_Turma::REJEITAR_CANCELAMENTO,
                    Fnde_Sice_Business_Turma::AGUARDANDO_AUTORIZACAO,
                    Fnde_Sice_Business_Turma::CANCELADA,
                    Fnde_Sice_Business_Turma::FINALIZACAO_ATRASADA,
                    Fnde_Sice_Business_Turma::FINALIZADA
                );

                if(in_array($arTurma['ST_TURMA'], $arStTurma)) {
                    throw new Exception('Para ser avaliada, a turma deve estar ativa.');
                }
                
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Avaliar');
                

		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
            }
            catch(Exception $e) {
                $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
                $this->_redirect("/");
            }
	}

	/**
	 * Valida o formulario antes de salvar.
	 * @param array $arParam
	 */
	function validarFormulario( $arParam ) {
		for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
			//Validando E1
			if ( str_replace(',', '.', $arParam['NU_NOTA_TUTOR'][$i]) > 9 && !$arParam['DESISTENTE'][$i] ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, "A nota do cursista só pode ser de 0.00 à 9.00");
				return false;
			}
		}
		return true;
	}

}
