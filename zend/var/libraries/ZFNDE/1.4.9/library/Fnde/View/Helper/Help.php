<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_Help
 */
class Fnde_View_Helper_Help
{
	protected $_link;
	protected static $_instance;
	
	/**
	 * @return Fnde_View_Helper_Help
	**/
	public static function help(){
		if(is_null(self::$_instance)){
			self::$_instance = new Fnde_View_Helper_Help();
		}
		return self::$_instance;
	}

	public function setLink($link){
		$this->_link = $link;
	}
	
	/**
	 * @return string
	**/		
	public function getLink(){
		return (!empty($this->_link)) ? '<a href="'.$this->_link.'" target="_blank">Ajuda</a>' : '';
	}
}