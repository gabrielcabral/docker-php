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
 * @name CriterioAvaliacao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_CriterioAvaliacao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_CriterioAvaliacao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_CRITERIO_AVALIACAO';
	protected $_sequence = 'SICE_FNDE.SCAV_NU_SEQ_CRITERIO_AVAL_SQ';
	protected $_primary = array('NU_SEQ_CRITERIO_AVAL',);
	protected $_cols = array('DS_SITUACAO', 'DS_CRITERIO_AVALIACAO', 'NU_SEQ_CRITERIO_AVAL', 'NU_SEQ_CONFIGURACAO',);
	protected $_metadata = array(
			'DS_SITUACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CRITERIO_AVALIACAO',
					'COLUMN_NAME' => 'DS_SITUACAO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '30', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_CRITERIO_AVALIACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CRITERIO_AVALIACAO',
					'COLUMN_NAME' => 'DS_CRITERIO_AVALIACAO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '30', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_CRITERIO_AVAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CRITERIO_AVALIACAO',
					'COLUMN_NAME' => 'NU_SEQ_CRITERIO_AVAL', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_CRITERIO_AVALIACAO',
					'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
