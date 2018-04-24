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
 * @name Ocupacao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Ocupacao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Ocupacao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_OCUPACAO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_OCUPACAO',);
	protected $_cols = array('NU_SEQ_OCUPACAO', 'DS_OCUPACAO',);
	protected $_metadata = array(
			'NU_SEQ_OCUPACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_OCUPACAO',
					'COLUMN_NAME' => 'NU_SEQ_OCUPACAO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_OCUPACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_OCUPACAO',
					'COLUMN_NAME' => 'DS_OCUPACAO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '60', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
