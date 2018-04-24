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
 * @name NivelEnsino
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_NivelEnsino
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_NivelEnsino extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_NIVEL_ENSINO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('CO_NIVEL_ENSINO',);
	protected $_cols = array('CO_NIVEL_ENSINO', 'DS_NIVEL_ENSINO',);
	protected $_metadata = array(
			'CO_NIVEL_ENSINO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_NIVEL_ENSINO',
					'COLUMN_NAME' => 'CO_NIVEL_ENSINO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_NIVEL_ENSINO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_NIVEL_ENSINO',
					'COLUMN_NAME' => 'DS_NIVEL_ENSINO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
