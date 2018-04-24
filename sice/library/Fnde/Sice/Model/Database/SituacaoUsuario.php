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
 * @name SituacaoUsuario
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_SituacaoUsuario
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_SituacaoUsuario extends Fnde_Model_Database_Abstract
{
    protected $_schema   = 'SICE_FNDE';
    protected $_name     = 'H_SITUACAO_USUARIO';
    protected $_sequence = 'SICE_FNDE.HSUS_NU_SEQ_HIST_SITUACAO_SQ';
    protected $_primary  = array(
        'NU_SEQ_HIST_SITUACAO',
        );
    protected $_cols     = array(
        'NU_SEQ_HIST_SITUACAO',
        'NU_SEQ_USUARIO',
        'ST_USUARIO',
        'NU_SEQ_USUARIO_RESPONSAVEL',
        'DT_INICIO',
        'DT_FIM',
        );
    protected $_metadata = array(
        'NU_SEQ_HIST_SITUACAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
            'COLUMN_NAME' => 'NU_SEQ_HIST_SITUACAO',
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
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
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
        'ST_USUARIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
            'COLUMN_NAME' => 'ST_USUARIO',
            'COLUMN_POSITION' => '3',
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
        'NU_SEQ_USUARIO_RESPONSAVEL' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
            'COLUMN_NAME' => 'NU_SEQ_USUARIO_RESPONSAVEL',
            'COLUMN_POSITION' => '4',
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
        'DT_INICIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
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
            'TABLE_NAME' => 'H_SITUACAO_USUARIO',
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
        );
}