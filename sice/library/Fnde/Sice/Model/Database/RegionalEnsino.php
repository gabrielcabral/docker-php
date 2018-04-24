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
 * @name RegionalEnsino
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_RegionalEnsino
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_RegionalEnsino extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_REGIONAL_ENSINO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_ENTIDADE', 'CO_MUNICIPIO_FNDE',);
	protected $_cols = array('NU_SEQ_ENTIDADE', 'CO_MUNICIPIO_FNDE',);
	protected $_metadata = array(
			'NU_SEQ_ENTIDADE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIONAL_ENSINO',
					'COLUMN_NAME' => 'NU_SEQ_ENTIDADE', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '12', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '2', 'IDENTITY' => false,),
			'CO_MUNICIPIO_FNDE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIONAL_ENSINO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_FNDE', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '6', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),);
}
