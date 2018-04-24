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
 * @name MesoRegiao
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_MesoRegiao
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_MesoRegiao extends Fnde_Model_Database_Abstract {
	protected $_schema = 'CTE_FNDE';
	protected $_name = 'T_MESO_REGIAO';
	//protected $_sequence = 'SEQUENCE_NAME';
	protected $_primary = array('CO_MUNICIPIO_IBGE',);
	protected $_cols = array('CO_UF', 'NO_UF', 'CO_MUNICIPIO_IBGE', 'NO_MUNICIPIO', 'CO_MESO_REGIAO', 'NO_MESO_REGIAO',);
	protected $_metadata = array(
			'CO_UF' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO', 'COLUMN_NAME' => 'CO_UF',
					'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_UF' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO', 'COLUMN_NAME' => 'NO_UF',
					'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'VARCHAR2', 'DEFAULT' => null, 'NULLABLE' => true,
					'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null, 'UNSIGNED' => null, 'PRIMARY' => false,
					'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MUNICIPIO_IBGE' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO',
					'COLUMN_NAME' => 'CO_MUNICIPIO_IBGE', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '7', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
			'NO_MUNICIPIO' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO',
					'COLUMN_NAME' => 'NO_MUNICIPIO', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'CO_MESO_REGIAO' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO',
					'COLUMN_NAME' => 'CO_MESO_REGIAO', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '4', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
			'NO_MESO_REGIAO' => array('SCHEMA_NAME' => 'CTE_FNDE', 'TABLE_NAME' => 'T_MESO_REGIAO',
					'COLUMN_NAME' => 'NO_MESO_REGIAO', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'VARCHAR2',
					'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '50', 'SCALE' => null, 'PRECISION' => null,
					'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
