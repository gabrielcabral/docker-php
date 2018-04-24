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
 * @name HistoricoTurma
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_HistoricoTurma
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_HistoricoTurma extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_HISTORICO_TURMA';
	protected $_sequence = 'SICE_FNDE.SHTR_NU_SEQ_HIST_TURMA_SQ';
	protected $_primary = array('NU_SEQ_HISTORICO_TURMA',);
	protected $_cols = array('NU_SEQ_HISTORICO_TURMA', 'NU_SEQ_TURMA', 'ST_TURMA', 'DT_HISTORICO', 'ID_AUTOR',
			'CO_MOTIVO_ALTERACAO', 'DS_OBSERVACAO',);
	protected $_metadata = array(
			'NU_SEQ_HISTORICO_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_HISTORICO_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'ST_TURMA', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null,
					'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_HISTORICO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'DT_HISTORICO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ID_AUTOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'ID_AUTOR', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null,
					'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MOTIVO_ALTERACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'CO_MOTIVO_ALTERACAO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_OBSERVACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_TURMA',
					'COLUMN_NAME' => 'DS_OBSERVACAO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1000', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
