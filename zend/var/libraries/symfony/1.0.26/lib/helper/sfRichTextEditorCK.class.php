<?php
/**
 * sfRichTextEditorCK implements the CK rich text editor.
 *
 * <b>Options:</b>
 *  - tool   - Sets the CKEditor toolbar style
 *  - config - Sets custom path to the CKEditor configuration file
 *
 * @package    symfony
 * @subpackage helper
 * @author     Rodrigo Régis Palmeira <regisbsb@gmail.com>
 */
class sfRichTextEditorCK extends sfRichTextEditor
{
	/**
	 * Returns the rich text editor as HTML.
	 *
	 * @return string Rich text editor HTML representation
	 */
	public function toHTML()
	{
		// we need to know the id for things the rich text editor
		// in advance of building the tag
		$id = _get_option($this->options, 'id', $this->name);

		$php_file = sfConfig::get('sf_rich_text_ck_js_dir').DIRECTORY_SEPARATOR.'ckeditor.php';
		//$php_file = 'js/ckeditor/ckeditor.php';

		if (!is_readable(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$php_file))
		{
			throw new sfConfigurationException('You must install CKEditor to use this helper (see rich_text_ck_js_dir settings).');
		}

		// CKEditor.php class is written with backward compatibility of PHP4.
		// This reportings are to turn off errors with public properties and already declared constructor
		$error_reporting = ini_get('error_reporting');
		error_reporting(E_ALL);

		require_once(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$php_file);

		// turn error reporting back to your settings
		error_reporting($error_reporting);

		$this->options['toolbar'] = array(
			array('Source','-',/*'Save',*/'NewPage','Preview','-','Templates'),
			array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'),
			array('Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'),
			//array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'),
			'/',
			array('Bold','Italic','Underline','Strike','-','Subscript','Superscript'),
			array('NumberedList','BulletedList','-','Outdent','Indent','Blockquote'),
			array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'),
			array('Link','Unlink','Anchor'),
			array( /*'Image','Flash',*/'Table','HorizontalRule', /*'Smiley',*/'SpecialChar','PageBreak'),
			'/',
			array('Styles','Format', /*'Font',*/ 'FontSize'),
			array('TextColor','BGColor'),
			array('Maximize', 'ShowBlocks' /*,'-','About'*/)
		);
		
		//$this->options['enterMode'] = array('CKEDITOR.ENTER_BR');

		$ckeditor           = new CKeditor();
		$ckeditor->basePath = sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/js/ckeditor/';
		$content = $ckeditor->editor($this->name, $this->content, $this->options);
		return $content;
	}
}
