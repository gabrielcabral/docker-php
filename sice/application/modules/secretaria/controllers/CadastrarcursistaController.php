<?php

/**
 * Controller do MatricularCursista
 *
 * @author diego.matos
 * @since 03/05/2012
 */

class Secretaria_CadastrarCursistaController extends Fnde_Sice_Controller_Action {

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
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function formAction() {
		$this->_helper->layout()->disableLayout();

		// Recuperando array de dados do banco para setar valores no formulário
		$arDados = $this->getRequest()->getParams();

		//Recupera o objeto de formulário para validação
		$form = $this->getForm($arDados);

		$this->view->form = $form;

		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);

		if ( $cpfUsuarioLogado ) {
			$infoUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		$form = new CadastrarCursista_Form($arDados, $infoUsuario);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/cadastrarcursista/salvar-cursista')->setMethod(
				'post')->setAttrib('id', 'form');

		$rsEstado = Fnde_Sice_Business_Componentes::getAllByTable("Uf", array("SG_UF", "SG_UF"));
		$this->preencheSelectsFormulario($form, $arDados, $infoUsuario, $rsEstado);

        if(
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, Zend_Auth::getInstance()->getIdentity()->credentials) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, Zend_Auth::getInstance()->getIdentity()->credentials)
        ){
            $form->getElement('SG_UF_ESCOLA')->setAttrib("disabled", null);
        }

		return $form;
	}

	/**
	 * Método para preencher os selects do formulario
	 * @author gustavo.gomes
	 * @param CadastrarCursista_Form $form
	 * @param array $arDados
	 * @param array $infoUsuario
	 * @param array $rsEstado
	 */
	public function preencheSelectsFormulario( $form, $arDados, $infoUsuario, $rsEstado ) {
		$this->setUfNascimento($form, $rsEstado);
		$this->setMunicipio($form, $arDados);
		$this->setUfEscola($form, $rsEstado);
		$this->setMunicipioEscola($form, $infoUsuario, $arDados);
		$this->setNomeEscola($form, $arDados);
		$this->setSegmento($form);
		$this->setRedeEnsino($form, $arDados);
		$this->setMesoRegiao($form, $arDados, $infoUsuario);
		$this->setValidatorEmail($form);
		$this->setFormacaoAcademica($form);
	}

	/**
	 * Método para inserir mesoregião no formulario
	 * @param CadastrarCursista_Form $form
	 * @param array $arDados
	 * @param array $arExtra
	 */
	public function setMesoRegiao( $form, $arDados, $arExtra ) {
		$businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

        if(
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, Zend_Auth::getInstance()->getIdentity()->credentials) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, Zend_Auth::getInstance()->getIdentity()->credentials)
        ){
            $result = $businessMesoregiao->getMesoRegiaoPorMunicipioFnde($arDados['CO_MUNICIPIO_ESCOLA']);
        }else{
            $result = $businessMesoregiao->search(array(
                'CO_MESO_REGIAO' => $arExtra['CO_MESO_REGIAO'],
                'CO_MUNICIPIO_IBGE' => $arExtra['CO_MUNICIPIO_PERFIL']
            ));
        }

        $form->getElement('NO_MESORREGIAO_ESCOLA')->setValue($result[0]['NO_MESO_REGIAO']);
        $form->getElement('CO_MESORREGIAO_ESCOLA_HIDDEN')->setValue($result[0]['CO_MESO_REGIAO']);
	}

	/**
	 * Método para inserir rede de ensino no select
	 * @author gustavo.gomes
	 * @param CadastrarCursista_Form $form
	 * @param array $arDados
	 */
	public function setRedeEnsino( $form, $arDados ) {

		if ( $arDados['CO_MUNICIPIO_ESCOLA'] != null ) {
			$codigoMunicipio = $arDados['CO_MUNICIPIO_ESCOLA'];
			$businessUsuario = new Fnde_Sice_Business_Usuario();
			$result = $businessUsuario->pesquisarRedeEnsinoPorMunicipio($codigoMunicipio);
			$form->setRedeEnsino($result, $arDados);

		}

	}

	/**
	 * Método para inserir UF Nascimento no select
	 * @author gustavo.gomes
	 * @param object $form
	 * @param array $rsEstado
	 */
	public function setUfNascimento( $form, $rsEstado ) {

		$form->setUfNascimento($rsEstado);

	}

	/**
	 * Método para inseir UF Escola no select
	 * @author
	 * @param CadastrarCursista_Form $form
	 * @param array $rsEstado
	 */
	public function setUfEscola( $form, $rsEstado ) {
		$form->setUfEscola($rsEstado);
	}

	/**
	 *
	 * Método para inserir municipio no select
	 * @author gustavo.gomes
	 * @param CadastrarCursista_Form $form
	 * @param array $arDados
	 */
	public function setMunicipio( $form, $arDados ) {
		if ( $arDados['SG_UF_NASCIMENTO'] != null ) {
			$obBusinessUF = new Fnde_Sice_Business_Uf();
			$result = $obBusinessUF->getMunicipioPorUf($arDados['SG_UF_NASCIMENTO']);
			$municipioAtual = $arDados['CO_MUNICIPIO_NASCIMENTO'];
			$form->setMunicipios($result, $municipioAtual);
		}
	}

	/**
	 * Método para inserir Municipio da Escola no Select
	 * @author gustavo.gomes
	 * @param CadastrarCursista_Form $form
	 * @param array $arExtra
	 * @param array $arDados
	 */
	public function setMunicipioEscola( $form, $arExtra, $arDados ) {
		$nuufEscola = $form->getUfEscola();

		if ( $nuufEscola != null ) {
            if(
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, Zend_Auth::getInstance()->getIdentity()->credentials) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, Zend_Auth::getInstance()->getIdentity()->credentials)
            ){
                $obBusinessUF = new Fnde_Sice_Business_Uf();
                $result = $obBusinessUF->getMunicipioCorpPorUf($nuufEscola);
            }else{
                $obBusinessMesoReg = new Fnde_Sice_Business_MesoRegiao();
                $result = $obBusinessMesoReg->getMunicipioFndePorMesoRegiao($arExtra['CO_MESORREGIAO']);
            }

            $form->setMunicipioEscola($result, $arDados);
		}
	}

	/**
	 * Método para inserir Nome da Escola no select
	 * @author gustavo.gomes
	 * @param CadastrarCursista_Form $form
	 * @param array $arDados
	 */
	public function setNomeEscola( $form, $arDados ) {
		if ( $arDados['CO_REDE_ENSINO'] != null ) {
			$businessUsuario = new Fnde_Sice_Business_Usuario();
			$result = $businessUsuario->pesquisarEscola(array(
			    "CO_REDE_ENSINO" => $arDados['CO_REDE_ENSINO'],
                "CO_MUNICIPIO_ESCOLA" => $arDados['CO_MUNICIPIO_ESCOLA']
            ));

			$form->setNomeEscola($result, $arDados);
		}
	}

	/**
	 * Método para inserir Segmento no select
	 * @author gustavo.gomes
	 * @param object $form
	 */
	public function setSegmento( $form ) {

		$rsSegmento = Fnde_Sice_Business_Componentes::getAllByTable("Segmento", array("NU_SEQ_SEGMENTO", "DS_SEGMENTO"));

		$form->setSegmento($rsSegmento);
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
	 * Metodo acessorio set de titles.
	 * @param array $arTitles
	 */
	public function setTitles( $arTitles ) {
		$this->_arTitles = $arTitles;
	}

	/**
	 * Recupera formulario de pesquisa.
	 * @param array $arDados
	 */
	public function getFormFilter( $arDados = array() ) {
		$form = new MatricularCursista_FormFilter($arDados);
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/turma/list')->setMethod('post');
		$form->populate($arDados);

		return $form;
	}

	/**
	 * Monta a tela de carregar turma.
	 */
	public function carregarTurmaAction() {

		$this->setTitle('Matricular');
		$this->setSubtitle('Filtrar');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Recupera as informacoes do usuario pelo CPF, caso ele ja esteja cadastrado.
	 */
	public function obterInformacoesPorCpfAction() {
		$oUsuBusiness = new Fnde_Sice_Business_Usuario();
		$this->_helper->layout()->disableLayout();

		$arParam = $this->_getAllParams();
		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$cpf = preg_replace('/[^0-9]/', '', $arParam['NU_CPF']);
		$cpf = trim($cpf);

		$cpfValido = Fnde_Sice_Business_Usuario::validaCPF($cpf, $form);
		$cursistaValido = $oUsuBusiness->validaCursista($cpf, $form);

		if ( $cpfValido && $cursistaValido ) {
			$oUsuBusiness->preencheDadosPorCpf($form, $arParam['NU_CPF']);
		}
		$this->view->form = $form;
		$this->render('form');
	}

	/**
	 * Renderiza Município por UF
	 *
	 * @author diego.matos
	 * @since 07/05/2012
	 */
	public function renderizaMunicipioAction() {

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Renderiza Mesoregião por Município
	 *
	 * @author diego.matos
	 * @since 07/05/2012
	 */

	public function renderizaMesorregiaoAction() {

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Cadastra os cursistas na base de dados
	 *
	 * @author diego.matos
	 * @since 08/05/2012
	 */
	public function salvarCursistaAction() {
		// Se os dados não foram enviados por post retorna para a index
		if ( !$this->getRequest()->isPost() ) {
			return $this->_forward('index');
		}
		$obBusinessUsuario = new Fnde_Sice_Business_Usuario();
		$obMatricular = new Fnde_Sice_Business_MatricularCursista();

		$this->_helper->layout()->disableLayout();
		//Recupera o objeto de formulário para validação
		$form = $this->getForm($this->_request->getParams());
		if ( !$form->isValid($_POST) ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
			$this->addInstantMessage(Fnde_Message::MSG_ERROR,
					Fnde_Sice_Business_Componentes::listarCamposComErros($form));

			$this->validaEmail($form);
			
			$this->view->form = $form;
			return $this->render('form');
		}
		//Recupera os parâmetros do request
		$arParams = $this->_request->getParams();
		try {
			$obBusinessUsuario->validaCursistaJaInserido(
				trim(preg_replace('/[^0-9]/', '', $arParams['NU_CPF'])),
                $arParams['NU_SEQ_TURMA']
            );
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->view->form = $form;
			return $this->render('form');
		}

		if ( Fnde_Sice_Business_Usuario::validaCPF($arParams['NU_CPF'], $form) ) {
			$arParamsUsuario = $this->getParamsCursista($arParams);
			$arParamsDadosEscolares = $this->getParamsDadosEscolares($arParams);
			$arParamsFormacaoAcademica = $this->getParamsFormacaoAcademica($arParams);
			$cursistaExistente = $obBusinessUsuario->getUsuarioByCpf($arParamsUsuario['NU_CPF']);

			if ( array_key_exists($arParamsUsuario['NU_CPF'], $_SESSION['rsCursista']) ) {
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, 'O CPF ' . $arParams['NU_CPF'] . ' já adicionado.');
				$this->view->form = $form;
				return $this->render('form');
			}

			try {
				$usuarioInserido = $obMatricular->preSalvarCursista($arParamsUsuario, $arParamsDadosEscolares,
						$cursistaExistente, $arParamsFormacaoAcademica);

				$cpf = trim(preg_replace('/[^0-9]/', '', $arParams['NU_CPF']));
				$arAlunosMatriculados = $_SESSION['rsCursista'];
				$arAlunosMatriculados[$cpf] = array("NO_USUARIO" => $arParams['NO_USUARIO'], "NU_CPF" => $cpf,
						"NU_SEQ_TURMA" => $arParams['NU_SEQ_TURMA'], "NU_SEQ_USUARIO_CURSISTA" => $usuarioInserido,);
				$_SESSION['rsCursista'] = $arAlunosMatriculados;

				$this->addMessage(Fnde_Message::MSG_SUCCESS, "Cursista adicionado com sucesso.");
				$this->addInstantMessage(Fnde_Message::MSG_SUCCESS, "FECHAR_POPUP");

				$this->_helper->viewRenderer->setNoRender();
				$this->_helper->layout()->disableLayout();
				$output = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
				<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"pt-BR\" lang=\"pt-BR\"><head>
				<script type=\"text/javascript\" >
				window.top.location.href = '" . $this->view->baseUrl()
						. "/index.php/secretaria/vinccursistaturma/carregar-turma/NU_SEQ_TURMA/{$arParams['NU_SEQ_TURMA']}';
				</script>";
				$this->getResponse()->setBody($output);
				return;
			} catch ( Exception $e ) {
				$this->trataException($form, $e, $cursistaExistente, $arParams);
				
				$this->view->form = $form;
				return $this->render('form');
			}
		}
		$this->view->form = $form;
		return $this->render('form');
	}
	
	/**
	 * Metodo auxiliar para tratar as execoes retornadas pelo banco.
	 * @param form $form Formulario.
	 * @param object $e Objeto Exception.
	 * @param array $cursistaExistente Dados do cursista existente.
	 * @param array $arParams Parametros enviados pelo request.
	 */
	private function trataException($form, $e, $cursistaExistente, $arParams) {
		$oTipoPerfil = new Fnde_Sice_Business_TipoPerfil();
		
		if ( strpos($e->getMessage(), 'ORA-00001', 0) ) {
			if ( strpos($e->getMessage(), 'SUSR_UK_01', 0) ) { //Validando E03
				$rPerfil = $oTipoPerfil->getTipoPerfilById($cursistaExistente['NU_SEQ_TIPO_PERFIL']);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						"O usuário, " . $cursistaExistente['NO_USUARIO'] . ", está com o perfil "
						. $rPerfil['DS_TIPO_PERFIL'] . ", portanto não poderá ser cursista.");
				$form->getElement('NU_CPF')->addError('O CPF ' . $arParams['NU_CPF'] . ' já adicionado.');
			} elseif ( strpos($e->getMessage(), 'SUSR_UK_02', 0) ) { //Validando E4
				$form->getElement('DS_EMAIL_USUARIO')->addError(
						'O e-mail informado já está vinculado a um CPF.');
			}
		} else {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}
	}
	
	/**
	 * Valida os emails informados.
	 * @param form $form Formulario.
	 */
	private function validaEmail($form) {
		$msgEmail = $form->getElement('DS_EMAIL_USUARIO')->getMessages();
		if ( count($msgEmail) > 0 && !$msgEmail['isEmpty'] ) {
			$form->getElement('DS_EMAIL_USUARIO')->addError("Informe um e-mail válido");
		}
		
		$msgEmailConfim = $form->getElement('DS_EMAIL_USUARIO_CONFIRM')->getMessages();
		if ( count($msgEmailConfim) > 0 && !$msgEmailConfim['isEmpty'] && !$msgEmailConfim['notSame'] ) {
			$form->getElement('DS_EMAIL_USUARIO_CONFIRM')->addError("Informe um e-mail válido");
		}
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
				'SG_UF_ATUACAO_PERFIL' => $arParams['SG_UF_ESCOLA_HIDDEN'],
				'CO_MUNICIPIO_PERFIL' => $municipio['CO_MUNICIPIO_IBGE'],
				'CO_MESORREGIAO' => $municipio['CO_MESO_REGIAO'],
				'NU_CPF' => trim(preg_replace('/[^0-9]/', '', $arParams['NU_CPF'])),
				'NO_USUARIO' => $arParams['NO_USUARIO'], 'CO_SEXO_USUARIO' => $arParams['CO_SEXO_USUARIO'],
				'DT_NASCIMENTO' => $arParams['DT_NASCIMENTO'], 'NO_MAE' => $arParams['NO_MAE'],
				'SG_UF_NASCIMENTO' => $arParams['SG_UF_NASCIMENTO'],
				'CO_MUNICIPIO_NASCIMENTO' => $arParams['CO_MUNICIPIO_NASCIMENTO'],
				'DS_EMAIL_USUARIO' => $arParams['DS_EMAIL_USUARIO'],
				'DS_TELEFONE_USUARIO' => trim(preg_replace('/[^0-9]/', '', $arParams['DS_TELEFONE_USUARIO'])),
				'DS_CELULAR_USUARIO' => trim(preg_replace('/[^0-9]/', '', $arParams['DS_CELULAR_USUARIO'])),
				'DT_CADASTRO' => date('d/m/Y'), 'ST_USUARIO' => 'L',);

		return $arParamsUsuario;
	}

	/**
	 * Recupera o array com informações para salvar os dados escolares do cursista
	 * @param array $arParams
	 * @return array 
	 */
	private function getParamsDadosEscolares( $arParams ) {
		$arParamsDadosEscolares = array('CO_UF_ESCOLA' => $arParams['SG_UF_ESCOLA_HIDDEN'],
				'CO_MUNICIPIO_ESCOLA' => $arParams['CO_MUNICIPIO_ESCOLA'],
				'CO_MESORREGIAO' => $arParams['CO_MESORREGIAO_ESCOLA_HIDDEN'],
				'CO_REDE_ENSINO' => $arParams['CO_REDE_ENSINO'], 'CO_ESCOLA' => $arParams['CO_ESCOLA'],
				'CO_SEGMENTO' => $arParams['CO_SEGMENTO'],);

		return $arParamsDadosEscolares;
	}

	/**
	 * Renderiza Rede de Ensino por município
	 *
	 * @author diego.matos
	 * @since 21/05/2012
	 */
	public function renderizaRedeEnsinoAction() {

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Renderiza o combo de nomes de escola por rede de ensino
	 *
	 * @author diego.matos
	 * @since 22/05/2012
	 */
	public function renderizaNomeEscolaAction() {

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);
		$this->view->form = $form;
		return $this->render('form');
	}

	/**
	 * Adiciona o validador de e-mail ao formulário
	 * @param CadastrarCursista_Form $form
	 */
	private function setValidatorEmail( $form ) {

		$validatorEmail = new Zend_Validate_EmailAddress();
		$validatorEmail->setMessages(Fnde_Sice_Business_Componentes::limpaMensagensEmailValidate());
		$optionsValidator = $validatorEmail->getOptions();
		$hstnameValidator = $optionsValidator['hostname'];
		$hstnameValidator->setMessages(Fnde_Sice_Business_Componentes::limpaMensagensEmailValidateHostName());

		$form->setValidatorEmail($validatorEmail);

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

}
