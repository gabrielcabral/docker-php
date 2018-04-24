<?php

/**
 * Form de cadastro Curso
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Curso_FormComboModulo extends Fnde_Form {
	/**
	 * Construtor do Combo de M�dulos (Complementar � tela de Curso).
	 *
	 * @return object - Objeto de formul�rio
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function __construct( $arDados, $arExtra, $arHora = array() ) {
		$this->setAttrib("class", "labelLongo");
		//Adicionando elementos no formul�rio

		$modulo = $this->createElement("select", 'NU_SEQ_MODULO',
				array('name' => 'NU_SEQ_MODULO', 'label' => 'M�dulo:', 'required' => true,));
		$modulo->addMultiOption(null, 'Selecione');

		$this->addElement($modulo);

		foreach ( $arHora as $row ) {
			$elemento = $this->createElement("hidden", "VL_HORA" . $row["NU_SEQ_MODULO"]);
			$elemento->setValue($row["VL_CARGA_HORARIA"]);
			$this->addElement($elemento);
		}

		parent::__construct();

		$this->getElement("NU_SEQ_MODULO")->addDecorator("htmlTag", array("tag" => "div", "class" => "remove"));

		return $this;
	}

	/**
	 * 
	 * @param array $arModulo
	 */
	public function setComboModulo( $arModulo ) {
		$this->getElement('NU_SEQ_MODULO')->addMultiOptions($arModulo);
	}

}
