<?php

/**
 * Interface para consumir o WS-CAStor
 * 
 * @category Fnde
 * @package Fnde_Model
 * @subpackage Castor 
 * @author Alberto Guimarães Viana <alberto.viana@fnde.gov.br>
 */
interface Fnde_Model_Castor_Interface
{

    public function write($dsLogin, $arquivo, $nomeArquivo, $extensao, $hashFile = null, $sgAplicacao = null,
                          $nuSeqArquivoMaster = 0, $eof = false);

    public function view($nuSeqArquivo, $coAplicacao = null);

    public function delete($nuSeqArquivo, $coAplicacao = null);

    public function info($nuSeqArquivo);
}