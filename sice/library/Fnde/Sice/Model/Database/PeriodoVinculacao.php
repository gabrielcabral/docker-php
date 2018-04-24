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
 * @name PeriodoVinculacao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_PeriodoVinculacao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_PeriodoVinculacao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_PERIODO_VINCULACAO';
	protected $_sequence = 'SICE_FNDE.SPRV_NU_SEQ_PERIODO_VINC_SQ';
	protected $_primary = array('NU_SEQ_PERIODO_VINCULACAO',);
	protected $_cols = array('NU_SEQ_PERIODO_VINCULACAO', 'VL_EXERCICIO', 'DT_INICIAL', 'DT_FINAL', 'DT_INCLUSAO',
			'NU_SEQ_TIPO_PERFIL',);
	protected $_metadata = array(
			'NU_SEQ_PERIODO_VINCULACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'NU_SEQ_PERIODO_VINCULACAO', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'VL_EXERCICIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'VL_EXERCICIO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '4',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_INICIAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'DT_INICIAL', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_FINAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'DT_FINAL', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_INCLUSAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'DT_INCLUSAO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'DATE', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_TIPO_PERFIL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_PERIODO_VINCULACAO',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_PERFIL', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
