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
 * @name TipoEntidade
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_TipoEntidade
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_TipoEntidade extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CORP_FNDE';
	protected $_name = 'S_TIPO_ENTIDADE';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('CO_TP_ENTIDADE',);
	protected $_cols = array('CO_TP_ENTIDADE', 'NO_TP_ENTIDADE', 'ST_CGC_OBRIGATORIO', 'ST_CPF_OBRIGATORIO',
			'ST_DIRIGENTE', 'ST_INSERE', 'DS_TP_ENTIDADE', 'ST_PERMITE_EXCLUSAO', 'ST_ACEITA_MAIS_DIRIGENTE',);
	protected $_metadata = array(
			'CO_TP_ENTIDADE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'CO_TP_ENTIDADE', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NO_TP_ENTIDADE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'NO_TP_ENTIDADE', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CGC_OBRIGATORIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_CGC_OBRIGATORIO', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_CPF_OBRIGATORIO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_CPF_OBRIGATORIO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_DIRIGENTE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_DIRIGENTE', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'CHAR',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_INSERE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_INSERE', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => 'S', 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'DS_TP_ENTIDADE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'DS_TP_ENTIDADE', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '4000', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_PERMITE_EXCLUSAO' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_PERMITE_EXCLUSAO', 'COLUMN_POSITION' => '8', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => 'N', 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'ST_ACEITA_MAIS_DIRIGENTE' => array('SCHEMA_NAME' => 'CORP_FNDE', 'TABLE_NAME' => 'S_TIPO_ENTIDADE',
					'COLUMN_NAME' => 'ST_ACEITA_MAIS_DIRIGENTE', 'COLUMN_POSITION' => '9', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '1', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
