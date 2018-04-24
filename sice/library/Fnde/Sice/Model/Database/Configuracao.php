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
 * @name Configuracao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Configuracao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Configuracao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_CONFIGURACAO';
	protected $_sequence = 'SICE_FNDE.SCON_NU_SEQ_CONFIGURACAO_SQ';
	protected $_primary = array('NU_SEQ_CONFIGURACAO',);
	protected $_cols = array('NU_SEQ_CONFIGURACAO', 'DT_INI_VIGENCIA', 'DT_TERMINO_VIGENCIA', 'DT_INICIO_NOVA_CONFIG',
			'DT_TERMINO_NOVA_CONFIG', 'QT_TURMA_TUTOR', 'QT_ALUNOS_TURMA', 'NU_SEQ_TIPO_CURSO', 'DT_INCLUSAO',
			'DT_ALTERACAO', 'ST_CONFIGURACAO',);
	protected $_metadata = array(
			'NU_SEQ_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DT_INI_VIGENCIA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_INI_VIGENCIA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_TERMINO_VIGENCIA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_TERMINO_VIGENCIA', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_INICIO_NOVA_CONFIG' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_INICIO_NOVA_CONFIG', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_TERMINO_NOVA_CONFIG' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_TERMINO_NOVA_CONFIG', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'QT_TURMA_TUTOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'QT_TURMA_TUTOR', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '5',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'QT_ALUNOS_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'QT_ALUNOS_TURMA', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '5',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_TIPO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_CURSO', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_INCLUSAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_INCLUSAO', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_ALTERACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'DT_ALTERACAO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CONFIGURACAO',
					'COLUMN_NAME' => 'ST_CONFIGURACAO', 'COLUMN_POSITION' => '11', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
