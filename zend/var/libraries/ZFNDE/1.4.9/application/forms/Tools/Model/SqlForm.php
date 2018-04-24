<?php

/**
 * Formul�rio de configura��o da ferramenta: Model
 */
class Tools_Model_SqlForm extends Fnde_Form {

    public function init() {
        $this->addElements(
            array(
                $this->createElement('Textarea', 'sqlcode')
                     ->setLabel('SQL:')
                     ->setRequired(true)
                     ->setAttrib('rows', 10),
                $this->createElement('Button','executar')
                     ->setAttrib('class', 'btnConfirmar')
                     ->setAttrib('type', 'Submit')
            )
        );
        $this->addDisplayGroup(array('executar'), 'ActionBar');
    }

}