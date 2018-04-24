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
 * @name JustifCancelamento
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_JustifCancelamento
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_JustifCancelamento extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_JUSTIF_CANCELAMENTO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_JUSTIF_CANCELAMENTO',);
	protected $_cols = array('NU_SEQ_JUSTIF_CANCELAMENTO', 'DS_JUSTIF_CANCELAMENTO',);
	protected $_metadata = array(
			'NU_SEQ_JUSTIF_CANCELAMENTO' => array('SCHEMA_NAME' => 'SICE_FNDE',
					'TABLE_NAME' => 'S_JUSTIF_CANCELAMENTO', 'COLUMN_NAME' => 'NU_SEQ_JUSTIF_CANCELAMENTO',
					'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null, 'PRIMARY' => true,
					'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_JUSTIF_CANCELAMENTO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_JUSTIF_CANCELAMENTO',
					'COLUMN_NAME' => 'DS_JUSTIF_CANCELAMENTO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '100', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
