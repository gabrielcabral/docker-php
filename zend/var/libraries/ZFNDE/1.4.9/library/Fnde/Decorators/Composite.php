<?php

class Fnde_Decorators_Composite extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface {

    public function buildLabel() {
        $element = $this->getElement();
        $label = $element->getLabel();
        $translator = $element->getTranslator();
        if ($translator) {
            $label = $translator->translate($label);
        }
        $labelSpan = '<span';

        /* Se o elemento for requerido, será adicionado um "*" após o seu nome */
        if ($element->isRequired()) {
            $labelSpan .= ' class="campoRequerido"';
        }
        $labelSpan .= '>' . $label . '</span>';
        return $labelSpan;
    }

    public function buildInput() {
        $element = $this->getElement();

        $element->setOptions(array('listsep' => "\n"));

        $helper = $element->helper;

        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        $errors = $this->buildErrors();
        if(!empty($errors)){
            $attribs['class'] .= ' campoErro';
        }

        if (in_array($element->getType(),
                        array('Zend_Form_Element_Reset', 'Zend_Form_Element_Submit', 'Zend_Form_Element_Button')
                ) && $element->getValue() == '') {
            $element->setValue(ucfirst($element->getName()));
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
        $label = $this->buildLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();
        $desc = $this->buildDescription();
        $id = $element->getId();

        /* Cria o elemento usando label */
        $output = $input . $desc;

        /* Input text com botoes auxiliares */
        if (in_array($element->getType(), array('Fnde_Form_Element_TextButton'))) {
            $element->setDecorators(array());
            $buttons = $element->getButtons();
            $outputBotao = '';
            foreach ($buttons as $button) {
                $this->setElement($button);
                $btInput = $this->buildInput();
                $btErrors = $this->buildErrors();
                $btDesc = $this->buildDescription();
                $outputButton .= $btInput . $btDesc . $btErrors;
            }
            $output = "<label for=\"{$id}\">{$label} {$output} {$outputButton} {$errors}</label>";
        }

        if (!in_array($element->getType(), array('Zend_Form_Element_Reset', 'Zend_Form_Element_Submit', 'Zend_Form_Element_Button', 'Zend_Form_Element_Hidden',
                    'Zend_Form_Element_Radio', 'Zend_Form_Element_MultiCheckbox', 'Fnde_Form_Element_TextButton','Fnde_Form_Element_HtmlEditor','Fnde_Form_Element_Html'))) {
            $output = "<label for=\"{$id}\">{$label} {$output} {$errors}</label>";
        }

        if (in_array($element->getType(), array('Zend_Form_Element_Radio', 'Zend_Form_Element_MultiCheckbox'))) {
            $class = $element->getAttrib('class');
            if ($element->isRequired()) {
                $class .= ' campoRequerido';
            }

            $output = "
                <fieldset class='" . $class . "'>
                    <legend>{$element->getLabel()}</legend>
                    {$output}
                    {$errors}
                </fieldset>";
        }

        if (in_array($element->getType(), array('Fnde_Form_Element_HtmlEditor'))) {
            $class = $element->getAttrib('class') . ' agrupador';
            if ($element->isRequired()) {
                $class .= ' campoRequerido';
            }

            $output = "
                <fieldset class='" . $class . "'>
                    <legend>{$element->getLabel()}</legend>
                    {$output}
                    {$errors}
                </fieldset>";
        }

        if(in_array($element->getType(),array('Fnde_Form_Element_Html'))){
            $output = "{$output} {$errors}";
        }

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }

}