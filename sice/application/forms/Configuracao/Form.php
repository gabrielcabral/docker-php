<?php

/**
 * Form de cadastro Configuracao
 *
 * @author diego.matos
 * @since 30/03/2012
 */
class Configuracao_Form extends Fnde_Form {
	/**
	 * Construtor do formulário da tela de Configuração.
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function __construct( $arDados, $arExtra, $record ) {
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$admin = false;
		if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
			$admin = true;
		}
		$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formulário
		$nuConfiguracao = $this->createElement('hidden', 'NU_SEQ_CONFIGURACAO');
		$v = $this->createElement('hidden', 'v');

		$nuSTipoCurso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO', array("label" => "Tipo de Curso: "));
//		$nuSTipoCurso->setRequired(true);

		if ( $arExtra['NU_SEQ_CONFIGURACAO'] != null && $arDados['v'] ) {
			$v->setValue($arDados['v']);
			$record['configuracaoVingente']['dataInicioVingencia'] = $arExtra['DT_INI_VIGENCIA'];
			$record['configuracaoVingente']['dataTerminoVingencia'] = $arExtra['DT_TERMINO_VIGENCIA'];
		}
		$nuSTipoCurso->setAttrib("disabled", "disabled");

		$nuSTipoCurso->setValue($arDados["configuracao"]["NU_SEQ_TIPO_CURSO"]);

		$txtInicioVigencia = $this->createElement('text', 'DT_INI_VIGENCIA', array("label" => "Início da Vigência: ", "class" => "date dp-applied"));
//		$txtInicioVigencia->setRequired(true);
		$txtInicioVigencia->setAttrib("disabled", "disabled");
		$txtInicioVigencia->setValue($arDados["configuracao"]["DT_INI_VIGENCIA"]);

		$txtfimVingencia = $this->createElement('text', 'DT_TERMINO_VIGENCIA',
				array("label" => "Término da Vigência: ", "class" => "date dp-applied"));
		// 		$txtfimVingencia->setRequired(true);
		$txtfimVingencia->setAttrib("disabled", "disabled");
		$txtfimVingencia->setValue($arDados["configuracao"]["DT_TERMINO_VIGENCIA"]);

		$this->addElements(array($nuConfiguracao, $v, $nuSTipoCurso, $txtInicioVigencia, $txtfimVingencia));

		$this->addDisplayGroup(array('NU_SEQ_TIPO_CURSO', 'v', 'DT_INI_VIGENCIA', 'DT_TERMINO_VIGENCIA',),
				'configuracaoVingente', array("legend" => "Configuração Vigente"));

		$this->addElement(new Html("htmlNovaConf"));

		$btConfirmar = $this->createElement('submit', 'confirmar',
				array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar",
						'onClick' => "$(this.form).find('input').attr('disabled',false)", "title" => "Confirmar"));

		if ( $arExtra['NU_SEQ_CONFIGURACAO'] ) {
			$btConfirmar->setAttrib("disabled", "disabled");
		}

		$btCancelar = $this->createElement('button', 'cancelar',
				array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar",
						'onClick' => "window.location='" . $this->getView()->baseUrl()
								. "/index.php/manutencao/configuracao/list/'"));

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

}
