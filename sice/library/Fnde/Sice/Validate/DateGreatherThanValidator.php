<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: GreaterThan.php 20358 2010-01-17 19:03:49Z thomas $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Fnde_Sice_Validate_DateGreatherThanValidator extends Zend_Validate_Abstract {

	const DATA_INICIO = 'dataAtual';
	const MISSING_TOKEN = "missingToken";

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
			self::DATA_INICIO => "A %data_inicio% tem de ser maior ou igual à %data_fim%.",
			self::MISSING_TOKEN => 'Não foi informado a data menor.',);

	/**
	 * @var array
	 */
	protected $_messageVariables = array('data_inicio' => '_lblDataInicio', 'data_fim' => '_lblDataFim');

	/**
	 * Data final
	 *
	 * @var mixed
	 */
	protected $_token;

	protected $_lblDataInicio = "Data Início";
	protected $_lblDataFim = "Data Atual";

	/**
	 * Sets validator options
	 *
	 * @param  mixed|Zend_Config $token 
	 * @return void
	 */
	public function __construct( $token ) {

		if ( $token instanceof Zend_Config ) {
			$token = $token->toArray();
		}

		if ( is_array($token) && array_key_exists('token', $token) ) {
			$this->setToken($token['token']);
		} else if ( null !== $token ) {
			$this->setToken($token);
		}
	}

	/**
	 * Retrieve token
	 *
	 * @return string
	 */
	public function getToken() {
		return $this->_token;
	}

	/**
	 * Set token against which to compare
	 *
	 * @param  mixed $token
	 * @return Zend_Validate_Identical
	 */
	public function setToken( $token ) {
		$this->_tokenString = ( string ) $token;
		$this->_token = $token;
		return $this;
	}

	/**
	 * Defined by Zend_Validate_Interface
	 *
	 * Returns true if and only if $value is greater than min option
	 *
	 * @param  mixed $value
	 * @return boolean
	 */
	public function isValid( $value, $context = null ) {

		$this->_setValue(( string ) $value);

		if ( ( $context !== null ) && isset($context) && is_string($this->getToken())
				&& array_key_exists($this->getToken(), $context) ) {
			$token = $context[$this->getToken()];
		} else {
			$token = $this->getToken();
		}

		if ( $token === null ) {
			$this->_error(self::MISSING_TOKEN);
			return false;
		}

		$dataInicio = new Zend_date($value, 'D/M/Y');

		$dtTeste = date('d/m/Y', $dataInicio->getTimestamp());

		if ( $dataInicio->isEarlier($token) ) {
			$this->_error(self::DATA_INICIO);
			return false;
		}

		return true;
	}

}
