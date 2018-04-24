<?php

/**
 * Controller do Turma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class Financeiro_BolsaController extends Fnde_Sice_Controller_Action {
	protected $_stSistema = 'sice';

	/**
	 * Ação de listagem
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function listAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		unset($_SESSION['IDENTIFICADOR_LINHA_VERIF_PEND']);
		unset($_SESSION['IDENTIFICADOR_LINHA_SOLI_HOMOL']);
		unset($_SESSION['IDENTIFICADOR_LINHA_HOMOL_BOLS']);
		unset($_SESSION['SG_UF_VERIF_PEND']);
		unset($_SESSION['NO_REGIAO_VERIF_PEND']);

		$this->setTitle('Bolsas');
		$this->setSubtitle('Filtrar');

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sessão
		$arFilter = $this->getSearchParamBolsa();

		$form = $this->getFormFilter($arFilter);

		$rsRegistros = array();

		if ( !$this->isPostValido($this->_request->isPost(), $arFilter, $_POST) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_Bolsa();
				$arParams = $this->retornaFiltroPesquisa($form);
				
				$businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
				$periodo = $businessPeriodoVinc->getDatasPeriodoById(
						array("NU_SEQ_PERIODO_VINCULACAO" => $arParams['NU_SEQ_PERIODO_VINCULACAO']));
				
				$rsRegistros = null;
				$dataAtual = date('d/m/Y');
				$dataAtual = Fnde_Sice_Business_Componentes::dataBRToEUA($dataAtual);
				$dataFimPeriodo = Fnde_Sice_Business_Componentes::dataBRToEUA($periodo['DT_FINAL']);
				
				if ( strtotime($dataFimPeriodo) < strtotime($dataAtual) ) {
					if ( $arFilter['ST_BOLSA'] == "6" && !$obBusiness->isBolsaAntiga($arParams['NU_SEQ_PERIODO_VINCULACAO'])) { //6 = Não avaliada
						try {
							$rsRegistros = $obBusiness->gerarBolsas($arParams);
						} catch ( Exception $e ) {
							$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
						}
					}
					$rsRegistros = $obBusiness->pesquisarBolsas($arParams);
				}
				if ( !count($rsRegistros) ) {
					$this->addInstantMessage(Fnde_Message::MSG_INFO,
							'Nenhum registro localizado para o filtro informado.');
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
		$this->montaGrid($rsRegistros, $arParams);
	}

	/**
	 * Verifica se o posta é valido para prosseguir com o filtro
	 * @param  $isPost
	 * @param  $arFilter
	 * @param  $post
	 */
	private function isPostValido( $isPost, $arFilter, $post ) {

		if ( ( $isPost || isset($arFilter['startlist']) || isset($arFilter['start']) || !empty($arFilter) )
				&& !$post['CK_ACEITACAO'] ) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * 
	 * @param unknown_type $rsRegistros
	 */
	private function montaGrid( $rsRegistros, $arParams ) {

		$busBolsa = new Fnde_Sice_Business_Bolsa();
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		if ( $rsRegistros ) {

			for ( $i = 0; $i < count($rsRegistros); $i++ ) {
				$temp = 'R$ ' . number_format(( float ) $rsRegistros[$i]["VL_BOLSA"], 2, ',', '.');
				$rsRegistros[$i]["VL_BOLSA"] = $temp;
			}

			$rowAction = $this->definirRowAction($perfilUsuario);

			if($busBolsa->isBolsaAntiga($arParams['NU_SEQ_PERIODO_VINCULACAO'])){
				$arrHeader = array('<center>UF</center>', '<center>Mesorregião</center>', '<center>Perfil</center>',
						'<center>Situação</center>', '<center>Qtd Bolsas</center>', '<center>Valor Total</center>',);
			}else{
				$arrHeader = array('<center>UF</center>', '<center>Mesorregião</center>', '<center>Perfil</center>',
					'<center>Situação</center>','<center>Período de Vinculação da Bolsa</center>', 
					'<center>Qtd Bolsas</center>', '<center>Valor Total</center>',);
			}
			

			$grid = $this->view->dataTables();

			$arrayMaisAcoes = $this->configurarComboMaisAcoes($perfilUsuario);

			$grid->setMainAction($arrayMaisAcoes);
			$grid->setAutoCallJs(true);
			$grid->setActionColumn("<center>Ações</center>");
			$grid->setColumnsHidden(array('NU_SEQ_PERIODO_VINCULACAO', 'NU_SEQ_TIPO_PERFIL', 'IDENTIFICADOR_LINHA'));
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
					"<i>Bolsas por UF's e Situações</i>")->setRowAction($rowAction)->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setId('IDENTIFICADOR_LINHA')->setTableAttribs(
					array('id' => 'edit'));
		}
	}

	/**
	 * Função que retorna o filtro de pesquisa de bolsas.
	 * @author diego.matos
	 * @since 07/11/2012
	 * @param array $arFilter
	 */
	private function retornaFiltroPesquisa( $form ) {
		$arParams = array();

		foreach ( $form->getElements() as $elemento ) {
			if ( !Fnde_Sice_Business_Componentes::isEmpty($elemento->getValue()) ) {
				$arParams[$elemento->getName()] = $elemento->getValue();
			}
		}
		return $arParams;
	}

	/**
	 * Função para especificar o rowAction dos resultados da pesquisa baseado no perfil do usuário.
	 * @author diego.matos
	 * @since 07/11/2012
	 * @param array $perfilUsuario
	 */
	private function definirRowAction( $perfilUsuario ) {
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
			$rowAction = array(
					'avaliar' => array('label' => 'Avaliar',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'avaliarbolsas',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoAvaliar', 'title' => 'Avaliar')),
					
					'solicitarHomologacao' => array('label' => 'Solicitar Homologação',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'solicitarhomologacao',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoEnviarHomologar', 'title' => 'Solicitar Homologação')),
					'homologar' => array('label' => 'Homologar', 'url' => '#h',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoHomologar disabled', 'title' => 'Homologar')),
					'enviarSGB' => array('label' => 'Enviar SGB', 'url' => '#s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoValor disabled', 'title' => 'Enviar SGB')),
					'verificarPendencias' => array('label' => 'Verificar Pendências',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'verificarpendencias',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoPendencias', 'title' => 'Verificar Pendências')));
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$rowAction = array(
					'avaliar' => array('label' => 'Avaliar',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'avaliarbolsas',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoAvaliar', 'title' => 'Avaliar')),
					'solicitarHomologacao' => array('label' => 'Solicitar Homologação', 'url' => '#s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoEnviarHomologar disabled',
									'title' => 'Solicitar Homologação')),
					'homologar' => array('label' => 'Homologar', 'url' => '#h',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoHomologar disabled', 'title' => 'Homologar')),
					'enviarSGB' => array('label' => 'Enviar SGB', 'url' => '#s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoValor disabled', 'title' => 'Enviar SGB')),
					'verificarPendencias' => array('label' => 'Verificar Pendências',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'verificarpendencias',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoPendencias', 'title' => 'Verificar Pendências')));
		} else {
			$rowAction = array(
					'avaliar' => array('label' => 'Avaliar',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'avaliarbolsas',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoAvaliar', 'title' => 'Avaliar')),
					'solicitarHomologacao' => array('label' => 'Solicitar Homologação',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'solicitarhomologacao',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoEnviarHomologar', 'title' => 'Solicitar Homologação')),
					'homologar' => array('label' => 'Homologar',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'homologarbolsas',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoHomologar', 'title' => 'Homologar')),
					'enviarSGB' => array('label' => 'Enviar SGB',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'enviarsgb', 'action' => 'form',
											'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoValor', 'title' => 'Enviar SGB')),
					'verificarPendencias' => array('label' => 'Verificar Pendências',
							'url' => $this->view->Url(
									array('module' => 'financeiro', 'controller' => 'verificarpendencias',
											'action' => 'form', 'identificador_linha' => '')) . '%s',
							'params' => array('IDENTIFICADOR_LINHA'),
							'attribs' => array('class' => 'icoPendencias', 'title' => 'Verificar Pendências')));
		}
		return $rowAction;
	}

	/**
	 * Função para configurar o combo de "Mais Ações" baseado no perfil do usuário.
	 * @author diego.matos
	 * @since 07/11/2012
	 * @param array $perfilUsuario
	 */
	private function configurarComboMaisAcoes( $perfilUsuario ) {
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ) {
			$arrayMaisAcoes["Avaliar"] = $this->getUrl('financeiro', 'avaliarbolsas', 'form', true);
			$arrayMaisAcoes["Enviar SGB"] = $this->getUrl('financeiro', 'enviarsgb', 'form', true);
			$arrayMaisAcoes["Homologar"] = $this->getUrl('financeiro', 'homologarbolsas', 'form', true);
			$arrayMaisAcoes["Solicitar Homologação"] = $this->getUrl('financeiro', 'solicitarhomologacao', 'form', true);
			$arrayMaisAcoes["Verificar Pendências"] = $this->getUrl('financeiro', 'verificarpendencias', 'form', true);
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
			$arrayMaisAcoes["Avaliar"] = $this->getUrl('financeiro', 'avaliarbolsas', 'form', true);
			$arrayMaisAcoes["Solicitar Homologação"] = $this->getUrl('financeiro', 'solicitarhomologacao', 'form', true);
			$arrayMaisAcoes["Verificar Pendências"] = $this->getUrl('financeiro', 'verificarpendencias', 'form', true);
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$arrayMaisAcoes["Avaliar"] = $this->getUrl('financeiro', 'avaliarbolsas', 'form', true);
			$arrayMaisAcoes["Verificar Pendências"] = $this->getUrl('financeiro', 'verificarpendencias', 'form', true);
		} else {
			$arrayMaisAcoes = array();
		}
		return $arrayMaisAcoes;
	}

	/**
	 * Remove um registro de Turma
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function delTurmaAction() {
		$arParam = $this->_getAllParams();

		$obTurma = new Fnde_Sice_Business_Turma();
		$resposta = $obTurma->del($arParam['NU_SEQ_TURMA']);

		$resposta = ( string ) $resposta;

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Operação realizada com sucesso!");
		} elseif ( $resposta == '0' ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Exclusão do registro já realizada.");
		} else {
			$this->addMessage(Fnde_Message::MSG_ERROR,
					"Turma não pode ser excluído, pois o mesmo está associado." . $resposta);
		}

		$this->_redirect("/secretaria/turma/list");
	}

	/**
	 * Método acessório get para obter o formulário da tela de pesquisa.
	 * @param array $arDados
	 * @param array $obGrid
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new Bolsa_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/financeiro/bolsa/list')->setMethod('post');

		$this->setPerfilUsuarioLogado($form);
		$this->setAnoExercicio($form);
		$this->setSituacaoBolsa($form);
		$this->setRegiao($form);
		$this->setPeridoVinculacao($form, $arDados);
		$this->setUf($form, $arDados);
		$this->setMesoregiao($form, $arDados);

		return $form;
	}

	/**
	 * Método para limpar os dados da última pesquisa realizada.
	 */
	public function clearSearchAction() {

		//limpa sessão
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessão
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * Método para recuperar os parâmetros de pesquisa.
	 */
	public function getSearchParamBolsa() {
		$arFilter = array();
		$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
		$arSession = $this->urlFilterNamespace->param;
		$stUrlAtual = $this->_getParam('module') . '/' . $this->_getParam('controller') . '/'
				. $this->_getParam('action');
		$stUrlSession = $arSession['module'] . '/' . $arSession['controller'] . '/' . $arSession['action'];
		if ( $stUrlAtual == $stUrlSession ) {
			$arFilter = $arSession;
		}
		return $arFilter;
	}

	/**
	 * Carrega as informações do combo de período de vinculação
	 */
	public function carregaPeriodoVincPorAnoAction() {
		$arDados = $this->_getAllParams();
		$form = $this->getFormFilter($arDados);
		$this->setPeridoVinculacao($form, $arDados);
		$this->view->formFilter = $form;

		return $this->render('list');
	}

	/**
	 * Carrega as informações do combo de UF
	 */
	public function renderizaUfAction() {
		$arDados = $this->_getAllParams();
		$form = $this->getFormFilter($arDados);
		$this->setUf($form, $arDados);
		$this->view->formFilter = $form;

		return $this->render('list');
	}

	/**
	 * Carrega as informações do combo de mesorregião
	 */
	public function renderizaMesorregiaoAction() {
		$arDados = $this->_getAllParams();
		$form = $this->getFormFilter($arDados);
		$this->setMesoregiao($form, $arDados);
		$this->view->formFilter = $form;

		return $this->render('list');
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setPerfilUsuarioLogado( $form ) {
		//SETANDO OS VALORES DO COMBO DE PERFIL DE ACORDO COM O PERFIL LOGADO.
		$rsPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil",
				array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			$perfilRetorno[5] = $rsPerfil[5]; //Articulador
			$perfilRetorno[8] = $rsPerfil[8]; //Coord. Exec.
			$perfilRetorno[6] = $rsPerfil[6]; //Tut.
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ) {
			$perfilRetorno[5] = $rsPerfil[5]; //Articulador
			$perfilRetorno[6] = $rsPerfil[6]; //Tut.
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
			$perfilRetorno[6] = $rsPerfil[6]; //Tut.
		}

		$form->setPerfilUsuarioLogado($perfilRetorno);

	}
	/**
	 * 
	 * @param form $form
	 */
	private function setAnoExercicio( $form ) {
		$businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();

		$rsAnosPeriodoVinc = $businessPeriodoVinc->obterAnosPeriodoVinculacao();
		$arAno = array(null => "Selecione");
		foreach ( $rsAnosPeriodoVinc as $row ) {
			$arAno[$row['VL_EXERCICIO']] = $row['VL_EXERCICIO'];
		}

		$form->setAnoExercicio($arAno);
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setSituacaoBolsa( $form ) {
		$rsSituacaoBolsa = Fnde_Sice_Business_Componentes::getAllByTable("SituacaoBolsa",
				array("NU_SEQ_SITUACAO_BOLSA", "DS_SITUACAO_BOLSA"));

		$form->setSituacaoBolsa($rsSituacaoBolsa);
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setRegiao( $form ) {
		$businessRegiao = new Fnde_Sice_Business_Regiao();
		$result = $businessRegiao->search(array('SG_REGIAO', 'NO_REGIAO'));

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arRegiao = array(null => "Selecione");
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			for ( $i = 0; $i < count($result); $i++ ) {
				$arRegiao[$result[$i]['SG_REGIAO']] = $result[$i]['NO_REGIAO'];
			}
			$arRegiao["T"] = "TODOS";
		} else {
			//TRATAMENTO DA RAS010 QUE DIZ QUE PERFIS DIFERENTES DE COORD. ADM. SÓ VISUALIZAM A PRÓPRIA REGIÃO DE SUA UF
			$result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arUsuario["SG_UF_ATUACAO_PERFIL"]));
			$arRegiao[$result['SG_REGIAO']] = $result['NO_REGIAO'];
		}

		$form->setRegiao($arRegiao);

	}

	/**
	 * 
	 * @param form $form
	 */
	private function setPeridoVinculacao( $form, $arDados ) {
		$arPeriodo = array(null => "Selecione");
		if ( $arDados['VL_EXERCICIO'] ) {
			$businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
			$rsPeriodoVinc = $businessPeriodoVinc->getListPeriodoVinculacaoByAno(
					array("VL_EXERCICIO" => $arDados['VL_EXERCICIO']));
			foreach ( $rsPeriodoVinc as $res ) {
				$arPeriodo[$res['NU_SEQ_PERIODO_VINCULACAO']] = $res['DT_INICIAL'] . " à " . $res['DT_FINAL'];
			}
		}

		$form->setPeridoVinculacao($arPeriodo);
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setUf( $form, $arDados ) {
		$obBusinessUF = new Fnde_Sice_Business_Uf();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arUf = array(null => "Selecione");
		if ( $arDados['SG_REGIAO'] ) {
			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
				if ( $arDados['SG_REGIAO'] == "T" ) {
					$result = $obBusinessUF->search(array('SG_UF'));
				} else {
					$result = $obBusinessUF->getUfByRegiao($arDados['SG_REGIAO']);
				}

				for ( $i = 0; $i < count($result); $i++ ) {
					$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
				}
			} else {
				$result = $obBusinessUF->search(array('SG_UF' => $arUsuario['SG_UF_ATUACAO_PERFIL']));
				$arUf[$result[0]['SG_UF']] = $result[0]['SG_UF'];
			}
		}

		$form->setUf($arUf);
	}

	/**
	 * 
	 * @param form $form
	 */
	private function setMesoregiao( $form, $arDados ) {
		$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		$arMesorregiao = array(null => 'Selecione');
		if ( $arDados['SG_UF'] ) {
			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
				$result = $obBusinessMesoregiao->getMesoRegiaoPorUF($arDados['SG_UF']);
				for ( $i = 0; $i < count($result); $i++ ) {
					$arMesorregiao[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
				}
			} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
				$result = $obBusinessMesoregiao->getMesoRegiaoPorUF($arUsuario['SG_UF_ATUACAO_PERFIL']);
				for ( $i = 0; $i < count($result); $i++ ) {
					if ( $result[$i]['CO_MESO_REGIAO'] == $arUsuario['CO_MESORREGIAO'] ) {
						$arMesorregiao[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
					}
				}
			} else {
				$result = $obBusinessMesoregiao->getMesoRegiaoPorUF($arUsuario['SG_UF_ATUACAO_PERFIL']);
				for ( $i = 0; $i < count($result); $i++ ) {
					$arMesorregiao[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
				}
			}
		}

		$form->setMesoregiao($arMesorregiao);
	}
}
