<?php
/**
 * Arquivo de classe de modelo do tipo: database
 *
 * Criado automaticamente pelo gerador: ZFnde Model.
 *
 * $Rev::                      $
 * $Date::                     $
 * $Author::                   $
 *
 * @package Sice
 * @category Model
 * @name Local
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Local
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Local extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_LOCAL';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_LOCAL',);
	protected $_cols = array('NU_SEQ_LOCAL', 'DS_LOCAL',);
	protected $_metadata = array(
			'NU_SEQ_LOCAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_LOCAL',
					'COLUMN_NAME' => 'NU_SEQ_LOCAL', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_LOCAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_LOCAL', 'COLUMN_NAME' => 'DS_LOCAL',
					'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '60', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
