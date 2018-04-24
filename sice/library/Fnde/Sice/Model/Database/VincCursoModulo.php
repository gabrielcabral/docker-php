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
 * @name VincCursoModulo
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VincCursoModulo
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VincCursoModulo extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_VINC_CURSO_MODULO';
	//protected $_sequence = 'SVCM_NU_SEQ_VINC_CURSO_MOD_SQ';
	protected $_primary = array('NU_SEQ_CURSO', 'NU_SEQ_MODULO',);
	protected $_cols = array('NU_SEQ_CURSO', 'NU_SEQ_MODULO',);
	protected $_metadata = array(
			'NU_SEQ_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSO_MODULO',
					'COLUMN_NAME' => 'NU_SEQ_CURSO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_MODULO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSO_MODULO',
					'COLUMN_NAME' => 'NU_SEQ_MODULO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '2', 'IDENTITY' => false,),);
}
