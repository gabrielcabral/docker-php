<?php

/**
 * Classe de sub formulário padrão
 *
 * @author Walker de Alencar
 */
class Fnde_Form_SubForm extends Fnde_Form {
    /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Sobrescreve o load dos Decorators Padrão.
     */
    public function loadDefaultDecorators() {
        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('Fieldset');
        }
        $this->setElementDecorators(array('Composite'));
        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));
        $this->setElementFilters(array('StripSlashes', 'stringTrim'));
    }
}