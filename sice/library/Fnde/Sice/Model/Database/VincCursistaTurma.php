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
 * @name VincCursistaTurma
 */

/**
 * Classe de Modelo do tipo Database: Fnde_Sice_Model_VincCursistaTurma
 * @uses Fnde_Model_Database_Abstract
 * @version $Id$
 */
abstract class Fnde_Sice_Model_Database_VincCursistaTurma extends Fnde_Model_Database_Abstract
{
    protected $_schema = 'SICE_FNDE';
    protected $_name = 'S_VINC_CURSISTA_TURMA';
    protected $_sequence = 'SICE_FNDE.SVCT_NU_MATRICULA_SQ';
    protected $_primary = array('NU_MATRICULA',);
    protected $_cols = array('NU_SEQ_TURMA', 'NU_SEQ_USUARIO_CURSISTA', 'NU_MATRICULA', 'NU_NOTA_TUTOR',
        'NU_NOTA_CURSISTA', 'NU_SEQ_CRITERIO_AVAL', 'ST_NOTIFICADO', 'NU_CERTIFICADO');
    protected $_metadata = array(
        'NU_SEQ_TURMA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_SEQ_TURMA', 'COLUMN_POSITION' => '1', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
        'NU_SEQ_USUARIO_CURSISTA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_SEQ_USUARIO_CURSISTA', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),

        'NU_CERTIFICADO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_CERTIFICADO', 'COLUMN_POSITION' => '2', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),

        'NU_MATRICULA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_MATRICULA', 'COLUMN_POSITION' => '3', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
            'UNSIGNED' => null, 'PRIMARY' => true, 'PRIMARY_POSITION' => '1', 'IDENTITY' => false,),
        'NU_NOTA_TUTOR' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_NOTA_TUTOR', 'COLUMN_POSITION' => '4', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '2', 'PRECISION' => '3',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
        'NU_NOTA_CURSISTA' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_NOTA_CURSISTA', 'COLUMN_POSITION' => '5', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '2', 'PRECISION' => '3',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
        'NU_SEQ_CRITERIO_AVAL' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'NU_SEQ_CRITERIO_AVAL', 'COLUMN_POSITION' => '6', 'DATA_TYPE' => 'NUMBER',
            'DEFAULT' => null, 'NULLABLE' => true, 'LENGTH' => '22', 'SCALE' => '0', 'PRECISION' => '10',
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),
        'ST_NOTIFICADO' => array('SCHEMA_NAME' => 'SICE_FNDE', 'TABLE_NAME' => 'S_VINC_CURSISTA_TURMA',
            'COLUMN_NAME' => 'ST_NOTIFICADO', 'COLUMN_POSITION' => '7', 'DATA_TYPE' => 'NCHAR',
            'DEFAULT' => null, 'NULLABLE' => false, 'LENGTH' => '2', 'SCALE' => null, 'PRECISION' => null,
            'UNSIGNED' => null, 'PRIMARY' => false, 'PRIMARY_POSITION' => null, 'IDENTITY' => false,),);
}
