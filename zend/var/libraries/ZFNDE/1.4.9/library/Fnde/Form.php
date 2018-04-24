<?php

/**
 * Classe de formulário padrão
 *
 * @author Leandro
 * @author Walker de Alencar
 * @author Theoziran Lima <theoziran.silva@fnde.gov.br>
 */
class Fnde_Form extends Zend_Form {

    public function __construct($options = null) {
        $this->config();
        parent::__construct($options);
    }

    private function config() {
        $this->addPrefixPath('Fnde_Form_Element', 'Fnde/Form/Element', 'element');
        $this->addElementPrefixPath('Fnde_Decorators', 'Fnde/Decorators', 'decorator');
        $this->addElementPrefixPath('Fnde_Filter', 'Fnde/Filter', 'filter');
    }

    /**
     * Sobrescreve o load dos Decorators Padrão.
     */
    public function loadDefaultDecorators() {
        $this->addDecorator('FormElements')
                ->addDecorator('Form');

        $this->setSubFormDecorators(array(
            'FormElements',
            'Fieldset'
        ));

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));

        $actionBar = $this->getDisplayGroup('ActionBar');
        if ($actionBar) {
            $actionBar->clearDecorators();
            $actionBar->addDecorator('FormElements')
                    ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'barraBtsAcoes'));
        }
        $this->setElementDecorators(array('Composite'));

        foreach($this->getElements() as $element){
            $element->addFilters(array('StripSlashes', 'stringTrim'));
        }
    }

    /**
     * Retorna todos elementos não validados
     * @return Zend_Form_Element
     */
    public function getInvalidElements() {
        $elements = array();
        foreach ($this->getErrors() as $name => $error) {
            if ($error) {
                if (!is_null($this->getElement($name))) {
                    $element = $this->getElement($name);
                    $elements[] = $element;
                }
            }
        }
        return $elements;
    }
    
}