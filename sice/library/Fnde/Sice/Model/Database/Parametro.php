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
 * @name Parametro
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Parametro
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Parametro extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_PARAMETRO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('SG_PARAMETRO',);
	protected $_cols = array('SG_PARAMETRO', 'DS_PARAMETRO',);
	protected $_metadata = array(
			'SG_PARAMETRO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PARAMETRO',
					'COLUMN_NAME' => 'SG_PARAMETRO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '60', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_PARAMETRO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PARAMETRO',
					'COLUMN_NAME' => 'DS_PARAMETRO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '150', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
