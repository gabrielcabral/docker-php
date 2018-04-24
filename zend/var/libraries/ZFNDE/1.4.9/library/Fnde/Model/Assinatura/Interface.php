<?php

interface Fnde_Model_Assinatura_Interface
{

    public function create($dsLogin, $qtAssinatura, $file, $filename, $mimeType, $tpDocumento, $nuDocumento = '',
                           $coAplicacao = null);

    public function update($nuSeqDocumento, $dsLogin, $file, $filename, $mimeType, $coAplicacao = null);

    public function info($nuSeqDocumento);

    public function getTipoDocumento();
}