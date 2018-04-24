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
 * @name ValorBolsaPerfil
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_ValorBolsaPerfil
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_ValorBolsaPerfil extends Fnde_Model_Database_Abstract
{
    protected $_schema   = 'SICE_FNDE';
    protected $_name     = 'S_VALOR_BOLSA_PERFIL';
    protected $_sequence = 'SICE_FNDE.SVBP_NU_SEQ_VAL_BOLSA_PERF_SQ';
    protected $_primary  = array(
        'NU_SEQ_VAL_BOLSA_PERF',
        );
    protected $_cols     = array(
        'NU_SEQ_VAL_BOLSA_PERF',
        'QT_TURMA',
        'VL_BOLSA',
        'NU_SEQ_VINC_CONF_PERF',
        );
    protected $_metadata = array(
        'NU_SEQ_VAL_BOLSA_PERF' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_VALOR_BOLSA_PERFIL',
            'COLUMN_NAME' => 'NU_SEQ_VAL_BOLSA_PERF',
            'COLUMN_POSITION' => '1',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '10',
            'UNSIGNED' => null,
            'PRIMARY' => true,
            'PRIMARY_POSITION' => '1',
            'IDENTITY' => false,
            ),
        'QT_TURMA' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_VALOR_BOLSA_PERFIL',
            'COLUMN_NAME' => 'QT_TURMA',
            'COLUMN_POSITION' => '2',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '5',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'VL_BOLSA' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_VALOR_BOLSA_PERFIL',
            'COLUMN_NAME' => 'VL_BOLSA',
            'COLUMN_POSITION' => '3',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '22',
            'SCALE' => '2',
            'PRECISION' => '6',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'NU_SEQ_VINC_CONF_PERF' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_VALOR_BOLSA_PERFIL',
            'COLUMN_NAME' => 'NU_SEQ_VINC_CONF_PERF',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '10',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        );
}