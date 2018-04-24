<?php

/**
 * Controller do Finalizar Turma
 * 
 * @author poliane.silva
 * @since 30/05/2012
 */

class Secretaria_FinalizarTurmaController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author poliane.silva
	 * @since 30/05/2012
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

		$form = new FinalizarTurma_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/finalizarturma/finalizar-turma')->setMethod(
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
	public function finalizarTurmaAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Finalizar');

		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
			$arNotaTutor[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
				Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TUTOR'][$i]);
		}

		$obBusinessHistTurma = new Fnde_Sice_Business_HistoricoTurma();
		$obBusinessTurma = new Fnde_Sice_Business_Turma();

		if ( !$obBusinessHistTurma->isTurmaAvaliada($arParam['NU_SEQ_TURMA']) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					'Existem alunos cuja avaliação não foi completada, por favor efetue a avaliação de TODOS os alunos para finalizar a turma.');
			$this->view->form = $form;
			return $this->render('form');
		}

		if ( !$obBusinessTurma->isDataMinimaConclusao($arParam['NU_SEQ_TURMA']) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					'Esta turma não poderá ser finalizada. Data atual menor que mínimo para conclusão.');
			$this->view->form = $form;
			return $this->render('form');
		}

		if($arParam['confirmar']){
			try {
				for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
					$arNotaTutor[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
						Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TUTOR'][$i]);
				}

				$arAlunosMatriculados = $obBusinessHistTurma->getAlunosMatriculadosPorTurma($arParam['NU_SEQ_TURMA']);

				$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
				$i = 0;

				$obModelo->getAdapter()->beginTransaction();
				foreach ( $arAlunosMatriculados as $aluno ) {
					$arDados['NU_NOTA_TUTOR'] = $arNotaTutor[$i];

					$where = "NU_MATRICULA = " . $aluno['NU_MATRICULA'];

					$obModelo->update($arDados, $where);
					$i++;
				}

				$obBusinessTurma->alteraSituacaoTurma($arParam, 11, date("d/m/Y"));

				//Sucesso
				$this->addMessage(Fnde_Message::MSG_SUCCESS, "Turma finalizada com sucesso.");
				$this->_redirect("/secretaria/avaliacaopedagogica/list");
			} catch ( Exception $e ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			}
		}


		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Retorna o HTML de alunos matriculados
	 * @param int $codTurma Codigo da turma.
	 * @return string $html
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table id='tbCursista'>";
		$html .= "<thead><tr><th style='text-align: center'>Contagem</th><th style='text-align: center'>Matrícula</th>";
		$html .= "<th style='text-align: center'>Nome</th><th style='text-align: center'>CPF</th><th style='text-align: center'>";
		$html .= "Nota tutor</th><th style='text-align: center'>Nota cursista</th><th style='text-align: center'>Nota total</th>";
		$html .= "<th style='text-align: center'>Situação</th></tr></thead>";
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

			if ( $notaCursista == null && $notaTutor == null ) {
				$total = "";
			} else {
				$total = number_format($notaTutor + $notaCursista, 2, ',', '.');
			}

			$html .= "<tr><td style='text-align: center'>" . ++$countLinha . "</td><td style='text-align: center'>"
					. $aluno['NU_MATRICULA'] . "</td><td>";
			$html .= $aluno['NO_USUARIO'] . "</td><td style='text-align: center'> ";
			$html .= Fnde_Sice_Business_Componentes::formataCpf($aluno['NU_CPF']) . "</td> ";
			$html .= "<td><center><input number='$countId' type='text' name='NU_NOTA_TUTOR[$countId]'id='NU_NOTA_TUTOR[$countId]' value='"
				. ( $notaTutor != null ? number_format($notaTutor, 1, ',', '.') : "" ) . "' ";
			$html .= "class='decimal4' ></center></td> ";
			if ( $notaCursista != null ) {
				$html .= "<td><center><input type='text' name='NU_NOTA_CURSISTA[$countId]'id='NU_NOTA_CURSISTA[$countId]' ";
				$html .= "value='" . number_format($notaCursista, 2, ',', '.')
						. "' readonly='readonly' class='decimal4'></center></td> ";
			} else {
				$html .= "<td style='text-align: center'>Não avaliou</td>";
			}
			$html .= "<td><center><input type='text' name='NU_NOTA_TOTAL[$countId]'id='NU_NOTA_TOTAL[$countId]' value='$total' ";

			$html .= "readonly='readonly' class='decimal3'></center></td> ";

			$html .= "<td id='DS_SITUACAO_$countId' style='text-align: center'> " . $aluno['DS_SITUACAO'];

			$html .= "</td> ";
			$html .= "</tr>";

			$countId++;
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Carrega tela de finalizacao.
	 */
	public function listAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Finalizar');

		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Salva as notas de avaliação do cursista feita pelo tutor.
	 */
	public function finalizarTurmaAtivaAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Finalizar');

		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$form = $this->getFormTurmaAtiva($arParam);
		$form->populate($arParam);

		for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
			$arNotaTutor[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
				Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TUTOR'][$i]);
		}

		$obBusinessHistTurma = new Fnde_Sice_Business_HistoricoTurma();
		$obBusinessTurma = new Fnde_Sice_Business_Turma();

		if($arParam['confirmar']){
			try {
				for ( $i = 0; $i < count($arParam['NU_NOTA_TUTOR']); $i++ ) {
					$arNotaTutor[$i] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
						Fnde_Sice_Business_Componentes::REPLACE_PARA, $arParam['NU_NOTA_TUTOR'][$i]);
				}

				$arAlunosMatriculados = $obBusinessHistTurma->getAlunosMatriculadosPorTurma($arParam['NU_SEQ_TURMA']);
				$vinculo = new Fnde_Sice_Business_VincCursistaTurma();
				$obModeloCriterio = new Fnde_Sice_Business_CriterioAvaliacao();
				$arrVinculos = array();
				foreach( $vinculo->retornaVinculosPorIdTurma($arParam['NU_SEQ_TURMA']) as $vinc){
					$arrVinculos[$vinc['NU_MATRICULA']] = $vinc['NU_SEQ_CRITERIO_AVAL'];

					$a = $obModeloCriterio->getCriterioAvaliacaoById($vinc['NU_SEQ_CRITERIO_AVAL']);
					$idConfig = $a['NU_SEQ_CONFIGURACAO'];
				}

				$arrCriterio = array();
				$ind = 0;
				foreach ($obModeloCriterio->getCriterioAvaliacaoByIdConfiguracao($idConfig) as $criterio) {
					$arrCriterio[$ind]['NU_MINIMO'] = $this->formatacaoFloat($criterio['NU_MINIMO']);
					$arrCriterio[$ind]['NU_MAXIMO'] = $this->formatacaoFloat($criterio['NU_MAXIMO']);
					$arrCriterio[$ind]['NU_SEQ_CRITERIO_AVAL'] = $criterio['NU_SEQ_CRITERIO_AVAL'];
					$arrCriterio[$ind]['DS_SITUACAO'] = $criterio['DS_SITUACAO'];

					$ind++;
				}

				foreach($arNotaTutor as $indice => $v){
					if(strstr($v, '.')){
						if(strlen(strstr($v, '.')) == 2){
							$arNotaTutor[$indice] = $v.'0';
						}
					} else {
						if(empty($v)){
							//unset($arNotaTutor[$i]);
						} else {
							if(strlen($v) == 2){
								$v = $v.'0';
								$v = substr($v,0,1) . '.' . substr($v,-2);
							} else {
								$v = substr($v,0,1) . '.' . substr($v,-2);
							}
							$arNotaTutor[$indice] = $v;
						}
					}
				}

				$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
				$obModelo->getAdapter()->beginTransaction();
				$i = 0;
				$situacao = array();
				foreach ( $arAlunosMatriculados as $aluno ) {
					$notaTotal = $arNotaTutor[$i] + $aluno['NU_NOTA_CURSISTA'];

					foreach($arrCriterio as $criterio){
						//desistente
						if( $criterio['NU_MINIMO'] == 0 && $criterio['NU_MAXIMO'] == 0){
							$desistente = $criterio['NU_SEQ_CRITERIO_AVAL'];
						}
						//Destaque
						if( $criterio['NU_MAXIMO'] == 10 ){
							$destaque = $criterio['NU_SEQ_CRITERIO_AVAL'];
						}

						if($notaTotal >= $criterio['NU_MINIMO'] && $notaTotal <= $criterio['NU_MAXIMO']){
							$situacao[$i] = $criterio['NU_SEQ_CRITERIO_AVAL'];
						} elseif($notaTotal == $criterio['NU_MINIMO'] && $notaTotal == $criterio['NU_MAXIMO']){
							$situacao[$i] = $criterio['NU_SEQ_CRITERIO_AVAL'];
						}
					}
					//1º
					$arDados['NU_NOTA_TUTOR'] = $arNotaTutor[$i];
					if(isset($situacao[$i])){
						$arDados['NU_SEQ_CRITERIO_AVAL'] = $situacao[$i];
					}
					$where = "NU_MATRICULA = " . $aluno['NU_MATRICULA'];

					if ($notaTotal == 10 && $arDados['NU_SEQ_CRITERIO_AVAL'] == $desistente){
						$arDados['NU_SEQ_CRITERIO_AVAL'] = $destaque;
					}

					$obModelo->update($arDados, $where);

					$i++;
				}

				$obBusinessTurma->alteraSituacaoTurma($arParam, 11, date("d/m/Y"));

				//Sucesso
				$this->addMessage(Fnde_Message::MSG_SUCCESS, "Turma finalizada com sucesso.");
				$this->_redirect("/secretaria/avaliacaopedagogica/list");
			} catch ( Exception $e ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			}
		}

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author poliane.silva
	 * @since 30/05/2012
	 */
	public function getFormTurmaAtiva( $arDados = array(), $arExtra = array() ) {

		//Recupera os parametros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
			array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
				'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new FinalizarTurma_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/finalizarturma/finalizar-turma-ativa')->setMethod(
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

	public function formatacaoFloat($v){
		if($v === '100'){
			return (float)10;
		}
		if(strlen($v) == 2){
			$v = $v.'0';
			$v = substr($v,0,1) . '.' . substr($v,-2);
		} else {
			$v = substr($v,0,1) . '.' . substr($v,-2);
		}

		return (float)$v;
	}
}
