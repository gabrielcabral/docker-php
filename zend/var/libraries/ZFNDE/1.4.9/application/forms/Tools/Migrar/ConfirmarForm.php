<?php

/**
 * Formulário de configuração da ferramenta: Model
 */
class Tools_Migrar_ConfirmarForm extends Fnde_Form {

    public function init() {
        $this->addElements(array(
            $this->createElement('Button','confirmar')
                 ->setAttrib('class', 'btnConfirmar')
                 ->setAttrib('type', 'Submit'),
            $this->createElement('Button','cancelar')
                 ->setAttrib('class', 'btnCancelar')
            ));
        $this->addDisplayGroup(array('confirmar','cancelar'), 'ActionBar');
    }

}