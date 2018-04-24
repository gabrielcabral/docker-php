<?php
class Fnde_Decorators_Picklist extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface {

    public function buildInput() {
        $element = $this->getElement();

        $element->setOptions(array('listsep' => "\n"));

        $helper = $element->helper;

        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        return $element->getView()->$helper(
                $element->getFullyQualifiedName(),
                $element->getValue(),
                $attribs,
                $element->options
        );
    }

    public function buildErrors() {
        $element = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return $element->getView()->formErrors($messages, array('class' => 'msgErro'));
    }

    public function buildDescription() {
        $element = $this->getElement();
        $desc = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<span class="msgOrientacao"> (' . $desc . ')</span>';
    }

    public function render($content) {
        $element = $this->getElement();

        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }

        /* Gera todos os códigos */
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $element->getLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();
        $desc = $this->buildDescription();
        $id = $element->getId();

        /* Cria o elemento usando label */
        $output = $input . $desc;

		$class = 'agrupador inLine listaSelecao';

		if($element->isRequired()){
			$class .= ' campoRequerido';
		}

		$output = "
                <fieldset class=\"{$class}\">
                    <legend>{$element->getLabel()}</legend>
                    {$output}
                    {$errors}
                </fieldset>";
        

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }

}