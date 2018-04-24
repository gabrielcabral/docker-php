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
 * @name Uf
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Uf
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Uf extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_UF';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('SG_UF',);
	protected $_cols = array('SG_UF', 'CO_UF_IBGE', 'CO_UF_INSS', 'NO_UF', 'CO_UF_SIAFI', 'DS_TRATAMENTO', 'SG_REGIAO',
			'CO_UF_SIAFI_BB',);
	protected $_metadata = array(
			'SG_UF' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF', 'COLUMN_NAME' => 'SG_UF',
					'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => true,
					'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'CO_UF_IBGE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF', 'COLUMN_NAME' => 'CO_UF_IBGE',
					'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_UF_INSS' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF', 'COLUMN_NAME' => 'CO_UF_INSS',
					'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_UF' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF', 'COLUMN_NAME' => 'NO_UF',
					'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_UF_SIAFI' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF',
					'COLUMN_NAME' => 'CO_UF_SIAFI', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_TRATAMENTO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF',
					'COLUMN_NAME' => 'DS_TRATAMENTO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'SG_REGIAO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF', 'COLUMN_NAME' => 'SG_REGIAO',
					'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_UF_SIAFI_BB' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_UF',
					'COLUMN_NAME' => 'CO_UF_SIAFI_BB', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '6', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
