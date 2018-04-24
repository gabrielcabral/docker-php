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
 * @name Bolsa
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_Bolsa
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_Bolsa extends Fnde_Model_Database_Abstract {
    protected $_schema   = 'SICE_FNDE';
    protected $_name     = 'S_BOLSA';
    protected $_sequence = 'SICE_FNDE.SBLS_NU_SEQ_BOLSA_SQ';
    protected $_primary  = array(
        'NU_SEQ_BOLSA',
        );
    protected $_cols     = array(
        'NU_SEQ_BOLSA',
        'NU_SEQ_USUARIO',
        'NU_SEQ_JUSTIF_INAPTIDAO',
        'ST_APTIDAO',
        'DS_OBSERVACAO_INAPTIDAO',
        'NU_SEQ_USUARIO_AVALIADOR',
        'ST_BOLSA',
        'NU_SEQ_PERIODO_VINCULACAO',
        'DT_FINALIZACAO_TURMA',
        'NU_SEQ_CONFIGURACAO',
        );
    protected $_metadata = array(
        'NU_SEQ_BOLSA' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_BOLSA',
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
        'NU_SEQ_USUARIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_USUARIO',
            'COLUMN_POSITION' => '2',
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
        'NU_SEQ_JUSTIF_INAPTIDAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_JUSTIF_INAPTIDAO',
            'COLUMN_POSITION' => '3',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '10',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'ST_APTIDAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'ST_APTIDAO',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'CHAR',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '1',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'DS_OBSERVACAO_INAPTIDAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'DS_OBSERVACAO_INAPTIDAO',
            'COLUMN_POSITION' => '5',
            'DATA_TYPE' => 'VARCHAR2',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '2000',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'NU_SEQ_USUARIO_AVALIADOR' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_USUARIO_AVALIADOR',
            'COLUMN_POSITION' => '6',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '10',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'ST_BOLSA' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'ST_BOLSA',
            'COLUMN_POSITION' => '7',
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
        'NU_SEQ_PERIODO_VINCULACAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_PERIODO_VINCULACAO',
            'COLUMN_POSITION' => '8',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '10',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'DT_FINALIZACAO_TURMA' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'DT_FINALIZACAO_TURMA',
            'COLUMN_POSITION' => '9',
            'DATA_TYPE' => 'DATE',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '7',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'NU_SEQ_CONFIGURACAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_BOLSA',
            'COLUMN_NAME' => 'NU_SEQ_CONFIGURACAO',
            'COLUMN_POSITION' => '10',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => true,
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