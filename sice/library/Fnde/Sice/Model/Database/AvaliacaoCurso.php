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
 * @name AvaliacaoCurso
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_AvaliacaoCurso
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_AvaliacaoCurso extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_AVALIACAO_CURSO';
	protected $_sequence = 'SICE_FNDE.SCUR_NU_SEQ_AVALIACAO_CURSO_SQ';
	protected $_primary = array('NU_SEQ_AVALIACAO_CURSO',);
	protected $_cols = array('NU_SEQ_AVALIACAO_CURSO', 'NU_SEQ_TURMA', 'NU_SEQ_USUARIO', 'NU_QUESTAO_1',
			'NU_QUESTAO_2', 'NU_QUESTAO_3', 'NU_QUESTAO_4', 'NU_QUESTAO_5', 'NU_QUESTAO_6', 'NU_QUESTAO_7',
			'NU_QUESTAO_8', 'NU_QUESTAO_9', 'NU_QUESTAO_10',);
	protected $_metadata = array(
			'NU_SEQ_AVALIACAO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_AVALIACAO_CURSO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_1' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_1', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_2' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_2', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_3' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_3', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_4' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_4', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_5' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_5', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_6' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_6', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_7' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_7', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_8' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_8', 'COLUMN_POSITION' => '11', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_9' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_9', 'COLUMN_POSITION' => '12', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_QUESTAO_10' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_AVALIACAO_CURSO',
					'COLUMN_NAME' => 'NU_QUESTAO_10', 'COLUMN_POSITION' => '13', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '1',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
