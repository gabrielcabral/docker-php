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
 * @name TipoCurso
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_TipoCurso
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_TipoCurso extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_TIPO_CURSO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_TIPO_CURSO',);
	protected $_cols = array('NU_SEQ_TIPO_CURSO', 'DS_TIPO_CURSO',);
	protected $_metadata = array(
			'NU_SEQ_TIPO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TIPO_CURSO',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_CURSO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_TIPO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_TIPO_CURSO',
					'COLUMN_NAME' => 'DS_TIPO_CURSO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
