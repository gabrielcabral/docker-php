<?php
/**
 * 
 * $Rev:: 195               $
 * $Date:: 2010-12-02 18:16#$
 * $Author:: TheoziranSilva $
 *
 * @package ZFnde
 * @category Form Element
 * @name TextButton
 *
 * Classe de elemento de formulário
 * @uses Fnde_Form_Element_HtmlEditor
 * @author theoziran<theoziran.silva@fnde.gov.br
 */
class Fnde_Form_Element_HtmlEditor extends Zend_Form_Element_Textarea {

	const CLASS_NAME = 'editorHtml';

	public function init(){
		$this->getView()->headScript()->appendFile('/static/js/ckeditor/ckeditor.js');
        $this->getView()->headScript()->appendFile('/static/js/ckeditor/adapters/jquery.js');
        $this->getView()->headScript()->appendFile('/static/js/ckeditor/ckeditor.settings.fnde.js');
        $this->setAttrib('class', $this->getAttrib('class') . ' ' . self::CLASS_NAME);
	}

}