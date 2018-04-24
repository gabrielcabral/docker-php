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
 * @name FormacaoAcademica
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_FormacaoAcademica
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_FormacaoAcademica extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_FORMACAO_ACADEMICA';
	protected $_sequence = 'SFAC_NU_SEQ_FORM_ACAD_SQ';
	protected $_primary = array('NU_SEQ_FORMACAO_ACADEMICA',);
	protected $_cols = array('NU_SEQ_FORMACAO_ACADEMICA', 'DS_FORMACAO_ACADEMICA',);
	protected $_metadata = array(
			'NU_SEQ_FORMACAO_ACADEMICA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_FORMACAO_ACADEMICA',
					'COLUMN_NAME' => 'NU_SEQ_FORMACAO_ACADEMICA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'DS_FORMACAO_ACADEMICA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_FORMACAO_ACADEMICA',
					'COLUMN_NAME' => 'DS_FORMACAO_ACADEMICA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
