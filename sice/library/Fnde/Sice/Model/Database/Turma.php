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
 * @name Turma
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Turma
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Turma extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_TURMA';
	protected $_sequence = 'SICE_FNDE.STUR_NU_SEQ_TURMA_SQ';
	protected $_primary = array('NU_SEQ_TURMA',);
	protected $_cols = array('NU_SEQ_TURMA', 'NU_SEQ_CURSO', 'NU_SEQ_USUARIO_TUTOR', 'NU_SEQ_USUARIO_ARTICULADOR',
			'DT_INICIO', 'DT_FIM', 'DT_FINALIZACAO', 'UF_TURMA', 'CO_MUNICIPIO', 'CO_MESORREGIAO', 'ST_TURMA',
			'NU_SEQ_CONFIGURACAO', 'ST_APROVACAO_TURMA', 'NU_SEQ_JUSTIFICATIVA_REPROV', 'DS_OBSERVACAO',
			'DT_AVALIACAO',);
	protected $_metadata = array(
			'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_CURSO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO_TUTOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO_TUTOR', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO_ARTICULADOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO_ARTICULADOR', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_INICIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA', 'COLUMN_NAME' => 'DT_INICIO',
					'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_FIM' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA', 'COLUMN_NAME' => 'DT_FIM',
					'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_FINALIZACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'DT_FINALIZACAO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'UF_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA', 'COLUMN_NAME' => 'UF_TURMA',
					'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'CO_MUNICIPIO', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MESORREGIAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'CO_MESORREGIAO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA', 'COLUMN_NAME' => 'ST_TURMA',
					'COLUMN_POSITION' => '11', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO', 'COLUMN_POSITION' => '12', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_APROVACAO_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'ST_APROVACAO_TURMA', 'COLUMN_POSITION' => '13', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_JUSTIFICATIVA_REPROV' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_JUSTIFICATIVA_REPROV', 'COLUMN_POSITION' => '14', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_OBSERVACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'DS_OBSERVACAO', 'COLUMN_POSITION' => '15', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '100', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_AVALIACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TURMA',
					'COLUMN_NAME' => 'DT_AVALIACAO', 'COLUMN_POSITION' => '16', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
