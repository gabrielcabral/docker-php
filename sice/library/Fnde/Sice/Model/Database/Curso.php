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
 * @name Curso
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Curso
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Curso extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_CURSO';
	protected $_sequence = 'SICE_FNDE.SCUR_NU_SEQ_CURSO_SQ';
	protected $_primary = array('NU_SEQ_CURSO',);
	protected $_cols = array('NU_SEQ_CURSO', 'NU_SEQ_TIPO_CURSO', 'DS_SIGLA_CURSO', 'DS_NOME_CURSO',
			'VL_CARGA_HORARIA', 'QT_MODULOS', 'ST_CURSO', 'DS_PREREQUISITO_CURSO', 'NU_SEQ_CURSO_PREREQUISITO',
			'DS_OBJETIVO_CURSO',);
	protected $_metadata = array(
			'NU_SEQ_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_CURSO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_TIPO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_CURSO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_SIGLA_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'DS_SIGLA_CURSO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '15', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_NOME_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'DS_NOME_CURSO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '150', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'VL_CARGA_HORARIA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'VL_CARGA_HORARIA', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'QT_MODULOS' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'QT_MODULOS', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO', 'COLUMN_NAME' => 'ST_CURSO',
					'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'CHAR', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_PREREQUISITO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'DS_PREREQUISITO_CURSO', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_CURSO_PREREQUISITO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_CURSO_PREREQUISITO', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_OBJETIVO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CURSO',
					'COLUMN_NAME' => 'DS_OBJETIVO_CURSO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '3700', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
