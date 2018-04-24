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
 * @name QuantidadeTurmas
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_QuantidadeTurmas
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_QuantidadeTurmas extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_QUANTIDADE_TURMAS';
	protected $_sequence = 'SICE_FNDE.SQTU_NU_SEQ_QUANT_TURMA_SQ';
	protected $_primary = array('NU_SEQ_QUANTIDADE_TURMA',);
	protected $_cols = array('NU_SEQ_QUANTIDADE_TURMA', 'CO_MESORREGIAO', 'QT_TURMAS', 'SG_REGIAO',
			'NU_SEQ_CONFIGURACAO',);
	protected $_metadata = array(
			'NU_SEQ_QUANTIDADE_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_QUANTIDADE_TURMAS',
					'COLUMN_NAME' => 'NU_SEQ_QUANTIDADE_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'CO_MESORREGIAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_QUANTIDADE_TURMAS',
					'COLUMN_NAME' => 'CO_MESORREGIAO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'QT_TURMAS' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_QUANTIDADE_TURMAS',
					'COLUMN_NAME' => 'QT_TURMAS', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '3', 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'SG_REGIAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_QUANTIDADE_TURMAS',
					'COLUMN_NAME' => 'SG_REGIAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_QUANTIDADE_TURMAS',
					'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
