<?php
require_once 'Zend/View/Helper/FormElement.php';

class Fnde_View_Helper_FormHtml extends Zend_View_Helper_FormElement{
    
    public function formHtml($name, $value, $attribs = array())
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
		$attribs = $this->view->escape($attribs);
		$name = $this->view->escape($name);
		$value = $this->view->escape($value);
		if(isset($attribs['tag'])){
			$tag = $attribs['tag'];
			unset($attribs['tag']);
		}else{
			$tag = 'div';
		}

		$xhtml = "<{$tag} id=\"{$name}\">{$value}</{$tag}>";
		return $xhtml;
    }
}