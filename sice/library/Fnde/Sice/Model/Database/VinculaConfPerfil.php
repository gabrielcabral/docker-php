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
 * @name VinculaConfPerfil
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VinculaConfPerfil
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VinculaConfPerfil extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_VINCULA_CONF_PERFIL';
	protected $_sequence = 'SICE_FNDE.SVCP_NU_SEQ_VINC_CONF_PERF_SQ';
	protected $_primary = array('NU_SEQ_VINC_CONF_PERF',);
	protected $_cols = array('NU_SEQ_VINC_CONF_PERF', 'QT_BOLSA_PERIODO', 'NU_SEQ_TIPO_PERFIL', 'NU_SEQ_CONFIGURACAO',);
	protected $_metadata = array(
			'NU_SEQ_VINC_CONF_PERF' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_CONF_PERFIL',
					'COLUMN_NAME' => 'NU_SEQ_VINC_CONF_PERF', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'QT_BOLSA_PERIODO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_CONF_PERFIL',
					'COLUMN_NAME' => 'QT_BOLSA_PERIODO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '5',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_TIPO_PERFIL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_CONF_PERFIL',
					'COLUMN_NAME' => 'NU_SEQ_TIPO_PERFIL', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_CONFIGURACAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_CONF_PERFIL',
					'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
