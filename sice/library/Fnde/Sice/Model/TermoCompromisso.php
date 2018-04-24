<?php
/**
 * Arquivo de classe de modelo do tipo: database
 * 
 * Gerado automaticamente pelo gerador: ZFnde Model.
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
 * Classe de Modelo: Fnde_Sice_Model_TermoCompromisso
 * @uses Fnde_Sice_Model_Database_TermoCompromisso
 * @version $Id$
 */
class Fnde_Sice_Model_TermoCompromisso extends Fnde_Sice_Model_Database_TermoCompromisso
{
    const CO_ACORDO_CONCORDO = 'C';
    const CO_ACORDO_NAOCONCORDO = 'N';

    const CO_ACAO_COMBOLSA = 'CB';
    const CO_ACAO_SEMBOLSA = 'SB';
    const CO_ACAO_VIEW = 'AV';

    static public $arrPerfis = array('sice_tutor', 'sice_articulador', 'sice_coord_executivo_estadual');

    static public $arrAcao = array(
        self::CO_ACAO_COMBOLSA => 'Recebe Bolsa',
        self::CO_ACAO_SEMBOLSA => 'Não Recebe Bolsa',
        self::CO_ACAO_VIEW => 'Permissão de Visualizar'
    );

    static public $arrAcordo = array(
        self::CO_ACORDO_CONCORDO => 'Concordo',
        self::CO_ACORDO_NAOCONCORDO => 'Não Concordo'
    );
}
