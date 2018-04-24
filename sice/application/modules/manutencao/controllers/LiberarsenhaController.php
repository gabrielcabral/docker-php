<?php

/**
 * Controller do Liberar Senha
 *
 * @author rafael.paiva
 * @since 05/06/2012
 */

class Manutencao_LiberarSenhaController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author rafael.paiva
	 * @since 05/06/2012
	 */
	public function listAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Acesso');
		$this->setSubtitle('Liberar senha');

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		/*
		 *  1	Coordenador Nacional Administrador
		 *  2	Coordenador Nacional Equipe
		 *  3	Coordenador Nacional Gestor
		 *  4	Coordenador Estadual
		 *  5	Articulador
		 *  6	Tutor
		 *  7	Cursista
		 *
		 */

		//recupera valores da sessão
		$arFilter = $this->_getAllParams();

		$form = $this->getFormFilter($arFilter);
		$form->populate($arFilter);

		$rsRegistros = array();

		if ( $this->_request->isPost() ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_LiberarSenha();

				$arParams = $this->getParams($arFilter);

				$businessUsuario = new Fnde_Sice_Business_Usuario();
				$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
				if ( $cpfUsuarioLogado ) {
					$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
				}
				$rsRegistros = $obBusiness->pesquisarUsuarios($arParams, $arUsuario);
				
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

			$arrHeader = array('<center>UF</center>', '<center>Município</center>', '<center>CPF</center>',
					'<center>Nome</center>', '<center>Grupo</center>', '<center>Tipo de Solicitação</center>',);

			if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario) ) {
				$arrayMaisAcoes = array(
						"Liberar Acesso" => $this->getUrl('manutencao', 'liberarsenha', 'liberar-acesso', false),);
			} else {
				$arrayMaisAcoes = array();
			}

			$grid = new Fnde_Sice_View_Helper_DataTables();
			$grid->setHeaderActive(false);
			$grid->setMainAction($arrayMaisAcoes);
			$grid->setAutoCallJs(true);
			$grid->setTitle("Listagem de usuários");
			$grid->setColumnsHidden(array('NU_SEQ_USUARIO'));
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setId('NU_SEQ_USUARIO')->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_CHECKBOX)->setTableAttribs(array('id' => 'edit'));
		}
	}

	/**
	 * Retorna os parametos de pesquisa.
	 * @param array $arFilter
	 */
	private function getParams( $arFilter ) {
		$arParams = array();

		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['SG_UF_ATUACAO_PERFIL']) ) {
			$arParams['SG_UF_ATUACAO_PERFIL'] = $arFilter['SG_UF_ATUACAO_PERFIL'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['CO_MESORREGIAO']) ) {
			$arParams['CO_MESORREGIAO'] = $arFilter['CO_MESORREGIAO'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['CO_MUNICIPIO_PERFIL']) ) {
			$arParams['CO_MUNICIPIO_PERFIL'] = $arFilter['CO_MUNICIPIO_PERFIL'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['NU_CPF']) ) {
			$arParams['NU_CPF'] = preg_replace("/[^0-9]/", "", $arFilter['NU_CPF']);
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['NU_SEQ_TIPO_PERFIL']) ) {
			$arParams['NU_SEQ_TIPO_PERFIL'] = $arFilter['NU_SEQ_TIPO_PERFIL'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['ST_USUARIO']) ) {
			$arParams['ST_USUARIO'] = $arFilter['ST_USUARIO'];
		}
		if ( !Fnde_Sice_Business_Componentes::isEmpty($arFilter['NU_SEQ_TURMA']) ) {
			$arParams['NU_SEQ_TURMA'] = $arFilter['NU_SEQ_TURMA'];
		}

		return $arParams;
	}

	/**
	 * Método para Liberar o Acesso do usuário.
	 * O usuário sera cadastrado no SEGWEB ou terá a senha renovada.
	 * Consome o Web Service do SEGWEB. O próprio Web Service já envia email para o usuário com os dados para login.
	 * 
	 * @author rafael.paiva 05/06/2012
	 */
	public function liberarAcessoAction() {
		$obUsuario = new Fnde_Sice_Business_Usuario();

		//Recupera parametros
		$arParam = $this->_getAllParams();

		//Recupera um ou mais usuários para liberar o acesso
		$arUsuario = $arParam['NU_SEQ_USUARIO'];

		try {
			if ( is_array($arUsuario) ) {
				foreach ( $arUsuario as $usuario ) {
					$usuarioSice = $obUsuario->getUsuarioById($usuario);

					$this->cadastraUsuarioSegweb($usuarioSice);
				}
			} else {
				$usuarioSice = $obUsuario->getUsuarioById($arUsuario);
				$this->cadastraUsuarioSegweb($usuarioSice);
			}
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
		}

		$this->_redirect('manutencao/liberarsenha/list');
	}

	/**
	 * Mètodo para cadastrar um usuário no sistema SEGWEB.
	 * 
	 * @param Usuario $usuarioSice
	 * @throws Exception
	 */
	public function cadastraUsuarioSegweb( $usuarioSice ) {
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set("display_errors", 1);

		$obPerfil = new Fnde_Sice_Business_TipoPerfil();
		$obUsuario = new Fnde_Sice_Business_Usuario();

		//Parâmetros cadastro SEGWEB
		$dsLogin = "SICE_" . $usuarioSice['NU_CPF'];
		$dsEmail = $usuarioSice['DS_EMAIL_USUARIO'];
		$nucaixapostal = null;
		$nucpf = $usuarioSice['NU_CPF'];
		$orgao = null;
		$descricaoorgao = 'MEC';
		$nudddfax = null;
		$nudddtel = substr($usuarioSice['DS_TELEFONE_USUARIO'], 0, 2);
		$nufax = null;
		$nutelefone = substr($usuarioSice['DS_TELEFONE_USUARIO'], 2);
		$nobairro = $usuarioSice['DS_BAIRRO_ENDERECO'];
		$nucep = $usuarioSice['NU_CEP'];
		$nucepcaixapostal = null;
		$comunicipiofnde = null;
		$nousuario = $usuarioSice['NO_USUARIO'];
		$dsendereco = $usuarioSice['DS_ENDERECO'];
		$dscomplemento = $usuarioSice['DS_COMPLEMENTO_ENDERECO'];
		$nuendereco = null;

		//Recuperando o Perfil do usuário
		$perfilUsuario = $obPerfil->getTipoPerfilById($usuarioSice['NU_SEQ_TIPO_PERFIL']);

			//Segweb
			$segweb = new Fnde_Model_Segweb();
			
			if($usuarioSice['ST_USUARIO'] == "L"){
				
				if($usuarioSice['NU_SEQ_TIPO_PERFIL'] == '8'){
					//verifica se é uma edição de usuário caso seja vai verificar se o id do usuário é o mesmo.
					$executivo = $obUsuario->validaExecutivoEstadual($usuarioSice['SG_UF_ATUACAO_PERFIL'],$usuarioSice['NU_SEQ_USUARIO']);
					// valida caso executivo retorne se for edição do coordenador executivo da uf
					// se for novo usuário executivo na mesma uf não cadastra e mostra amensagem
					if($executivo){
						$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Usuario::MSG_ERRO_EXECUTIVO);
						return false;
					}
				}
				
				//Cadastrando usuário diferenciado no SEGWEB
				try{
				$resultSegweb = $segweb->setUserByAddDiferenciado($dsLogin, $dsEmail, $nucaixapostal, $nucpf, $orgao,
						$descricaoorgao, $nudddfax, $nudddtel, $nufax, $nutelefone, null, $nucep, $nucepcaixapostal,
						$comunicipiofnde, $nousuario, $dsendereco, $dscomplemento, $nuendereco);
				}catch(Exception $e){
                    $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
				}

                $idUsuario = $resultSegweb['user']['idUsuario'];

                //busca o usuario do segweb
                $usuarioSegweb = $obUsuario->getUsuarioSegweb($idUsuario);
                //e atualiza o ds_login para vinculação
                $dsLogin = $usuarioSegweb['DS_LOGIN'];

                if ( $resultSegweb['result'] == 1 ) {
					//Cadastrando o Grupo do usuário de acordo com o tipo do perfil cadastrado.
					
					$resultGrupo = $segweb->setVincularGrupos($dsLogin, $perfilUsuario['DS_TIPO_PERFIL_SEGWEB']);
                    //header("_debug_usuario_grupo:" . json_encode($resultGrupo));
					if ( $resultGrupo['result'] == 1 ) {
						$obUsuario->alteraSituacaoUsuario($usuarioSice['NU_SEQ_USUARIO'], 'A');
						$obUsuario->atualizaAcessoSegweb($usuarioSice['NU_SEQ_USUARIO'], $resultSegweb['user']['idUsuario']);
						//Sucesso
						$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Acesso autorizado com sucesso.');
					} else {
						foreach ( $resultGrupo['message'] as $errorMessage ) {
							$this->addMessage(Fnde_Message::MSG_ERROR, $errorMessage['text']);
						}
					}
				} else {
					$this->addMessage(Fnde_Message::MSG_ERROR, $resultSegweb['message']['text']);
				}
			}else{
				//Renovar Senha no SEGWEB
				$usuSegweb = $obUsuario->getDataExpiracaoAtualizadaSegweb($usuarioSice['NU_SEQ_USUARIO_SEGWEB']);
				$resultSegwebUpdate = $segweb->setUserUpdate($usuSegweb['NU_SEQ_USUARIO'], $usuSegweb['ST_ATIVO'], $usuSegweb['DT_EXPIRACAO_SENHA'], $usuSegweb['DT_VALIDADE_SENHA'], 'N');
				
				if ( $resultSegwebUpdate['result'] == 1 ) {
					//Sucesso
					$this->addMessage(Fnde_Message::MSG_SUCCESS, 'Acesso autorizado com sucesso.');
				} else {
					$this->addMessage(Fnde_Message::MSG_ERROR, $resultSegwebUpdate['message']['text']);
				}
			
			}
	}

	/**
	 * Renderiza a mesorregião preenchendo os devidos valores
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
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arParam = $this->_getAllParams();

		//Carregando opcoes da Mesorregiao.
		$arMesorregiao = $businessMesoregiao->getMesoRegiaoPorUF($arParam['SG_UF_ATUACAO_PERFIL']);
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

		$retorno['MESORREGIAO'] = $options;

		//Carregando opcoes de Municipio.
		$options = array();
		if ( $arParam['SG_UF_ATUACAO_PERFIL'] ) {
			if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
					|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
				$arMunicipio = $businessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
			} else {
				$arMunicipio = $businessUF->getMunicipioPorUf($arParam['SG_UF_ATUACAO_PERFIL']);
			}
			foreach ( $arMunicipio as $municipio ) {
				$options[] = array(utf8_encode($municipio['CO_MUNICIPIO_IBGE']),
						utf8_encode($municipio['NO_MUNICIPIO']));
			}
		}

		$retorno['MUNICIPIO'] = $options;

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Renderiza o município preenchendo os devidos valores
	 */
	public function renderizaMunicipioAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessUF = new Fnde_Sice_Business_Uf();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$arParam = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);

		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		if ( $arParam['CO_MESORREGIAO'] ) {
			$arMunicipio = $businessMesoregiao->getMunicipioPorMesoRegiao($arParam['CO_MESORREGIAO']);
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
				|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
			$arMunicipio = $businessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
		} else if ( $arParam['SG_UF_ATUACAO_PERFIL'] ) {
			$arMunicipio = $businessUF->getMunicipioPorUf($arParam['SG_UF_ATUACAO_PERFIL']);
		}

		foreach ( $arMunicipio as $municipio ) {
			$options[] = array(utf8_encode($municipio['CO_MUNICIPIO_IBGE']), utf8_encode($municipio['NO_MUNICIPIO']));
		}

		$retorno['MUNICIPIO'] = $options;

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Renderiza o município preenchendo os devidos valores.
	 */
	public function municipioChangeAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$businessTurma = new Fnde_Sice_Business_Turma();
		$arParam = $this->_getAllParams();

		//Setando a Mesorregiao de acordo com o municipio selecionado.
		$mesorregiao = $businessMesoregiao->getMesoRegiaoPorMunicipio($arParam['CO_MUNICIPIO_PERFIL']);

		$retorno['MESORREGIAO_VAL'] = $mesorregiao[0]['CO_MESO_REGIAO'];

		//Carregando as opcoes de Turma.
		$options = array();
		$arTurma = $businessTurma->obterDadosTurmaCurso($arParam["CO_MUNICIPIO_PERFIL"]);
		foreach ( $arTurma as $turma ) {
			$options[] = array(utf8_encode($turma['NU_SEQ_TURMA']),
					utf8_encode($turma['NU_SEQ_TURMA'] . " - " . $turma['DS_SIGLA_CURSO']));
			//$NU_SEQ_TURMA->addMultiOption($turma['NU_SEQ_TURMA'], $turma['NU_SEQ_TURMA']." - ".$turma['DS_SIGLA_CURSO']);
		}

		$retorno['TURMA'] = $options;

		$this->_helper->json($retorno);
		return $retorno;
	}

	/**
	 * Método acessório para o formulário de pesquisa da tela de liberação de senha.
	 * @param array $arDados
	 * @param array $obGrid
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new LiberarSenha_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/liberarsenha/list')->setMethod('post');

		$this->setUfPerfil($form);
		$this->setMesorregiao($form);
		$this->setPerfil($form);
		$this->setMunicipio($form);
		$this->setTurma($form);

		return $form;
	}

	/**
	 * Método para limpar os dados da última pesquisa realizada.
	 */
	public function clearSearchAction() {

		//limpa sessÃ£o
		Zend_Session::namespaceUnset('searchParam');

		//redireciona para pagina de listagem da ultima sessÃ£o
		$this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
	}

	/**
	 * Adiciona os valores de Uf ao fomulário
	 * @param form $form
	 */
	private function setUfPerfil( $form ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$obBusinessUF = new Fnde_Sice_Business_Uf();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
			$result = $obBusinessUF->search(array('SG_UF'));
		} else {
			$result = $obBusinessUF->search(array('SG_UF' => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		}

		$arUf = array(null => "Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
		}

		$form->setUfPerfil($arUf);
	}

	/**
	 * Adiciona os valores de Mesorregião ao fomulário
	 * @param form $form
	 */
	private function setMesorregiao( $form ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

		$arDados = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arMesorregiao = array(null => "Selecione");
		if ( $arDados["SG_UF_ATUACAO_PERFIL"] ) {
			$resut = $obBusinessMesoregiao->getMesoRegiaoPorUF($arDados["SG_UF_ATUACAO_PERFIL"]);

			for ( $i = 0; $i < count($resut); $i++ ) {

				if ( !in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
						&& !in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
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
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		$arDados = $this->_getAllParams();

		$arMuicipio = array(null => "Selecione");

		$result = $this->getMunicipios($arDados["CO_MESORREGIAO"], $arDados["SG_UF_ATUACAO_PERFIL"],
				$arUsuario['CO_MESORREGIAO']);

		foreach ( $result as $res ) {
			$arMuicipio[$res['CO_MUNICIPIO_IBGE']] = $res['NO_MUNICIPIO'];
		}

		if ( $arDados["CO_MUNICIPIO_PERFIL"] && $arDados["SG_UF_ATUACAO_PERFIL"] ) {
			$resut = $obBusinessMesoregiao->getMesoRegiaoPorMunicipio($arDados["CO_MUNICIPIO_PERFIL"]);
			$coMesorregiao = $resut[0]['CO_MESO_REGIAO'];
		}

		$form->setMunicipio($arMuicipio, $coMesorregiao);
	}

	/**
	 * Retorna os municipios de acordo com o perfil do usuario logado no sistema.
	 * @param $mesorregiaoSelecionada
	 * @param $ufSelecionada
	 * @param $mesorregiaoUsuario
	 */
	private function getMunicipios( $mesorregiaoSelecionada, $ufSelecionada, $mesorregiaoUsuario ) {
		$obBusinessUF = new Fnde_Sice_Business_Uf();
		$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		if ( $mesorregiaoSelecionada ) {
			if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
					|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
				$result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($mesorregiaoUsuario);
			} else {
				$result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($mesorregiaoSelecionada);
			}
		} elseif ( $ufSelecionada ) {
			if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
					|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
				$result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($mesorregiaoUsuario);
			} else {
				$result = $obBusinessUF->getMunicipioPorUf($ufSelecionada);
			}
		}

		return $result;
	}

	/**
	 * Adiciona os valores de Perfil ao fomulário
	 * @param form $form
	 */
	private function setPerfil( $form ) {
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		//SETANDO OS VALORES DO COMBO DE PERFIL DE ACORDO COM O PERFIL LOGADO.
		$rsPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil",
				array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));

		//mostra os perfis de acordo com o perfil do usuário logado
		$perfilRetorno = array(null => "Selecione");
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			$perfilRetorno[5] = $rsPerfil[5];
			$perfilRetorno[4] = $rsPerfil[4];
			$perfilRetorno[8] = $rsPerfil[8];
			$perfilRetorno[1] = $rsPerfil[1];
			$perfilRetorno[2] = $rsPerfil[2];
			$perfilRetorno[3] = $rsPerfil[3];
			$perfilRetorno[7] = $rsPerfil[7];
			$perfilRetorno[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado) ) {
			$perfilRetorno[5] = $rsPerfil[5];
			$perfilRetorno[4] = $rsPerfil[4];
			$perfilRetorno[8] = $rsPerfil[8];
			$perfilRetorno[3] = $rsPerfil[3];
			$perfilRetorno[7] = $rsPerfil[7];
			$perfilRetorno[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) 
					||in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado)) {
			$perfilRetorno[5] = $rsPerfil[5];
			$perfilRetorno[7] = $rsPerfil[7];
			$perfilRetorno[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
			$perfilRetorno[7] = $rsPerfil[7];
			$perfilRetorno[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
			$perfilRetorno[7] = $rsPerfil[7];
		}

		$form->setPerfil($perfilRetorno);
	}

	/**
	 * Adiciona os valores de Turma ao fomulário
	 * @param form $form
	 */
	private function setTurma( $form ) {
		$obBusinessTurma = new Fnde_Sice_Business_Turma();

		$arDados = $this->_getAllParams();
		$rsTurma = $obBusinessTurma->obterDadosTurmaCurso($arDados["CO_MUNICIPIO_PERFIL"]);

		$arTurma = array(null => "Selecione");
		if ( $rsTurma ) {
			foreach ( $rsTurma as $turma ) {
				$arTurma[$turma['NU_SEQ_TURMA']] = $turma['NU_SEQ_TURMA'] . " - " . $turma['DS_SIGLA_CURSO'];
			}
		}

		$form->setTurma($arTurma);
	}
}
