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
 * @name VincFormAcadUsu
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VincFormAcadUsu
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VincFormAcadUsu extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_VINC_FORM_ACAD_USU';
	protected $_sequence = 'SICE_FNDE.SVFR_NU_SEQ_VINC_FORM_ACAD_SQ';
	protected $_primary = array('NU_SEQ_VINC_FORM_ACAD_USU',);
	protected $_cols = array('NU_SEQ_VINC_FORM_ACAD_USU', 'TP_ESCOLARIDADE', 'TP_INSTITUICAO', 'NO_INSTITUICAO',
			'NO_CURSO', 'DT_CONCLUSAO', 'NU_SEQ_USUARIO', 'NU_SEQ_FORMACAO_ACADEMICA',);
	protected $_metadata = array(
			'NU_SEQ_VINC_FORM_ACAD_USU' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'NU_SEQ_VINC_FORM_ACAD_USU', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'TP_ESCOLARIDADE' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'TP_ESCOLARIDADE', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '2',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'TP_INSTITUICAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'TP_INSTITUICAO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '2',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_INSTITUICAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'NO_INSTITUICAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '80', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_CURSO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'NO_CURSO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '80', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DT_CONCLUSAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'DT_CONCLUSAO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'DATE',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_USUARIO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'NU_SEQ_USUARIO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NU_SEQ_FORMACAO_ACADEMICA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_FORM_ACAD_USU',
					'COLUMN_NAME' => 'NU_SEQ_FORMACAO_ACADEMICA', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
