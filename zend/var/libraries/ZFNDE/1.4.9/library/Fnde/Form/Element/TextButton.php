<?php
/**
 * 
 * $Rev:: 144                  $
 * $Date:: 2010-10-20 19:51:57#$
 * $Author:: WalkerAlencar     $
 *
 * @package ZFnde
 * @category Form Element
 * @name TextButton
 *
 * Classe de elemento de formulário
 * @uses Zend_Form_Element_Text
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br
 */
class Fnde_Form_Element_TextButton extends Zend_Form_Element_Text{

    private $buttons;

    public function  __construct($spec, $options = null) {
        parent::__construct($spec, $options);
        $this->buttons = array();
    }

    /**
     * Adiciona botão auxiliares no componente
     * @param Zend_Form_Element_Button $button
     * @return Fnde_Form_Element_TextButton
     */
    public function addButton(Zend_Form_Element_Button $button){
        $this->buttons[] = $button;
        return $this;
    }

    /**
     * Retorna todos os botões auxiliares do input
     * @return array
     */
    public function getButtons(){
        return $this->buttons;
    }

    /**
     * Remove todos os botões auxiliares do componente
     */
    public function clearButtons(){
        $this->buttons = array();
    }
    
}