<?php

/**
 * Controller do QuantidadeTurma
 * 
 * @author diego.matos
 * @since 30/03/2012
 */

class Manutencao_QuantidaDeTurmaController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function listAction() {
		$this->setTitle('Configuração');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'quantidadeturma', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'quantidadeturma', 'form', ' ') => 'cadastrar');
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

				$obBusiness = new Fnde_Sice_Business_QuantidadeTurma();
				$rsRegistros = $obBusiness->searchQtTurma($form->getValues());
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
							'params' => array('NU_SEQ_QUANTIDADE_TURMA'), 'attribs' => array('class' => 'icoEditar')),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(
									array('action' => 'del-quantidadeturma', 'NU_SEQ_QUANTIDADE_TURMA' => '')) . '%s',
							'params' => array('NU_SEQ_QUANTIDADE_TURMA'),
							'attribs' => array('class' => 'icoExcluir excluir',
									'mensagem' => 'Confirma a exclusão das informações a regional selecionada?')));

			$arrHeader = array('coMesorregiao', 'SConfiguracao', 'qtTurmas', 'coRegiao',);

			$grid = new Fnde_View_Helper_DataTables();
			$grid->setHeaderActive(false);
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setTitle('QuantidadeTurma')->setRowAction(
					$rowAction)->setId('NU_SEQ_QUANTIDADE_TURMA')->setColumnsHidden(array('NU_SEQ_QUANTIDADE_TURMA'))->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Remove um registro de QuantidadeTurma
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function delQuantidadeTurmaAction() {
		$arParam = $this->_getAllParams();

		$obQuantidadeTurma = new Fnde_Sice_Business_QuantidadeTurma();
		$resposta = $obQuantidadeTurma->del($arParam['NU_SEQ_QUANTIDADE_TURMA']);

		$resposta = ( string ) $resposta;

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Operação realizada com sucesso!");
		} elseif ( $resposta == '0' ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Exclusão do registro já realizada.");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"QuantidadeTurma não pode ser excluído, pois o mesmo está associado." . $resposta);
		}

		$this->_redirect("/manutencao/quantidadeturma/list");
	}

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function formAction() {

		$nuSeqConfig = $arParams = $this->_request->getParams();
		$this->view->nu_seq_conf = $nuSeqConfig;
		if ( !$nuSeqConfig || !Fnde_Sice_Business_Componentes::validaConfigAtiva() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"É necessário informar os dados da aba de configuração para salvar mesorregião");
			$this->_redirect("/manutencao/configuracao/visualizar-configuracao/v/1/NU_SEQ_CONFIGURACAO/".$nuSeqConfig);
		}

		$this->setTitle('Configuração');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('manutencao', 'configuracao', 'list', ' ') => 'filtrar',
				$this->getUrl('manutencao', 'quantidadeturma', 'form', ' ') => 'cadastrar');
		$this->setActionMenu($menu);

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarQuantidadeTurmaAction();
		} else {
			//Recupera o objeto de formulário para validação
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
	 * @since 30/03/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$params = $this->_getAllParams();

		if ( $params['NU_SEQ_CONFIGURACAO'] ) {
			$arDados['NU_SEQ_CONFIGURACAO'] = $params['NU_SEQ_CONFIGURACAO'];
		}

		$this->setTitles(array('coMesorregiao', 'SConfiguracao', 'nuSeqQuantidadeTurma', 'qtTurmas', 'coRegiao',));
		$this->setNameList(
				array('CO_MESORREGIAO', 'NU_SEQ_CONFIGURACAO', 'NU_SEQ_QUANTIDADE_TURMA', 'QT_TURMAS', 'CO_REGIAO',));

		$buss = new Fnde_Sice_Business_Regiao();
		$resultRegiao = $buss->search(array('SG_REGIAO', 'NO_REGIAO'));

		$form = new QuantidadeTurma_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setRegiao($resultRegiao, $arDados['SG_REGIAO']);

		$form->setAction(
				$this->view->baseUrl() . '/index.php/manutencao/quantidadeturma/form/NU_SEQ_CONFIGURACAO/'
						. $params['NU_SEQ_CONFIGURACAO'])->setMethod('post')->setAttrib('id', 'form');

		return $form;
	}

	/**
	 * Salva os dados do formulário para o banco
	 *
	 * @access public
	 *
	 * @author Daniel Wilson <daniel.alvarenga@fnde.gov.br>, Jânio Eduardo <janio.magalhaes@fnde.gov.br> e Orion Teles <orion.mesquita@fnde.gov.br>
	 * @since 18/03/2010
	 */
	public function selecionaRegiaoAction() {
		$this->setTitle($this->stTitle);
		$this->setSubtitle('Cadastro');

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			parent::setSearchParam();
		}

		//recupera valores da sessão
		$arFilter = $this->_request->getParams();
		$form = $this->getForm();
		$form->populate($arFilter);

		// Recupera o objeto de formulário para validação
		$this->view->form = $form;

		$arParam = $this->_getAllParams();
		$rsRegistros = array();

		$obBusiness = new Fnde_Sice_Business_QuantidadeTurma();

		//Ao vizualizar preenche as quantidades cadastradas no banco.
		if ( $arParam['NU_SEQ_CONFIGURACAO'] ) {
			$rsQtdeTurmas = $obBusiness->searchQtTurma(
					array('SG_REGIAO' => $arParam['REGIAO'], 'NU_SEQ_CONFIGURACAO' => $arParam['NU_SEQ_CONFIGURACAO']));
			if ( !$rsQtdeTurmas ) {
				$rsQtdeTurmas = true;
			}
		}

		if ( isset($arParam['REGIAO']) && $arParam['REGIAO'] != "" ) {
			$sgRegiao = $form->getElement("sg_regiao");
			$sgRegiao->setValue($arParam['REGIAO']);
			$rsRegistros = $obBusiness->obterTurmasPorRegiao($arParam['REGIAO'], $arParam['NU_SEQ_CONFIGURACAO']);
			if ( !count($rsRegistros) ) {
				$this->addInstantMessage(Fnde_Message::MSG_INFO,
						'Não foram encontrados registros para os filtros informados!');
			}
		} else {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
		}

		$this->view->registros = $rsRegistros;

		$gridQuantidadeTurmas = $form->getElement("gridQuantidadeTurmas");
		$gridQuantidadeTurmas->setValue(
				$this->retornaHtmlGridQuantidadeTurma($rsRegistros, $rsQtdeTurmas));

		return $this->render('form');
	}

	/**
	 * Método acessório get de nameList
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Método acessório set de nameList
	 * @param array $arList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Método acessório get de titles
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Método acessório set de titles
	 */
	public function setTitles( $titles ) {
		$this->_arTitles = $titles;
	}

	/**
	 * Método acessório get que recupera o formulário de pesquisa da tela de Quantidade de Turmas.
	 * @param array $arDados
	 * @param array $obGrid
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new QuantidadeTurma_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/quantidadeturma/list')->setMethod('post');

		return $form;
	}

	/**
	 * Método acessório get de arTitlesList
	 */
	public function getArTitlesList() {
		return array('coMesorregiao', 'SConfiguracao', 'nuSeqQuantidadeTurma', 'qtTurmas', 'coRegiao',);
	}

	/**
	 * Método que retorna o código em HTML da tabela de quantidade de turmas.
	 * @param array $results
	 * @param array $rsQtdeTurmas
	 * @param unknown_type $v
	 */
	public function retornaHtmlGridQuantidadeTurma( $results, $rsQtdeTurmas = null) {
		$arParam = $this->_getAllParams();

		$obBusiness = new Fnde_Sice_Business_Configuracao();
		$dadosConfig = $obBusiness->getDadosConfiguracaoById($arParam['NU_SEQ_CONFIGURACAO']);

		$limiteTurma = '';
		$totais = '';
		if($dadosConfig["configuracao"]["ST_CONFIGURACAO"] == 'D'){
			$limiteTurma = "disabled = 'disabled'";
			$totais = "disabled = 'disabled'";
		}

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		if (!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) &&
			!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)){
			$limiteTurma = "disabled = 'disabled'";
		}
		if(!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
			$totais = "disabled = 'disabled'";
		}

		$html = '<div class="listagem">';
		$i = 1;
		$html .= '<input type="hidden" name="QTD_MESOREGIAO" id="QTD_MESOREGIAO" value="um teste a mais">';
		foreach ( $results as $array ) {
			$html .= "<table class='tabelaQtdTurma'>";
			$html .= "<caption>";
			$html .= "<div align='center'>" . $array[0]['NO_UF'] . ' - ' . $array[0]['SG_UF'] . "</div>";
			$html .= "</caption>";
			$html .= "<tr>";
			$html .= "<th>Mesorregião</th>";
			$html .= "<th>Qtd de Municípios</th>";
			$html .= "<th>Turmas Cadastradas</th>";
			$html .= "<th>Turmas Disponíveis</th>";
			$html .= "<th>Limite de Turmas</th>";
			$html .= "</tr>";
			$totMun = 0;
			$total = 0;
			$totalTurmas = 0;
			foreach ( $array as $linha ) {
				$value = 0;
				//VISUALIZAR, verificando se já existe valor cadastrado
				foreach ( $rsQtdeTurmas as $quantidade ) {
					if ( $linha['CO_MESO_REGIAO'] == $quantidade['CO_MESORREGIAO'] ) {
						$value = $quantidade['QT_TURMAS'];
						break;
					}
				}
				$total += $value;
				$html .= "<tr>";
				$html .= "<td width='40%'>";
				$html .= $linha["NO_MESO_REGIAO"];
				$html .= "</td>";
				$html .= "<td width='15%' align='center'>";
				$html .= "<div align='center'>";
				$var = $this->view->baseUrl();
				$html .= "<a href='" . $var . "/index.php/manutencao/mesoregiao/list/CO_MESO_REGIAO/"
						. $linha['CO_MESO_REGIAO'] . "' class='dialog' >" . $linha['TOTAL_MUNICIPIOS'] . "</a>";
				$totMun += $linha['TOTAL_MUNICIPIOS'];
				$html .= "</div>";
				$html .= "</td>";
				$html .= "<td width='15%' align='center'>";
				$html .= $linha["TURMAS_CADSATRADAS"];
				$totalTurmas += $linha["TURMAS_CADSATRADAS"];
				$html .= "</td>";
				$html .= "<td width='15%' align='center'>";
				$html .= $value - $linha["TURMAS_CADSATRADAS"];
				$html .= "</td>";
				$html .= "<td width='15%' align='center' nowrap>";
				$html .= "<div class='teste' align='center'>";
				$html .= "<input type='text' maxlength='3' tabindex=\"$i\" name='valQtdTurma"
						. $linha['CO_MESO_REGIAO'] . "' class='inteiro mesorregiao' data-min-turmas='" . $linha['TURMAS_CADSATRADAS'] ."' value='" . $value . "' size='2' "
						. $limiteTurma . " />";
				$html .= "</div>";
				$html .= "</td>";
				$html .= "</tr>";
			}
			$html .= "<tr style='background-color: #e6eff4;'>";
			$html .= "<td align='center'>";
			$html .= "<b>Totais:</b>";
			$html .= "</td>";
			$html .= "<td>";
			$html .= "<div align='center'><b>";
			$html .= $totMun;
			$html .= "</b></div>";
			$html .= "</td>";
			$html .= "<td align='center'><b>";
			$html .= $totalTurmas;
			$html .= "</b></td>";
			$html .= "<td align='center'><b>";
			$html .= $linha["TOTAL_TURMAS"]-$totalTurmas;
			$html .= "</b></td>";
			$html .= "<td>";
			$html .= "<div align='center'>";
			$html .= "<input type='text' maxlength='3' tabindex=\"$i\" name='valQtdTurma" . $array[0]['SG_UF'] . "'
						class='inteiro total' value='" . ($linha["TOTAL_TURMAS"] ? $linha["TOTAL_TURMAS"] : 0) . "' size='3'
						" . $totais . " style='font-weight:bold'/>";
			$html .= "</div>";
			$html .= "</td>";
			$html .= "</tr>";
			$html .= "</table>";
			$html .= "<br />";
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Método para gravar a quantidade de turmas no banco de dados.
	 * 
	 * @since 18/03/2010
	 */
	public function salvarQuantidadeTurmaAction() {

		$this->setTitle('QuantidadeTurma');
		$this->setSubtitle('Cadastro');

		$arFilter = $this->_request->getParams();

		$form = $this->getForm();
		$form->populate($arFilter);

		if ( !$form->isValid($arFilter) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			$this->view->form = $form;
			return $this->render("form");
		}

		// Recupera o objeto de formulário para validação
		$this->view->form = $form;

		$obBusiness = new Fnde_Sice_Business_QuantidadeTurma();
		$obBusinessTotal = new Fnde_Sice_Business_QuantidadeTotalTurma();
		$params = $form->getValues();
		$results = $obBusiness->obterTurmasPorRegiao($params['SG_REGIAO']);

		$arQtdTurma = array();


		$configuracaoTurmas = $obBusiness->obterTurmasPorRegiao($params['SG_REGIAO'],$arFilter['NU_SEQ_CONFIGURACAO'] );

		// Verifica se o total limite de turma é menor que o total de turmas cadastradas para aquela mesoregiao
		foreach($configuracaoTurmas as $configUF){
			foreach($configUF as $config){
				if(isset($_REQUEST['valQtdTurma' . $config['CO_MESO_REGIAO']]) && (int)$_REQUEST['valQtdTurma' . $config['CO_MESO_REGIAO']] < (int)$config['TURMAS_CADSATRADAS']){
					$this->addMessage(Fnde_Message::MSG_ALERT, 'O valor atribuído ao Limite de Turmas é inferior ao número de Turmas Cadastradas. Favor redistribuir.');
					$this->_redirect("manutencao/quantidadeturma/visualizar-quantidade/v/1/NU_SEQ_CONFIGURACAO/" . $arFilter['NU_SEQ_CONFIGURACAO']);
					return;
				}
			}
		}

		foreach ( $results as $array ) {
			foreach ( $array as $i => $linha ) {

				if($_REQUEST['valQtdTurma' . $linha['CO_MESO_REGIAO']] != ''){
					$qtdTurma = $obBusiness->getQuantidadeTurmaByConfigMesorregiao($arFilter['NU_SEQ_CONFIGURACAO'],
							$linha['CO_MESO_REGIAO']);

					$qtTurma = $_REQUEST['valQtdTurma' . $linha['CO_MESO_REGIAO']];
					$arElemento = array();

					$arElemento['NU_SEQ_QUANTIDADE_TURMA'] = $qtdTurma['NU_SEQ_QUANTIDADE_TURMA'] ? $qtdTurma['NU_SEQ_QUANTIDADE_TURMA']
							: null;
					$arElemento['CO_MESORREGIAO'] = $linha['CO_MESO_REGIAO'];
					$arElemento['QT_TURMAS'] = $qtTurma;
					$arElemento['SG_REGIAO'] = $params['SG_REGIAO'];
					$arElemento['NU_SEQ_CONFIGURACAO'] = $arFilter['NU_SEQ_CONFIGURACAO'];
					$arQtdTurma[] = $arElemento;
				}

				//Salvar Total Turma
				if(count($array) - 1 == $i && $_REQUEST['valQtdTurma' . $linha['SG_UF']] != ''){
					$qtdTotalTurma = $obBusinessTotal->getQuantidadeTurmaByConfigUf($arFilter['NU_SEQ_CONFIGURACAO'],
						$linha['SG_UF']);

					$qtTotalTurma = $_REQUEST['valQtdTurma' . $linha['SG_UF']];

					$arTotal = array();
					$arTotal['NU_SEQ_QUANTIDADE_TOTAL_TURMA'] = $qtdTotalTurma['NU_SEQ_QUANTIDADE_TOTAL_TURMA'] ? $qtdTotalTurma['NU_SEQ_QUANTIDADE_TOTAL_TURMA']
						: null;
					$arTotal['SG_UF'] = $linha['SG_UF'];
					$arTotal['QT_TOTAL_TURMA'] = $qtTotalTurma;
					$arTotal['NU_SEQ_CONFIGURACAO'] = $arFilter['NU_SEQ_CONFIGURACAO'];
					$arQtdTotalTurma[] = $arTotal;
				}
			}
		}

		// Salva o registro Turma
		$obModel = new Fnde_Sice_Model_QuantidadeTurmas();

		foreach ( $arQtdTurma as $var ) {

			if ( $var['NU_SEQ_QUANTIDADE_TURMA'] ) {
				$lastId = $obModel->update($var, "NU_SEQ_QUANTIDADE_TURMA = " . $var["NU_SEQ_QUANTIDADE_TURMA"]);
			} else {
				$lastId = $obModel->insert($var);
			}

			if ( !$lastId ) {
				$this->addMessage(Fnde_Message::MSG_ERROR, $obBusiness->getMensagem());
				break;
			}
		}

		// Salva o registro Total Turma
		$obModel = new Fnde_Sice_Model_QuantidadeTotalTurmas();
		foreach ( $arQtdTotalTurma as $var ) {
			if ( $var['NU_SEQ_QUANTIDADE_TOTAL_TURMA'] ) {
				$lastId = $obModel->update($var, "NU_SEQ_QUANTIDADE_TOTAL_TURMA = " . $var["NU_SEQ_QUANTIDADE_TOTAL_TURMA"]);
			} else {
				$lastId = $obModel->insert($var);
			}

			if ( !$lastId ) {
				$this->addMessage(Fnde_Message::MSG_ERROR, $obBusiness->getMensagem());
				break;
			}
		}

		$this->addMessage(Fnde_Message::MSG_SUCCESS, "Dados registrados com sucesso.");
		$this->_redirect("manutencao/quantidadeturma/visualizar-quantidade/v/1/NU_SEQ_CONFIGURACAO/" . $arFilter['NU_SEQ_CONFIGURACAO']);
	}

	/**
	 * Mètodo para visualizar a quantidade de turmas.
	 */
	public function visualizarQuantidadeAction() {
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$this->setTitle('Configuração');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
			$menu[$this->getUrl('manutencao', 'configuracao', 'form', ' ')] =  'cadastrar';
		}
		$menu[$this->getUrl('manutencao', 'configuracao', 'list', ' ')] = 'filtrar';
		$this->setActionMenu($menu);

		$arDados['v'] = $this->getRequest()->getParam("v");
		$arDados['NU_SEQ_CONFIGURACAO'] = $this->getRequest()->getParam("NU_SEQ_CONFIGURACAO");

		//Recupera o objeto de formulário para validação
		$this->view->form = $this->getForm($arDados);

		$this->render('form');
		if(is_null($arDados['NU_SEQ_CONFIGURACAO'])){
			$this->addMessage(Fnde_Message::MSG_ERROR, "Favor cadastrar a configuração.");

			$this->_redirect("manutencao/configuracao/form");
		}
	}

}
