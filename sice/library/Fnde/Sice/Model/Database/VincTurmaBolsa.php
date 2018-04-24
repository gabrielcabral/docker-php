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
 * @name VincTurmaBolsa
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VincTurmaBolsa
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VincTurmaBolsa extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'SICE_FNDE.S_VINC_TURMA_BOLSA';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_TURMA', 'NU_SEQ_BOLSA',);
	protected $_cols = array('NU_SEQ_TURMA', 'NU_SEQ_BOLSA',);
	protected $_metadata = array(
			'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_TURMA_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_TURMA_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_BOLSA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '2', 'IDENTITY' => false,),);
}
