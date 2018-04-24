<?php

/**
 * Form de cadastro Turma
 *
 * @author diego.matos
 * @since 25/04/2012
 */
class CadastrarCursista_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Cadastro de Cursistas.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 03/05/2012
	 */
	public function __construct( $arDados, $arExtra = null ) {
		//DADOS CURSISTA
		$this->addElementsDadosCursista($arDados, $arExtra);
		$this->addDisplayGroup(
				array('NU_SEQ_USUARIO', 'NU_SEQ_TURMA', 'CO_SEXO_USUARIO', 'NU_CPF', 'NO_USUARIO',
						'NO_USUARIO_CONFIRM', 'DS_SEXO_USUARIO', 'DT_NASCIMENTO', 'NO_MAE', 'SG_UF_NASCIMENTO',
						'CO_MUNICIPIO_NASCIMENTO', 'DS_EMAIL_USUARIO', 'DS_EMAIL_USUARIO_CONFIRM',
						'DS_TELEFONE_USUARIO', 'DS_CELULAR_USUARIO','NU_SEQ_FORMACAO_ACADEMICA', 'TP_INSTITUICAO',), 'dadosPessoais',
				array("legend" => "Dados Pessoais"));

		//DADOS DA ESCOLA
		$this->addElementsDadosEscolares($arDados, $arExtra);
		$this->addDisplayGroup(
				array('SG_UF_ESCOLA', 'SG_UF_ESCOLA_HIDDEN', 'CO_MUNICIPIO_ESCOLA', 'NO_MESORREGIAO_ESCOLA',
						'CO_MESORREGIAO_ESCOLA_HIDDEN', 'CO_REDE_ENSINO', 'CO_ESCOLA', 'CO_SEGMENTO',), 'dadosEscola',
				array("legend" => "Dados da Escola"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						"title" => "Confirmar", "onclick" => "$(this.form).find(':disabled').attr('disabled',false);"));
		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.top.location.href='" . $this->getView()->baseUrl()
								. "/index.php/secretaria/vinccursistaturma/carregar-turma/NU_SEQ_TURMA/"
								. $arDados['NU_SEQ_TURMA'] . "'"));

		//Adicionado Componentes no formulário
		$this->addElements(array($btConfirmar, $btCancelar));

		$obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
				array('class' => 'agrupador barraBtsAcoes'));

		parent::__construct();

		$obDisplayGroup->botoes->addDecorator('HtmlTag',
				array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
		$obDisplayGroup->botoes->removeDecorator('fieldset');

		return $this;
	}

	private function addElementsDadosCursista( $arDados, $arExtra = null ) {
		// ID DA TURMA
		$nuTurma = $this->createElement('hidden', 'NU_SEQ_TURMA')->setValue($arDados['NU_SEQ_TURMA']);
		// CPF DO CURSISTA
		$nunuCpf = $this->createElement('text', 'NU_CPF',
				array("label" => "CPF: ", 'value' => $arDados['NU_CPF'], "class" => "cpf"))->setRequired(true);
		// NOME DO CURSISTA
		$nunoUsuario = $this->createElement('text', 'NO_USUARIO',
				array("label" => "Nome Completo: ", 'value' => $arDados['NO_USUARIO'], "maxlength" => "70", "readonly",
						'true'))->setRequired(true)->setAttrib("size", 40)->setDescription("Nome na Receita Federal.");
		$validatorNomeIdentical = new Zend_Validate_Identical();
		$validatorNomeIdentical->setMessages(
				array(
						'notSame' => 'Nome diferente do campo Nome Completo, constante na base de dados da Receita Federal.'))->setToken(
				"NO_USUARIO");
		// CONFIRMAÇÃO DO NOME
		$nunoUsuarioConfirm = $this->createElement('text', 'NO_USUARIO_CONFIRM',
				array("label" => "Confirmar Nome Completo: ", 'value' => $arDados['NO_USUARIO'],
						"onfocus" => "javascript:window.clipboardData.clearData()", "maxlength" => "70",
						"size" => '40'))->setRequired(true)->addValidator($validatorNomeIdentical)->setDescription(
				"Confirmação do nome de acordo com a Receita Federal");
		// SEXO
		$nuSexoHidden = $this->createElement('hidden', 'CO_SEXO_USUARIO');
		$nucoSexoUsuario = $this->createElement('text', 'DS_SEXO_USUARIO', array("label" => "Sexo: "))->setRequired(
				true)->setAttrib('readonly', true);
		// DATA DE NASCIMENTO
		$nudtNascimento = $this->createElement('text', 'DT_NASCIMENTO',
				array("label" => "Data de Nascimento: ", 'value' => $arDados['DT_NASCIMENTO']))->setRequired(true)->setAttrib(
				"readonly", true);
		// NOME DA MÃE
		$nunoMae = $this->createElement('text', 'NO_MAE',
				array("label" => "Nome da Mãe: ", 'value' => $arDados['NO_MAE'], "size" => '30', "readonly" => 'true'));
		// UF DE NASCIMENTO
		$nuufCursista = $this->createElement('select', 'SG_UF_NASCIMENTO',
				array('name' => 'SG_UF_NASCIMENTO', 'label' => 'UF Nascimento: ', $arDados['SG_UF_NASCIMENTO']))->addMultiOption(
				null, "Selecione")->setRequired(true);

		$nucoMunicipioDadosPessoais = $this->createElement('select', 'CO_MUNICIPIO_NASCIMENTO',
				array('name' => 'CO_MUNICIPIO_NASCIMENTO', 'label' => 'Município Nascimento: ',
						$arDados['CO_MUNICIPIO_NASCIMENTO']))->addMultiOption(null, "Selecione")->setRequired(true);
		// EMAIL
		$nudsEmailUsuario = $this->createElement('text', 'DS_EMAIL_USUARIO',
				array("label" => "E-mail: ", 'value' => $arDados['DS_EMAIL_USUARIO'], "maxlength" => "60"))->setRequired(
				true);
		$validatorEmailIdentical = new Zend_Validate_Identical();
		$validatorEmailIdentical->setMessages(array('notSame' => 'Os e-mails estão diferentes. Favor conferir!'))->setToken(
				"DS_EMAIL_USUARIO");
		// CONFIRMACAO EMAIL
		$nudsEmailUsuarioConfirma = $this->createElement('text', 'DS_EMAIL_USUARIO_CONFIRM',
				array("label" => "Confirmar E-mail: ", 'value' => $arDados['DS_EMAIL_USUARIO'],
						"onfocus" => "javascript:window.clipboardData.clearData()", "maxlength" => "60"))->setRequired(
				true)->addValidator($validatorEmailIdentical);
		//TELEFONE
		$validatorTelefone = new Zend_Validate_Regex('/^[(][0-9]{2}[)] [0-9]{4,5}[-][0-9]{4}/');
		$validatorTelefone->setMessages(array("regexNotMatch" => "Número do telefone inválido!"));
		$nudsTelefoneUsuario = $this->createElement('text', 'DS_TELEFONE_USUARIO',
				array("label" => "Telefone: ", 'value' => $arDados['DS_TELEFONE_USUARIO'], "class" => "fone fonegeral"))->setRequired(
				true)->addValidator($validatorTelefone);
		//CELULAR
		$nudsCelularUsuario = $this->createElement('text', 'DS_CELULAR_USUARIO',
				array("label" => "Celular: ", 'value' => $arDados['DS_CELULAR_USUARIO'], "class" => "celular"))->addValidator(
				$validatorTelefone);

		$nucoFormacaoAcademica = $this->createElement('select', 'NU_SEQ_FORMACAO_ACADEMICA',
			array('name' => 'NU_SEQ_FORMACAO_ACADEMICA', 'label' => 'Formação Acadêmica: ',
				$arDados['NU_SEQ_FORMACAO_ACADEMICA']))->addMultiOption(null, "Selecione")->setRequired(true);


		$tipoInstituicao = $this->createElement('select', 'TP_INSTITUICAO',
			array('name' => 'TP_INSTITUICAO', 'label' => 'Tipo de Instituição: ',
				$arDados['TP_INSTITUICAO']))->addMultiOption(null, "Selecione")->setRequired(true);
		$tipoInstituicao->addMultiOption("1", "Pública");
		$tipoInstituicao->addMultiOption("2", "Privada");

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nuTurma, $nuSexoHidden, $nunuCpf, $nunoUsuario, $nunoUsuarioConfirm,
						$nucoSexoUsuario, $nudtNascimento, $nunoMae, $nuufCursista, $nucoMunicipioDadosPessoais,
						$nudsEmailUsuario, $nudsEmailUsuarioConfirma, $nudsTelefoneUsuario, $nudsCelularUsuario,$nucoFormacaoAcademica, $tipoInstituicao, ));
	}

	/**
	 * Adicionar o elementos de dados escolares ao formulário
	 */
	private function addElementsDadosEscolares( $arDados, $arExtra = null ) {
		$nuufEscola = $this->createElement('select', 'SG_UF_ESCOLA',
				array('name' => 'SG_UF_ESCOLA', 'label' => 'UF: ', $arDados['SG_UF_ESCOLA']))->addMultiOption(null,
				"Selecione")->setAttrib("disabled", "disabled");

		$nuufEscolaHidden = $this->createElement("hidden", 'SG_UF_ESCOLA_HIDDEN');
		if ( $arExtra['SG_UF_ATUACAO_PERFIL'] ) {
			$nuufEscolaHidden->setValue($arExtra['SG_UF_ATUACAO_PERFIL']);
		}

        if(
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, Zend_Auth::getInstance()->getIdentity()->credentials) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, Zend_Auth::getInstance()->getIdentity()->credentials)
        ){
            $nuufEscola->setValue($arDados['SG_UF_ESCOLA']);
        }else{
            $nuufEscola->setValue($nuufEscolaHidden->getValue());
        }

		// MUNICIPIO DA ESCOLA
		$nucoMunicipioEscola = $this->createElement('select', 'CO_MUNICIPIO_ESCOLA',
				array('name' => 'CO_MUNICIPIO_ESCOLA', 'label' => 'Município: ', $arDados['CO_MUNICIPIO_ESCOLA']))->addMultiOption(
				null, "Selecione")->setRequired(true);
		// MESORREGIÃO DA ESCOLA
		$nucoMesorregiaoEscola = $this->createElement('text', 'NO_MESORREGIAO_ESCOLA',
				array("label" => "Mesorregião: "))->setAttrib("readonly", true);
		$nucoMesorregiaoEscolaHidden = $this->createElement('hidden', 'CO_MESORREGIAO_ESCOLA_HIDDEN');
		// REDE DE ENSINO
		$nucoRedeEnsino = $this->createElement('select', 'CO_REDE_ENSINO',
				array('name' => 'CO_REDE_ENSINO', 'label' => 'Rede de ensino: ', $arDados['CO_REDE_ENSINO']))->addMultiOption(
				null, "Selecione")->setRequired(true);
		// NOME DA ESCOLA
		$nucoNomeEscola = $this->createElement('select', 'CO_ESCOLA',
				array('name' => 'CO_ESCOLA', 'label' => 'Nome da Escola: ', $arDados['CO_ESCOLA'],
						"style" => "width:200px;"))->addMultiOption(null, "Selecione")->setRequired(true);
		// SEGMENTO
		$nucoSegmento = $this->createElement('select', 'CO_SEGMENTO',
				array('name' => 'CO_SEGMENTO', 'label' => 'Segmento: ', $arDados['CO_SEGMENTO']))->addMultiOption(
				null, "Selecione")->setRequired(true);

		// Adiciona os elementos ao formulário
		$this->addElements(
				array($nucoRedeEnsino, $nucoMunicipioEscola, $nuufEscola, $nuufEscolaHidden, $nucoMesorregiaoEscola,
						$nucoMesorregiaoEscolaHidden, $nucoNomeEscola, $nucoSegmento,));
	}

	/**
	 * Método para inserir UF Nascimento no select
	 * @author gustavo.gomes
	 * @param array $rsEstado
	 */
	public function setUfNascimento( $rsEstado ) {
		$this->getElement('SG_UF_NASCIMENTO')->addMultiOptions($rsEstado);
	}

	/**
	 * Método para inserir os municipios no select
	 * @author gustavo.gomes
	 * @param array $result
	 * @param string $municipioAtual
	 */
	public function setMunicipios( $result, $municipioAtual ) {

		$nucoMunicipioDadosPessoais = $this->getElement('CO_MUNICIPIO_NASCIMENTO');

		for ( $i = 0; $i < count($result); $i++ ) {
			$nucoMunicipioDadosPessoais->addMultiOption($result[$i]['CO_MUNICIPIO_IBGE'], $result[$i]['NO_MUNICIPIO']);
		}

		$nucoMunicipioDadosPessoais->setValue($municipioAtual);
	}

	/**
	 * Método para inserir valores em Municipio Escola
	 * @author gustavo.gomes
	 * @param array $result
	 */
	public function setMunicipioEscola( $result, $arDados ) {
	    /** @var Zend_Form_Element_Multi $element */
        $element = $this->getElement('CO_MUNICIPIO_ESCOLA');
        $arOption = array();

        if($result){
            foreach($result as $item){
                $arOption[$item['CO_MUNICIPIO_FNDE']] = $item['NO_MUNICIPIO'];
            }
        }
		$element->addMultiOptions($arOption);
        $element->setValue($arDados['CO_MUNICIPIO_ESCOLA']);
	}

	/**
	 * Método para inserir valores em SG_UF_ESCOLA
	 * @author gustavo.gomes
	 * @param array $rsEstado
	 */
	public function setUfEscola( $rsEstado ) {
		$this->getElement('SG_UF_ESCOLA')->addMultiOptions($rsEstado);

	}

	/**
	 * Método que retorna UF de Escola
	 * @author gustavo.gomes
	 */
	public function getUfEscola() {
		return $this->getElement('SG_UF_ESCOLA')->getValue();
	}

	/**
	 * Método para inserir valores em CO_ESCOLA
	 * @author gustavo.gomes
	 * @param array $result
	 * @param array $arDados
	 */
	public function setNomeEscola( $result, $arDados ) {

		$nucoNomeEscola = $this->getElement('CO_ESCOLA');

		for ( $i = 0; $i < count($result); $i++ ) {
			$nucoNomeEscola->addMultiOption($result[$i]['CO_ESCOLA'], $result[$i]['NO_ESCOLA']);
		}
		$nucoNomeEscola->setValue($arDados['CO_ESCOLA']);
	}

	/**
	 * Método para inserir valores em CO_SEGMENTO
	 * @author gustavo.gomes
	 * @param array $rsSegmento
	 */
	public function setSegmento( $rsSegmento ) {

		$this->getElement('CO_SEGMENTO')->addMultiOptions($rsSegmento);

	}

	/**
	 * Método para inserir rede de ensino no select
	 * @author gustavo.gomes
	 * @param array $result
	 * @param array $arDados
	 */
	public function setRedeEnsino( $result, $arDados ) {

		$nucoRedeEnsino = $this->getElement('CO_REDE_ENSINO');

		for ( $i = 0; $i < count($result); $i++ ) {
			$nucoRedeEnsino->addMultiOption($result[$i]['CO_ESFERA_ADM'], $result[$i]['NO_ESFERA_ADM']);
		}

		$nucoRedeEnsino->setValue($arDados['CO_REDE_ENSINO']);
	}

	/**
	 * Adiciona validação aos campos de e-mail
	 * @param validate $validatorEmail
	 */
	public function setValidatorEmail( $validatorEmail ) {
		$this->getElement('DS_EMAIL_USUARIO')->addValidator($validatorEmail);
		$this->getElement('DS_EMAIL_USUARIO_CONFIRM')->addValidator($validatorEmail);
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
