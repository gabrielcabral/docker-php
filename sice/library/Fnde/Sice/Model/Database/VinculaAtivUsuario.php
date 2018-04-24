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
 * @name VinculaAtivUsuario
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VinculaAtivUsuario
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VinculaAtivUsuario extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_VINCULA_ATIV_USUARIO';
	protected $_sequence = 'SICE_FNDE.SVAU_NU_SEQ_VINC_ATIV_USU_SQ';
	protected $_primary = array('NU_SEQ_VINC_ATIV_USU',);
	protected $_cols = array('NU_SEQ_VINC_ATIV_USU', 'NU_SEQ_ATIVIDADE', 'NU_SEQ_USUARIO', 'DS_ATIVIDADE_ALTERNATIVA',);
	protected $_metadata = array(
			'NU_SEQ_VINC_ATIV_USU' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_ATIV_USUARIO',
					'COLUMN_NAME' => 'NU_SEQ_VINC_ATIV_USU', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NU_SEQ_ATIVIDADE' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_ATIV_USUARIO',
					'COLUMN_NAME' => 'NU_SEQ_ATIVIDADE', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_ATIV_USUARIO',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_ATIVIDADE_ALTERNATIVA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINCULA_ATIV_USUARIO',
					'COLUMN_NAME' => 'DS_ATIVIDADE_ALTERNATIVA', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '80', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
