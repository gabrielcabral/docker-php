<?php
/**
 * @package ZFnde
 * @category View Helper
 * @name FormError
 *
 * Classe para mostrar corretamente os erros dos elementos com dados inválidos
 * @uses Zend_View_Helper_Abstract
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br
 */
class Fnde_View_Helper_FormError extends Zend_View_Helper_Abstract {

    /**
     *
     * @var array
     */
    protected $_invalidElements = array();

    /**
     * @param array $elements
     * @return Fnde_View_Helper_FormError 
     */
    public function FormError(array $elements = array()) {
        $this->_invalidElements = $elements;
        return $this;
    }

    /**
     * @return string 
     */
    protected function render() {
        $ul = "<ul>\n";
        $li = '';
        foreach ($this->_invalidElements as $element) {
			$li .= "\t<li>" . $element->getLabel() . "</li>\n";
        }
        $ul .= $li . "</ul>\n";
        return $ul;
    }

    public function  __toString() {
        return $this->render();
    }

}