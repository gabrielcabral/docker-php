<?php

/**
 * Form de cadastro OutrasInformacoes
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class VinculaAtivUsuario_Form extends Fnde_Form {
	/**
	 * Construtor do formulário de outras informações
	 *
	 * @return object - Objeto de formulário
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function __construct( $arDados, $arExtra, $formSecundario = false) {
		$btAddInfo = $this->createElement('button', 'adicionar',
				array("label" => "Adicionar", "value" => "Adicionar", "class" => "btnAdicionar",
						"title" => "Adicionar", "id" => "btAdcionarAtividade"));

		$this->addElements(array($btAddInfo));

		$group = array();

		if ( $arDados['NU_SEQ_ATIVIDADE'] == null || !is_array($arDados['NU_SEQ_ATIVIDADE']) ) {

			$nuAtividade = $this->createElement('select', 'NU_SEQ_ATIVIDADE',
					array('name' => 'NU_SEQ_ATIVIDADE', 'label' => 'Atividade: ', $arDados['NU_SEQ_ATIVIDADE']))->setRequired(
					true)->addMultiOption(null, "Selecione");

			$this->addElements(array($nuAtividade));

			$txtOutra = $this->createElement('text', 'DS_ATIVIDADE_ALTERNATIVA',
					array("label" => "Qual?", "maxlength" => "60"));
			$txtOutra->setRequired(true);
			$this->addElements(array($txtOutra));

			$group[] = $this->addDisplayGroup(array('NU_SEQ_ATIVIDADE', 'DS_ATIVIDADE_ALTERNATIVA'), 'dadosAtividades0');

		} else {
			$i = 0;
			foreach ( $arDados['NU_SEQ_ATIVIDADE'] as $ativ ) {

				$select = $this->createElement("select", "NU_SEQ_ATIVIDADE" . $i);
				$select->setLabel("Atividade: ")->setRequired(true);
				$select->addMultiOption("", "Selecione");

				//monta combo com valor selecionado
				if ( $ativ != '' ) {
					$select->setValue($ativ);
				} else {
					$select->addError("O campo é obrigatório e não pode estar vazio");
				}
				//Adicionado Componentes no formulário
				$this->addElement($select);

				$txtOutra = $this->createElement('text', 'DS_ATIVIDADE_ALTERNATIVA' . $i, array("label" => "Qual?"));
				$txtOutra->setRequired(true);

				$txtOutra->setValue($arDados['DS_ATIVIDADE_ALTERNATIVA'][$i]);
				$this->erroCampoDsAtividade($ativ, $arDados['DS_ATIVIDADE_ALTERNATIVA'][$i], $txtOutra);

				$this->addElement($txtOutra);

				$group[] = $this->addDisplayGroup(array('NU_SEQ_ATIVIDADE' . $i, 'DS_ATIVIDADE_ALTERNATIVA' . $i),
						'dadosAtividades' . $i);

				$i++;
			}
		}

		parent::__construct();

		$i = '0';

		$dadosAtividades = "dadosAtividades" . $i;

		foreach ( $group as $grp ) {

			$grp->$dadosAtividades->addDecorator('HtmlTag',
					array('tag' => 'div',
							//'class' => 'agrupador inLine ' . ( $i > 0 || $arDados == null ? 'remove' : '' )));
                                            'class' => $i . 'agrupador inLine ' . ( $i > 0 || $formSecundario ? 'remove' : '' )));
			$grp->$dadosAtividades->removeDecorator("fieldset");

			if ( $i != null ){
				$i++;
			}
			$dadosAtividades = "dadosAtividades" . $i;

		}

		$btAddInfo->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'adicionar', 'class' => 'agrupador'));

		return $this;
	}

	/**
	 * 
	 * @param array $arAtividade
	 */
	public function setAtividade( $arAtividade, $nomeCampo ) {
		$this->getElement($nomeCampo)->addMultiOptions($arAtividade);
	}
	
	private function erroCampoDsAtividade($ativ, $ativAlternativa, &$txtOutra){
		if ( $ativ == '10' && $ativAlternativa == '' ) {
			$txtOutra->addError("O campo é obrigatório e não pode estar vazio");
		}
	}
}
