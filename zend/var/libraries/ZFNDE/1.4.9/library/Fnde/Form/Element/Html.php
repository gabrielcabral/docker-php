<?php
require_once 'Zend/Form/Element/Xhtml.php';

class Fnde_Form_Element_Html extends Zend_Form_Element_Xhtml {

    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formNote';

    public function isValid($value, $context = null) {
        return true;
    }

}
