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
 * @name HistoricoBolsa
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_HistoricoBolsa
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_HistoricoBolsa extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_HISTORICO_BOLSA';
	protected $_sequence = 'SICE_FNDE.SHSB_NU_SEQ_HIST_BOLSA_SQ';
	protected $_primary = array('NU_SEQ_HISTORICO_BOLSA',);
	protected $_cols = array('NU_SEQ_HISTORICO_BOLSA', 'NU_SEQ_BOLSA', 'NU_SEQ_USUARIO', 'DT_HISTORICO', 'ST_BOLSA',
			'ST_APTIDAO', 'NU_SEQ_JUSTIF_INAPTIDAO', 'NU_SEQ_JUSTIF_CANCELAMENTO', 'NU_SEQ_JUSTIF_DEV_BOLSA',
			'DS_OBSERVACAO',);
	protected $_metadata = array(
			'NU_SEQ_HISTORICO_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_HISTORICO_BOLSA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_BOLSA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_HISTORICO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'DT_HISTORICO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'ST_BOLSA', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_APTIDAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'ST_APTIDAO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_JUSTIF_INAPTIDAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_JUSTIF_INAPTIDAO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_JUSTIF_CANCELAMENTO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_JUSTIF_CANCELAMENTO', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_JUSTIF_DEV_BOLSA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'NU_SEQ_JUSTIF_DEV_BOLSA', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_OBSERVACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_HISTORICO_BOLSA',
					'COLUMN_NAME' => 'DS_OBSERVACAO', 'COLUMN_POSITION' => '10', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '200', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
