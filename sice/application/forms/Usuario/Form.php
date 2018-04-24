<?php

/**
 * Form de cadastro Usuario
 *
 * @author diego.matos
 * @since 10/04/2012
 */
class Usuario_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Usuário.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra = null ) {
		$this->setAttrib('id', 'form');
		//Adicionando elementos no formulário

		$nuSeqUsuario = $this->createElement('hidden', 'NU_SEQ_USUARIO');
		$nuSeqUsuario->setValue($arDados['NU_SEQ_USUARIO']);

		$stUsuario = $this->createElement("hidden", "ST_USUARIO");
		$stUsuario->setValue($arDados['ST_USUARIO']);

		//desabilitanto campos da vizualização
		if($arExtra['desabilitar']){
			// ABA PERFIL
			$obAbaPerfil = $this->configuraAbaPerfil($arDados,true);
			// ABA DADOS PESSOAIS
			$obAbaDadosPessoais = $this->configuraAbaDadosPessoais($arDados,true);
			// ABA DOCUMENTACAO
			$obAbaDocumentacao = $this->configuraAbaDocumentacao($arDados,true);
			// ABA LOGRADOURO
			$obAbaLogradouro = $this->configuraAbaLogradouro($arDados, true);
			// ABA FORMAÇÃO ACADEMICA
			$obAbaFormacaoAcademica = $this->configuraAbaFormacaoAcademica($arDados);
			// ABA DADOS FUNCIONAIS
			$obAbaDadosFuncionais = $this->configuraAbaDadosFuncionais($arDados);
			// ABA OUTRAS INFORMACOES
			$obAbaOutrasInformacoes = $this->configurarAbaOutrasInformacoes($arDados);
			// ABA DADOS PAGAMENTOS
			$obAbaDadosPagamento = $this->configurarAbaDadosPagamentos($arDados);
			// ABA DADOS DA ESCOLA
			$obAbaDadosEscola = $this->configurarAbaDadosEscola($arDados, true);
		} else {
			// ABA PERFIL
			$obAbaPerfil = $this->configuraAbaPerfil($arDados);
			// ABA DADOS PESSOAIS
			$obAbaDadosPessoais = $this->configuraAbaDadosPessoais($arDados);
			// ABA DOCUMENTACAO
			$obAbaDocumentacao = $this->configuraAbaDocumentacao($arDados);
			// ABA LOGRADOURO
			$obAbaLogradouro = $this->configuraAbaLogradouro($arDados);
			// ABA FORMAÇÃO ACADEMICA
			$obAbaFormacaoAcademica = $this->configuraAbaFormacaoAcademica($arDados);
			// ABA DADOS FUNCIONAIS
			$obAbaDadosFuncionais = $this->configuraAbaDadosFuncionais($arDados);
			// ABA OUTRAS INFORMACOES
			$obAbaOutrasInformacoes = $this->configurarAbaOutrasInformacoes($arDados);
			// ABA DADOS PAGAMENTOS
			$obAbaDadosPagamento = $this->configurarAbaDadosPagamentos($arDados);
			// ABA DADOS DA ESCOLA
			$obAbaDadosEscola = $this->configurarAbaDadosEscola($arDados);
		}


		// Adiciona os elementos ao formulário
		$this->addElements(array($nuSeqUsuario, $stUsuario));

		$hiddenDisplayGroup = $this->addDisplayGroup(array("NU_SEQ_USUARIO", "ST_USUARIO"), "identificadorHidden");

		if(!$arExtra['desabilitar']) {
			$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
					"onclick" => "$(this.form).find(':disabled').attr('disabled',false);", "title" => "Confirmar"));
			$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
					'onClick' => "window.location='" . $this->getView()->baseUrl()
						. "/index.php/manutencao/usuario/list/'"));

			//Adicionado Componentes no formulário
			$this->addElements(array($btConfirmar, $btCancelar));

			$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));
		}

		parent::__construct();

		$hiddenDisplayGroup->identificadorHidden->removeDecorator("fieldset");

		$obAbaPerfil->abaPerfil->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaPerfil', 'class' => 'tabContainer'));
		$obAbaDadosPessoais->abaDadosPessoais->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaDadosPessoais', 'class' => 'tabContainer'));
		$obAbaDocumentacao->abaDocumentacao->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaDocumentacao', 'class' => 'tabContainer'));
		$obAbaLogradouro->abaLogradouro->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaLogradouro', 'class' => 'tabContainer'));
		$obAbaFormacaoAcademica->abaFormacaoAcademica->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaFormacaoAcademica',
						'class' => 'tabContainer' . ( $arDados['readonly'] == 1 ? ' readonly' : '' )));
		$obAbaDadosFuncionais->abaDadosFuncionais->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaDadosFuncionais', 'class' => 'tabContainer'));
		$obAbaDadosPagamento->abaDadosPagamento->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaDadosPagamento', 'class' => 'tabContainer'));
		$obAbaDadosEscola->abaDadosEscola->addDecorator('HtmlTag',
			array('tag' => 'div', 'id' => 'abaDadosEscola', 'class' => 'tabContainer'));

		if(!$arExtra['desabilitar']) {
			$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
			$obDisplayGroup->botoes->removeDecorator('fieldset');
		}

		$obAbaFormacaoAcademica->getElement('Adicionar')->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'style' => 'text-align: right'));
		$obAbaFormacaoAcademica->htmlFormacaoAcademica->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'htmlDivFormacaoAcademica'));
		$obAbaOutrasInformacoes->abaOutrasInformacoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'abaOutrasInformacoes',
						'class' => 'tabContainer' . ( $arDados['readonly'] == 1 ? ' readonly' : '' )));
		$obAbaOutrasInformacoes->htmlOutrasInformacoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'htmlDivOutrasInformacoes'));

		return $this;
	}

	/**
	 * Monta o formulário da aba perfil
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaPerfil( $arDados , $desabilitar = false) {

		$nuSTipoPerfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL', array('label' => 'Perfil: '));
		$nuSTipoPerfil->addMultiOption(null, 'Selecione');
		$nuSTipoPerfil->setRequired(true);

		if ( $arDados['NU_SEQ_TIPO_PERFIL'] != null ) {
			$nuSTipoPerfil->setValue($arDados['NU_SEQ_TIPO_PERFIL']);
		}

		$nuufAtuacaoPerfil = $this->createElement('select', 'SG_UF_ATUACAO_PERFIL_CAD',
				array("label" => "UF de Atuação: "));
		$nuufAtuacaoPerfil->setRequired(true);
		$nuufAtuacaoPerfil->addMultiOption(null, 'Selecione');

                
		if ( $arDados['SG_UF_ATUACAO_PERFIL'] != null ) {
			$nuufAtuacaoPerfil->setValue($arDados['SG_UF_ATUACAO_PERFIL']);
		}

		$nucoMunicipioPerfil = $this->createElement('select', 'CO_MUNICIPIO_PERFIL',
				array("label" => "Município de atuação: "));
		$nucoMunicipioPerfil->setRequired(true);
		$nucoMunicipioPerfil->addMultiOption(null, 'Selecione');
           

//		if ( $arDados['SG_UF_ATUACAO_PERFIL'] != null ) {
//			$nucoMunicipioPerfil->setValue($arDados['CO_MUNICIPIO_PERFIL']);
//		}

		$nucoMesorregiao = $this->createElement('text', 'NO_MESORREGIAO_CAD',
				array("label" => "Mesorregião: ", 'size' => '50'));
		$nucoMesorregiao->setRequired(true);
		$nucoMesorregiao->setAttrib("readonly", true);

		$nucoMesorregiaoHidden = $this->createElement('hidden', 'CO_MESORREGIAO_CAD');

		$nucoRepresentacao = $this->createElement('select', 'CO_REPRESENTACAO_CAD', array("label" => "Representação: "));
		if ( $nuSTipoPerfil->getValue() == 4 ) {
			$nucoRepresentacao->setRequired(true);
		}
		$nucoRepresentacao->addMultiOptions(
				array(null => "Selecione", 1 => "Secretaria Estadual", 2 => "Secretaria Municipal",
						3 => "Universidades Públicas", 4 => "UNDIME"));

		$nucoRepresentacao->setValue($arDados['CO_REPRESENTACAO']);
		$nucoRepresentacao->setRequired(true);

		$this->addElements(
				array($nuSTipoPerfil, $nuufAtuacaoPerfil, $nucoMunicipioPerfil, $nucoMesorregiao, $nucoRepresentacao,
						$nucoMesorregiaoHidden));

		$obAbaPerfil = $this->addDisplayGroup(
				array('NU_SEQ_TIPO_PERFIL', 'SG_UF_ATUACAO_PERFIL_CAD', 'CO_MUNICIPIO_PERFIL', 'CO_MESORREGIAO_CAD',
						'NO_MESORREGIAO_CAD', 'CO_REPRESENTACAO_CAD',), 'abaPerfil', array("legend" => "Perfil"));
		return $obAbaPerfil;
	}

	/**
	 * Monta formulário da aba de dados pessoais
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaDadosPessoais( $arDados , $desabiliar = false) {

		$nunuCpf = $this->createElement('text', 'NU_CPF',
				array("label" => "CPF: ", 'value' => $arDados['NU_CPF'], "class" => "cpf"));
		$nunuCpf->setRequired(true);

		$nunoUsuario = $this->createElement('text', 'NO_USUARIO',
				array("label" => "Nome Completo: ", 'value' => $arDados['NO_USUARIO'], 'disabled' => 'disabled',
						"maxlength" => "70"));
		$nunoUsuario->setAttrib("readonly", true);
		$nunoUsuario->setAttrib("size", 100);

		$validatorNomeIdentical = new Zend_Validate_Identical();
		$validatorNomeIdentical->setMessages(
				array('notSame' => 'O nome para confirmação informado não confere com o da Receita Federal.'));
		$validatorNomeIdentical->setToken("NO_USUARIO");

		// CONFIRMAÇÃO DO NOME
		$nunoUsuarioConfirm = $this->createElement('text', 'NO_USUARIO_CONFIRM',
				array("label" => "Confirmar Nome Completo: ", 'value' => $arDados['NO_USUARIO'],
						"onfocus" => "javascript:window.clipboardData.clearData()", "maxlength" => "70"));
		$nunoUsuarioConfirm->setRequired(true);
		$nunoUsuarioConfirm->setAttrib("size", 100);
		$nunoUsuarioConfirm->addValidator($validatorNomeIdentical);

		$nucoEstadoCivil = $this->createElement('select', 'CO_ESTADO_CIVIL',
				array("label" => "Estado Civil: ", 'value' => $arDados['CO_ESTADO_CIVIL']));
		$nucoEstadoCivil->setRequired(true);
		$nucoEstadoCivil->addMultiOption(null, 'Selecione');
		$nucoEstadoCivil->addMultiOption(1, 'Casado');
		$nucoEstadoCivil->addMultiOption(2, 'Divorciado');
		$nucoEstadoCivil->addMultiOption(3, 'Solteiro');
		$nucoEstadoCivil->addMultiOption(4, 'Viuvo');

		$nucoSexoUsuario = $this->createElement('select', 'CO_SEXO_USUARIO',
				array("label" => "Sexo: ", 'value' => $arDados['CO_SEXO_USUARIO']));
		$nucoSexoUsuario->setRequired(true);
		$nucoSexoUsuario->addMultiOption(null, 'Selecione');
		$nucoSexoUsuario->addMultiOption(2, 'Feminino');
		$nucoSexoUsuario->addMultiOption(1, 'Masculino');
		$nucoSexoUsuario->setAttrib("readonly", true);

		$nudtNascimento = $this->createElement('text', 'DT_NASCIMENTO',
				array("label" => "Data de Nascimento: ", 'value' => $arDados['DT_NASCIMENTO'], 'disabled' => 'disabled'));
		$nudtNascimento->setRequired(true);
		$nudtNascimento->setAttrib("readonly", true);

		$nuufAtuacaoPerfil = $this->createElement("select", 'SG_UF_NASCIMENTO',
				array('name' => 'SG_UF_NASCIMENTO', 'label' => 'UF Nascimento: ', 'value' => $arDados['SG_UF_NASCIMENTO']));
		$nuufAtuacaoPerfil->addMultiOption(null, 'Selecione');
		$nuufAtuacaoPerfil->setAttrib("readonly", true);

		$nucoMunicipioDadosPessoais = $this->createElement("select", 'CO_MUNICIPIO_NASCIMENTO',
				array('name' => 'CO_MUNICIPIO_NASCIMENTO', 'label' => 'Município Nascimento: ',
						'value' => $arDados['CO_MUNICIPIO_NASCIMENTO']));
		$nucoMunicipioDadosPessoais->addMultiOption(null, 'Selecione');

		$nutpEscolaridade = $this->createElement('select', 'NU_SEQ_FORMACAO_ACADEMICA',
			array('name' => 'NU_SEQ_FORMACAO_ACADEMICA', 'label' => 'Escolaridade: ', 'value' => $arDados['NU_SEQ_FORMACAO_ACADEMICA']));
		$nutpEscolaridade->setRequired(true);
		$nutpEscolaridade->addMultiOption(null, "Selecione");

		$nutpInstituicao = $this->createElement('select', 'TP_INSTITUICAO', array("label" => "Tipo de Instituição: ", 'value' => $arDados['TP_INSTITUICAO']));
		$nutpInstituicao->setRequired(true);
		$nutpInstituicao->addMultiOption(null, 'Selecione');
		$nutpInstituicao->addMultiOption("1", "Pública");
		$nutpInstituicao->addMultiOption("2", "Privada");


		$nunoMae = $this->createElement('text', 'NO_MAE',
				array("label" => "Nome da Mãe: ", 'value' => $arDados['NO_MAE'], 'disabled' => 'disabled'));
		$nunoMae->setAttrib("readonly", true);
		$nunoMae->setAttrib("size", 100);

		if($desabiliar){
			$nunuCpf->setAttrib('disabled', 'disabled');
			$nunoUsuario->setAttrib('disabled', 'disabled');
			$nunoUsuarioConfirm->setAttrib('disabled', 'disabled');
			$nucoEstadoCivil->setAttrib('disabled', 'disabled');
			$nucoSexoUsuario->setAttrib('disabled', 'disabled');
			$nudtNascimento->setAttrib('disabled', 'disabled');
			$nuufAtuacaoPerfil->setAttrib('disabled', 'disabled');
			$nucoMunicipioDadosPessoais->setAttrib('disabled', 'disabled');
			$nunoMae->setAttrib('disabled', 'disabled');
			$nutpEscolaridade->setAttrib('disabled', 'disabled');
			$nutpInstituicao->setAttrib('disabled', 'disabled');
		}

		$this->addElements(
				array($nunuCpf, $nunoUsuario, $nunoUsuarioConfirm, $nucoEstadoCivil, $nucoSexoUsuario, $nudtNascimento,
						$nuufAtuacaoPerfil, $nucoMunicipioDadosPessoais, $nunoMae, $nutpEscolaridade, $nutpInstituicao));

		$obAbaDadosPessoais = $this->addDisplayGroup(
				array('NU_CPF', 'NO_USUARIO', 'NO_USUARIO_CONFIRM', 'CO_ESTADO_CIVIL', 'CO_SEXO_USUARIO',
						'DT_NASCIMENTO', 'SG_UF_NASCIMENTO', 'CO_MUNICIPIO_NASCIMENTO', 'NO_MAE',
					'NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO',), 'abaDadosPessoais',
				array("legend" => "Dados Pessoais"));
		return $obAbaDadosPessoais;
	}

	/**
	 * Monta formulário da aba de documentação
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaDocumentacao( $arDados ) {

		$campoNumerico = new Zend_Validate_Digits();
		$campoNumerico->setMessage("O campo deve conter somente números");

		$nuidentidadeDocumentacao = $this->createElement('text', 'NU_IDENTIDADE',
				array("label" => "Identidade: ", 'value' => $arDados['NU_IDENTIDADE'], "class" => 'inteiro',
						'maxlength' => '14'));
		$nuidentidadeDocumentacao->setRequired(true);
		$nuidentidadeDocumentacao->addValidator($campoNumerico);

		$nudtEmissaoDocumentacao = $this->createElement('text', 'DT_EMISSAO_DOCUMENTACAO',
				array("label" => "Data de expedição: ", 'value' => $arDados['DT_EMISSAO_DOCUMENTACAO'],
						"class" => "date dp-applied"));
		$nudtEmissaoDocumentacao->setRequired(true);

		$nucoOrgaoEmissor = $this->createElement('text', 'CO_ORGAO_EMISSOR',
				array("label" => "Órgão Emissor: ", 'value' => $arDados['CO_ORGAO_EMISSOR'], 'maxlength' => '10'));
		$nucoOrgaoEmissor->setRequired(true);

		$nuufEmissaoDoc = $this->createElement('select', 'SG_UF_EMISSAO_DOC',
				array("label" => "UF: ", 'value' => $arDados['SG_UF_EMISSAO_DOC']));
		$nuufEmissaoDoc->setRequired(true);
		$nuufEmissaoDoc->addMultiOption(null, 'Selecione');

		$this->addElements(
				array($nuidentidadeDocumentacao, $nudtEmissaoDocumentacao, $nucoOrgaoEmissor, $nuufEmissaoDoc));

		$obAbaDocumentacao = $this->addDisplayGroup(
				array('NU_IDENTIDADE', 'DT_EMISSAO_DOCUMENTACAO', 'CO_ORGAO_EMISSOR', 'SG_UF_EMISSAO_DOC',),
				'abaDocumentacao', array("legend" => "Documentação"));

		return $obAbaDocumentacao;
	}

	/**
	 * Monta formulário da aba de logradouro
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaLogradouro( $arDados, $desabilitar = false ) {

		$nutpEndereco = $this->createElement('select', 'TP_ENDERECO',
				array("label" => "Tipo de endereço: ", 'value' => $arDados['TP_ENDERECO']));
		$nutpEndereco->addMultiOption(null, 'Selecione');
		$nutpEndereco->addMultiOption('P', 'Profissional');
		$nutpEndereco->addMultiOption('R', 'Residencial');
		$nutpEndereco->setRequired(true);

		$nucepUsuario = $this->createElement('text', 'NU_CEP',
				array("label" => "CEP: ", "class" => "cep", 'value' => $arDados['NU_CEP']));
		$nucepUsuario->setRequired(true);

		$nudsEndereco = $this->createElement('text', 'DS_ENDERECO',
				array("label" => "Endereço: ", 'value' => $arDados['DS_ENDERECO'], 'maxlength' => '50'));
		$nudsEndereco->setRequired(true);

		$nudsComplementoEndereco = $this->createElement('text', 'DS_COMPLEMENTO_ENDERECO',
				array("label" => "Complemento: ", 'value' => $arDados['DS_COMPLEMENTO_ENDERECO'], 'maxlength' => '25'));

		$nudsBairroEndereco = $this->createElement('text', 'DS_BAIRRO_ENDERECO',
				array("label" => "Bairro: ", 'value' => $arDados['DS_BAIRRO_ENDERECO'], 'maxlength' => '25'));
		$nudsBairroEndereco->setRequired(true);

		$nucoUfEndereco = $this->createElement('select', 'CO_UF_ENDERECO', array("label" => "UF: "));
		$nucoUfEndereco->setRequired(true);
		$nucoUfEndereco->addMultiOption(null, 'Selecione');
		$nucoUfEndereco->setValue($arDados['CO_UF_ENDERECO']);

		$nucoMunicipioEndereco = $this->createElement('select', 'CO_MUNICIPIO_ENDERECO',
				array("label" => "Município: "));
		$nucoMunicipioEndereco->setRequired(true);
		$nucoMunicipioEndereco->addMultiOption(null, 'Selecione');

		if ( $arDados['CO_UF_ENDERECO'] != null ) {
			$nucoMunicipioEndereco->setValue($arDados['CO_MUNICIPIO_ENDERECO']);
		}

		$validatorTelefone = new Zend_Validate_Regex('/^[(][0-9]{2}[)] ([0-9]{4}|[0-9]{5})[-][0-9]{4}$/');
		$validatorTelefone->setMessages(array("regexNotMatch" => "Número do telefone inválido!"));

		$nudsTelefoneUsuario = $this->createElement('text', 'DS_TELEFONE_USUARIO',
				array("label" => "Telefone: ", 'value' => $arDados['DS_TELEFONE_USUARIO'], 'maxlength' => '13'));
		$nudsTelefoneUsuario->setRequired(true);
		$nudsTelefoneUsuario->addValidator($validatorTelefone);

		$nudsCelularUsuario = $this->createElement('text', 'DS_CELULAR_USUARIO',
				array("label" => "Celular: ", 'value' => $arDados['DS_CELULAR_USUARIO'], 'maxlength' => '14'));
		$nudsCelularUsuario->addValidator($validatorTelefone);

		$validatorEmailIdentical = new Zend_Validate_Identical();
		$validatorEmailIdentical->setMessages(
				array('notSame' => 'Os e-mails estão diferentes. Favor conferir!', 'missingToken' => ''));
		$validatorEmailIdentical->setToken("DS_EMAIL_USUARIO");

		$nudsEmailUsuario = $this->createElement('text', 'DS_EMAIL_USUARIO',
				array("label" => "E-mail: ", 'value' => $arDados['DS_EMAIL_USUARIO'], 'maxlength' => '60'));
		$nudsEmailUsuario->setRequired(true);

		// Confirmação email
		$nudsEmailUsuarioConfirma = $this->createElement('text', 'DS_EMAIL_USUARIO_CONFIRM',
				array("label" => "Confirmar E-mail: ", 'value' => $arDados['DS_EMAIL_USUARIO'], 'maxlength' => '60',
						"onfocus" => "javascript:window.clipboardData.clearData()"));
		$nudsEmailUsuarioConfirma->setRequired(true);
		$nudsEmailUsuarioConfirma->addValidator($validatorEmailIdentical);

		if($desabilitar){
			$nutpEndereco->setAttrib('disabled', 'disabled');
			$nucepUsuario->setAttrib('disabled', 'disabled');
			$nudsEndereco->setAttrib('disabled', 'disabled');
			$nudsComplementoEndereco->setAttrib('disabled', 'disabled');
			$nudsBairroEndereco->setAttrib('disabled', 'disabled');
			$nucoUfEndereco->setAttrib('disabled', 'disabled');
			$nucoMunicipioEndereco->setAttrib('disabled', 'disabled');
			$nudsTelefoneUsuario->setAttrib('disabled', 'disabled');
			$nudsCelularUsuario->setAttrib('disabled', 'disabled');
			$nudsEmailUsuario->setAttrib('disabled', 'disabled');
			$nudsEmailUsuarioConfirma->setAttrib('disabled', 'disabled');
		}
		$this->addElements(
				array($nutpEndereco, $nucepUsuario, $nudsEndereco, $nudsComplementoEndereco, $nudsBairroEndereco,
						$nucoUfEndereco, $nucoMunicipioEndereco, $nudsTelefoneUsuario, $nudsCelularUsuario,
						$nudsEmailUsuario, $nudsEmailUsuarioConfirma));

		$obAbaLogradouro = $this->addDisplayGroup(
				array('TP_ENDERECO', 'NU_CEP', 'DS_ENDERECO', 'DS_COMPLEMENTO_ENDERECO', 'DS_BAIRRO_ENDERECO',
						'CO_UF_ENDERECO', 'CO_MUNICIPIO_ENDERECO', 'DS_TELEFONE_USUARIO', 'DS_CELULAR_USUARIO',
						'DS_EMAIL_USUARIO', 'DS_EMAIL_USUARIO_CONFIRM',), 'abaLogradouro',
				array("legend" => "Logradouro"));

		return $obAbaLogradouro;
	}

	/**
	 * Monta formulário da aba de formação acadêmica
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaFormacaoAcademica( $arDados ) {

		$htmlDivFormacaoAcademica = new Html('htmlFormacaoAcademica', array("class" => "readonly"));

		if ( $arDados['readonly'] == 1 ) {
			$htmlDivFormacaoAcademica->addDecorator('HtmlTag',
					array('tag' => 'div', 'id' => 'htmlDivFormacaoAcademica'));
		} else {
			$htmlDivFormacaoAcademica->addDecorator('HtmlTag',
					array('tag' => 'div', 'id' => 'htmlDivFormacaoAcademica'));
		}

		$btAddFormacao = $this->createElement('button', 'Adicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btnAdicionar",
						"title" => "Adicionar",
						"href" => $this->getView()->baseUrl() . "/index.php/manutencao/formacaoacademica/form/"));

		$this->addElements(array($htmlDivFormacaoAcademica, $btAddFormacao));

		$obAbaFormacaoAcademica = $this->addDisplayGroup(array('htmlFormacaoAcademica', 'Adicionar'),
				'abaFormacaoAcademica', array("legend" => "Formação Acadêmica"));

		return $obAbaFormacaoAcademica;

	}

	/**
	 * Monta formulário da aba de dados funcionais
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configuraAbaDadosFuncionais( $arDados ) {
		$nucoOcupacaoUsuario = $this->createElement('select', 'CO_OCUPACAO_USUARIO', array("label" => "Ocupação: "));
		$nucoOcupacaoUsuario->setRequired(true);
		$nucoOcupacaoUsuario->addMultiOption(null, 'Selecione');
		$nucoOcupacaoUsuario->setValue($arDados['CO_OCUPACAO_USUARIO']);

		$nudsOcupacaoAlternativa = $this->createElement('text', 'DS_OCUPACAO_ALTERNATIVA',
				array("label" => "Qual? ", 'value' => $arDados['DS_OCUPACAO_ALTERNATIVA'], "maxlength" => "60"));
		$nudsOcupacaoAlternativa->setRequired(true);

		$nucoServidorPublico = $this->createElement('radio', 'CO_SERVIDOR_PUBLICO',
				array("class" => "agrupador inLine", "label" => "Servidor Público: "));
		$nucoServidorPublico->setRequired(true);
		$nucoServidorPublico->addMultiOption("1", "Estadual");
		$nucoServidorPublico->addMultiOption("3", "Federal");
		$nucoServidorPublico->addMultiOption("2", "Municipal");

		$nucoServidorPublico->setValue($arDados['CO_SERVIDOR_PUBLICO']);

		$nudsCargoFuncao = $this->createElement('text', 'DS_CARGO_FUNCAO',
				array("label" => "Cargo/Função: ", 'value' => $arDados['DS_CARGO_FUNCAO'], 'maxlength' => '80'));
		$nudsCargoFuncao->setRequired(true);

		$nucoLocalLotacao = $this->createElement('select', 'CO_LOCAL_LOTACAO', array("label" => "Lugar de Lotação: "));
		$nucoLocalLotacao->setRequired(true);
		$nucoLocalLotacao->addMultiOption(null, 'Selecione');
		$nucoLocalLotacao->setValue($arDados['CO_LOCAL_LOTACAO']);

		$nudsLocalLotacaoAlternativa = $this->createElement('text', 'DS_LOCAL_LOTACAO_ALTERNATIVA',
				array("label" => "Qual? ", 'value' => $arDados['DS_LOCAL_LOTACAO_ALTERNATIVA'], "maxlength" => "60"));
		$nudsLocalLotacaoAlternativa->setRequired(true);

		$this->addElements(
				array($nucoOcupacaoUsuario, $nudsOcupacaoAlternativa, $nucoServidorPublico, $nudsCargoFuncao,
						$nucoLocalLotacao, $nudsLocalLotacaoAlternativa));

		$obAbaDadosFuncionais = $this->addDisplayGroup(
				array('CO_OCUPACAO_USUARIO', 'DS_OCUPACAO_ALTERNATIVA', 'CO_SERVIDOR_PUBLICO', 'DS_CARGO_FUNCAO',
						'CO_LOCAL_LOTACAO', 'DS_LOCAL_LOTACAO_ALTERNATIVA'), 'abaDadosFuncionais',
				array("legend" => "Dados Funcionais"));

		return $obAbaDadosFuncionais;
	}

	/**
	 * Monta formulário da aba de outras informações
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configurarAbaOutrasInformacoes( $arDados ) {
		$htmlOutrasInformacoes = new Html('htmlOutrasInformacoes');
		$htmlOutrasInformacoes->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'htmlDivOutrasInformacoes'));
		$this->addElements(array($htmlOutrasInformacoes));
		$obAbaOutrasInformacoes = $this->addDisplayGroup(array('htmlOutrasInformacoes',), 'abaOutrasInformacoes',
				array("legend" => "Outras Atividades"));
		return $obAbaOutrasInformacoes;
	}

	/**
	 * Monta formulário da aba de dados para pagamentos
	 * @author poliane.silva
	 * @since 14/11/2012
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configurarAbaDadosPagamentos( $arDados ) {

		$nucoDistanciaPagamento = $this->createElement('select', 'CO_DISTANCIA_PAGAMENTO',
				array("label" => "Distância: "));
		$nucoDistanciaPagamento->setRequired(true);
		$nucoDistanciaPagamento->addMultiOption(null, 'Selecione');
		$nucoDistanciaPagamento->addMultiOption(50, '0 a 100 KM');
		$nucoDistanciaPagamento->addMultiOption(150, '101 a 200 KM');
		$nucoDistanciaPagamento->addMultiOption(250, '201 a 300 KM');
		$nucoDistanciaPagamento->addMultiOption(350, '301 a 400 KM');
		$nucoDistanciaPagamento->addMultiOption(450, '401 a 500 KM');

		$nuufEstadoPagamento = $this->createElement('select', 'SG_UF_PAGAMENTO',
				array("label" => "Estado(s) Localizado(s): "));
		$nuufEstadoPagamento->setRequired(true);
		$nuufEstadoPagamento->addMultiOption(null, 'Selecione');

		$nucoMunicipioPagamento = $this->createElement('select', 'CO_MUNICIPIO_PAGAMENTO',
				array("label" => "Municipío(s) localizado(s):"));
		$nucoMunicipioPagamento->setRequired(true);
		$nucoMunicipioPagamento->addMultiOption(null, 'Selecione');

		$nucoAgenciaPagamento = $this->createElement('select', 'CO_AGENCIA_PAGAMENTO', array("label" => "Agências:"));
		$nucoAgenciaPagamento->setRequired(true);
		$nucoAgenciaPagamento->addMultiOption(null, 'Selecione');

		$arAgencia = array();
		$agenciaBB = new Fnde_Model_AgenciaBB();
                
		if ( $arDados['CO_MUNICIPIO_PERFIL'] != null && $arDados['CO_DISTANCIA_PAGAMENTO'] != null ) {
                    
			$arAgencia = $agenciaBB->getMunicipio($arDados['CO_MUNICIPIO_PERFIL'], $arDados['CO_DISTANCIA_PAGAMENTO']);
		}
		if ( empty($arAgencia[0]) ) {
			$agencia[0] = $arAgencia;
			$arAgencia = array();
			$arAgencia = $agencia;
		}

		// 		die(var_dump($arAgencia));

		//Carregando informações de UFs e Municípios
		foreach ( $arAgencia as $agencia ) {
			$nuufEstadoPagamento->addMultiOption($agencia['sg_uf'], $agencia['sg_uf']);
			$nucoMunicipioPagamento->addMultiOption($agencia['co_municipio'], $agencia['no_municipio']);
		}

		$nuufEstadoPagamento->setValue($arDados['SG_UF_PAGAMENTO']);
		$nucoMunicipioPagamento->setValue($arDados['CO_MUNICIPIO_PAGAMENTO']);

		//Carregando informações de agências
		if ( $arDados['CO_MUNICIPIO_PAGAMENTO'] && $arDados['CO_DISTANCIA_PAGAMENTO'] ) {

			$arAgencia = $agenciaBB->getMunicipio($arDados['CO_MUNICIPIO_PAGAMENTO'],
					$arDados['CO_DISTANCIA_PAGAMENTO']);

			if ( empty($arAgencia[0]) ) {
				$agencia[0] = $arAgencia;
				$arAgencia = array();
				$arAgencia = $agencia;
			}

			foreach ( $arAgencia as $agencia ) {
				$nucoAgenciaPagamento->addMultiOption($agencia['co_agencia'], $agencia['no_agencia']);
			}
			$nucoAgenciaPagamento->setValue($arDados['CO_AGENCIA_PAGAMENTO']);
		}

		$this->addElements(
				array($nucoDistanciaPagamento, $nuufEstadoPagamento, $nucoMunicipioPagamento, $nucoAgenciaPagamento));

		$obAbaDadosPagamento = $this->addDisplayGroup(
				array('CO_DISTANCIA_PAGAMENTO', 'SG_UF_PAGAMENTO', 'CO_MUNICIPIO_PAGAMENTO', 'CO_AGENCIA_PAGAMENTO'),
				'abaDadosPagamento', array("legend" => "Informações para pagamento de bolsas"));

		// 		$obAbaDadosPagamento->getElement("CO_DISTANCIA_PAGAMENTO")->addDecorator('HtmlTag', array('div' => 'label', "id" => "divDistanciaPagamento" , "style"=>"padding-right:240px !important; margin:10px;"));
		// 		$obAbaDadosPagamento->getElement("SG_UF_PAGAMENTO")->addDecorator('HtmlTag', array('div' => 'label', "id" => "divUfPagamento" , "style"=>"padding-rigth:240px !important; margin:10px;"));

		return $obAbaDadosPagamento;
	}

	/**
	 * Monta formulário da aba de dados pa escola
	 * @author pedro.correia
	 * @since 03/05/2016
	 * @param array $arDados
	 * @return Zend_Form
	 */
	private function configurarAbaDadosEscola($arDados, $desabilitar = false)
	{

		$nuufEscola = $this->createElement('select', 'SG_UF_ESCOLA',
			array('name' => 'SG_UF_ESCOLA', 'label' => 'UF: ', $arDados['SG_UF_ESCOLA']))->addMultiOption(null,
			"Selecione");

		// MUNICIPIO DA ESCOLA
		$nucoMunicipioEscola = $this->createElement('select', 'CO_MUNICIPIO_ESCOLA',
			array('name' => 'CO_MUNICIPIO_ESCOLA', 'label' => 'Município: ', $arDados['CO_MUNICIPIO_ESCOLA']))->addMultiOption(
			null, "Selecione");

		// MESORREGIÃO DA ESCOLA
		$nucoMesorregiaoEscola = $this->createElement('select', 'NO_MESORREGIAO_ESCOLA',
			array("label" => "Mesorregião: "));
		$nucoMesorregiaoEscolaHidden = $this->createElement('hidden', 'CO_MESORREGIAO_ESCOLA_HIDDEN');

		// REDE DE ENSINO
		$nucoRedeEnsino = $this->createElement('select', 'CO_REDE_ENSINO',
			array('name' => 'CO_REDE_ENSINO', 'label' => 'Rede de ensino: ', $arDados['CO_REDE_ENSINO']))->addMultiOption(
			null, "Selecione");

		// NOME DA ESCOLA
		$nucoNomeEscola = $this->createElement('select', 'CO_ESCOLA',
			array('name' => 'CO_ESCOLA', 'label' => 'Nome da Escola: ', $arDados['CO_ESCOLA'],
				"style" => "width:200px;"))->addMultiOption(null, "Selecione")->setRequired(true);

		// SEGMENTO
		$nucoSegmento = $this->createElement('select', 'CO_SEGMENTO',
			array('name' => 'CO_SEGMENTO', 'label' => 'Segmento: ', $arDados['CO_SEGMENTO']))->addMultiOption(
			null, "Selecione");

		if($desabilitar){
			$nucoRedeEnsino->setAttrib('disabled', 'disabled');
			$nucoMunicipioEscola->setAttrib('disabled', 'disabled');
			$nuufEscola->setAttrib('disabled', 'disabled');
			$nucoMesorregiaoEscola->setAttrib('disabled', 'disabled');
			$nucoMesorregiaoEscolaHidden->setAttrib('disabled', 'disabled');
			$nucoNomeEscola->setAttrib('disabled', 'disabled');
			$nucoNomeEscola->setRequired(false);
			$nucoSegmento->setAttrib('disabled', 'disabled');
		}
		// Adiciona os elementos ao formulário
		$this->addElements(
			array($nucoRedeEnsino, $nucoMunicipioEscola, $nuufEscola, $nucoMesorregiaoEscola,
				$nucoMesorregiaoEscolaHidden, $nucoNomeEscola, $nucoSegmento,));

		$obAbaDadosEscola = $this->addDisplayGroup(
			array('SG_UF_ESCOLA', 'SG_UF_ESCOLA_HIDDEN', 'CO_MUNICIPIO_ESCOLA', 'NO_MESORREGIAO_ESCOLA', 'CO_REDE_ENSINO',
				'CO_ESCOLA', 'CO_SEGMENTO',), 'abaDadosEscola',
			array("legend" => "Dados da Escola"));

		return $obAbaDadosEscola;

	}

	/**
	 * Adiciona as opções do combo de Perfil da aba Perfil
	 * @author poliane.silva
	 * @since 14/11/2012
	 */
	public function setComboPerfilAbaPerfil( $arPerfilAbaPerfil ) {
		$this->getElement('NU_SEQ_TIPO_PERFIL')->setMultiOptions($arPerfilAbaPerfil);
	}

	/**
	 * Adiciona as opções do combo de UF da aba Perfil
	 * @author poliane.silva
	 * @since 14/11/2012
	 */
	public function setComboUfAbaPerfil( $arUfAbaPerfil ) {
		$this->getElement('SG_UF_ATUACAO_PERFIL_CAD')->setMultiOptions($arUfAbaPerfil);
	}

	/**
	 * Adiciona as opções do combo de Município da aba Perfil
	 * @param array $arMunicipioAbaPerfil
	 */
	public function setComboMunicipioAbaPerfil( $arMunicipioAbaPerfil ) {
		$this->getElement('CO_MUNICIPIO_PERFIL')->setMultiOptions($arMunicipioAbaPerfil);
	}

	/**
	 * Adiciona as opções do campo Mesorregião da aba Perfil
	 * @param string $noMesorregiao
	 * @param string $coMesorregiao
	 */
	public function setMesorregiaoAbaPerfil( $noMesorregiao, $coMesorregiao ) {
		$this->getElement('NO_MESORREGIAO_CAD')->setValue($noMesorregiao);
		$this->getElement('CO_MESORREGIAO_CAD')->setValue($coMesorregiao);
	}

	/**
	 * Adiciona as opções do combo de UF da aba Dados Pessoais
	 * @param array $arUfAbaDadosPessoais
	 */
	public function setUfAbaDadosPessoais( $arUfAbaDadosPessoais ) {
		$this->getElement('SG_UF_NASCIMENTO')->addMultiOptions($arUfAbaDadosPessoais);
	}

	/**
	 * Adiciona as opções do combo de Município da aba Dados Pessoais
	 * @param array $arMunicipioAbaDadosPessoais
	 */
	public function setMunicipioAbaDadosPessoais( $arMunicipioAbaDadosPessoais ) {
		$this->getElement('CO_MUNICIPIO_NASCIMENTO')->setMultiOptions($arMunicipioAbaDadosPessoais);
	}

	/**
	 * Adiciona as opções do combo de UF da aba Documentação
	 * @param array $arUfAbaDadosDocumentacao
	 */
	public function setUfAbaDocumentacao( $arUfAbaDadosDocumentacao ) {
		$this->getElement('SG_UF_EMISSAO_DOC')->setMultiOptions($arUfAbaDadosDocumentacao);
	}

	/**
	 * Adiciona as opções do combo de UF da aba Logradouro
	 * @param array $arUfAbaLogradouro
	 */
	public function setUfAbaLogradouro( $arUfAbaLogradouro ) {
		$this->getElement('CO_UF_ENDERECO')->setMultiOptions($arUfAbaLogradouro);
	}

	/**
	 * Adiciona as opções do combo de Municipio da aba Logradouro
	 * @param array $arMunicipioAbaLogradouro
	 */
	public function setMunicipioAbaLogradouro( $arMunicipioAbaLogradouro ) {
		$this->getElement('CO_MUNICIPIO_ENDERECO')->setMultiOptions($arMunicipioAbaLogradouro);
	}

	/**
	 * Adiciona a validação de e-mail aos campos do formulario
	 * @param validate $validatorEmail
	 */
	public function setValidatorEmail( $validatorEmail ) {
		$this->getElement('DS_EMAIL_USUARIO')->addValidator($validatorEmail);
		$this->getElement('DS_EMAIL_USUARIO_CONFIRM')->addValidator($validatorEmail);
	}

	/**
	 * Adiciona as opções do combo de Ocupação da aba Dados Funcionais
	 * @param array $arOcupacao
	 */
	public function setOcupacaoAbaDadosFuncionais( $arOcupacao ) {
		$this->getElement('CO_OCUPACAO_USUARIO')->setMultiOptions($arOcupacao);
	}

	/**
	 * Adiciona as opções do combo de Local Lotação da aba de Dados Funcionais
	 * @param form $arLocalLotacao
	 */
	public function setLocalLotacaoAbaDadosFuncionais( $arLocalLotacao ) {
		$this->getElement('CO_LOCAL_LOTACAO')->setMultiOptions($arLocalLotacao);
	}

	public function setUfAbaDadosEscola($arUfAbaDadosEscola)
	{
		$this->getElement('SG_UF_ESCOLA')->setMultiOptions($arUfAbaDadosEscola);
	}

	public function setMunicipioAbaEscola($arMunicipioAbaEscola)
	{
		$this->getElement('CO_MUNICIPIO_ESCOLA')->setMultiOptions($arMunicipioAbaEscola);
	}

	public function setMesoregiaoAbaEscola($arMesoregiaoAbaEscola)
	{
		$element = $this->getElement('NO_MESORREGIAO_ESCOLA');
		foreach($arMesoregiaoAbaEscola as $row){
			$element->addMultiOption($row['CO_MESO_REGIAO'], $row['NO_MESO_REGIAO']);
		}
	}

	public function setRedeEnsinoAbaEscola($arRedeEnsinoAbaEscola){
		$this->getElement('CO_REDE_ENSINO')->setMultiOptions($arRedeEnsinoAbaEscola);
	}

	public function setNomeEscolaAbaEscola($arNomeEscolaAbaEscola){
		$this->getElement('CO_ESCOLA')->setMultiOptions($arNomeEscolaAbaEscola);
	}

	public function setSegmentoAbaEscola($arSegmentoAbaEscola){
		$this->getElement('CO_SEGMENTO')->setMultiOptions($arSegmentoAbaEscola);
	}

	/**
	 * Método para inserir valores em NU_SEQ_FORMACAO_ACADEMICA
	 * @author pedro.correia
	 * @param array $rsFormacaoAcademica
	 */
	public function setFormacaoAcademica( $rsFormacaoAcademica ) {

		$this->getElement('NU_SEQ_FORMACAO_ACADEMICA')->addMultiOptions($rsFormacaoAcademica);

	}

}
