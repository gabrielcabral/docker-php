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
 * @name Modulo
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Modulo
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Modulo extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_MODULO';
	protected $_sequence = 'SICE_FNDE.SMOD_NU_SEQ_MODULO_SQ';
	protected $_primary = array('NU_SEQ_MODULO',);
	protected $_cols = array('NU_SEQ_MODULO', 'NU_SEQ_TIPO_CURSO', 'DS_SIGLA_MODULO', 'DS_NOME_MODULO', 'ST_MODULO',
			'VL_CARGA_HORARIA', 'VL_CARGA_PRESENCIAL', 'VL_CARGA_DISTANCIA', 'DS_PREREQUISITO_MODULO',
			'NU_SEQ_MODULO_PREREQUISITO', 'VL_MIN_CONCLUSAO', 'VL_MAX_CONCLUSAO', 'DS_CONTEUDO_PROGRAMATICO',);
	protected $_metadata = array(
			'NU_SEQ_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'NU_SEQ_MODULO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_TIPO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_CURSO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_SIGLA_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'DS_SIGLA_MODULO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '15', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_NOME_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'DS_NOME_MODULO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '150', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'ST_MODULO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'CHAR', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_CARGA_HORARIA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'VL_CARGA_HORARIA', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_CARGA_PRESENCIAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'VL_CARGA_PRESENCIAL', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_CARGA_DISTANCIA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'VL_CARGA_DISTANCIA', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_PREREQUISITO_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'DS_PREREQUISITO_MODULO', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_MODULO_PREREQUISITO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'NU_SEQ_MODULO_PREREQUISITO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_MIN_CONCLUSAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'VL_MIN_CONCLUSAO', 'COLUMN_POSITION' => '11', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_MAX_CONCLUSAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'VL_MAX_CONCLUSAO', 'COLUMN_POSITION' => '12', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_CONTEUDO_PROGRAMATICO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_MODULO',
					'COLUMN_NAME' => 'DS_CONTEUDO_PROGRAMATICO', 'COLUMN_POSITION' => '13', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '3700', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
