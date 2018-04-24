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
 * @name AvaliacaoTurma
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_AvaliacaoTurma
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_AvaliacaoTurma extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_AVALIACAO_TURMA';
	protected $_sequence = 'SICE_FNDE.SVLT_NU_SEQ_AVALIACAO_TUR_SQ';
	protected $_primary = array('NU_SEQ_AVALIACAO_TURMA',);
	protected $_cols = array('NU_SEQ_AVALIACAO_TURMA', 'NU_SEQ_TURMA', 'ST_APROVACAO', 'NU_SEQ_JUSTIF_REPROV',
			'DS_OBSERVACAO', 'DT_AVALIACAO', 'NU_SEQ_USUARIO_AVALIADOR', 'NU_SEQ_BOLSA',);
	protected $_metadata = array(
			'NU_SEQ_AVALIACAO_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_AVALIACAO_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_APROVACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'ST_APROVACAO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_JUSTIF_REPROV' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_JUSTIF_REPROV', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_OBSERVACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'DS_OBSERVACAO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '100', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_AVALIACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'DT_AVALIACAO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO_AVALIADOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO_AVALIADOR', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_TURMA',
					'COLUMN_NAME' => 'NU_SEQ_BOLSA', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
