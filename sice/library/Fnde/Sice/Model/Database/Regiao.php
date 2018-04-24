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
 * @name Regiao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Regiao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Regiao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_REGIAO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('SG_REGIAO',);
	protected $_cols = array('SG_REGIAO', 'CO_REGIAO_IBGE', 'CO_REGIAO_SIAFI', 'NO_REGIAO',);
	protected $_metadata = array(
			'SG_REGIAO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIAO',
					'COLUMN_NAME' => 'SG_REGIAO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'CO_REGIAO_IBGE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIAO',
					'COLUMN_NAME' => 'CO_REGIAO_IBGE', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_REGIAO_SIAFI' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIAO',
					'COLUMN_NAME' => 'CO_REGIAO_SIAFI', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_REGIAO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_REGIAO',
					'COLUMN_NAME' => 'NO_REGIAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '15', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
