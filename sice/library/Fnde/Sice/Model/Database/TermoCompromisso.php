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
 * @name TermoCompromisso
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_TermoCompromisso
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_TermoCompromisso extends Fnde_Model_Database_Abstract
{
    protected $_schema   = 'SICE_FNDE';
    protected $_name     = 'S_TERMO_COMPROMISSO';
    protected $_sequence = 'SICE_FNDE.STEC_NU_SEQ_TERMO_COMPROMIS_SQ';
    protected $_primary  = array(
        'NU_SEQ_TERMO_COMPROMISSO',
        );
    protected $_cols     = array(
        'NU_SEQ_TERMO_COMPROMISSO',
        'NU_SEQ_USUARIO',
        'NU_SEQ_TIPO_PERFIL',
        'NU_ANO',
        'DT_INICIO',
        'DT_FIM',
        'CO_ACORDO',
        'CO_ACAO',
        );
    protected $_metadata = array(
        'NU_SEQ_TERMO_COMPROMISSO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'NU_SEQ_TERMO_COMPROMISSO',
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
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
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
        'NU_SEQ_TIPO_PERFIL' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'NU_SEQ_TIPO_PERFIL',
            'COLUMN_POSITION' => '3',
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
        'NU_ANO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'NU_ANO',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '22',
            'SCALE' => '0',
            'PRECISION' => '4',
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'DT_INICIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'DT_INICIO',
            'COLUMN_POSITION' => '5',
            'DATA_TYPE' => 'DATE',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '7',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'DT_FIM' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'DT_FIM',
            'COLUMN_POSITION' => '6',
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
        'CO_ACORDO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'CO_ACORDO',
            'COLUMN_POSITION' => '7',
            'DATA_TYPE' => 'CHAR',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '1',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'CO_ACAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_TERMO_COMPROMISSO',
            'COLUMN_NAME' => 'CO_ACAO',
            'COLUMN_POSITION' => '8',
            'DATA_TYPE' => 'CHAR',
            'DEFAULT' => null,
            'NULLABLE' => false,
            'LENGTH' => '2',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        );
}