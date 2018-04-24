<?php

/**
 * Controller do Turma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class Secretaria_AvaliacaoPedagogicaController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function listAction() {
		$this->validaAcesso($this->_request->isPost());
		
		//Retirando os valores de cursistas da sessao, usado na tela matricular cursistas
		$_SESSION['rsCursista'] = null;

		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Filtrar');

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		/*
		 * sice_coordenador_nacional_administrador 
		 * sice_coordenador_nacional_equipe 
		 * sice_coordenador_nacional_gestor 
		 * sice_coordenador_estadual 
		 * sice_articulador 
		 * sice_tutor 
		 * sice_cursista 
		 * 
		 */ 
		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		//recupera valores da sessão
		$arFilter = $this->getSearchParamAvaliacaoPedagogica();
		$form = $this->getFormFilter($arFilter);
		$form->populate($arFilter);

		$rsRegistros = array();
		if ( $this->_request->isPost() || !empty($arFilter) ) {
			if ( $form->isValid($arFilter) ) {
				$obBusiness = new Fnde_Sice_Business_AvaliacaoPedagogica();
				$arParams = $this->getParams($arFilter);

				$perfilUsuarioLog = $usuarioLogado->credentials;
				$businessComponetes = new Fnde_Sice_Business_Usuario();
				$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
				if ( $cpfUsuarioLogado ) {
					$arUsuario = $businessComponetes->getUsuarioByCpf($cpfUsuarioLogado);
				}
				$rsRegistros = $obBusiness->pesquisaAvaliacaoPedagogica($arParams, $arUsuario, $perfilUsuarioLog);
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
		if ( $rsRegistros ) {
			$rowAction = array(
					'visualizar' => array('label' => 'Visualisar',
							'url' => $this->view->Url(
									array('action' => 'visualizar-avaliacao-pedagogica', 'NU_SEQ_TURMA' => '')) . '%s',
							'params' => array('NU_SEQ_TURMA'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')));/*,
					'Avaliar Cursista' => array('label' => 'Avaliar Cursista',
							'url' => $this->view->Url(
									array('controller'=>'avaliarcursista','action' => 'avaliar-cursista', 'NU_SEQ_TURMA' => '')) . '%s',
							'params' => array('NU_SEQ_TURMA'), 'title' => 'Avaliar Cursista',
							'attribs' => array('class' => 'icoAvaliar', 'title' => 'Avaliar Cursista')),
					'Finalizar Turma' => array('label' => 'Finalizar Turma',
						'url' => $this->view->Url(
								array('controller'=>'FinalizarTurma','action' => 'finalizar-turma', 'NU_SEQ_TURMA' => '')) . '%s',
						'params' => array('NU_SEQ_TURMA'), 'title' => 'Finalizar Turma',
						'attribs' => array('class' => 'icoHomologar', 'title' => 'Finalizar Turma')));*/
			$arrHeader = array('<center>ID</center>', '<center>UF</center>', '<center>Município</center>',
					'<center>Curso</center>', '<center>Tutor</center>', '<center>Articulador</center>',
					'<center>Data início</center>', '<center>Data fim prevista</center>',
					'<center>Data finalização</center>', '<center>Situação</center>',);

			$grid = new Fnde_View_Helper_DataTables();

            $arrayMaisAcoes = array();

            if(
                in_array(Fnde_Sice_Business_Componentes::TUTOR, $usuarioLogado->credentials) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $usuarioLogado->credentials) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $usuarioLogado->credentials) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $usuarioLogado->credentials)
            ){
				$arrayMaisAcoes["Avaliar Cursista"] = $this->getUrl('secretaria', 'avaliarcursista', 'avaliar-cursista', true);
				$arrayMaisAcoes["Finalizar Turma"] = $this->getUrl('secretaria', 'finalizarturma', 'finalizar-turma-ativa', true);
			}

			$grid->setMainAction($arrayMaisAcoes);
			$grid->setActionColumn("<center>Ações</center>");
			$grid->setAutoCallJs(true);
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
					'Listagem de Turmas')->setRowAction($rowAction)->setId('NU_SEQ_TURMA')->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_RADIO)->setTableAttribs(array('id' => 'edit'));
		}
	}
	
	/**
	 * Metodo auxiliar que permite o acesso a pagina e configura os valores da sessao.
	 * @param boolean $boPost Se e post.
	 */
	private function validaAcesso($boPost) {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}
		
		//seta novos valores na sessão
		if ( $boPost ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}
	}

	/**
	 * Retorna o filtro de pesquisa.
	 * @param $arFilter
	 */
	private function getParams( $arFilter ) {
		$arParams = array();

		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_TIPO_CURSO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'UF_TURMA');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'CO_MESORREGIAO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'CO_MUNICIPIO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_USUARIO_TUTOR');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_USUARIO_ARTICULADOR');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_TURMA');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_MODULO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'NU_SEQ_CURSO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'DT_INICIO');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'DT_FIM');
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'ST_TURMA');
		if ( $arFilter['DT_FINAL'] ) {
			$arFilter['DT_FINALIZACAO'] = $arFilter['DT_FINAL'];
		}
		$this->montaParamsAvalPedagogica($arParams, $arFilter, 'DT_FINALIZACAO');

		return $arParams;
	}

	/**
	 * Metodo auxiliar para montar o arParams conforme a descricao passada como parametro.
	 * @param $arParams
	 * @param $arFilter
	 * @param $descricao
	 */
	private function montaParamsAvalPedagogica( &$arParams, $arFilter, $descricao ) {
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter[$descricao]) ) {
			$arParams[$descricao] = $arFilter[$descricao];
		}
	}

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function formAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');

		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		$turma = new Fnde_Sice_Business_Turma();
		if ( $this->getRequest()->getParam("NU_SEQ_TURMA") ) {
			$arDados = $turma->getTurmaPorId($this->getRequest()->getParam("NU_SEQ_TURMA"));
		}

		//Recuperando array de dados extras para setar valores extras no formulário
		//$arExtra = $this->getArExtraFormulario();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados);

		if ( $arDados['NU_SEQ_TURMA'] ) {
			$this->view->form = $form->populate($arDados);
		} else {
			$this->view->form = $this->getForm();
		}
		$this->render('form');
	}

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

		$form = new AvaliacaoPedagogica_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));

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
	 * Visualiza avaliacao pedagogica.
	 */
	public function visualizarAvaliacaoPedagogicaAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Visualizar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');

		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		$turma = new Fnde_Sice_Business_Turma();
		if ( $this->getRequest()->getParam("NU_SEQ_TURMA") ) {
			$arDados = $turma->getTurmaPorId($this->getRequest()->getParam("NU_SEQ_TURMA"));
		}

		//Recuperando array de dados extras para setar valores extras no formulário
		//$arExtra = $this->getArExtraFormulario();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados);

		$elementos = $form->getElements();

		foreach ( $elementos as $elemento ) {
			if ( $elemento->getName() != "cancelar" ) {
				$elemento->setAttrib("disabled", true);
			}
		}

		//$form->setElements($elementos);

		$this->view->form = $form->populate($arDados);

		$this->render('form');
	}

	/**
	 * Metodo acessorio get de namelist.
	 */
	public function getNameList() {
		return $this->_arList;
	}

	/**
	 * Metodo acessorio set de namelist.
	 * @param array $arList
	 */
	public function setNameList( $arList ) {
		$this->_arList = $arList;
	}

	/**
	 * Metodo acessorio get de titles.
	 */
	public function getTitles() {
		return $this->_arTitles;
	}

	/**
	 * Metodo acessorio set de titles
	 * @param array $arTitles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Retorna formulario de pesquisa.
	 * @param array $arDados
	 */
	public function getFormFilter( $arDados = array() ) {
		$form = new AvaliacaoPedagogica_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/avaliacaopedagogica/list')->setMethod('post');

		$this->setTipoCurso($form);
		$this->setUf($form);
		$this->setMesorregiao($form);
		$this->setMunicipio($form);
		$this->setTutor($form);
		$this->setArticulador($form);
		$this->setModulo($form);
		$this->setCurso($form);

		return $form;
	}

	/**
	 * Metodo acessorio get de titles.
	 */
	public function getArTitlesList() {
		return array('dtInicio', 'coMunicipio', 'nuSeqTurma', 'dtFinalizacao', 'nuSeqUsuarioArticulador',
				'coMesorregiao', 'dtFim', 'ufTurma', 'nuSeqUsuarioTutor', 'nuSeqCurso', 'stTurma',);
	}

	/**
	 * Renderiza mesorregiao de acordo com os dados selecionados.
	 */
	public function renderizaMesoregiaoAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessUF = new Fnde_Sice_Business_Uf();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);

		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		$arParam = $this->_getAllParams();

		//Carregando opcoes da Mesorregiao.
		$retorno['MESORREGIAO'] = $this->getMesorregiao($arParam['UF_TURMA'], $perfilUsuario, $arUsuario);

		//Carregando opcoes do Municipio.
		$options = array();
		if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
				|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$arMunicipio = $businessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
		} else {
			$arMunicipio = $businessUF->getMunicipioPorUf($arParam['UF_TURMA']);
		}
		foreach ( $arMunicipio as $municipio ) {
			$options[] = array(utf8_encode($municipio['CO_MUNICIPIO_IBGE']), utf8_encode($municipio['NO_MUNICIPIO']));
		}

		$retorno['MUNICIPIO'] = $options;

		//Setando valor da Mesorregiao de acordo com Municipio selecionado.
		if ( $arParam['CO_MUNICIPIO'] ) {
			$mesorregiao = $businessMesoregiao->getMesoRegiaoPorMunicipio($arParam['CO_MUNICIPIO']);
			$retorno['MESORREGIAO_VAL'] = $mesorregiao[0]['CO_MESO_REGIAO'];
		}

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Retorna as opcoes de Mesorregiao de acordo com o perfil autenticado no sistema.
	 * @param $ufSelecionada
	 * @param $perfilUsuario
	 * @param $arUsuario
	 */
	private function getMesorregiao( $ufSelecionada, $perfilUsuario, $arUsuario ) {
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

		$arMesorregiao = $businessMesoregiao->getMesoRegiaoPorUF($ufSelecionada);
		$options = array();
		foreach ( $arMesorregiao as $mesorregiao ) {
			if ( !in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
					&& !in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
				$options[] = array(utf8_encode($mesorregiao['CO_MESO_REGIAO']),
						utf8_encode($mesorregiao['NO_MESO_REGIAO']));
			} elseif ( $arUsuario['CO_MESORREGIAO'] == $mesorregiao['CO_MESO_REGIAO'] ) {
				$options[] = array(utf8_encode($mesorregiao['CO_MESO_REGIAO']),
						utf8_encode($mesorregiao['NO_MESO_REGIAO']));
				continue;
			}
		}

		return $options;
	}

	/**
	 * Renderiza campos da tela ao mudar o valor de municipio.
	 */
	public function municipioChangeAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$arParam = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		//Setando o valor da Mesorregiao de acordo com o Municipio selecionado.
		$mesorregiao = $businessMesoregiao->getMesoRegiaoPorMunicipio($arParam['CO_MUNICIPIO']);
		$retorno['MESORREGIAO_VAL'] = $mesorregiao[0]['CO_MESO_REGIAO'];

		//Carregando as opcoes de Tutor.
		$retorno['TUTOR'] = $this->getTutor($arParam["CO_MUNICIPIO"], $perfilUsuario, $arUsuario);

		//Carregando as opcoes de Articulador.
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario)
				||in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
			$arArticulador = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "5", "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$arArticulador = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$arArticulador = $businessUsuario->getArticuladorPorTurtor($arUsuario['NU_SEQ_USUARIO']);
		} else if ( $arParam["CO_MUNICIPIO"] ) {
			$arArticulador = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "5", "CO_MUNICIPIO_PERFIL" => $arParam["CO_MUNICIPIO"]));
		}
		$options = array();
		foreach ( $arArticulador as $articulador ) {
			$options[] = array(utf8_encode($articulador['NU_SEQ_USUARIO']), utf8_encode($articulador['NO_USUARIO']));
		}
		$retorno['ARTICULADOR'] = $options;

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Retorna as opcoes do combo de Tutor de acordo com o perfil do usuario logado no sistema.
	 * @param $municipio
	 * @param $perfilUsuario
	 * @param $arUsuario
	 */
	private function getTutor( $municipio, $perfilUsuario, $arUsuario ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario) 
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)) {
			$arTutor = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "6", "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		} elseif ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$arTutor = $businessUsuario->getTutorPorArticulador($arUsuario['NU_SEQ_USUARIO']);
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$arTutor = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
		} else if ( $municipio ) {
			$arTutor = $businessUsuario->search(array("NU_SEQ_TIPO_PERFIL" => "6", "CO_MUNICIPIO_PERFIL" => $municipio));
		}
		$options = array();
		foreach ( $arTutor as $tutor ) {
			$options[] = array(utf8_encode($tutor['NU_SEQ_USUARIO']), utf8_encode($tutor['NO_USUARIO']));
		}

		return $options;
	}

	/**
	 * Renderiza municipio de acordo com os dados selecionados.
	 */
	public function renderizaMunicipioAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessUF = new Fnde_Sice_Business_Uf();
		$arParam = $this->_getAllParams();

		if ( $arParam['CO_MESORREGIAO'] ) {
			$arMunicipio = $businessMesoregiao->getMunicipioPorMesoRegiao($arParam['CO_MESORREGIAO']);
		} elseif ( $arParam['UF_TURMA'] ) {
			$arMunicipio = $businessUF->getMunicipioPorUf($arParam['UF_TURMA']);
		}

		foreach ( $arMunicipio as $municipio ) {
			$options[] = array(utf8_encode($municipio['CO_MUNICIPIO_IBGE']), utf8_encode($municipio['NO_MUNICIPIO']));
		}

		$retorno['MUNICIPIO'] = $options;

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Renderiza município pof UF para a tela de Cadastro/Edição
	 */
	public function renderizaMunicipioCadAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Renderiza mesorregião por município para a tela de cadastro/edição.
	 */
	public function renderizaMesorregiaoCadAction() {

		$this->_helper->layout()->disableLayout();

		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Renderiza Curso por Tipo de Curso
	 *
	 * @author diego.matos
	 * @since 26/04/2012
	 */

	public function renderizaCursoPorTipoCadAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Renderiza informações relacionadas ao curso selecionado
	 * 
	 * @author diego.matos
	 * @since 27/04/2012
	 */
	public function renderizaInfoCursoCadAction() {
		$this->setTitle('Avaliação pedagógica');
		$this->setSubtitle('Cadastrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'avaliacaopedagogica', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Limpa dados de pesquisa da sessao
	 */
	public function clearSearchAction() {

		//limpa sessão
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessão
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * Recupera dados de pesquisa da sessao.
	 */
	public function getSearchParamAvaliacaoPedagogica() {
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
	 * Retorna HTML de alunos matriculados
	 * @param int $codTurma Codigo da turma.
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table id='tbCursista' style='text-align: center'>";
		$html .= "<thead><tr><th style='text-align: center'>Contagem</th><th style='text-align: center'>Matrícula</th>";
		$html .= "<th style='text-align: center'>Nome</th><th style='text-align: center'>CPF</th><th style='text-align: center'>";
		$html .= "Nota tutor</th><th style='text-align: center'>Nota cursista</th><th style='text-align: center'>Nota total</th>";
		$html .= "<th style='text-align: center'>Situação</th></tr></thead>";
		$html .= "<tbody>";

		$count = 0;

		foreach ( $arAlunosMatriculados as $aluno ) {

			$nTut = 0;
			$nCur = 0;
			$notaTutor = 0;
			$notaCursista = 0;

			//Formatando valores
			if ( $aluno['NU_NOTA_TUTOR'] ) {
				$nota = str_replace(',', '.', $aluno['NU_NOTA_TUTOR']);
				$nTut = $nota;
				$notaTutor = number_format($nota, 2, ',', '.');
			}
			if ( $aluno['NU_NOTA_CURSISTA'] ) {
				$nota = str_replace(',', '.', $aluno['NU_NOTA_CURSISTA']);
				$nCur = $nota;
				$notaCursista = number_format($nota, 2, ',', '.');
			}

			$html .= "<tr><td>" . ++$count . "</td><td>" . $aluno['NU_MATRICULA']
					. "</td><td style='text-align: left'>" . $aluno['NO_USUARIO'] . "</td><td> ";
			$html .= Fnde_Sice_Business_Componentes::formataCpf($aluno['NU_CPF']) . "</td> ";
			$html .= "<td><center><input type='text' name='NU_NOTA_TUTOR' id='NU_NOTA_TUTOR' disabled='disabled' value='$notaTutor' ";
			$html .= "class='decimal' maxlength=4'></center></td> ";
			if ( $notaCursista ) {
				$html .= "<td><center><input type='text' name='NU_NOTA_CURSISTA'id='NU_NOTA_CURSISTA' ";
				$html .= "value='$notaCursista' disabled='disabled' class='decimal' maxlength=4></center></td> ";
			} else {
				$html .= "<td>Não avaliou</td>";
			}

			$notaTotal = $nTut + $nCur;
			$notaTotal = number_format($notaTotal, 2, ',', '.');

			$html .= "<td><center><input type='text' name='NU_NOTA_TOTAL'id='NU_NOTA_TOTAL' value='$notaTotal' ";
			$html .= " disabled='disabled' class='decimal' maxlength=4></center></td> ";
			//Terminar de preencher a situação do cursista.
			$html .= "<td>" . $aluno['DS_SITUACAO'];
			$html .= "</td> ";
			$html .= "</tr>";
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Adiciona os valores de Tipo de Turma ao fomulário 
	 * @param form $form
	 */
	private function setTipoCurso( $form ) {
		$arTipoCurso = Fnde_Sice_Business_Componentes::getAllByTable("TipoCurso",
				array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

		$form->setTipoCurso($arTipoCurso);
	}

	/**
	 * Adiciona os valores de UF ao fomulário 
	 * @param form $form
	 */
	private function setUf( $form ) {
		$businessUF = new Fnde_Sice_Business_Uf();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$result = $businessUF->search(array('SG_UF'));

		$arUf = array(null => "Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {

			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)
					|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ) {
				$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
			} else if ( $arUsuario['SG_UF_ATUACAO_PERFIL'] == $result[$i]['SG_UF'] ) {
				$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
			}
		}

		$form->setUf($arUf);
	}

	/**
	 * Adiciona os valores de Mesorregiao ao fomulário 
	 * @param form $form
	 */
	private function setMesorregiao( $form ) {
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDados = $this->_getAllParams();

		$arMesorregiao = array(null => "Selecione");
		if ( $arDados["UF_TURMA"] ) {
			$resut = $businessMesoregiao->getMesoRegiaoPorUF($arDados["UF_TURMA"]);

			for ( $i = 0; $i < count($resut); $i++ ) {
				if ( !in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
						&& !in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
					$arMesorregiao[$resut[$i]['CO_MESO_REGIAO']] = $resut[$i]['NO_MESO_REGIAO'];
				} else if ( $arUsuario['CO_MESORREGIAO'] == $resut[$i]['CO_MESO_REGIAO'] ) {
					$arMesorregiao[$resut[$i]['CO_MESO_REGIAO']] = $resut[$i]['NO_MESO_REGIAO'];
				}
			}

		}

		$form->setMesorregiao($arMesorregiao);
	}

	/**
	 * Adiciona os valores de Município ao fomulário 
	 * @param form $form
	 */
	private function setMunicipio( $form ) {
		$businessUF = new Fnde_Sice_Business_Uf();
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDados = $this->_getAllParams();

		$arMunicipio = array(null => "Selecione");
		if ( $arDados["CO_MESORREGIAO"] ) {
			$resut = $businessMesoregiao->getMunicipioPorMesoRegiao($arDados["CO_MESORREGIAO"]);
			for ( $i = 0; $i < count($resut); $i++ ) {
				$arMunicipio[$resut[$i]['CO_MUNICIPIO_IBGE']] = $resut[$i]['NO_MUNICIPIO'];
			}
		} else if ( $arDados["UF_TURMA"] ) {
			if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
					|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
				$resut = $businessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
			} else {
				$resut = $businessUF->getMunicipioPorUf($arDados["UF_TURMA"]);
			}

			for ( $i = 0; $i < count($resut); $i++ ) {
				$arMunicipio[$resut[$i]['CO_MUNICIPIO_IBGE']] = $resut[$i]['NO_MUNICIPIO'];
			}
		}

		if ( $arDados["CO_MUNICIPIO"] ) {
			$resut = $businessMesoregiao->getMesoRegiaoPorMunicipio($arDados["CO_MUNICIPIO"]);
			$mesorregiao = $resut[0]['CO_MESO_REGIAO'];
		}

		$form->setMunicipio($arMunicipio, $mesorregiao);
	}

	/**
	 * Adiciona os valores de Tutor ao fomulário  
	 * @param form $form
	 */
	private function setTutor( $form ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDados = $this->_getAllParams();

		$arTutor = array(null => "Selecione");
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
			$rsTutor = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "6", "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$rsult = $businessUsuario->getTutorPorArticulador($arUsuario['NU_SEQ_USUARIO']);

			for ( $i = 0; $i < count($rsult); $i++ ) {
				$arTutor[$rsult[$i]['NU_SEQ_USUARIO']] = $rsult[$i]['NO_USUARIO'];
			}
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$rsTutor = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
		} else if ( $arDados["CO_MUNICIPIO"] ) {
			$rsult = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "6", "CO_MUNICIPIO_PERFIL" => $arDados["CO_MUNICIPIO"]));

			for ( $i = 0; $i < count($rsult); $i++ ) {
				$arTutor[$rsult[$i]['NU_SEQ_USUARIO']] = $rsult[$i]['NO_USUARIO'];
			}
		}

		if ( $rsTutor ) {
			foreach ( $rsTutor as $tutor ) {
				$arTutor[$tutor['NU_SEQ_USUARIO']] = $tutor['NO_USUARIO'];
			}
		}

		$form->setTutor($arTutor);
	}

	/**
	 * Adiciona os valores de Articulador ao fomulário 
	 * @param form $form
	 */
	private function setArticulador( $form ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDados = $this->_getAllParams();

		$arArticulador = array(null => "Selecione");
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
			$rsArticulador = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "5", "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
			$rsArticulador = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$result = $businessUsuario->getArticuladorPorTurtor($arUsuario['NU_SEQ_USUARIO']);

			for ( $i = 0; $i < count($result); $i++ ) {
				$arArticulador[$result[$i]['NU_SEQ_USUARIO']] = $result[$i]['NO_USUARIO'];
			}
		} else if ( $arDados["CO_MUNICIPIO"] ) {
			$result = $businessUsuario->search(
					array("NU_SEQ_TIPO_PERFIL" => "5", "CO_MUNICIPIO_PERFIL" => $arDados["CO_MUNICIPIO"]));

			for ( $i = 0; $i < count($result); $i++ ) {
				$arArticulador[$result[$i]['NU_SEQ_USUARIO']] = $result[$i]['NO_USUARIO'];
			}

		}

		if ( $rsArticulador ) {
			foreach ( $rsArticulador as $articulador ) {
				$arArticulador[$articulador['NU_SEQ_USUARIO']] = $articulador['NO_USUARIO'];
			}
		}

		$form->setArticulador($arArticulador);
	}

	/**
	 * Adiciona os valores de Módulo ao fomulário
	 * @param form $form
	 */
	private function setModulo( $form ) {
		$arModulo = Fnde_Sice_Business_Componentes::getAllByTable("Modulo", array("NU_SEQ_MODULO", "DS_NOME_MODULO"));

		$form->setModulo($arModulo);
	}

	/**
	 * Adiciona os valores de Curso ao fomulário
	 * @param form $form
	 */
	private function setCurso( $form ) {
		$arCurso = Fnde_Sice_Business_Componentes::getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"));

		$form->setCurso($arCurso);
	}

}
