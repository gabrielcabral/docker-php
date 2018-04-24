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
 * @name DadosEscolaresCursista
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_DadosEscolaresCursista
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_DadosEscolaresCursista extends Fnde_Model_Database_Abstract {
	protected $_schema = 'SICE_FNDE';
	protected $_name = 'S_DADOS_ESCOLARES_CURSISTA';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('NU_SEQ_USUARIO_CURSISTA',);
	protected $_cols = array('NU_SEQ_USUARIO_CURSISTA', 'CO_UF_ESCOLA', 'CO_MUNICIPIO_ESCOLA', 'CO_MESORREGIAO',
			'CO_REDE_ENSINO', 'CO_ESCOLA', 'CO_SEGMENTO',);
	protected $_metadata = array(
			'NU_SEQ_USUARIO_CURSISTA' => array('SCHEMA_NAME' => 'SICE_FNDE',
					'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA', 'COLUMN_NAME' => 'NU_SEQ_USUARIO_CURSISTA',
					'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null, 'NULLABLE' => false,
					'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null, 'PRIMARY' => true,
					'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'CO_UF_ESCOLA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_UF_ESCOLA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_ESCOLA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_MUNICIPIO_ESCOLA', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MESORREGIAO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_MESORREGIAO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_REDE_ENSINO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_REDE_ENSINO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_ESCOLA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_ESCOLA', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER', 'DEFAULT' => null,
					'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10', 'UNSIGNED' => null,
					'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_SEGMENTO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_DADOS_ESCOLARES_CURSISTA',
					'COLUMN_NAME' => 'CO_SEGMENTO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NUMBER',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
