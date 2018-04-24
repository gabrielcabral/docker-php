<?php

/**
 * Formulário de configuração da ferramenta: Model
 */
class Tools_Model_ListForm extends Fnde_Form {

    public function init() {
        $this->addElements(array(
            $this->createElement('Multiselect', 'tables')
                ->setLabel('Entidades:')
                ->setDescription('Lista de Entidades que o usuário da aplicação possui acesso.')
                ->setRequired(true),
            $this->createElement('Button','confirmar')
                 ->setAttrib('class', 'btnConfirmar')
                 ->setAttrib('type', 'Submit'),
            $this->createElement('Button','cancelar')
                 ->setAttrib('class', 'btnCancelar')
            ));
        $this->addDisplayGroup(array('confirmar','cancelar'), 'ActionBar');
    }

    /**
     *
     * @param array $tableList
     */
    public function __construct($tableList = null) {
        parent::__construct();
        if (!is_null($tableList)){
            $this->setEntityOptions($tableList);
        }
    }

    public function setEntityOptions(array $tableList) {
        $opts = array();
        foreach($tableList as $table){
            $name = "{$table['TABLE_SCHEMA']}.{$table['TABLE_NAME']}";
            $opts["{$table['OBJECT_TYPE']}"]["{$name}"] = $name;
        }
        $this->getElement('tables')->addMultiOptions($opts);
    }

}