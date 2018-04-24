<?php
/**
 * Arquivo de classe de modelo do tipo: database
 *
 * Criado automaticamente pelo gerador: ZFnde Model.
 *
 * $Rev::                      $
 * $Date::  12/04/2013         $
 * $Author::                   $
 *
 * @package Sice
 * @category Model
 * @name ParametroCertificado
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_ParametroCertificado
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_ParametroCertificado extends Fnde_Model_Database_Abstract
{
    protected $_schema   = 'SICE_FNDE';
    protected $_name     = 'S_PARAMETRO_CERTIFICADO';
    protected $_sequence = 'SICE_FNDE.SPAC_NU_SEQ_PARAM_CERT_SQ';
    protected $_primary  = array(
        'NU_SEQ_PARAM_CERT',
        );
    public $_cols     = array(
        'NU_SEQ_PARAM_CERT',
        'NO_SECRETARIO',
        'NO_CARGO',
        'NO_LOCAL_ATUACAO',
        'DT_INICIO',
        'DT_FIM',
        'NU_SEQ_LOGOMARCA_CASTOR',
        'DS_FRASE_EFEITO',
        'NU_SEQ_USUARIO_ATUALIZADOR'
    );

    protected $_metadata = array(
        'NU_SEQ_PARAM_CERT' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NU_SEQ_PARAM_CERT',
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
        'NO_SECRETARIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NO_SECRETARIO',
            'COLUMN_POSITION' => '2',
            'DATA_TYPE' => 'VARCHAR2',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '100',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'NO_CARGO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NO_CARGO',
            'COLUMN_POSITION' => '3',
            'DATA_TYPE' => 'VARCHAR2',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '100',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'NO_LOCAL_ATUACAO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NO_LOCAL_ATUACAO',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'VARCHAR2',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '100',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
            ),
        'DT_INICIO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'DT_INICIO',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'DATE',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '7',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,),
        'DT_FIM' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'DT_FIM',
            'COLUMN_POSITION' => '4',
            'DATA_TYPE' => 'DATE',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '7',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,),
        'NU_SEQ_LOGOMARCA_CASTOR' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NU_SEQ_LOGOMARCA_CASTOR',
            'COLUMN_POSITION' => '7',
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
        'DS_FRASE_EFEITO' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'DS_FRASE_EFEITO',
            'COLUMN_POSITION' => '8',
            'DATA_TYPE' => 'VARCHAR2',
            'DEFAULT' => null,
            'NULLABLE' => true,
            'LENGTH' => '100',
            'SCALE' => null,
            'PRECISION' => null,
            'UNSIGNED' => null,
            'PRIMARY' => false,
            'PRIMARY_POSITION' => null,
            'IDENTITY' => false,
        ),
        'NU_SEQ_USUARIO_ATUALIZADOR' => array(
            'SCHEMA_NAME' => 'SICE_FNDE',
            'TABLE_NAME' => 'S_PARAMETRO_CERTIFICADO',
            'COLUMN_NAME' => 'NU_SEQ_USUARIO_ATUALIZADOR',
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
        )
        );

}