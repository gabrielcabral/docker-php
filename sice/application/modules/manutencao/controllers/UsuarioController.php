<?php 

/**
 * Controller do Usuario
 *
 * @author diego.matos
 * @since 10/04/2012
 */

class Manutencao_UsuarioController extends Fnde_Sice_Controller_Action {

	/**
	 * Ação de listagem
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	
	public function listAction() {
		$this->verificaPermissaoUsuario();
		
		$this->setTitle('Usuário');
		$this->setSubtitle('Filtrar');

		// limpa a sessão do Formacao Academica
		unset($_SESSION['rsDataFormacaoAcademica']);
		unset($_SESSION['NU_SEQ_ATIVIDADE']);
		unset($_SESSION['DS_ATIVIDADE_ALTERNATIVA']);

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

		//monta menu de contexto
		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$urlCadastrar = $this->getUrl('manutencao', 'usuario', 'form', ' ');

		$menu = Fnde_Sice_Business_Componentes::montaMenuContextoUsuario($perfilUsuario, $urlFiltrar, $urlCadastrar);
		$this->setActionMenu($menu);

		//seta novos valores na sessão
		if ( $this->_request->isPost() ) {
			$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
			$this->urlFilterNamespace->param = $this->_getAllParams();
		}

		//recupera valores da sessão
		$arFilter = $this->getSearchParamUsuario();

		$form = $this->getFormFilter();

		$businessUsuario->setUfFilter($form, $perfilUsuario, $arUsuario);
		$this->setMesorregiaoFilter($form, $arFilter['SG_UF_ATUACAO_PERFIL'], $perfilUsuario, $arUsuario);
		$this->setMunicipioFilter($form, $arFilter['SG_UF_ATUACAO_PERFIL'], $arFilter['CO_MESORREGIAO'],
				$perfilUsuario, $arUsuario);
		$this->setTipoPerfil($form, $perfilUsuario);

		$rsRegistros = array();

		if ( $this->isPostValido($this->_request->isPost(), $arFilter) ) {
			if ( $form->isValid($arFilter) ) {

				$obBusiness = new Fnde_Sice_Business_Usuario();
				$arParams = array();

				foreach ( $form->getElements() as $elemento ) {
					if ( !Fnde_Sice_Business_Componentes::isEmpty($elemento->getValue()) ) {

						if ( $elemento->getName() == 'NO_USUARIO' ) {
							$arParams[$elemento->getName()] = strtoupper($elemento->getValue());
						} else {
							$arParams[$elemento->getName()] = $elemento->getValue();
						}
					}
				}
				$rsRegistros = $obBusiness->pesquisarUsuarios($arParams, $arUsuario);
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
			$rowAction = $this->getArRowAction($perfilUsuario);

			$arrHeader = array('<center>ID</center>', '<center>UF</center>', '<center>Município</center>',
					'<center>CPF</center>', '<center>Nome</center>', '<center>Dt. Cadastro Perfil</center>',
					'<center>Dt. Alteração Usuário</center>', '<center>Situação</center>',);
			$grid = new Fnde_View_Helper_DataTables();
			$arrayMaisAcoes = $this->definirOpcoesComboMaisAcoes($perfilUsuario);
			$grid->setMainAction($arrayMaisAcoes);
			$grid->setAutoCallJs(true);
			$grid->setActionColumn("<center>Ações</center>");
			$this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setHeaderActive(false)->setTitle(
					"Listagem de usuários")->setRowAction($rowAction)->setId('NU_SEQ_USUARIO')->setRowInput(
					Fnde_View_Helper_DataTables::INPUT_TYPE_RADIO)->setTableAttribs(array('id' => 'edit'))->setColumnsHidden(
					array("NO_MESO_REGIAO", "NU_SEQ_CONFIGURACAO"));
		}
	}

	/**
	 * Verifica a permissão do usuário em acessar a página
	 */
	private function verificaPermissaoUsuario(){
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}
	}
	
	/**
	 * Verifica se os parametros retornados para pesquisa são válido para prosseguir com o filtro
	 * @param unknown_type $post
	 * @param unknown_type $arFilter
	 */
	private function isPostValido( $post, $arFilter ) {
		if ( $post || isset($arFilter['startlist']) || isset($arFilter['start']) || !empty($arFilter) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $perfilUsuario
	 */
	private function definirOpcoesComboMaisAcoes( $perfilUsuario ) {
		$arrayMaisAcoes = array();
		if ( !in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfilUsuario) ) {
			$arrayMaisAcoes = array(
					"Visualizar histórico bolsista" => $this->getUrl('manutencao', 'visualizahistbolsa', 'form', true),
					"Visualizar histórico perfil" => $this->getUrl('manutencao', 'usuario', 'visualizarhistoricoperfil', true)
            );


            /*
             * sgd 26371
             *
             * adicionando visualização de termos de compromisso
             * */
            if (
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
                in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
            ) {
                $arrayMaisAcoes = array_merge($arrayMaisAcoes, array(
					"Visualizar termos de compromisso" => $this->getUrl('manutencao', 'usuario', 'visualizarhistoricotermos', true),
					"Avaliar Curso" => $this->getUrl('avaliacaoinstitucional', 'avaliarcurso', 'form', true)
                ));
            }

		} else {
			$arrayMaisAcoes = array();
		}

		return $arrayMaisAcoes;
	}
	
	/**
	 * Recupera o array de ações do grid de acordo com o perfil do usuário logado
	 */
	private function getArRowAction( $perfilUsuario ) {
		$rowAction = array();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario)
				&& !in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfilUsuario) ) {
			$rowAction = array(
					'visualizar' => array('label' => 'Visualizar',
							'url' => $this->view->Url(array('action' => 'visualizar-usuario', 'NU_SEQ_USUARIO' => ''))
									. '%s', 'params' => array('NU_SEQ_USUARIO'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'NU_SEQ_USUARIO' => '')) . '%s',
							'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoEditar', 'title' => 'Editar')),
					'delete' => array('label' => 'Excluir',
							'url' => $this->view->Url(array('action' => 'remover-usuario', 'NU_SEQ_USUARIO' => ''))
									. '%s', 'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoExcluir excluir', 'title' => 'Excluir',
									'mensagem' => 'Deseja realmente excluir o registro?')),
					'ativarInativar' => array('label' => 'Ativar/Inativar',
							'url' => $this->view->Url(array('action' => 'ativar-inativar', 'id' => ''))
									. '%s/ST_USUARIO/%s', 'params' => array('NU_SEQ_USUARIO', 'ST_USUARIO'),
							'attribs' => array('class' => 'icoAceitar', 'title' => 'Ativa/Desativa',
									'mensagem' => 'Deseja realmente alterar a situação deste registro?'))
			);
		} else if ( in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfilUsuario) ) {
			$rowAction = array(
					'visualizar' => array('label' => 'Visualizar',
							'url' => $this->view->Url(array('action' => 'visualizar-usuario', 'NU_SEQ_USUARIO' => ''))
									. '%s', 'params' => array('NU_SEQ_USUARIO'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
					'edit' => array('label' => 'editar',
							'url' => $this->view->Url(array('action' => 'form', 'NU_SEQ_USUARIO' => '')) . '%s',
							'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoEditar', 'title' => 'Editar')),
					'delete' => array('label' => 'Excluir', 'url' => '#x', 'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoExcluir excluir disabled', 'title' => 'Excluir',
									'mensagem' => '')),
					'ativarInativar' => array('label' => 'Ativar/Inativar', 'url' => '#a',
							'params' => array('NU_SEQ_USUARIO', 'ST_USUARIO'),
							'attribs' => array('class' => 'icoAceitar disabled', 'title' => 'Ativar/Inativar',
									'mensagem' => ''))
			);
		} else {
			$rowAction = array(
					'visualizar' => array('label' => 'Visualizar',
							'url' => $this->view->Url(array('action' => 'visualizar-usuario', 'NU_SEQ_USUARIO' => ''))
									. '%s', 'params' => array('NU_SEQ_USUARIO'), 'title' => 'Visualisar',
							'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
					'edit' => array('label' => 'editar', 'url' => '#e', 'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoEditar disabled', 'title' => 'Editar')),
					'delete' => array('label' => 'Excluir', 'url' => '#x', 'params' => array('NU_SEQ_USUARIO'),
							'attribs' => array('class' => 'icoExcluir excluir disabled', 'title' => 'Excluir',
									'mensagem' => '')),
					'ativarInativar' => array('label' => 'Ativar/Inativar', 'url' => '#a',
							'params' => array('NU_SEQ_USUARIO', 'ST_USUARIO'),
							'attribs' => array('class' => 'icoAceitar disabled', 'title' => 'Ativar/Inativar',
									'mensagem' => '')));
		}

		return $rowAction;
	}
	
	/**
	 * Ativa ou Desativa o status do Usuário
	 *
	 * @author rafael.paiva
	 * @since 10/04/2012
	 */
	public function ativarInativarAction() {
		$arParam = $this->_getAllParams();
		$retorno = false;

		$obBusiness = new Fnde_Sice_Business_Usuario();
		$rsRegistro = $obBusiness->getUsuarioById($arParam["id"]);

		if ( $rsRegistro['ST_USUARIO'] == 'A' ) {
			$rsRegistro['ST_USUARIO'] = 'D';
			$retorno = true;
		} else if ( $rsRegistro['ST_USUARIO'] == 'D' ) {
			$rsRegistro['ST_USUARIO'] = 'A';
			$retorno = true;
			
			if($rsRegistro['NU_SEQ_TIPO_PERFIL'] == '8'){
				//verifica se é uma edição de usuário caso seja vai verificar se o id do usuário é o mesmo.
				$executivo = $obBusiness->validaExecutivoEstadual($rsRegistro['SG_UF_ATUACAO_PERFIL'],$rsRegistro['NU_SEQ_USUARIO']);
				// valida caso executivo retorne se for edição do coordenador executivo da uf
				// se for novo usuário executivo na mesma uf não cadastra e mostra amensagem
				if($executivo){
					$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Usuario::MSG_ERRO_EXECUTIVO);
					$this->_redirect("/manutencao/usuario/list");
					return false;
				}
			}
		}

		if ( $retorno ) {
			$rsRegistro['DT_ALTERACAO'] = date('d/m/Y H:i:s', time());
			try{
				$resposta = $obBusiness->ativarInativaUsuario($rsRegistro);
				$logMessage = json_encode($resposta);
			}catch(Exception $e){
				$logMessage = $e->getMessage(); 
			}
			file_put_contents("/u02/repositorio/php/sice/log/liberar_usuario.log",$logMessage,FILE_APPEND);
			$resposta = ( string ) $resposta;
		}else{
			$resposta = '0';
			//$this->addMessage(Fnde_Message::MSG_ERROR, "Não foi possível ativar o usuário.");
		}

		if ( $resposta ) {
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Situação do usuário alterada com sucesso.");
		} elseif ( $resposta == '0' ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao alterar a situação.");
		}

		$this->_redirect("/manutencao/usuario/list");
	}

	/**
	 * renderiza MesoregiÃ£o por UF
	 *
	 * @author vinicius.cancado
	 * @since 10/04/2012
	 */
	public function renderizaMesoregiaoAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		$form = $this->getFormFilter();

		$this->setMesorregiaoFilter($form, $arParam['SG_UF_ATUACAO_PERFIL'], $perfilUsuario, $arUsuario);
		$this->setMunicipioFilter($form, $arParam['SG_UF_ATUACAO_PERFIL'], $arParam['CO_MESORREGIAO'], $perfilUsuario,
				$arUsuario);

		$this->getResponse()->setBody($form);

		return $this;
	}

	/**
	 * Renderiza municípios na tela de filtro.
	 */
	public function renderizaMunicipioAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		$form = $this->getFormFilter();

		$this->setMunicipioFilter($form, $arParam['SG_UF_ATUACAO_PERFIL'], $arParam['CO_MESORREGIAO'], $perfilUsuario,
				$arUsuario);

		$this->getResponse()->setBody($form);

		return $this;
	}

	/**
	 * Renderiza municípios na tela de cadastro/edição.
	 */
	public function renderizaMunicipioCadAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$this->setComboMunicipioAbaPerfil($form, $arParam);
		$this->setMunicipioAbaDadosPessoais($form, $arParam);
		$this->setMunicipioAbaLogradouro($form, $arParam);

		$form->populate($arParam);

		$this->getResponse()->setBody($form);

		return $this;
	}

	/**
	 * Renderiza mesorregião na tela de cadastro/edição.
	 */
	public function renderizaMesoregiaoCadAction() {

		$this->_helper->layout()->disableLayout();
		$this->setTitle('Usuario');
		$this->setSubtitle('Cadastro');

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$this->setMesorregiaoAbaPerfil($form, $arParam);

		$form->populate($arParam);
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Método para pesquisar o CPF informado na receita federal.
	 */
	public function obterInformacoesPorCpfAction() {
		$oUsuBusiness = new Fnde_Sice_Business_Usuario();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();
		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$cpf = preg_replace('/[^0-9]/', '', $arParam['NU_CPF']);
		$cpf = trim($cpf);

		$retorno = Fnde_Sice_Business_Usuario::validaCPF($cpf, $form, true);
		if ( $retorno ) {
			$oUsuBusiness->preencheDadosPorCpf($form, $arParam['NU_CPF']);

			//Verifica se o cpf já possui cadastro em algum outro perfil.
			$consultaPerfil = $oUsuBusiness->buscaPerfilUsuarioPorCpf($cpf);
			if($consultaPerfil){
				$perfil = $consultaPerfil["DS_TIPO_PERFIL"];
				$form->getElement('NU_CPF')->addError("O CPF já está cadastrado no sistema com perfil de $perfil");
			}
		}
		$this->view->form = $form;
		$this->render('form');
	}
	
	/**
	 * Método para pesquisar o cep informado no webservice dos correios.
	 */
	public function obterInformacoesPorCepAction() {
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();

		$cepAdapter = new Fnde_Model_Correios();
		$resultado = $cepAdapter->consultarCep($arParam['NU_CEP']);

		if ( $resultado['result'] == '1' ) {
			$row = $resultado['content']['row'];
			$arParam['CO_UF_ENDERECO'] = $row['uf'];
			$form = $this->getForm($arParam);
			$this->setMunicipioAbaLogradouro($form, $arParam);

			$form->populate($arParam);
			$form->getElement("TP_ENDERECO")->setValue($arParam['TP_ENDERECO']);
			$form->getElement("DS_ENDERECO")->setValue($row['endereco']);
			$form->getElement("DS_BAIRRO_ENDERECO")->setValue($row['bairro']);
			$form->getElement("CO_UF_ENDERECO")->setValue($row['uf']);

			$form->getElement("CO_MUNICIPIO_ENDERECO")->setValue($row['municipioCodigoIBGE']);

		} else {
			$form = $this->getForm($arParam);
			$form->populate($arParam);
			$form->getElement("NU_CEP")->addError("CEP inválido!");
		}

		$this->view->form = $form;

		return $this->render('form');

	}

    public function alterarEmailAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);

			$email = $this->getRequest()->getParam("email");
			$emailConfirmacao = $this->getRequest()->getParam("emailConf");

			if ($email != $emailConfirmacao) {
				die(json_encode(array("status" => "error", "message" => utf8_encode("O e-mails estão diferentes!"))));
			} else {
				$usuario = base64_decode($this->getRequest()->getParam("usuario"));

				$businessUsuario = new Fnde_Sice_Business_Usuario();
				$usuarioReg = $businessUsuario->getUsuarioById($usuario);


				if ($usuarioReg["DS_EMAIL_USUARIO"] != $email) {
					$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
					$updated = $adapter->update("SICE_FNDE.S_USUARIO", array("DS_EMAIL_USUARIO" => $email), $adapter->quoteInto("NU_SEQ_USUARIO = ?", $usuario));

					$config = Zend_Registry::get('config');
					$urlSegWeb = $config['webservices']['segweb']['uri'] . 'usuario/Edit';
					$urlSegWeb = 'http://www.fnde.gov.br/webservices/segweb/index.php/usuario/Edit';
					$xml = '<request>
						  <header>
							<app>SICE</app>
							<version>1.0</version>
							<created>' . date("Y-m-d\TH:i:s") . '</created>
						  </header>
						  <body>
							<usuario>
							  <idusuario>' . $usuarioReg['NU_SEQ_USUARIO_SEGWEB'] . '</idusuario>
							  <alterarsenha>N</alterarsenha>
							  <email>' . $email . '</email>
							</usuario>
						  </body>
					</request>';

					$postdata = http_build_query(
						array(
							'xml' => $xml
						)
					);

                    $curl = curl_init($urlSegWeb);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl,CURLOPT_POSTFIELDS, $postdata);
                    $result = curl_exec($curl);
                    curl_close($curl);


					$obj = simplexml_load_string($result);
					$ok = (int)$obj->status->result;

					if ($obj && $ok == 1) {
						die(json_encode(array("status" => "true", "message" => "E-mail alterado com sucesso!")));
					} else {
						die(json_encode(array("status" => "error", "message" => "Erro ao acessar o SEGWEB!")));
					}
				} else {
					die(json_encode(array("status" => "error", "message" => utf8_encode("E-mail igual ao já cadastrado!"))));
				}
			}
		} catch (Exception $e){
			// armazenando log dos erros de alteração de email no intuito de investigar o sgo FNDE-1598
			$data = new DateTime('now');
			$sufixo = $data->format('Y-m-d');
			$stream = @fopen(APPLICATION_ROOT . '/application/logs/ErroAlteracaoEmail'.$sufixo , 'a+', false);
			// se conseguir acessar a pasta logs e criar o arquivo
			if ($stream !== false) {
				$writer = new Zend_Log_Writer_Stream($stream);
				$logger = new Zend_Log($writer);
				$logger->err($e);
			}
			die(json_encode(array("status" => "error", "message" => utf8_encode("Ocorreu um erro. Tente novamente." ))));
		}
    }


	/**
	 * Monta o formulÃ¡rio e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function formAction() {

        // limpa a sessão do Formacao Academica
        unset($_SESSION['rsDataFormacaoAcademica']);
        unset($_SESSION['NU_SEQ_ATIVIDADE']);
        unset($_SESSION['DS_ATIVIDADE_ALTERNATIVA']);
		$this->setTitle('Usuário');
		if($this->getRequest()->getParam("NU_SEQ_USUARIO")){
			$this->setSubtitle('Editar');
		}else{
			$this->setSubtitle('Cadastrar');
		}

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessTurma = new Fnde_Sice_Business_Turma();
		$businessVincForm = new Fnde_Sice_Business_VincFormAcadUsu();
		$perfilUsuario = $usuarioLogado->credentials;

		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$urlCadastrar = $this->getUrl('manutencao', 'usuario', 'form', ' ');
		$menu = Fnde_Sice_Business_Componentes::montaMenuContextoUsuario($perfilUsuario, $urlFiltrar, $urlCadastrar);
		$this->setActionMenu($menu);

		$usuario = new Fnde_Sice_Business_Usuario();
		$idUsuario = $this->getRequest()->getParam("NU_SEQ_USUARIO");

		if ( $idUsuario ) {
			$arDados = $usuario->getUsuarioById($idUsuario);
			$dadosFormacao = $businessVincForm->getVinculoByUsuario($idUsuario);
			$arDados['NU_SEQ_FORMACAO_ACADEMICA'] = $dadosFormacao[0]['NU_SEQ_FORMACAO_ACADEMICA'];
			$arDados['TP_INSTITUICAO'] = $dadosFormacao[0]['TP_INSTITUICAO'];
		}

		if(
			in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
		) {
            if($arDados['ST_USUARIO'] == "A"){
                $this->view->isAllowedChangeMail = true;
            }
        }

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();
        $this->view->arDados = $arDados;

		if($arDados["NU_SEQ_TIPO_PERFIL"] == 7) {
			$elementos = $form->getElements();
			if($idUsuario){
				$dadosTurma = $businessTurma->getTurmaPorAluno($idUsuario);
			}


			$form->removeElement('CO_ESTADO_CIVIL');
			$form->removeElement('TP_ENDERECO');
			$form->removeElement('NU_CEP');
			$form->removeElement('DS_ENDERECO');
			$form->removeElement('DS_COMPLEMENTO_ENDERECO');
			$form->removeElement('DS_BAIRRO_ENDERECO');
			$form->removeElement('CO_UF_ENDERECO');
			$form->removeElement('CO_MUNICIPIO_ENDERECO');

			//Verifica os perfils e desabilita os campos de acordo com o escolhido.
			if (($dadosTurma["DS_ST_TURMA"] != "Finalizada") &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario))) {
				$campos = array("SG_UF_NASCIMENTO","CO_MUNICIPIO_NASCIMENTO","DS_EMAIL_USUARIO",
					"DS_EMAIL_USUARIO_CONFIRM","DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO","CO_REDE_ENSINO",
					"CO_MUNICIPIO_ESCOLA", "CO_ESCOLA","CO_SEGMENTO", "confirmar", "cancelar", 'NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO', "SG_UF_ESCOLA", "NO_MESORREGIAO_ESCOLA");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			} else if ($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))) {
				$campos = array("DS_EMAIL_USUARIO", "DS_EMAIL_USUARIO_CONFIRM", "confirmar", "cancelar");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			} else if($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))){
				$campos = array("SG_UF_NASCIMENTO","CO_MUNICIPIO_NASCIMENTO","DS_EMAIL_USUARIO",
					"CO_MUNICIPIO_ESCOLA", "DS_EMAIL_USUARIO_CONFIRM","DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO",
					"CO_REDE_ENSINO","CO_ESCOLA","CO_SEGMENTO", "confirmar", "cancelar", 'NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO',"SG_UF_ESCOLA", "NO_MESORREGIAO_ESCOLA");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			}
			if($idUsuario){
				$dadosEscolares = Fnde_Sice_Business_DadosEscolaresCursista::getDadosEscolaresById($idUsuario);
			}
			if($dadosEscolares){
				$arDados['SG_UF_ESCOLA'] = $dadosEscolares['CO_UF_ESCOLA'];
				$arDados['SG_UF_ESCOLA_HIDDEN'] = $dadosEscolares['CO_UF_ESCOLA'];
				$arDados['CO_MUNICIPIO_ESCOLA'] = $dadosEscolares['CO_MUNICIPIO_ESCOLA'];
				$arDados['NO_MESORREGIAO_ESCOLA'] = $dadosEscolares['CO_MESORREGIAO'];
				$arDados['CO_REDE_ENSINO'] = $dadosEscolares['CO_REDE_ENSINO'];
				$arDados['CO_ESCOLA'] = $dadosEscolares['CO_ESCOLA'];
				$arDados['CO_SEGMENTO'] = $dadosEscolares['CO_SEGMENTO'];
			}

		}

		if ( $arDados['NU_SEQ_USUARIO'] ) {
			$form->populate($arDados);
			$this->view->form = $form;
		} else {
			$this->view->form = $this->getForm();
		}

		if ( $idUsuario && ( $businessTurma->identificarVinculosUsuarioTurma($idUsuario) > 0 ) ) {
            $form->getElement("SG_UF_ATUACAO_PERFIL_CAD")->setAttrib("disabled", true)->setRequired(false);
            $form->getElement("CO_MUNICIPIO_PERFIL")->setAttrib("disabled", true)->setRequired(false);
            $form->getElement("NU_SEQ_TIPO_PERFIL")->setAttrib("disabled", true)->setRequired(false);
		}

		if ( $this->getRequest()->isPost() ) {
			return $this->salvarUsuarioAction();
		}

		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {
		$params = $this->_getAllParams();

		if(!isset($params['NU_SEQ_USUARIO'])){
			$arDados = array();
			$this->setNameList(
				array('NO_USUARIO', 'CO_OCUPACAO_USUARIO', 'CO_SEXO_USUARIO', 'CO_DISTANCIA_PAGAMENTO',
					'SG_UF_PAGAMENTO', 'SG_UF_EMISSAO_DOC', 'SG_UF_NASCIMENTO', 'NU_SEQ_TIPO_PERFIL',
					'CO_LOCAL_LOTACAO', 'CO_MUNICIPIO_PERFIL', 'CO_MUNICIPIO_PAGAMENTO', 'DS_CELULAR_USUARIO',
					'DS_COMPLEMENTO_ENDERECO', 'DT_NASCIMENTO', 'CO_ESTADO_CIVIL', 'DS_ENDERECO', 'NU_IDENTIDADE',
					'NO_MAE', 'DS_BAIRRO_ENDERECO', 'DS_EMAIL_USUARIO', 'SG_UF_ATUACAO_PERFIL',
					'DS_TELEFONE_USUARIO', 'CO_MESORREGIAO', 'CO_ORGAO_EMISSOR', 'CO_MUNICIPIO_ENDERECO',
					'DT_EMISSAO_DOCUMENTACAO', 'TP_ENDERECO', 'NU_CPF', 'DS_OCUPACAO_ALTERNATIVA',
					'CO_SERVIDOR_PUBLICO', 'NU_CEP', 'CO_AGENCIA_PAGAMENTO', 'DS_CARGO_FUNCAO', 'NU_SEQ_USUARIO',
					'CO_MUNICIPIO_NASCIMENTO', 'DT_CADASTRO', 'DT_ALTERACAO', 'ST_USUARIO','CO_MUNICIPIO_ESCOLA',));

			$form = new Usuario_Form($arDados);

			$this->addOptionsAoForm($form, $arDados);

			$form->setDecorators(array('FormElements', 'Form'));
			$form->setAction($this->view->baseUrl() . '/index.php/manutencao/usuario/form')->setMethod('post');

			return $form;
		}elseif ( !isset($_SESSION['rsDataFormacaoAcademica'][0])
				&& !Fnde_Sice_Business_Componentes::isEmpty($params['NU_SEQ_USUARIO'])) {

			$obBusinessVinculoAtivUsuario = new Fnde_Sice_Business_VinculaAtivUsuario();
			$obBusinessUsuario = new Fnde_Sice_Business_Usuario();
			$obBusinessVincFormAcadUsu = new Fnde_Sice_Business_VincFormAcadUsu();
			$obBusinessFormacaoAcademica = new Fnde_Sice_Business_FormacaoAcademica();
			$obModeloDadosEscolares = new Fnde_Sice_Business_DadosEscolaresCursista();

			$rsUsuario = $obBusinessUsuario->getUsuarioById($params['NU_SEQ_USUARIO']);

			$rsUsuario['DT_NASCIMENTO'] = substr($rsUsuario['DT_NASCIMENTO'], 0, 10);
			$rsUsuario['DT_EMISSAO_DOCUMENTACAO'] = substr($rsUsuario['DT_EMISSAO_DOCUMENTACAO'], 0, 10);

			$atividades = $obBusinessVinculoAtivUsuario->buscarVinculoPorUsuario($params['NU_SEQ_USUARIO']);
			$dadosEscolares = $obModeloDadosEscolares->getDadosEscolaresById($rsUsuario['NU_SEQ_USUARIO']);

			$formacaoAcademia = $obBusinessVincFormAcadUsu->getVinculoByUsuario($params['NU_SEQ_USUARIO']);
			$arrayFormacaoAcademica = array();

			for ( $i = 0; $i < count($formacaoAcademia); $i++ ) {
				$arrayFormacaoAcademica[$i]['ID'] = $i;

				$dsFormacaoAcademica = $obBusinessFormacaoAcademica->getFormacaoAcademicaById(
						$formacaoAcademia[$i]['NU_SEQ_FORMACAO_ACADEMICA']);


				$arrayFormacaoAcademica[$i]['DS_TP_ESCOLARIDADE'] = $dsFormacaoAcademica['DS_FORMACAO_ACADEMICA'];

				if ( $formacaoAcademia[$i]['TP_INSTITUICAO'] == 1 ) {
					$arrayFormacaoAcademica[$i]['DS_TP_INSTITUICAO'] = 'Pública';
				} else if ( $formacaoAcademia[$i]['TP_INSTITUICAO'] == 2 ) {
					$arrayFormacaoAcademica[$i]['DS_TP_INSTITUICAO'] = 'Privada';
				} else if ( $formacaoAcademia[$i]['TP_INSTITUICAO'] == 3 ) {
					$arrayFormacaoAcademica[$i]['DS_TP_INSTITUICAO'] = 'Comunitária';
				}

				$arrayFormacaoAcademica[$i]['TP_ESCOLARIDADE'] = $formacaoAcademia[$i]['NU_SEQ_FORMACAO_ACADEMICA'];
				$arrayFormacaoAcademica[$i]['TP_INSTITUICAO'] = $formacaoAcademia[$i]['TP_INSTITUICAO'];
				$arrayFormacaoAcademica[$i]['NO_INSTITUICAO'] = $formacaoAcademia[$i]['NO_INSTITUICAO'];
				$arrayFormacaoAcademica[$i]['NO_CURSO'] = $formacaoAcademia[$i]['NO_CURSO'];
				$arrayFormacaoAcademica[$i]['DT_CONCLUSAO'] = $formacaoAcademia[$i]['DT_CONCLUSAO'];

				$_SESSION['rsDataFormacaoAcademica'][] = $arrayFormacaoAcademica[$i];
			}

			//$_SESSION['rsDataFormacaoAcademica'] = $arrayFormacaoAcademica;

			$arrAtiv = array();
			$arrDsOUtraAtiv = array();
			foreach ( $atividades as $ativ ) {
				$arrAtiv[] = $ativ['NU_SEQ_ATIVIDADE'];
				$arrDsOUtraAtiv[] = $ativ['DS_ATIVIDADE_ALTERNATIVA'];
			}

			$_SESSION['NU_SEQ_ATIVIDADE'] = $arrAtiv;
			$_SESSION['DS_ATIVIDADE_ALTERNATIVA'] = $arrDsOUtraAtiv;


			if ( $arDados['readonly'] == 1 ) {
				$arDados = $rsUsuario;
				$arDados['readonly'] = 1;
			} else {
				$arDados = $rsUsuario;
			}


                        /*
                         * Atualizando dados vindos do post
                         */
                        if($this->getRequest()->isPost()) {
                            $arDados['SG_UF_ATUACAO_PERFIL'] = $this->_getParam('SG_UF_ATUACAO_PERFIL_CAD');
                            $arDados['CO_MUNICIPIO_PERFIL'] = $this->_getParam('CO_MUNICIPIO_PERFIL');
                            $arDados['CO_MESORREGIAO'] = $this->_getParam('CO_MESORREGIAO_CAD');
                            $arDados['CO_AGENCIA_PAGAMENTO'] = $this->_getParam('CO_AGENCIA_PAGAMENTO');
                            $arDados['CO_MUNICIPIO_PAGAMENTO'] = $this->_getParam('CO_MUNICIPIO_PAGAMENTO');
                            $arDados['SG_UF_PAGAMENTO'] = $this->_getParam('SG_UF_PAGAMENTO');
                            $arDados['CO_DISTANCIA_PAGAMENTO'] = $this->_getParam('CO_DISTANCIA_PAGAMENTO');
                        }
		}

		//dados escolares
		$obBusinessDadosEscolares = new Fnde_Sice_Business_DadosEscolaresCursista();
		$arDadosEscolares = $obBusinessDadosEscolares->getDadosEscolaresCursistaById($params['NU_SEQ_USUARIO']);

		$arDados['CO_UF_ESCOLA'] = $arDadosEscolares['CO_UF_ESCOLA'];
		$arDados['CO_MUNICIPIO_ESCOLA'] = $arDadosEscolares['CO_MUNICIPIO_ESCOLA'];
		$arDados['CO_MESORREGIAO_ESCOLA'] = $arDadosEscolares['CO_MESORREGIAO'];
		$arDados['CO_REDE_ENSINO'] = $arDadosEscolares['CO_REDE_ENSINO'];
		$arDados['CO_ESCOLA'] = $arDadosEscolares['CO_ESCOLA'];
		$arDados['CO_SEGMENTO'] = $arDadosEscolares['CO_SEGMENTO'];
		//dados escolares
		$this->setNameList(
				array('NO_USUARIO', 'CO_OCUPACAO_USUARIO', 'CO_SEXO_USUARIO', 'CO_DISTANCIA_PAGAMENTO',
						'SG_UF_PAGAMENTO', 'SG_UF_EMISSAO_DOC', 'SG_UF_NASCIMENTO', 'NU_SEQ_TIPO_PERFIL',
						'CO_LOCAL_LOTACAO', 'CO_MUNICIPIO_PERFIL', 'CO_MUNICIPIO_PAGAMENTO', 'DS_CELULAR_USUARIO',
						'DS_COMPLEMENTO_ENDERECO', 'DT_NASCIMENTO', 'CO_ESTADO_CIVIL', 'DS_ENDERECO', 'NU_IDENTIDADE',
						'NO_MAE', 'DS_BAIRRO_ENDERECO', 'DS_EMAIL_USUARIO', 'SG_UF_ATUACAO_PERFIL',
						'DS_TELEFONE_USUARIO', 'CO_MESORREGIAO', 'CO_ORGAO_EMISSOR', 'CO_MUNICIPIO_ENDERECO',
						'DT_EMISSAO_DOCUMENTACAO', 'TP_ENDERECO', 'NU_CPF', 'DS_OCUPACAO_ALTERNATIVA',
						'CO_SERVIDOR_PUBLICO', 'NU_CEP', 'CO_AGENCIA_PAGAMENTO', 'DS_CARGO_FUNCAO', 'NU_SEQ_USUARIO',
					 	'CO_MUNICIPIO_NASCIMENTO', 'DT_CADASTRO', 'DT_ALTERACAO', 'ST_USUARIO','CO_MUNICIPIO_ESCOLA',));

		$form = new Usuario_Form($arDados, $arExtra);
                
		$this->addOptionsAoForm($form, $arDados, $dadosEscolares);

		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/usuario/form')->setMethod('post');

		return $form;
	}

	/**
	 * Adiciona o conteúdo do formulario
	 * @param form $form
	 */
	private function addOptionsAoForm($form, $arDados, $dadosEscolares){
		$this->setComboPerfilAbaPerfil($form);
		$this->setComboUfAbaPerfil($form);
		$this->setComboMunicipioAbaPerfil($form, $arDados);
		$this->setMesorregiaoAbaPerfil($form, $arDados);
		$this->setUfAbaDadosPessoais($form);
		$this->setMunicipioAbaDadosPessoais($form, $arDados);
		$this->setUfAbaDocumentacao($form);
		$this->setUfAbaLogradouro($form);
		$this->setMunicipioAbaLogradouro($form, $arDados);
		$this->setValidatorEmail($form);
		$this->setOcupacaoAbaDadosFuncionais($form);
		$this->setLocalLotacaoAbaDadosFuncionais($form);
		//
		$this->setUfAbaDadosEscola($form);
		$this->setmunicipioAbaDadosEscola($form, $dadosEscolares);
		$this->setMesoregiaoAbaDadosEscola($form, $dadosEscolares);
		$this->setRedeEnsinoAbaDadosEscola($form, $dadosEscolares);
		$this->setNomeEscolaAbaDadosEscola($form, $dadosEscolares);
		$this->setSegmentoAbaDadosEscola($form);
		$this->setFormacaoAcademica($form, $arDados);
	}
	/**
	 * Método acessório get de nameList.
	 */
	public function getNameList() {
		return $this->_arList;
	}
	
	/**
	 * Método acessório set de nameList.
	 */
	public function setNameList($arList) {
		$this->_arList = $arList;
	}
	
	/**
	 * Método acessório get de titles.
	 */
	public function getTitles() {
		return $this->_arTitles;
	}
	
	/**
	 * Método acessório set de titles.
	 */
	public function setTitles($arTitles) {
		$this->_arTitles = $arTitles;
	}
	
	/**
	 * Método acessório get do formulário de pesquisa da tela de usuários.
	 */
	public function getFormFilter( $arDados = array(), $obGrid = null ) {
		$form = new Usuario_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/manutencao/usuario/list')->setMethod('post');
		$form->populate($arDados);
		return $form;
	}

	/**
	 * Método acessório get de arTitlesList.
	 */
	public function getArTitlesList() {
		return array(
				'noUsuario',
				'coOcupacaoUsuario',
				'coSexoUsuario',
				'coDistanciaPagamento',
				'ufEstadoPagamento',
				'ufEmissaoDoc',
				'ufNascimento',
				'STipoPerfil',
				'coLocalLotacao',
				'coMunicipioPerfil',
				'coMunicipioPagamento',
				'dsCelularUsuario',
				'dsComplementoEndereco',
				'dtNascimento',
				'coEstadoCivil',
				'dsEndereco',
				'identidadeDocumentacao',
				'noMae',
				'dsBairroEndereco',
				'dsEmailUsuario',
				'ufAtuacaoPerfil',
				'dsTelefoneUsuario',
				'coMesorregiao',
				'coOrgaoEmissor',
				'coMunicipioEndereco',
				'dtEmissaoDocumentacao',
				'tpEndereco',
				'nuCpf',
				'dsOcupacaoAlternativa',
				'coServidorPublico',
				'cepUsuario',
				'coAgenciaPagamento',
				'dsCargoFuncao',
				'nuSeqUsuario',
				'coMunicipioNascimento',
				'dtCadastro',
				'dtAlteracao',
				'stUsuario',
		);
	}

	/**
	 * Método para verificação de data de expedição de documentação.
	 * @param String $dataExpedicao
	 */
	public function verificaData($dataExpedicao){
		$oUsuarioBisness = new Fnde_Sice_Business_Usuario();
		
		if($oUsuarioBisness->validaDataExpedicao($dataExpedicao)){
			return true;
		}else{
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, "A data de expedição não pode ser maior que a data atual.");
			return false;
		}
	}
	
	/**
	 * Método para gravar um usuário no banco de dados.
	 */
	public function salvarUsuarioAction(){
		$this->montaCabecalhoFormAoSalvar($this->getRequest()->isPost());
		
        $arDados = $this->_getAllParams();
		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados);
                              
		$_SESSION['NU_SEQ_ATIVIDADE'] = $_POST['NU_SEQ_ATIVIDADE'];
		$_SESSION['DS_ATIVIDADE_ALTERNATIVA'] = $_POST['DS_ATIVIDADE_ALTERNATIVA'];
                
		$vinculaAtivUsuarioForm = new VinculaAtivUsuario_Form($this->_request->getParams(),array());
	
		$valorElementos = $vinculaAtivUsuarioForm->getElements();
		
                $this->preparaValidacaoAtividade($valorElementos, $form);
		$this->preparaValidacaoForm($form);
		
		$arDados = $this->getValoresAtividade($vinculaAtivUsuarioForm, $_POST);
     
		$form->getElement('TP_INSTITUICAO')->setRequired(false);
		$form->getElement('NU_SEQ_FORMACAO_ACADEMICA')->setRequired(false);
		$form->getElement('TP_INSTITUICAO')->setRequired(false);
		$form->getElement('NU_SEQ_FORMACAO_ACADEMICA')->setRequired(false);
		$form->getElement('CO_ESCOLA')->setRequired(false);

		if (!$form->isValid($_POST) || !$vinculaAtivUsuarioForm->isValid($arDados)) {
			if(!$_POST['NU_SEQ_USUARIO']) {
				$form->addElements($vinculaAtivUsuarioForm->getElements());
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
				foreach ($vinculaAtivUsuarioForm->getElements() as $elemento) {
					$form->removeElement($elemento->getName());
				}
				$this->preparaRetornoForm($valorElementos, $form);
				$this->view->form = $form;
				return $this->render('form');
			} else {
				//validar edição
				$strErro = $this->validarFormEdicao($_POST);

				if(!empty($strErro)){

					$this->addMessage(Fnde_Message::MSG_ERROR, $strErro);
					$this->_redirect("/manutencao/usuario/form/NU_SEQ_USUARIO/".$_POST['NU_SEQ_USUARIO']);
				}
			}
		}

		//Recupera os parÃ¢metros do request
		$arParams = $this->_request->getParams();


		if(Fnde_Sice_Business_Usuario::validaCPF($arParams['NU_CPF'], $form, true)
				&& $this->verificaData($arParams['DT_EMISSAO_DOCUMENTACAO'])){

			$arParamsUsuario = $this->getParamsUsuario($arParams);
			
			$arSessionFormAcad = $this->getAtividadeSession();
			
			$arAtividades = $this->getParamsAtividade($arDados, $_POST);
			
			$obBusUsu = new Fnde_Sice_Business_Usuario();
			$dadosAnteriores = null;
			
			if ($arParams['NU_SEQ_USUARIO'] != null) {
				$dadosAnteriores = $obBusUsu->getUsuarioById($arParams['NU_SEQ_USUARIO']);
				// verifica se é coordenador executivo para validar a E20.
				if($arParams['ST_USUARIO'] == 'A'){
					if($arParams['NU_SEQ_TIPO_PERFIL'] == '8'){
						//verifica se é uma edição de usuário caso seja vai verificar se o id do usuário é o mesmo.
						$executivo = $obBusUsu->validaExecutivoEstadual($arParams['SG_UF_ATUACAO_PERFIL_CAD'],$arParams['NU_SEQ_USUARIO']);
						// valida caso executivo retorne se for edição do coordenador executivo da uf
						// se for novo usuário executivo na mesma uf não cadastra e mostra amensagem
						if($executivo){
							$this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Usuario::MSG_ERRO_EXECUTIVO);
							$this->view->form = $form;
							return $this->render('form');
						}
					}
				}
				
				$arParamsUsuario['NU_SEQ_USUARIO'] = $arParams['NU_SEQ_USUARIO'];
				unset($arParamsUsuario['DT_CADASTRO']);
				$arParamsUsuario['DT_ALTERACAO'] = date('d/m/Y');
				$obModeloVinculoFormacaoUsuario = new Fnde_Sice_Business_VincFormAcadUsu();
				$obModeloVinculaAtivUsuario = new Fnde_Sice_Business_VinculaAtivUsuario();
				///$obModeloVinculoFormacaoUsuario->removerPorUsuario($arParams['NU_SEQ_USUARIO']);
				$obModeloVinculaAtivUsuario->del($arParams['NU_SEQ_USUARIO']);
			}

			try {
                if($obBusUsu->verificaEmailCadastrado($arParams['NU_SEQ_USUARIO'], $arParams['DS_EMAIL_USUARIO'])){
                    throw new Exception("1 ORA-00001: SUSR_UK_02");
                }

                if (isset($_POST['NU_SEQ_FORMACAO_ACADEMICA']) &&
                    isset($_POST['TP_INSTITUICAO']) &&
                    isset($_POST['NO_INSTITUICAO']) &&
                    isset($_POST['NO_CURSO']) &&
                    isset($_POST['DT_CONCLUSAO']))
                {
                    $salvarVincFormAcad = true;
                } else {
                    $salvarVincFormAcad = false;
                }

				if(empty($_POST['NU_SEQ_USUARIO'])){
					$id = $obBusUsu->salvar($arParamsUsuario, $arSessionFormAcad, $arAtividades);
				} else {
				    $id = $obBusUsu->salvarEdicao($_POST);
				}

				if($salvarVincFormAcad){
                    $this->salvarVinculoDeFormacaoAcademica($_POST);
                }

				if(empty($id)){
				    throw new Exception("Sem ID");
                }

				if($arParams['NU_SEQ_USUARIO'] == null){
					$busSituUsu = new Fnde_Sice_Business_SituacaoUsuario();
					$busSituUsu->setLogSituacao($arParamsUsuario['ST_USUARIO'], $id);
				}

                if($dadosAnteriores) {
                    /*
                     * SGD 26371
                     *
                     * ao alterar o perfil, o termo de compromisso deve ser finalizado
                     * */

                    //pego ds_perfil anterior
                    $businessTipoPerfil = new Fnde_Sice_Business_TipoPerfil();
                    $perfil = $businessTipoPerfil->getTipoPerfilById($dadosAnteriores['NU_SEQ_TIPO_PERFIL']);

                    //verifico se faz parte dos que tem termo
                    $podeTerTermo = in_array($perfil['DS_TIPO_PERFIL_SEGWEB'], Fnde_Sice_Model_TermoCompromisso::$arrPerfis);

                    if ($podeTerTermo) {
                        //buscar termo ativo desse perfil
                        $businessTermo = new Fnde_Sice_Business_TermoCompromisso();
                        $businessTermo->getTermoAtivoUsuario($dadosAnteriores['NU_SEQ_USUARIO'], $dadosAnteriores['NU_SEQ_TIPO_PERFIL']);

                        //finaliza-lo
                        foreach ($businessTermo as $termo) {
                            $paramsTermo = array(
                                'NU_SEQ_TERMO_COMPROMISSO' => $termo['NU_SEQ_TERMO_COMPROMISSO'],
                                'DT_FIM' => date('d/m/Y G:i:s')
                            );
                            $businessTermo->salvar($paramsTermo);
                        }
                    }
                }

                if (is_null($dadosAnteriores) || $dadosAnteriores['NU_SEQ_TIPO_PERFIL'] != $arParamsUsuario['NU_SEQ_TIPO_PERFIL']) {
                    $obHistorico = new Fnde_Sice_Business_PerfilUsuario();
                    $obHistorico->setHistoricoPerfil($arParamsUsuario['NU_SEQ_TIPO_PERFIL'], $id);
                }

			} catch (Exception $e) {
			    echo '<pre>';
			    echo $e->getTraceAsString();
			    echo '</pre>';

				$this->verificaErroSalvar($form, $e, $arParamsUsuario);

				$this->view->form = $form;
				return $this->render('form');
			}

			//ajustando email no segweb
			$config = Zend_Registry::get('config');
			$urlSegWeb = $config['webservices']['segweb']['uri'] . 'usuario/Edit';
			//$urlSegWeb = 'http://www.fnde.gov.br/webservices/segweb/index.php/usuario/Edit';
			$xml = '<request>
						<header>
							<app>SICE</app>
							<version>1.0</version>
							<created>' . date("Y-m-d\TH:i:s") . '</created>
						</header>
						<body>
							<usuario>
								<idusuario>' . $dadosAnteriores['NU_SEQ_USUARIO_SEGWEB'] . '</idusuario>
								<alterarsenha>N</alterarsenha>
								<email>' . $arParamsUsuario['DS_EMAIL_USUARIO'] . '</email>
							</usuario>
						</body>
					</request>';

			$postdata = http_build_query(
				array(
					'xml' => $xml
				)
			);

			$opts = array('http' =>
				array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);

			$context = stream_context_create($opts);
			$result = file_get_contents($urlSegWeb, false, $context);

			$obj = simplexml_load_string($result);
			$ok = (int)$obj->status->result;

			if (!($obj && $ok == 1)) {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, "Erro ao acessar o SEGWEB! Tente novamente mais tarde.");
            }

			//fim ajustando email

			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Ação realizada com sucesso.");
			$this->_redirect("/manutencao/usuario/list");
		}
		
		$this->view->form = $form;
		return $this->render('form');
	}
	public function salvarVinculoDeFormacaoAcademica($potagem){
		$arrVincFormAcadUs = array();

		if(isset($potagem['NU_SEQ_FORMACAO_ACADEMICA'])){$arrVincFormAcadUs['NU_SEQ_FORMACAO_ACADEMICA'] = $potagem['NU_SEQ_FORMACAO_ACADEMICA'];}
		if(isset($potagem['TP_INSTITUICAO'])){$arrVincFormAcadUs['TP_INSTITUICAO'] = $potagem['TP_INSTITUICAO'];}
		if(isset($potagem['NO_INSTITUICAO'])){$arrVincFormAcadUs['NO_INSTITUICAO'] = $potagem['NO_INSTITUICAO'];}
		if(isset($potagem['NO_CURSO'])){$arrVincFormAcadUs['NO_CURSO'] = $potagem['NO_CURSO'];}
		if(isset($potagem['DT_CONCLUSAO'])){$arrVincFormAcadUs['DT_CONCLUSAO'] = $potagem['DT_CONCLUSAO'];}
		$arrVincFormAcadUs['NU_SEQ_USUARIO'] = $potagem['NU_SEQ_USUARIO'];

		$objVincFormAcadUs = new Fnde_Sice_Business_VincFormAcadUsu();
		$objVincFormAcadUsModel = new Fnde_Sice_Model_VincFormAcadUsu();
		$dadosVincFormAcadUs = $objVincFormAcadUs->getVinculoByUsuario($potagem['NU_SEQ_USUARIO']);

		if($dadosVincFormAcadUs){
			foreach($arrVincFormAcadUs as $k => $i ){
				$dadosVincFormAcadUs[0][$k] = $i;
			}
			$objVincFormAcadUsModel->update($arrVincFormAcadUs);
		}else{
			$objVincFormAcadUsModel->insert($arrVincFormAcadUs);
		}

		return true;
	}
	/**
	 * Método para gravar um usuário no banco de dados.
	 */
	public function salvarUsuarioCursistaAction(){
		$obModeloUsuario = new Fnde_Sice_Model_Usuario();
		$obBusinessUsuario = new Fnde_Sice_Business_Usuario();
		$obModeloDadosEscolares = new Fnde_Sice_Model_DadosEscolaresCursista();
		$obBussinessUsuario = new Fnde_Sice_Business_Municipio();
		$obBussinessVincForm = new Fnde_Sice_Model_VincFormAcadUsu();
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessTurma = new Fnde_Sice_Business_Turma();
		$perfilUsuario = $usuarioLogado->credentials;

		$obModeloUsuario->getAdapter()->beginTransaction();
		try {
			$arParamsUsuario = $this->_getAllParams();
			//Recupera o objeto de formulário para validação
			$form = $this->getForm($arParamsUsuario);
			$form->removeElement('CO_ESTADO_CIVIL');
			$form->removeElement('TP_ENDERECO');
			$form->removeElement('NU_CEP');
			$form->removeElement('DS_ENDERECO');
			$form->removeElement('DS_COMPLEMENTO_ENDERECO');
			$form->removeElement('DS_BAIRRO_ENDERECO');
			$form->removeElement('CO_UF_ENDERECO');
			$form->removeElement('CO_MUNICIPIO_ENDERECO');
			$elementos = $form->getElements();
			$dadosTurma = $businessTurma->getTurmaPorAluno($arParamsUsuario['NU_SEQ_USUARIO']);

			//Verifica os perfils e desabilita os campos de acordo com o escolhido.
			if (($dadosTurma["DS_ST_TURMA"] != "Finalizada") &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario))
			) {
				$campos = array("SG_UF_NASCIMENTO", "CO_MUNICIPIO_NASCIMENTO", "DS_EMAIL_USUARIO", "CO_MUNICIPIO_ESCOLA",
					"DS_EMAIL_USUARIO_CONFIRM", "DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO", "CO_REDE_ENSINO",
					"CO_ESCOLA", "CO_SEGMENTO", "NU_SEQ_FORMACAO_ACADEMICA", "TP_INSTITUICAO");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos) && ($elemento->getName() != "confirmar" && $elemento->getName() != "cancelar")) {
						$elemento->setAttrib("disabled", true);
						$elemento->setRequired(false);
					}
				}
			} else if ($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))
			) {
				$campos = array("DS_EMAIL_USUARIO", "DS_EMAIL_USUARIO_CONFIRM", "confirmar", "cancelar");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos)) {
						$elemento->setAttrib("disabled", true);
						$elemento->setRequired(false);
					}
				}
			} else if ($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))
			) {
				$campos = array("SG_UF_NASCIMENTO", "CO_MUNICIPIO_NASCIMENTO", "DS_EMAIL_USUARIO", "CO_MUNICIPIO_ESCOLA",
					"DS_EMAIL_USUARIO_CONFIRM", "DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO", "CO_REDE_ENSINO",
					"CO_ESCOLA", "CO_SEGMENTO", "NU_SEQ_FORMACAO_ACADEMICA", "TP_INSTITUICAO");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos) && ($elemento->getName() != "confirmar" && $elemento->getName() != "cancelar")) {
						$elemento->setAttrib("disabled", true);
						$elemento->setRequired(false);
					}
				}
			}

			$camposMensagem = "";
			$cont = 1;
			if (!$form->isValid($arParamsUsuario)) {
				foreach ($form->getElements() as $elemento) {

					$msg = $elemento->getMessages();
					if ($elemento->getMessages() && !strstr($msg["notInArray"], 'não faz parte dos valores esperados')) {
						$camposMensagem .= $cont . ". " . str_replace(":", "", $elemento->getLabel()) . "<br/>";
						$cont++;
					}
				}
			}

//			if ($arParamsUsuario['NU_SEQ_FORMACAO_ACADEMICA'] == '') {
//				$camposMensagem .= $cont . ". Escolaridade<br/>";
//				$cont++;
//			}
//			if ($arParamsUsuario['TP_INSTITUICAO'] == '') {
//				$camposMensagem .= $cont . ". Tipo de Instituição <br/>";
//				$cont++;
//			}

			if ($camposMensagem != "") {
				$this->addMessage(Fnde_Message::MSG_ERROR, $camposMensagem);
				$this->_redirect("/manutencao/usuario/form/NU_SEQ_USUARIO/{$arParamsUsuario['NU_SEQ_USUARIO']}");
			}

			//Altera dados do cursista
			$dadosCursista = $this->getParamsCursista($arParamsUsuario);
			$obModeloUsuario->update($dadosCursista, "NU_SEQ_USUARIO = {$arParamsUsuario['NU_SEQ_USUARIO']}");

			//Altera Tipo da Instituição
			$dadosFormacao['NU_SEQ_FORMACAO_ACADEMICA'] = $arParamsUsuario['NU_SEQ_FORMACAO_ACADEMICA'];
			$dadosFormacao['TP_INSTITUICAO'] = $arParamsUsuario['TP_INSTITUICAO'];

			$businessVincForm = new Fnde_Sice_Business_VincFormAcadUsu();
			$formacao = $businessVincForm->getVinculoByUsuario($arParamsUsuario['NU_SEQ_USUARIO']);

			if (count($formacao) > 0) {
				$obBussinessVincForm->update($dadosFormacao, "NU_SEQ_USUARIO = {$arParamsUsuario['NU_SEQ_USUARIO']}");
			} else if ($dadosFormacao['NU_SEQ_FORMACAO_ACADEMICA'] != '' && $dadosFormacao['TP_INSTITUICAO'] != '') {
				$dadosFormacao['NU_SEQ_USUARIO'] = $arParamsUsuario['NU_SEQ_USUARIO'];
				$obBussinessVincForm->insert($dadosFormacao);
			}

			//Altera dados Escolares
			$dadosMunicipio = $obBussinessUsuario->getDadosMunicipioFndeByCodIbge($arParamsUsuario['CO_MUNICIPIO_ESCOLA']);
			$dadosEscola = $this->getParamsDadosEscolares($arParamsUsuario);

			$dadosEscola ["CO_MUNICIPIO_ESCOLA"] = $arParamsUsuario['CO_MUNICIPIO_ESCOLA'];
			$dadosEscolares = Fnde_Sice_Business_DadosEscolaresCursista::getDadosEscolaresById($arParamsUsuario['NU_SEQ_USUARIO']);

			if ($dadosEscolares) {
				$obModeloDadosEscolares->update($dadosEscola, "NU_SEQ_USUARIO_CURSISTA = {$arParamsUsuario['NU_SEQ_USUARIO']}");
			} else {
				$dadosEscola['NU_SEQ_USUARIO_CURSISTA'] = $arParamsUsuario['NU_SEQ_USUARIO'];
				$obModeloDadosEscolares->insert($dadosEscola);
			}


            $obBusinessUsuario->atualizarDadosSegWeb(array(
                'NU_SEQ_USUARIO' => $arParamsUsuario['NU_SEQ_USUARIO'],
//                'NU_SEQ_TIPO_PERFIL' => $dadosCursista['NU_SEQ_TIPO_PERFIL'],
                'DS_EMAIL_USUARIO' => $dadosCursista['DS_EMAIL_USUARIO'],
                'DS_TELEFONE_USUARIO' => $dadosCursista['DS_TELEFONE_USUARIO'],
                'DS_BAIRRO_ENDERECO' => $dadosCursista['DS_BAIRRO_ENDERECO'],
                'NU_CEP' => $dadosCursista['NU_CEP'],
                'DS_ENDERECO' => $dadosCursista['DS_ENDERECO'],
                'DS_COMPLEMENTO_ENDERECO' => $dadosCursista['DS_COMPLEMENTO_ENDERECO']
            ));

			$obModeloUsuario->getAdapter()->commit();
			$this->addMessage(Fnde_Message::MSG_SUCCESS, "Ação realizada com sucesso.");
		}catch(Exception $e){
			$obModeloUsuario->getAdapter()->rollback();
			$this->addMessage(Fnde_Message::MSG_ERROR, "Error ao inserir registros, verifique os dados: " . $e->getMessage());
		}
		$this->_redirect("/manutencao/usuario/form/NU_SEQ_USUARIO/{$arParamsUsuario['NU_SEQ_USUARIO']}");

	}
	/**
	 * Recupera um array de valores das atividades da sessão
	 */
	private function getAtividadeSession(){
		$arSessionFormAcad = array();
		if(isset($_SESSION['rsDataFormacaoAcademica'])){
			for($i = 0; $i < count($_SESSION['rsDataFormacaoAcademica']); $i++){
				$arrayAux = array();
				$arrayAux["TP_ESCOLARIDADE"] = $_SESSION['rsDataFormacaoAcademica'][$i]["TP_ESCOLARIDADE"];
				$arrayAux["TP_INSTITUICAO"] = $_SESSION['rsDataFormacaoAcademica'][$i]["TP_INSTITUICAO"];
				$arrayAux["NO_INSTITUICAO"] = $_SESSION['rsDataFormacaoAcademica'][$i]["NO_INSTITUICAO"];
				$arrayAux["NO_CURSO"] = $_SESSION['rsDataFormacaoAcademica'][$i]["NO_CURSO"];
				$arrayAux["DT_CONCLUSAO"] = $_SESSION['rsDataFormacaoAcademica'][$i]["DT_CONCLUSAO"];
				$arrayAux["NU_SEQ_FORMACAO_ACADEMICA"] = null;
				
				/* unset($_SESSION['rsDataFormacaoAcademica'][$i]["ID"]);
				unset($_SESSION['rsDataFormacaoAcademica'][$i]["DS_TP_ESCOLARIDADE"]);
				unset($_SESSION['rsDataFormacaoAcademica'][$i]["DS_TP_INSTITUICAO"]);
				$_SESSION['rsDataFormacaoAcademica'][$i]["NU_SEQ_FORMACAO_ACADEMICA"] = null; */
				$arSessionFormAcad[] = $arrayAux;
			}
		}
		
		return $arSessionFormAcad;
	}
	
	/**
	 * Adiciona erro ao formulário na função salvarUsuarioAction
	 * @param  $form
	 * @param  $e
	 */
	private function verificaErroSalvar( &$form, $e, $arParamsUsuario ) {

		if ( strpos($e->getMessage(), 'ORA-00001', 0) ) {
			if ( strpos($e->getMessage(), 'SUSR_UK_01', 0) ) { //Validando E03
				$form->getElement('NU_CPF')->addError(
						'O CPF ' . $arParamsUsuario['NU_CPF'] . ' já está cadastrado em nosso banco de Dados.');
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			} elseif ( strpos($e->getMessage(), 'SUSR_UK_02', 0) ) { //Validando E4
				$form->getElement('DS_EMAIL_USUARIO')->addError('O email já está cadastrado para outro usuário');
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						Fnde_Sice_Business_Componentes::listarCamposComErros($form));
			} else {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
			}
		} else {
			
			if(strpos($e->getFile(), "Usuario.php") || strpos($e->getMessage(), 'ORA-', 0)){
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			}else{
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, "Não foi possível alterar o perfil do usuário devido a indisponibilidade do Segweb.");
			}
			
		}
	}
	
	/**
	 * Remonta cabeçalho do formulário ao salvar usuário
	 */
	private function montaCabecalhoFormAoSalvar($postRequest){
		$this->setTitle('Usuário');
		if($this->getRequest()->getParam("NU_SEQ_USUARIO")){
			$this->setSubtitle('Editar');
		}else{
			$this->setSubtitle('Cadastro');
		}
		
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;
		
		//monta menu de contexto
		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$urlCadastrar = $this->getUrl('manutencao', 'usuario', 'form', ' ');
		$menu = Fnde_Sice_Business_Componentes::montaMenuContextoUsuario($perfilUsuario,$urlFiltrar,$urlCadastrar);
		$this->setActionMenu($menu);
		
		// Se os dados nÃ£o foram enviados por post retorna para a index
		if (!$postRequest) {
			return $this->_forward('index');
		}
	}
	
	/**
	 * Retorna o array com os valores de atividade e preenche o combo do formulario de atividade
	 * @param  $vinculaAtivUsuarioForm
	 * @param  $post
	 */
	private function getValoresAtividade(&$vinculaAtivUsuarioForm,$post){
		$arDados = array();
		$i = 0;
		foreach($post['NU_SEQ_ATIVIDADE'] as $ativ){
			$arDados['NU_SEQ_ATIVIDADE' . $i] = $ativ;
			$arDados['DS_ATIVIDADE_ALTERNATIVA' . $i] = $post['DS_ATIVIDADE_ALTERNATIVA'][$i];
		
			$vinculaAtivUsuarioForm->getElement('NU_SEQ_ATIVIDADE' . $i)->setMultiOptions($this->arOpcoesAtividade());
		
			$i++;
		}
		
		return $arDados;
	}
	
	/**
	 * Prepara os elementos do formulário para validação
	 * @param elemento $valorElementos
	 * @param formulario $form
	 */
	private function preparaValidacaoForm( &$form ){
		
		if($form->getElement('CO_OCUPACAO_USUARIO')->getValue() != 18){
			$form->getElement('DS_OCUPACAO_ALTERNATIVA')->setRequired(false);
		}
		
		if($form->getElement('CO_LOCAL_LOTACAO')->getValue() != 8){
			$form->getElement('DS_LOCAL_LOTACAO_ALTERNATIVA')->setRequired(false);
		}
                    
        if($this->_getParam('NU_SEQ_TIPO_PERFIL') != 4
				&& $this->_getParam('NU_SEQ_TIPO_PERFIL') != 8
				&& $this->_getParam('NU_SEQ_TIPO_PERFIL') != 5
				&& $this->_getParam('NU_SEQ_TIPO_PERFIL') != 6){
		
			$form->getElement('CO_DISTANCIA_PAGAMENTO')->setRequired(false);
			$form->getElement('SG_UF_PAGAMENTO')->setRequired(false);
			$form->getElement('CO_MUNICIPIO_PAGAMENTO')->setRequired(false);
			$form->getElement('CO_AGENCIA_PAGAMENTO')->setRequired(false);
		}
		
		if($form->getElement('NU_SEQ_TIPO_PERFIL')->getValue() != 4 && $form->getElement('NU_SEQ_TIPO_PERFIL')->getValue() != 8){
			$form->getElement('CO_REPRESENTACAO_CAD')->setRequired(false);
		}
	}
	
	/**
	 * Prepara os elementos do formulário de atividade para validação
	 * @param $valorElementos
	 */
	private function preparaValidacaoAtividade( &$valorElementos, &$form ){
		
		for($i = 0; $i <= (count($valorElementos ) - 1)/2; $i++){
			$idElementoAtiv = 'NU_SEQ_ATIVIDADE'.$i;
			$idElementoQual = 'DS_ATIVIDADE_ALTERNATIVA'.$i;
		
			$elemento = $valorElementos[$idElementoAtiv];
		
			if(isSet($elemento)){
				if($elemento->getValue() != '10'){
					$valorElementos[$idElementoQual]->setRequired(false);
				}
			}
		}
		
		//Implementação da RAS23, o campo Local de Lotação deve ser obrigatório apenas se a ocupação for Diretor de Escola, Merendeira ou Professor
		if($form->getElement('CO_OCUPACAO_USUARIO')->getValue() != 2
				&& $form->getElement('CO_OCUPACAO_USUARIO')->getValue() != 4
				&& $form->getElement('CO_OCUPACAO_USUARIO')->getValue() != 7){
		
			$form->getElement('CO_LOCAL_LOTACAO')->setRequired(false);
		}
	}
	
	/**
	 * Prepara os elementos ao formulário
	 * @param $valorElementos
	 * @param $form
	 */
	private function preparaRetornoForm(&$valorElementos, &$form){
		
		$msgEmail = $form->getElement('DS_EMAIL_USUARIO')->getMessages();
		if(count($msgEmail) > 0 && !$msgEmail['isEmpty']){
			$form->getElement('DS_EMAIL_USUARIO')->addError("Informe um e-mail válido");
		}
		
		$msgEmailConfim = $form->getElement('DS_EMAIL_USUARIO_CONFIRM')->getMessages();
		if(count($msgEmailConfim) > 0 &&  !$msgEmailConfim['isEmpty'] && !$msgEmailConfim['notSame']){
			$form->getElement('DS_EMAIL_USUARIO_CONFIRM')->addError("Informe um e-mail válido");
		}
		
		for($i = 0; $i <= (count($valorElementos ) - 1)/2; $i++){
			$idElementoAtiv = 'NU_SEQ_ATIVIDADE'.$i;
			$idElementoQual = 'DS_ATIVIDADE_ALTERNATIVA'.$i;
		
			$elemento = $valorElementos[$idElementoAtiv];
		
			if(isSet($elemento)){
				$valorElementos[$idElementoQual]->setRequired(true);
			}
		}
		$form->getElement('CO_LOCAL_LOTACAO')->setRequired(true);
		$form->getElement('DS_OCUPACAO_ALTERNATIVA')->setRequired(true);
		$form->getElement('DS_LOCAL_LOTACAO_ALTERNATIVA')->setRequired(true);
		$form->getElement('CO_DISTANCIA_PAGAMENTO')->setRequired(true);
		$form->getElement('SG_UF_PAGAMENTO')->setRequired(true);
		$form->getElement('CO_MUNICIPIO_PAGAMENTO')->setRequired(true);
		$form->getElement('CO_AGENCIA_PAGAMENTO')->setRequired(true);
		$form->getElement('CO_REPRESENTACAO_CAD')->setRequired(true);
		
	}
	
	/**
	 * Retorna o array com os dados para salvar o usuário
	 * @param array $arParams
	 * @return array
	 */
	private function  getParamsUsuario($arParams){
		$arParamsUsuario =
		array(
				'NU_SEQ_TIPO_PERFIL'            =>$arParams['NU_SEQ_TIPO_PERFIL'],
				'SG_UF_ATUACAO_PERFIL'          =>$arParams['SG_UF_ATUACAO_PERFIL_CAD'],
				'CO_MUNICIPIO_PERFIL'           =>$arParams['CO_MUNICIPIO_PERFIL'],
				'CO_MESORREGIAO'                =>$arParams['CO_MESORREGIAO_CAD'],
				'NU_CPF'                        =>trim(preg_replace('/[^0-9]/', '', $arParams['NU_CPF'])),
				'NO_USUARIO'                    =>$arParams['NO_USUARIO'],
				'CO_ESTADO_CIVIL'               =>$arParams['CO_ESTADO_CIVIL'],
				'CO_SEXO_USUARIO'               =>$arParams['CO_SEXO_USUARIO'],
				'DT_NASCIMENTO'                 =>$arParams['DT_NASCIMENTO'],
				'SG_UF_NASCIMENTO'              =>$arParams['SG_UF_NASCIMENTO'],
				'CO_MUNICIPIO_NASCIMENTO'       =>$arParams['CO_MUNICIPIO_NASCIMENTO'],
				'NO_MAE'                        =>$arParams['NO_MAE'],
				'NU_IDENTIDADE'     		    =>trim(preg_replace('/[^0-9]/', '', $arParams['NU_IDENTIDADE'])),
				'DT_EMISSAO_DOCUMENTACAO'       =>$arParams['DT_EMISSAO_DOCUMENTACAO'],
				'CO_ORGAO_EMISSOR'              =>$arParams['CO_ORGAO_EMISSOR'],
				'SG_UF_EMISSAO_DOC'             =>$arParams['SG_UF_EMISSAO_DOC'],
				'TP_ENDERECO'                   =>$arParams['TP_ENDERECO'],
				'NU_CEP'                        =>trim(preg_replace('/[^0-9]/', '', $arParams['NU_CEP'])),
				'DS_ENDERECO'                   =>$arParams['DS_ENDERECO'],
				'DS_COMPLEMENTO_ENDERECO'       =>$arParams['DS_COMPLEMENTO_ENDERECO'],
				'DS_BAIRRO_ENDERECO'            =>$arParams['DS_BAIRRO_ENDERECO'],
				'CO_UF_ENDERECO'            	=>$arParams['CO_UF_ENDERECO'],
				'CO_MUNICIPIO_ENDERECO'         =>$arParams['CO_MUNICIPIO_ENDERECO'],
				'DS_TELEFONE_USUARIO'           =>trim(preg_replace('/[^0-9]/', '', $arParams['DS_TELEFONE_USUARIO'])),
				'DS_CELULAR_USUARIO'            =>trim(preg_replace('/[^0-9]/', '', $arParams['DS_CELULAR_USUARIO'])),
				'DS_EMAIL_USUARIO'              =>$arParams['DS_EMAIL_USUARIO'],
				'CO_OCUPACAO_USUARIO'           =>$arParams['CO_OCUPACAO_USUARIO'],
				'DS_OCUPACAO_ALTERNATIVA'       =>$arParams['DS_OCUPACAO_ALTERNATIVA'],
				'CO_SERVIDOR_PUBLICO'           =>$arParams['CO_SERVIDOR_PUBLICO'],
				'DS_CARGO_FUNCAO'               =>$arParams['DS_CARGO_FUNCAO'],
				'CO_LOCAL_LOTACAO'              =>$arParams['CO_LOCAL_LOTACAO'],
				'CO_AGENCIA_PAGAMENTO'          =>$arParams['CO_AGENCIA_PAGAMENTO'],
				'CO_MUNICIPIO_PAGAMENTO'        =>$arParams['CO_MUNICIPIO_PAGAMENTO'],
				'SG_UF_PAGAMENTO'               =>$arParams['SG_UF_PAGAMENTO'],
				'CO_DISTANCIA_PAGAMENTO'        =>$arParams['CO_DISTANCIA_PAGAMENTO'],
				'DT_CADASTRO'                   =>date('d/m/Y'),
				'DT_ALTERACAO'                  =>null,
				'ST_USUARIO'                    =>$arParams['ST_USUARIO'] ? $arParams['ST_USUARIO'] :'L',
				'CO_REPRESENTACAO'              =>$arParams['CO_REPRESENTACAO_CAD'],
				'DS_LOCAL_LOTACAO_ALTERNATIVA'  =>$arParams['DS_LOCAL_LOTACAO_ALTERNATIVA'],);

		return $arParamsUsuario;
	}
	
	/**
	 * Retorna o array com os valores de atividade a serem salvos
	 * @param array $arDados
	 * @param $_POST $post
	 */
	private function getParamsAtividade($arDados, $post){
		$arAtividades = array();
		if(count($arDados) > 0){
			$idx = 0;
			foreach($post['NU_SEQ_ATIVIDADE'] as $ativ){
				$arAtividades[] =  array("NU_SEQ_ATIVIDADE"=>$ativ , "DS_ATIVIDADE_ALTERNATIVA" => $post['DS_ATIVIDADE_ALTERNATIVA'][$idx++]);
			}
		}
		return $arAtividades;
	}
	/**
	 * Retorna as opçõe de atividade
	 */
	private function arOpcoesAtividade(){
		$business = new Fnde_Sice_Business_Componentes();
		$arAtividade = array(null=>"Selecione");
		$arAtividade += $business->getAllByTable("Atividade", array("NU_SEQ_ATIVIDADE", "DS_ATIVIDADE"));
		
		return $arAtividade;
	}
	
	/**
	 * Método para remover um usuário do banco de dados.
	 */
	public function removerUsuarioAction() {

		$arParam = $this->_getAllParams();
		$obModel = new Fnde_Sice_Model_Usuario();

		$obBusinessVinculoAtivUsuario = new Fnde_Sice_Business_VinculaAtivUsuario();
		$obBusinessUsuario = new Fnde_Sice_Business_Usuario();
		$obBusinessVincCursistaTurma = new Fnde_Sice_Business_VincCursistaTurma();
		$obBusinessVincFormAcadUsu = new Fnde_Sice_Business_VincFormAcadUsu();
		$obBusHisPerfil = new Fnde_Sice_Business_PerfilUsuario();
		$obBusHisSituacao = new Fnde_Sice_Business_SituacaoUsuario();

		$obModel->getAdapter()->beginTransaction();
		try {
			$obBusHisSituacao->excluirLogSituacao($arParam["NU_SEQ_USUARIO"]);
			$obBusHisPerfil->excluirHistoricoPerfil($arParam["NU_SEQ_USUARIO"]);
			$obBusinessVinculoAtivUsuario->del($arParam["NU_SEQ_USUARIO"]);
			$obBusinessVincFormAcadUsu->removerPorUsuario($arParam["NU_SEQ_USUARIO"]);
			$obBusinessVincCursistaTurma->excluirVinculoPorCursista($arParam["NU_SEQ_USUARIO"]);
			$obBusinessUsuario->del($arParam["NU_SEQ_USUARIO"]);

			//Sucesso transação
			$obModel->getAdapter()->commit();
		} catch (Exception $e) {
			//Transação falhou
			$obModel->getAdapter()->rollBack();

			$this
					->addMessage(Fnde_Message::MSG_ERROR,
							"O usuário não poderá ser excluído, pois está vinculado a um módulo ou matrícula.");
			$this->_redirect("/manutencao/usuario/list");
		}

		$this->addMessage(Fnde_Message::MSG_SUCCESS, "O usuário foi excluído com sucesso.");
		$this->_redirect("/manutencao/usuario/list");
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
	 * Método para visualizar os dados do usuário cadastrado no banco de dados.

	public function visualizarUsuarioAction() {
		$this->setTitle('Usuario');
		$this->setSubtitle('Cadastrar');

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
		 *

		//monta menu de contexto
		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$urlCadastrar = $this->getUrl('manutencao', 'usuario', 'form', ' ');
		$menu = Fnde_Sice_Business_Componentes::montaMenuContextoUsuario($perfilUsuario, $urlFiltrar, $urlCadastrar);
		$this->setActionMenu($menu);

		// Recuperando array de dados do banco para setar valores no formulário
		$usuario = new Fnde_Sice_Business_Usuario();
		$idUsuario = $this->getRequest()->getParam("NU_SEQ_USUARIO");
		if ($idUsuario) {
			$arDados = $usuario->getUsuarioById($idUsuario);
		}

		$arDados['readonly'] = 1;
		$form = $this->getForm($arDados);

		$elementos = $form->getElements();

		foreach ($elementos as $elemento) {
			if ($elemento->getName() != "cancelar") {
				$elemento->setAttrib("disabled", true);
			}
		}

		$this->view->form = $form->populate($arDados);
		$this->render('form');
	}
	*/

	/**
	 * Método para visualizar os dados do usuário cadastrado no banco de dados.
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function visualizarUsuarioAction() {
		$this->setTitle('Usuário');
		if($this->getRequest()->getParam("NU_SEQ_USUARIO")){
			$this->setSubtitle('Editar');
		}else{
			$this->setSubtitle('Cadastrar');
		}

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessTurma = new Fnde_Sice_Business_Turma();
		$businessVincForm = new Fnde_Sice_Business_VincFormAcadUsu();
		$perfilUsuario = $usuarioLogado->credentials;

		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$urlCadastrar = $this->getUrl('manutencao', 'usuario', 'form', ' ');
		$menu = Fnde_Sice_Business_Componentes::montaMenuContextoUsuario($perfilUsuario, $urlFiltrar, $urlCadastrar);
		$this->setActionMenu($menu);

		$usuario = new Fnde_Sice_Business_Usuario();
		$idUsuario = $this->getRequest()->getParam("NU_SEQ_USUARIO");

		if ( $idUsuario ) {
			$arDados = $usuario->getUsuarioById($idUsuario);
			$dadosFormacao = $businessVincForm->getVinculoByUsuario($idUsuario);
			$arDados['NU_SEQ_FORMACAO_ACADEMICA'] = $dadosFormacao[0]['NU_SEQ_FORMACAO_ACADEMICA'];
			$arDados['TP_INSTITUICAO'] = $dadosFormacao[0]['TP_INSTITUICAO'];
		}

		if(
			in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
			in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
		) {
			if($arDados['ST_USUARIO'] == "A"){
				$this->view->isAllowedChangeMail = true;
			}
		}

		//Recupera o objeto de formulário para validação
		$arExtra = array();
		$arExtra['desabilitar'] = true;
		$form = $this->getForm($arDados,$arExtra);
		$this->view->arDados = $arDados;

		if($arDados["NU_SEQ_TIPO_PERFIL"] == 7) {
			$elementos = $form->getElements();
			$dadosTurma = $businessTurma->getTurmaPorAluno($idUsuario);

			$form->removeElement('CO_ESTADO_CIVIL');
			$form->removeElement('TP_ENDERECO');
			$form->removeElement('NU_CEP');
			$form->removeElement('DS_ENDERECO');
			$form->removeElement('DS_COMPLEMENTO_ENDERECO');
			$form->removeElement('DS_BAIRRO_ENDERECO');
			$form->removeElement('CO_UF_ENDERECO');
			$form->removeElement('CO_MUNICIPIO_ENDERECO');

			//Verifica os perfils e desabilita os campos de acordo com o escolhido.
			if (($dadosTurma["DS_ST_TURMA"] != "Finalizada") &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
					in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario))) {
				$campos = array("SG_UF_NASCIMENTO","CO_MUNICIPIO_NASCIMENTO","DS_EMAIL_USUARIO",
					"DS_EMAIL_USUARIO_CONFIRM","DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO","CO_REDE_ENSINO",
					"CO_MUNICIPIO_ESCOLA", "CO_ESCOLA","CO_SEGMENTO", "confirmar", "cancelar", 'NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO');
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			} else if ($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))) {
				$campos = array("DS_EMAIL_USUARIO", "DS_EMAIL_USUARIO_CONFIRM", "confirmar", "cancelar");
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			} else if($dadosTurma["DS_ST_TURMA"] == "Finalizada" &&
				(in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario))){
				$campos = array("SG_UF_NASCIMENTO","CO_MUNICIPIO_NASCIMENTO","DS_EMAIL_USUARIO",
					"CO_MUNICIPIO_ESCOLA", "DS_EMAIL_USUARIO_CONFIRM","DS_TELEFONE_USUARIO", "DS_CELULAR_USUARIO",
					"CO_REDE_ENSINO","CO_ESCOLA","CO_SEGMENTO", "confirmar", "cancelar", 'NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO');
				foreach ($elementos as $elemento) {
					if (!in_array($elemento->getName(), $campos))
						$elemento->setAttrib("disabled", true)
							->setRequired(false);
				}
			}

			$dadosEscolares = Fnde_Sice_Business_DadosEscolaresCursista::getDadosEscolaresById($idUsuario);

			if($dadosEscolares){
				$arDados['SG_UF_ESCOLA'] = $dadosEscolares['CO_UF_ESCOLA'];
				$arDados['SG_UF_ESCOLA_HIDDEN'] = $dadosEscolares['CO_UF_ESCOLA'];
				$arDados['CO_MUNICIPIO_ESCOLA'] = $dadosEscolares['CO_MUNICIPIO_ESCOLA'];
				$arDados['NO_MESORREGIAO_ESCOLA'] = $dadosEscolares['CO_MESORREGIAO'];
				$arDados['CO_REDE_ENSINO'] = $dadosEscolares['CO_REDE_ENSINO'];
				$arDados['CO_ESCOLA'] = $dadosEscolares['CO_ESCOLA'];
				$arDados['CO_SEGMENTO'] = $dadosEscolares['CO_SEGMENTO'];
			}
		}

		$form->populate($arDados);
		$this->view->form = $form;

		$this->render('form');
	}
	/**
	 * Método para recuperar os parâmetros de pesquisa.
	 */
	public function getSearchParamUsuario() {
		$arFilter = array();

		$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');

		$arSession = $this->urlFilterNamespace->param;

		$stUrlAtual = $this->_getParam('module') . '/' . $this->_getParam('controller') . '/'
				. $this->_getParam('action');
		$stUrlSession = $arSession['module'] . '/' . $arSession['controller'] . '/' . $arSession['action'];

		if ($stUrlAtual == $stUrlSession) {
			$arFilter = $arSession;
		}
		return $arFilter;
	}
	
	/**
	 * Método para carregar o combo de UF's pesquisando no webservice de dados de pagamento.
	 */
	public function carregaUfAction() {
            
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$agenciaBB = new Fnde_Model_AgenciaBB();
		$arAgencia = $agenciaBB
				->getMunicipio($this->_request->getParam('CO_MUNICIPIO_PERFIL'),
						$this->_request->getParam('CO_DISTANCIA_PAGAMENTO'));

		$arUfRetornado = array();

		if (empty($arAgencia[0])) {
			$agencia[0] = $arAgencia;
			$arAgencia = array();
			$arAgencia = $agencia;
		} else {
			foreach ($arAgencia as $key) {
				$arUfRetornado[$key['sg_uf']] = $key['sg_uf'];
			}
			ksort($arUfRetornado);
		}
                
                $output = '<option SELECTED value="" >Selecione</option>';
		foreach ($arUfRetornado as $agencia) {
                    $output .= '<option value="' . $agencia . '">' . $agencia . '</option>';
		}

		$this->getResponse()->setBody($output);
	}
	
	/**
	 * Método para carregar o combo de municípios pesquisando no webservice de dados de pagamento.
	 */
	public function carregaMunicipioAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$agenciaBB = new Fnde_Model_AgenciaBB();
		$arAgencia = $agenciaBB
				->getMunicipio($this->_request->getParam('CO_MUNICIPIO_PERFIL'),
						$this->_request->getParam('CO_DISTANCIA_PAGAMENTO'));

		if (empty($arAgencia[0])) {
			$agencia[0] = $arAgencia;
			$arAgencia = array();
			$arAgencia = $agencia;
		} else {
			foreach ($arAgencia as $key) {
				$sortEsc[] = $key['no_municipio'];
				$sortRef[] = $key['co_municipio'];
			}

			array_multisort($sortEsc, SORT_STRING, $sortRef, SORT_ASC, $arAgencia);

		}

		foreach ($arAgencia as $agencia) {
			if ($this->_request->getParam('SG_UF_PAGAMENTO') == $agencia['sg_uf']) {
				$arMunicipio[$agencia['co_municipio']] = $agencia['no_municipio'];
			}
		}

		$output = '<option SELECTED value="">Selecione</option>';

		foreach ($arMunicipio as $key => $value) {
			$output .= '<option value="' . $key . '">' . $value . '</option>';
		}

		$this->getResponse()->setBody($output);
	}
	
	/**
	 * Método para carregar o combo de agências pesquisando no webservice de dados de pagamento.
	 */
	public function carregaAgenciasAction() {
            
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();

		$agenciaBB = new Fnde_Model_AgenciaBB();
		$arAgencia = $agenciaBB
				->getMunicipio($this->_request->getParam('CO_MUNICIPIO_PERFIL'),
						$this->_request->getParam('CO_DISTANCIA_PAGAMENTO'));

		$output = '<option SELECTED value="">Selecione</option>';

		if (empty($arAgencia[0])) {
			$agencia[0] = $arAgencia;
			$arAgencia = array();
			$arAgencia = $agencia;
		} else {
			foreach ($arAgencia as $key) {
				$sortEsc[] = $key['no_agencia'];
				$sortRef[] = $key['co_agencia'];
			}

			array_multisort($sortEsc, SORT_STRING, $sortRef, SORT_ASC, $arAgencia);
		}

		foreach ($arAgencia as $agencia) {
			if ($this->_request->getParam('CO_MUNICIPIO_PAGAMENTO') == $agencia['co_municipio']) {
				$arMunicipio[$agencia['co_agencia']] = $agencia['no_agencia'];
			}
		}

		if (!empty($arMunicipio)) {
			foreach ($arMunicipio as $key => $value) {
				$output .= '<option value="' . $key . '">' . $value . '</option>';
			}
		}
		$this->getResponse()->setBody($output);
	}
	
	/**
	 * Seta o valor do combo de Mesorregiao de acordo com a UF selecionada
	 * E de acordo com o usuario logado no sistema.
	 * @param $form Formulario.
	 * @param $ufSelecionada Uf selecionada.
	 * @param $perfisUsuarioLogado Perfil do usuario logado no sistema
	 * @param $arUsuario Dados cadastrados no banco do usuario logado no sistema.
	 */
	public function setMesorregiaoFilter($form, $ufSelecionada, $perfisUsuarioLogado, $arUsuario) {
		try {
			$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
			
			$options = array(null => 'Selecione');
			
			if ( $ufSelecionada ) {
				$result = $obBusinessMesoregiao->getMesoRegiaoPorUF($ufSelecionada);
				for ( $i = 0; $i < count($result); $i++ ) {
					if ( !in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)
							&& !in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
						$options[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
					} else if ( $arUsuario['CO_MESORREGIAO'] == $result[$i]['CO_MESO_REGIAO'] ) {
						$options[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
					}
				}
			}
			
			$form->setMesorregiao($options);
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}
	}
	
	/**
	 * Seta o valor do combo de Municipio de acordo com a UF selecionada ou Mesorregiao selecionada
	 * E de acordo tambem com o usuario logado no sistema.
	 * @param $form Formulario.
	 * @param $ufSelecionada UF selecionada.
	 * @param $mesorregiaoSelecionada Mesorregiao selecionada.
	 * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
	 * @param $arUsuario Dados cadastrados no banco do usuario logado no sistema.
	 */
	public function setMunicipioFilter($form, $ufSelecionada = null, $mesorregiaoSelecionada = null, $perfisUsuarioLogado, $arUsuario) {
		try {
			$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
			$obBusinessUF = new Fnde_Sice_Business_Uf();
			
			$options = array(null => 'Selecione');
			
			if ($mesorregiaoSelecionada) {
				if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)
						|| in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
					$result = $obBusinessMesoregiao->getMunicipioById($arUsuario['CO_MUNICIPIO_PERFIL']);
				} else {
					$result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($mesorregiaoSelecionada);
				}
			} elseif ($ufSelecionada) {
				if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)
						|| in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
					$result = $obBusinessMesoregiao->getMunicipioById($arUsuario['CO_MUNICIPIO_PERFIL']);
				} else {
					$result = $obBusinessUF->getMunicipioCorpPorUf($ufSelecionada);
				}
			}
			
			for ( $i = 0; $i < count($result); $i++ ) {
			    if(isset($result[$i]['CO_MUNICIPIO_FNDE'])){
                    $options[$result[$i]['CO_MUNICIPIO_FNDE']] = $result[$i]['NO_MUNICIPIO'];
                }else{
                    $options[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
                }
			}
			
			$form->setMunicipio($options);
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}
	}
	
	/**
	 * Seleciona a mesorregiao de acordo com o municipio selecionado.
	 */
	public function municipioChangeAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
	
		$arParam = $this->_getAllParams();
	
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
		$mesorregiao = $businessMesoregiao->getMesoRegiaoPorMunicipio($arParam['CO_MUNICIPIO_PERFIL']);
	
		$retorno = $mesorregiao[0]['CO_MESO_REGIAO'];
	
		$this->_helper->json($retorno);
		return $retorno;
	}
	
	/**
	 * Seta as opcoes do combo de Perfil de acordo com o perfil do usuario logado no sistema.
	 * @param $form Formulario.
	 * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
	 */
	public function setTipoPerfil($form, $perfisUsuarioLogado) {
		try {
			//SETANDO OS VALORES DO COMBO DE PERFIL DE ACORDO COM O PERFIL LOGADO.
			$rsPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil",
					array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));
			
			$perfilRetorno[null] = 'Selecione';
			
			//mostra os perfis de acordo com o perfil do usuário logado
			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
				$perfilRetorno[5] = $rsPerfil[5]; //Articulador
				$perfilRetorno[4] = $rsPerfil[4]; //Coord. Est.
				$perfilRetorno[8] = $rsPerfil[8]; //Coord. Executivo.
				$perfilRetorno[1] = $rsPerfil[1]; //Coord. Adm.
				$perfilRetorno[2] = $rsPerfil[2]; //Coord. Equip.
				$perfilRetorno[3] = $rsPerfil[3]; //Coord. Gest.
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado) ) {
				$perfilRetorno[5] = $rsPerfil[5]; //Articulador
				$perfilRetorno[4] = $rsPerfil[4]; //Coord. Est.
				$perfilRetorno[8] = $rsPerfil[8]; //Coord. Executivo.
				$perfilRetorno[3] = $rsPerfil[3]; //Coord. Gest.
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
				$perfilRetorno[5] = $rsPerfil[5]; //Articulador
				$perfilRetorno[4] = $rsPerfil[4]; //Coord. Est.
				$perfilRetorno[8] = $rsPerfil[8]; //Coord. Executivo.
				$perfilRetorno[2] = $rsPerfil[2]; //Coord. Equip.
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado) ) {
				$perfilRetorno[5] = $rsPerfil[5]; //Articulador
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			}else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ) {
				$perfilRetorno[5] = $rsPerfil[5]; //Articulador
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			} else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
				$perfilRetorno[6] = $rsPerfil[6]; //Tut.
			} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
			} else if ( in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
				$perfilRetorno[7] = $rsPerfil[7]; //Curs.
			}
			
			$form->setTipoPerfil($perfilRetorno);
		} catch (Exception $e) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}
	}
	
	/**
	 * Adiciona os valores de perfil ao formulário
	 * @param form $form
	 */
	private function setComboPerfilAbaPerfil($form){
		$rsPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil",
				array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));
	
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
	
		$arPerfil = array(null=>'Selecione');
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			$arPerfil[5] = $rsPerfil[5];
			$arPerfil[4] = $rsPerfil[4];
			$arPerfil[8] = $rsPerfil[8]; //Coord. Executivo.
			$arPerfil[1] = $rsPerfil[1];
			$arPerfil[2] = $rsPerfil[2];
			$arPerfil[3] = $rsPerfil[3];
			$arPerfil[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado) ) {
			$arPerfil[5] = $rsPerfil[5];
			$arPerfil[4] = $rsPerfil[4];
			$arPerfil[8] = $rsPerfil[8]; //Coord. Executivo.
			$arPerfil[3] = $rsPerfil[3];
			$arPerfil[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
			$arPerfil[5] = $rsPerfil[5];
			$arPerfil[4] = $rsPerfil[4];
			$arPerfil[8] = $rsPerfil[8]; //Coord. Executivo.
			$arPerfil[2] = $rsPerfil[2];
			$arPerfil[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado) ) {
			$arPerfil[5] = $rsPerfil[5];
			$arPerfil[6] = $rsPerfil[6];
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ) {
			$arPerfil[5] = $rsPerfil[5];
			$arPerfil[6] = $rsPerfil[6];
		} 
//		  else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
//			$arPerfil[6] = $rsPerfil[6];
//		}
	
		$form->setComboPerfilAbaPerfil($arPerfil);
	
	}
	
	/**
	 * Adiciona os valores de uf ao formulário
	 * @param form $form
	 */
	private function setComboUfAbaPerfil($form){
		$obBusinessUF = new Fnde_Sice_Business_Uf();
		$business = new Fnde_Sice_Business_Usuario();
	
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		$arUsuario = $business->getUsuarioByCpf($cpfUsuarioLogado);
		$perfisUsuarioLogado = $usuarioLogado->credentials;
	
		$arUfAtuacaoPerfil = array(null=>'Selecione');
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
			$result = $obBusinessUF->search(array('SG_UF'));
		} else {
			$result = $obBusinessUF->search(array('SG_UF' => $arUsuario['SG_UF_ATUACAO_PERFIL']));
		}
	
		for ( $i = 0; $i < count($result); $i++ ) {
			$arUfAtuacaoPerfil[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
		}
                
		$form->setComboUfAbaPerfil($arUfAtuacaoPerfil);
	}
	
	/**
	 * Adiciona os valores de município ao formulário
	 * @param form $form
	 */
	private function setComboMunicipioAbaPerfil($form, $arDados){
		$obBusinessUF = new Fnde_Sice_Business_Uf();
	
		$arMunicipioAbaPerfil = array(null=>"Selecione");
		if ( $arDados['SG_UF_ATUACAO_PERFIL'] != null ) {
			$result = $obBusinessUF->getMunicipioPorUf($arDados['SG_UF_ATUACAO_PERFIL']);
			for ( $i = 0; $i < count($result); $i++ ) {
				$arMunicipioAbaPerfil[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
			}
		}
		//if ( $arDados["SG_UF_ATUACAO_PERFIL_CAD"] != null ) {
                //if($this->_getParam('SG_UF_ATUACAO_PERFIL_CAD') != null && $this->_getParam('SG_UF_ATUACAO_PERFIL_CAD') != $arDados['SG_UF_ATUACAO_PERFIL']) {
                if($this->_getParam('SG_UF_ATUACAO_PERFIL_CAD') != null) {
                    $arMunicipioAbaPerfil = array(null=>"Selecione");
                    
			$siglaUF = $this->_getParam('SG_UF_ATUACAO_PERFIL_CAD');
                        
			$result = $obBusinessUF->getMunicipioPorUf($siglaUF);
			for ( $i = 0; $i < count($result); $i++ ) {
				$arMunicipioAbaPerfil[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
			}
		}
	
		$form->setComboMunicipioAbaPerfil($arMunicipioAbaPerfil);
	}
	
	/**
	 * Adiciona os valores de mesorregião ao formulário
	 * @param form $form
	 */
	private function setMesorregiaoAbaPerfil($form, $arDados){
		$obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

		if ( $arDados['CO_MUNICIPIO_PERFIL'] != null ) {
			$result = $obBusinessMesoregiao->getMesoRegiaoPorMunicipio($arDados['CO_MUNICIPIO_PERFIL']);
			$noMesorregiao = $result[0]['NO_MESO_REGIAO'];
			$coMesorregiao = $result[0]['CO_MESO_REGIAO'];
		}

		$form->setMesorregiaoAbaPerfil($noMesorregiao, $coMesorregiao);
	}
	
	/**
	 * Adiciona os valores de uf ao formulário
	 * @param form $form
	 */
	private function setUfAbaDadosPessoais($form){
		$rsEstado = Fnde_Sice_Business_Componentes::getAllByTable("Uf", array("SG_UF", "SG_UF"));
		
		$form->setUfAbaDadosPessoais($rsEstado);
	}
	
	/**
	 * Adiciona os valores de muinicípio ao formulário
	 * @param form $form
	 */
	private function setMunicipioAbaDadosPessoais( $form, $arDados ) {

		$arMunicipio = array(null => "Selecione");
		
                $sg_uf = ($this->_getParam('SG_UF_NASCIMENTO') != null) ? $this->_getParam('SG_UF_NASCIMENTO') : $form->getElement('SG_UF_NASCIMENTO')->getValue();
		
                $obBusinessUF = new Fnde_Sice_Business_Uf();
                $result = $obBusinessUF->getMunicipioPorUf($sg_uf);
                for ( $i = 0; $i < count($result); $i++ ) {
                        $arMunicipio[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
                }
		$form->setMunicipioAbaDadosPessoais($arMunicipio);
	}
	
	/**
	 * Adiciona valores de uf ao formulario
	 * @param form $form
	 */
	private function setUfAbaDocumentacao( $form ) {
		$businessUF = new Fnde_Sice_Business_Uf();
		$result = $businessUF->search(array('SG_UF'));
		
		$arrUf = array(null=>"Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$arrUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
		}
		
		$form->setUfAbaDocumentacao($arrUf);
	}

	/**
	 * Adiciona valores de uf ao formulário
	 * @param form $form
	 */
	private function setUfAbaLogradouro( $form ) {
		$businessUF = new Fnde_Sice_Business_Uf();
		$result = $businessUF->search(array('SG_UF'));

		$arUf = array(null => "Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
		}

		$form->setUfAbaLogradouro($arUf);
	}

	/**
	 * Adiciona valores de uf ao formulário
	 * @param form $form
	 */
	private function setUfAbaDadosEscola( $form ) {
		$businessUF = new Fnde_Sice_Business_Uf();
		$result = $businessUF->search(array('SG_UF'));

		$arUf = array(null => "Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$arUf[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
		}

		$form->setUfAbaDadosEscola($arUf);
	}

	/**
	 * Adiciona valores do município ao formulário
	 * @param form $form
	 */
	private function setMunicipioAbaLogradouro($form, $arDados){
		$businessUF = new Fnde_Sice_Business_Uf();

		$sg_uf = ($this->_getParam('CO_UF_ENDERECO') != null) ? $this->_getParam('CO_UF_ENDERECO') : $form->getElement('CO_UF_ENDERECO')->getValue();

		$arMunicipio = array(null=>"Selecione");

		$result = $businessUF->getMunicipioPorUf($sg_uf);
		for ( $i = 0; $i < count($result); $i++ ) {
			$arMunicipio[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
		}

		$form->setMunicipioAbaLogradouro($arMunicipio);
	}

	/***
	 * Adiciona valores da rede de ensino
	 * @param form $form
	 */
	private function setRedeEnsinoAbaEscola($form, $arDados){
		$businessUF = new Fnde_Sice_Business_Uf();

		$arMunicipio = array(null=>"Selecione");

		$result = $businessUF->getMunicipioPorUf($sg_uf);
		for ( $i = 0; $i < count($result); $i++ ) {
			$arMunicipio[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
		}

		$form->setMunicipioAbaLogradouro($arMunicipio);
	}

	/**
	 * Adiciona valores do município ao formulário escola
	 * @param form $form
	 */
	private function setMunicipioAbaEscola($form, $arDados){
		$businessUF = new Fnde_Sice_Business_Uf();

		$arMunicipio = array(null=>"Selecione");

		$result = $businessUF->getMunicipioPorUf($arDados['CO_UF_ESCOLA']);
		for ( $i = 0; $i < count($result); $i++ ) {
			$arMunicipio[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
		}

		$form->setMunicipioAbaEscola($arMunicipio);
	}
	
	/**
	 * Adiciona o validador de e-mail ao formulário
	 * @param form $form
	 */
	private function setValidatorEmail($form){
		
		$validatorEmail = new Zend_Validate_EmailAddress();
		$validatorEmail->setMessages(Fnde_Sice_Business_Componentes::limpaMensagensEmailValidate());
		$optionsValidator = $validatorEmail->getOptions();
		$hstnameValidator = $optionsValidator['hostname'];
		$hstnameValidator->setMessages(Fnde_Sice_Business_Componentes::limpaMensagensEmailValidateHostName());
		
		$form->setValidatorEmail($validatorEmail);
		
	}
	
	/**
	 * Adiciona os valores de ocupação ao formulário
	 * @param form $form
	 */
	private function setOcupacaoAbaDadosFuncionais($form){
		$obBusinessOcupacao = new Fnde_Sice_Business_Ocupacao();
		$result = $obBusinessOcupacao->search(array('NU_SEQ_OCUPACAO'));
		
		$arOcupacao = array(null=>"Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$arOcupacao[$result[$i]['NU_SEQ_OCUPACAO']] = $result[$i]['DS_OCUPACAO'];
		}
		
		$form->setOcupacaoAbaDadosFuncionais($arOcupacao);
	}
	
	/**
	 * Adiciona os valores de local lotação ao fomulário
	 * @param form $form
	 */
	private function setLocalLotacaoAbaDadosFuncionais($form){
		$obBusinessLocal = new Fnde_Sice_Business_Local();
		$resultLocal = $obBusinessLocal->search(array('NU_SEQ_LOCAL'));
		
		$arLocalLotacao = array(null=>'Selecione');
		for ( $i = 0; $i < count($resultLocal); $i++ ) {
			$arLocalLotacao[$resultLocal[$i]['NU_SEQ_LOCAL']] = $resultLocal[$i]['DS_LOCAL'];
		}
		
		$form->setLocalLotacaoAbaDadosFuncionais($arLocalLotacao);
	}

    public function visualizarhistoricotermosAction(){
        $this->setTitle('Usuário');
        $this->setSubtitle('Visualizar Termos de Compromissos');

        $urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
        $this->setActionMenu(array($urlFiltrar => 'filtrar'));

        $params = $this->_getAllParams();

        $form = new Usuario_FormHistoricoTermos();
        $form->setDecorators(array('FormElements', 'Form'));

        $htmlDadosUsuario = $form->getElement("htmlDadosUsuario");
        $strDadosUsuario = $this->htmlDadosUsuarioHistPerfil($params['NU_SEQ_USUARIO']);
        $htmlDadosUsuario->setValue($strDadosUsuario);

        //buscar termos assinados
        $obBusiness = new Fnde_Sice_Business_TermoCompromisso();
        $rsRegistros = $obBusiness->getAssinaturas($params['NU_SEQ_USUARIO']);

        $businessTipoPerfil = new Fnde_Sice_Business_TipoPerfil();

        $result = array();

        foreach ($rsRegistros as $i=>$registro) {
			$tipoPerfil = $businessTipoPerfil->getTipoPerfilById($registro['NU_SEQ_TIPO_PERFIL']);

            $result[$i]['NU_SEQ_TERMO_COMPROMISSO'] = $registro['NU_SEQ_TERMO_COMPROMISSO'];
            $result[$i]['NU_ANO'] = $registro['NU_ANO'];
            $result[$i]['NU_SEQ_TIPO_PERFIL'] = $tipoPerfil['DS_TIPO_PERFIL'];
            $result[$i]['DT_INICIO'] = $registro['DT_INICIO'];
            $result[$i]['DT_FIM'] = $registro['DT_FIM'];
            $result[$i]['CO_ACORDO'] = Fnde_Sice_Model_TermoCompromisso::$arrAcordo[$registro['CO_ACORDO']];
            $result[$i]['CO_ACAO'] = Fnde_Sice_Model_TermoCompromisso::$arrAcao[$registro['CO_ACAO']];

            $imprimir = "<a href='{$this->getUrl('manutencao', 'termocompromisso', 'imprimir')}/NU_SEQ_TERMO_COMPROMISSO/{$registro['NU_SEQ_TERMO_COMPROMISSO']}' class='icoImprimir imprimir' title='Imprimir' target='blank'><span>Imprimir</span></a>";

            $result[$i]['ACAO'] = $imprimir;
        }

        //monta grid
        $arrHeader = array('<center>Ano</center>', '<center>Perfil</center>', '<center>Data Início</center>', '<center>Data Término</center>', '<center>Acordo</center>', '<center>Permissão</center>', '<center>Ações</center>');
//        $rowAction = $this->getArRowAction();

        $grid = new Fnde_View_Helper_DataTables();
//        $grid->setMainAction(array("Visualizar histórico bolsista" => $this->getUrl('manutencao', 'visualizahistbolsa', 'form', true),));
        $grid->setAutoCallJs(true);
//        $grid->setActionColumn("<center>Ações</center>");

        $gridTermos = $grid->setData($result)
            ->setHeader($arrHeader)
            ->setHeaderActive(false)
            ->setTitle("Listagem de Termos de Compromissos")
//            ->setRowAction($rowAction)
            ->setId('NU_SEQ_TERMO_COMPROMISSO')
//            ->setRowInput(Fnde_View_Helper_DataTables::INPUT_TYPE_RADIO)
            ->setTableAttribs(array('id' => 'edit'))
            ->setColumnsHidden(array("NU_SEQ_TERMO_COMPROMISSO"))
        ;

        $htmlDadosTermos = $form->getElement("htmlDadosTermos");
        $htmlDadosTermos->setValue($gridTermos);

        $this->view->histTermos = $form;
    }
	
	public function visualizarhistoricoperfilAction(){
		
		$this->setTitle('Usuário');
		$this->setSubtitle('Visualizar Histórico de Perfis');
		
		$urlFiltrar = $this->getUrl('manutencao', 'usuario', 'list', ' ');
		$this->setActionMenu(array($urlFiltrar => 'filtrar'));
		
		$params = $this->_getAllParams();
		
		$form = new Usuario_FormHistoricoPerfil();
		$form->setDecorators(array('FormElements', 'Form'));
		
		$htmlDadosUsuario = $form->getElement("htmlDadosUsuario");
		$strDadosUsuario = $this->htmlDadosUsuarioHistPerfil($params['NU_SEQ_USUARIO']);
		$htmlDadosUsuario->setValue($strDadosUsuario);
		
		$htmlDadosPerfil = $form->getElement("htmlDadosPerfil");
		$strHistorico = $this->htmlDadosHistPerfil($params['NU_SEQ_USUARIO']);
		$htmlDadosPerfil->setValue($strHistorico);
		
                $htmlDadosHistUsuario = $form->getElement("htmlDadosHistUsuario");
		$strDadosHistUsuario = $this->htmlDadosHistUsuario($params['NU_SEQ_USUARIO']);
		$htmlDadosHistUsuario->setValue($strDadosHistUsuario);
		
		$this->view->histPerfil = $form;
	}
	
	private function htmlDadosUsuarioHistPerfil($idUsuario){
		$obBusUsuario = new Fnde_Sice_Business_Usuario();
		$obBusMesorregiaco = new Fnde_Sice_Business_MesoRegiao();
		$arDadosUsuario = $obBusUsuario->getUsuarioById($idUsuario);

		$arDadosMesoregiao = $obBusMesorregiaco->getMesoRegiaoPorMunicipio($arDadosUsuario['CO_MUNICIPIO_PERFIL']);
        $arDadosMesoregiao = $arDadosMesoregiao[0];
		
		$html = "<div class='listagem' style='display:inline;'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption>Dados do Usuário</caption>";
		
		$html .= "<tr>";
		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "Nome";
		$html .= "</th>";
		$html .= "<td>";
		$html .= "{$arDadosUsuario['NO_USUARIO']}";
		$html .= "</td>";

		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "{$arDadosUsuario['SG_UF_ATUACAO_PERFIL']}";
		$html .= "</td>";
		$html .= "</tr>";

		$cpfFormatado = Fnde_Sice_Business_Componentes::formataCpf($arDadosUsuario['NU_CPF']);

		$html .= "<tr>";
		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "CPF";
		$html .= "</th>";
		$html .= "<td>";
		$html .= "{$cpfFormatado}";
		$html .= "</td>";

		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "Mesorregião";
		$html .= "</th>";
		$html .= "<td>";
		$html .= "{$arDadosMesoregiao['NO_MESO_REGIAO']}";
		$html .= "</td>";
		$html .= "</tr>";
		
		$html .= "<tr>";
		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "Perfil";
		$html .= "</th>";
		$html .= "<td>";
		
		if($arDadosUsuario['ST_USUARIO'] == 'A'){
			$html .=  'Ativo';
		}else if($arDadosUsuario['ST_USUARIO'] == 'D'){
			$html .=  'Inativo';
		}else{
			$html .=  'Liberação Pendente';
		}
		
		$html .= "</td>";
		
		$html .= "<th style='text-align:left; font-weight:normal; width:25%'>";
		$html .= "Município";
		$html .= "</th >";
		$html .= "<td>";
		$html .= "{$arDadosMesoregiao['NO_MUNICIPIO']}";
		$html .= "</td>";
		$html .= "</tr>";
		
		$html .= "</table>";

		$html .= "</div>";

		return $html;
		
	}
	
	private function htmlDadosHistPerfil($idUsuario){
		$obBusHistPerfil = new Fnde_Sice_Business_PerfilUsuario();
		$historico = $obBusHistPerfil->getHistoricoPerfilByUsu($idUsuario);
			
		$html = "<div class='listagem' style='display:inline;'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption>Dados dos Perfis</caption>";
		
		$html .= "<tr>";
		$html .= "<th>Perfil</th>";
		$html .= "<th>Data Início</th>";
		$html .= "<th>Data Fim</th>";
		$html .= "<th>Data/Hora Alteração</th>";
		$html .= "<th>Usuário Responsável</th>";
		$html .= "</tr>";
		
		foreach ($historico as $hist){
				$html .= "<tr>";
				$html .= "<td>{$hist['DS_TIPO_PERFIL']}</td>";
				$html .= "<td>{$hist['DT_INICIO']}</td>";
				$html .= "<td>{$hist['DT_FIM']}</td>";
				$html .= "<td>{$hist['DT_ALTERACAO']}</td>";
				$html .= "<td>{$hist['NO_RESPONSAVEL']}</td>";
				$html .= "</tr>";
		}
		$html .= "</table>";
	
		$html .= "</div>";
	
		return $html;
	
	}
        
	private function htmlDadosHistUsuario($idUsuario){
		$obBusHistPerfil = new Fnde_Sice_Business_PerfilUsuario();
		$historico = $obBusHistPerfil->getHistoricoByUsu($idUsuario);
			
		$html = "<div class='listagem' style='display:inline;'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption>Histórico do Usuário</caption>";
		
		$html .= "<tr>";
		$html .= "<th>Status</th>";
		$html .= "<th>Data Início</th>";
		$html .= "<th>Data Fim</th>";
		$html .= "<th>Usuário Responsável</th>";
		$html .= "</tr>";
		
		foreach ($historico as $hist){
				$html .= "<tr>";
				$html .= "<td>{$hist['SITUACAO']}</td>";
				$html .= "<td>{$hist['DT_INICIO']}</td>";
				$html .= "<td>{$hist['DT_FIM']}</td>";
				$html .= "<td>{$hist['NO_RESPONSAVEL']}</td>";
				$html .= "</tr>";
		}
		$html .= "</table>";
	
		$html .= "</div>";
	
		return $html;
	
	}

	public function setmunicipioAbaDadosEscola($form, $dadosEscolares){
		$businessUF = new Fnde_Sice_Business_Uf();
		$arMunicipio = array(null=>"Selecione");

		$result = $businessUF->getMunicipioCorpPorUf($dadosEscolares['CO_UF_ESCOLA']);
		for ( $i = 0; $i < count($result); $i++ ) {
			$arMunicipio[$result[$i]['CO_MUNICIPIO_FNDE']] = $result[$i]['NO_MUNICIPIO'];
		}

		$form->setMunicipioAbaEscola($arMunicipio);
	}

	public function setMesoregiaoAbaDadosEscola($form, $dadosEscolares){
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$rsMesoregiao = $businessUsuario->getTodasMesorregiaoUF($dadosEscolares);
		$form->setMesoregiaoAbaEscola($rsMesoregiao);
	}

	public function setRedeEnsinoAbaDadosEscola($form, $dadosEscolares){
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$result = $businessUsuario->pesquisarRedeEnsinoPorMunicipio($dadosEscolares['CO_MUNICIPIO_ESCOLA']);
		$options = array(null=>"Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$options[$result[$i]['CO_ESFERA_ADM']] = $result[$i]['NO_ESFERA_ADM'];
		}
		$form->setRedeEnsinoAbaEscola($options);
	}

	public function setNomeEscolaAbaDadosEscola($form, $dadosEscolares){
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$dados = array("CO_REDE_ENSINO" => $dadosEscolares["CO_REDE_ENSINO"],  "CO_MUNICIPIO_ESCOLA" => $dadosEscolares["CO_MUNICIPIO_ESCOLA"]);
		if(!is_null($dadosEscolares["CO_REDE_ENSINO"]) && !is_null($dadosEscolares["CO_MUNICIPIO_ESCOLA"])){
			$result = $businessUsuario->pesquisarEscola($dados);
		}
		$options = array(null=>"Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$options[$result[$i]['CO_ESCOLA']] = $result[$i]['NO_ESCOLA'];
		}
		$form->setNomeEscolaAbaEscola($options);
	}

	public function setSegmentoAbaDadosEscola($form){
		$bussinesSegmento = new Fnde_Sice_Business_Usuario();
		$result = $bussinesSegmento->getSegmentos();

		$options = array(null=>"Selecione");
		for ( $i = 0; $i < count($result); $i++ ) {
			$options[$result[$i]['NU_SEQ_SEGMENTO']] = $result[$i]['DS_SEGMENTO'];
		}
		$form->setSegmentoAbaEscola($options);

	}

	/**
	 * Recupera o array com informações para salvar o cursista
	 * @param arry $arParams
	 * @return array
	 */
	private function getParamsCursista( $arParams ) {
		/*
		 * sgd 29905
		 * alteração da regra RNS126
		 * o municipio do perfil do cursista sera o municipio da escola de atuação do mesmo
		 * */

		$municipioBiz = new Fnde_Sice_Business_Municipio();
		$municipio = $municipioBiz->getDadosMunicipio($arParams['CO_MUNICIPIO_ESCOLA']);
		$arParamsUsuario = array('NU_SEQ_TIPO_PERFIL' => 7,
			'NU_CPF' => trim(preg_replace('/[^0-9]/', '', $arParams['NU_CPF'])),
			'NO_USUARIO' => $arParams['NO_USUARIO'],
			'CO_ESTADO_CIVIL' =>	$arParams['CO_ESTADO_CIVIL'],
			'CO_SEXO_USUARIO' => $arParams['CO_SEXO_USUARIO'],
			'DT_NASCIMENTO' => $arParams['DT_NASCIMENTO'],
			'NO_MAE' => $arParams['NO_MAE'],
			'SG_UF_NASCIMENTO' => $arParams['SG_UF_NASCIMENTO'],
			'CO_MUNICIPIO_NASCIMENTO' => $arParams['CO_MUNICIPIO_NASCIMENTO'],
			'TP_ENDERECO' => $arParams['TP_ENDERECO'],
			'NU_CEP'=> trim(preg_replace('/[^0-9]/', '', $arParams['NU_CEP'])),
  			'DS_ENDERECO' => $arParams['DS_ENDERECO'],
  			'DS_COMPLEMENTO_ENDERECO' => $arParams['DS_COMPLEMENTO_ENDERECO'],
			'DS_BAIRRO_ENDERECO' => $arParams['DS_BAIRRO_ENDERECO'],
			'CO_UF_ENDERECO' => $arParams['CO_UF_ENDERECO'],
			'CO_MUNICIPIO_ENDERECO'=> $arParams['CO_MUNICIPIO_ENDERECO'],
			'DS_TELEFONE_USUARIO' => trim(preg_replace('/[^0-9]/', '', $arParams['DS_TELEFONE_USUARIO'])),
			'DS_CELULAR_USUARIO' => trim(preg_replace('/[^0-9]/', '', $arParams['DS_CELULAR_USUARIO'])),
			'DS_EMAIL_USUARIO'=> $arParams['DS_EMAIL_USUARIO'],
			'DT_ALTERACAO' => date('d/m/Y'),);

		return $arParamsUsuario;
	}

	/**
	 * Recupera o array com informações para salvar os dados escolares do cursista
	 * @param array $arParams
	 * @return array
	 */
	private function getParamsDadosEscolares( $arParams ) {
		$arParamsDadosEscolares = array('CO_UF_ESCOLA' => $arParams['SG_UF_ESCOLA'],
			'CO_MUNICIPIO_ESCOLA' => $arParams['CO_MUNICIPIO_ESCOLA'],
			'CO_MESORREGIAO' => $arParams['NO_MESORREGIAO_ESCOLA'],
			'CO_REDE_ENSINO' => $arParams['CO_REDE_ENSINO'],
			'CO_ESCOLA' => $arParams['CO_ESCOLA'],
			'CO_SEGMENTO' => $arParams['CO_SEGMENTO'],);

		return $arParamsDadosEscolares;
	}

	public function setFormacaoAcademica( $form ) {
		$rsFormacao = Fnde_Sice_Business_Componentes::getAllByTable("FormacaoAcademica", array("NU_SEQ_FORMACAO_ACADEMICA", "DS_FORMACAO_ACADEMICA"));

		$form->setFormacaoAcademica($rsFormacao);
	}

	/**
	 * Recupera o array com informações para salvar os dados da formação academica do cursista
	 * @param array $arParams
	 * @return array
	 */
	private function getParamsFormacaoAcademica( $arParams ) {
		$arParamsFormacaoAcademica = array('NU_SEQ_FORMACAO_ACADEMICA' => $arParams['NU_SEQ_FORMACAO_ACADEMICA'],
			'TP_INSTITUICAO' => $arParams['TP_INSTITUICAO'],);

		return $arParamsFormacaoAcademica;
	}

	private function validarFormEdicao($dados){
		$arrErro = array();

		foreach ( $dados as $k => $v) {
			switch ($k){
				case 'NU_SEQ_FORMACAO_ACADEMICA':
					if(empty($v)){
						//$arrErro[1][] = 'Escolaridade';
					}
					break;
				case 'TP_INSTITUICAO':
					if(empty($v)) {
						//$arrErro[1][] = 'Tipo de Instituição';
					}
					break;
				case 'DS_TELEFONE_USUARIO':
					if(empty($v)) {
						$arrErro[2][] = 'Telefone';
					}
					break;
				case 'DS_EMAIL_USUARIO':
					if(empty($v)) {
						$arrErro[2][] = 'E-mail';
					}
					break;
				case 'DS_EMAIL_USUARIO_CONFIRM':
					if(empty($v)) {
						$arrErro[2][] = 'Confirmar E-mail';
					}
					break;
				case 'CO_MUNICIPIO_ESCOLA':
					if(empty($v)){
						$arrErro[3][] = 'Município';
					}
					break;
				case 'CO_REDE_ENSINO':
					if(empty($v)) {
						$arrErro[3][] = 'Rede de ensino';
					}
					break;
				case 'CO_ESCOLA':
					if(empty($v)) {
						$arrErro[3][] = 'Nome da Escola';
					}
					break;
				case 'CO_SEGMENTO':
					if(empty($v)) {
						$arrErro[3][] = 'Segmento';
					}
					break;
			}
		}

		$strErro = '';
		ksort($arrErro);
		foreach ( $arrErro as $k => $v) {
			if($k === 1){
				$cnt1 = 1;
				$strErro .= '<b>Aba Dados Pessoais: </b><br />';
				foreach($v as $erro){
					$strErro .= $cnt1.'. '.$erro.'<br />';
					$cnt1++;
				}
			}

			if($k === 2){
				$cnt2 = 1;
				if($cnt1){$strErro .= '<br />';}
				$strErro .= '<b>Aba Contatos: </b><br />';
				foreach($v as $erro){
					$strErro .= $cnt2.'. '.$erro.'<br />';
					$cnt2++;
				}
			}

			if($k === 3){
				$cnt3 = 1;
				if($cnt1 || $cnt2){$strErro .= '<br />';}
				$strErro .= '<b>Aba Dados da Escola: </b><br />';
				foreach($v as $erro){
					$strErro .= $cnt3.'. '.$erro.'<br />';
					$cnt3++;
				}
			}
		}
		return $strErro;
	}
}
