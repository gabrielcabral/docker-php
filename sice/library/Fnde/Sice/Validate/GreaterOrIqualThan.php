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

class Fnde_Sice_Validate_GreaterOrIqualThan extends Zend_Validate_GreaterThan
{

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_GREATER => "'%value%' não é maior ou igual à '%min%'",
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		if ($this->_min > $value) {
			$this->_error(self::NOT_GREATER);
			return false;
		}
		return true;
	}

}
