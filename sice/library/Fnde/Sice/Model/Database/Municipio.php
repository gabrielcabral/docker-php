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
 * @name Municipio
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Municipio
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Municipio extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_MUNICIPIO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('CO_MUNICIPIO_FNDE',);
	protected $_cols = array('CO_MUNICIPIO_FNDE', 'CO_MUNICIPIO_CORREIO', 'CO_MUNICIPIO_IBGE', 'CO_MUNICIPIO_INSS',
			'CO_MUNICIPIO_SIAFI', 'AN_INCLUSAO_SISTEMA', 'NO_MUNICIPIO', 'NO_ANT_MUNICIPIO', 'NU_CEP_MUNICIPIO',
			'ST_CEP_MUNICIPIO', 'ST_CAPITAL_ESTADO', 'ST_CAPITAL_ESPECIAL_PNAE', 'SG_UF', 'NO_ABREVIADO',
			'CO_MICROREGIAO_IBGE', 'CO_MESOREGIAO_IBGE', 'ST_POLO', 'CO_MUNICIPIO_IBGE_COMPLETO', 'CO_ST_MUNICIPIO',);
	protected $_metadata = array(
			'CO_MUNICIPIO_FNDE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_FNDE', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '6', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'CO_MUNICIPIO_CORREIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_CORREIO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '6', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_IBGE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_IBGE', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '12', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_INSS' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_INSS', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '5', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_SIAFI' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_SIAFI', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '4', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'AN_INCLUSAO_SISTEMA' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'AN_INCLUSAO_SISTEMA', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '4', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_MUNICIPIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'NO_MUNICIPIO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_ANT_MUNICIPIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'NO_ANT_MUNICIPIO', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_CEP_MUNICIPIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'NU_CEP_MUNICIPIO', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '8', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CEP_MUNICIPIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'ST_CEP_MUNICIPIO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CAPITAL_ESTADO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'ST_CAPITAL_ESTADO', 'COLUMN_POSITION' => '11', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CAPITAL_ESPECIAL_PNAE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'ST_CAPITAL_ESPECIAL_PNAE', 'COLUMN_POSITION' => '12', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'SG_UF' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO', 'COLUMN_NAME' => 'SG_UF',
					'COLUMN_POSITION' => '13', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_ABREVIADO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'NO_ABREVIADO', 'COLUMN_POSITION' => '14', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '17', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MICROREGIAO_IBGE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MICROREGIAO_IBGE', 'COLUMN_POSITION' => '15', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '3', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MESOREGIAO_IBGE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MESOREGIAO_IBGE', 'COLUMN_POSITION' => '16', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_POLO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO', 'COLUMN_NAME' => 'ST_POLO',
					'COLUMN_POSITION' => '17', 'DATA_TYPE' => 'CHAR', 'DEFAULT' => 'N', 'NULLABLE' => false,
					'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_IBGE_COMPLETO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_IBGE_COMPLETO', 'COLUMN_POSITION' => '18',
					'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '12',
					'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_ST_MUNICIPIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_MUNICIPIO',
					'COLUMN_NAME' => 'CO_ST_MUNICIPIO', 'COLUMN_POSITION' => '19', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => '1', 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '2',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
