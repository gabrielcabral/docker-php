<?php

class Relatorios_SituacaodebolsasController extends Fnde_Sice_Controller_Action
{
    /**
     * @param $form SituacaoDeBolsas_FormFilter
     * @param $param array
     */
    private function setForm(&$form, &$param){
        // AUTH
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        if (
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario)
        ) {
            $rsUf = Fnde_Sice_Business_Componentes::getAllByTable("Uf", array("SG_UF", "SG_UF"));
        } else {
            $rsUf = Fnde_Sice_Business_Componentes::getAllByTable(
                "Uf",
                array("SG_UF", "SG_UF"),
                array("stWhere" => "sg_uf = '{$arUsuario['SG_UF_ATUACAO_PERFIL']}'")
            );
        }

        if($param['UF_TURMA']) {
            if (in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)) {
                $rsMunicipio = Fnde_Sice_Business_Componentes::getAllByTable(
                    "Municipio",
                    array("CO_MUNICIPIO_IBGE", "NO_MUNICIPIO"),
                    array("stWhere" => sprintf("CO_MUNICIPIO_IBGE = '%s'", $arUsuario['CO_MUNICIPIO_PERFIL']))
                );
            } else {
                $rsMunicipio = Fnde_Sice_Business_Componentes::getAllByTable(
                    "Municipio",
                    array("CO_MUNICIPIO_IBGE", "NO_MUNICIPIO"),
                    array("stWhere" => sprintf("SG_UF = '%s'", $param['UF_TURMA']))
                );
            }
        }else{
            $rsMunicipio = array();
        }

        if($param['CO_MESORREGIAO']){
            $rsMessoregiao = Fnde_Sice_Business_Componentes::getAllByTable(
                "MesoRegiao",
                array("CO_MESO_REGIAO", "NO_MESO_REGIAO"),
                array("stWhere" => sprintf("CO_MESO_REGIAO = '%s'", $param['CO_MESORREGIAO']))
            );
        }else {
            $rsMessoregiao = array();
        }

        $buss = new Fnde_Sice_Business_Regiao();
        $resultRegiao = $buss->search(array('SG_REGIAO', 'NO_REGIAO'));
        $form->setRegiao($resultRegiao, $param['SG_REGIAO']);

        $rsTipoCurso = Fnde_Sice_Business_Componentes::getAllByTable("TipoCurso", array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

        $perfilRetorno = array();
        $rsPerfil = Fnde_Sice_Business_Componentes::getAllByTable("TipoPerfil", array("NU_SEQ_TIPO_PERFIL", "DS_TIPO_PERFIL"));
        if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ) {
            $perfilRetorno[5] = $rsPerfil[5]; //Articulador
            $perfilRetorno[8] = $rsPerfil[8]; //Coord. Exec.
            $perfilRetorno[6] = $rsPerfil[6]; //Tut.
        } else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ) {
            $perfilRetorno[5] = $rsPerfil[5]; //Articulador
            $perfilRetorno[6] = $rsPerfil[6]; //Tut.
        } else if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
            $perfilRetorno[6] = $rsPerfil[6]; //Tut.
        }

        // FORM
        $form->setUf($rsUf, $param['UF_TURMA']);
        $form->setMunicipio($rsMunicipio, $param['CO_MUNICIPIO']);
        $form->setMesorregiao($rsMessoregiao, $param['CO_MESORREGIAO']);
        $form->setCpf($param['NU_CPF']);
        $form->setAnoReferencia($param['NU_ANO']);
        $form->setMesReferencia($param['NU_MES']);
        $form->setNomeCursista($param['NO_CURSISTA']);
        $form->setTipoCurso($rsTipoCurso);
        $form->setPerfil($perfilRetorno);
    }

    /**
     * @return void
     */
    public function situacaodebolsasAction(){
        $this->setTitle('Situação das Bolsas');
        $this->setSubtitle('Exportar');

        $form = new SituacaoDeBolsas_FormFilter();
        $param = $this->_getAllParams();

        // FORM COMBOS
        $this->setForm($form, $param);

        $this->view->form = $form;

        // GRID
        if($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {

                // se for uma solicitação de excel, faz o encaminhamento para a outra action
                if($param['excel']) {
                    return $this->_forward("situacaodebolsasexcel", "situacaodebolsas", "relatorios");
                }

                $modelBolsa = new Fnde_Sice_Model_Bolsa($param);
                $rsBolsa = $modelBolsa->retornaQuerySituacaoBolsa($param);

                $this->mergeSGB($rsBolsa);

                if (count($rsBolsa)) {
                    foreach ($rsBolsa as $i => $col) {
                        foreach ($col as $item => $value) {
                            if (!in_array($item, array(
                                'NO_REGIAO', 'NO_MESO_REGIAO', 'SG_UF',
                                'NO_MUNICIPIO', 'NU_ANO', 'NU_MES',
                                'DS_TIPO_CURSO', 'DS_TIPO_PERFIL', 'NU_CPF',
                                'NO_USUARIO', 'VL_BOLSA', 'QT_BOLSAS', 'DS_SITUACAO_BOLSA',
                            ))
                            ) {
                                unset($rsBolsa[$i][$item]);
                            }
                        }
                        $rsBolsa[$i]['NU_CPF'] = $this->mascara($rsBolsa[$i]['NU_CPF'],'###.###.###-##');
                    }
                }

                $grid = new Fnde_View_Helper_DataTables();
                $grid->setAutoCallJs(true);
                $grid->setData($rsBolsa);
                $grid->setHeader(array(
                    'Região',
                    'Mesorregião',
                    'UF',
                    'Município',
                    'Ano Ref.',
                    'Mês Ref.',
                    'Nome do Curso',
                    'Perfil',
                    'CPF',
                    'Nome',
                    'Qtd. Bolsas',
                    'Valor',
                    'Situação',
                ));
                $grid->setHeaderActive(false);
                $grid->setTitle("Situação das Bolsas");
                $grid->setId('NU_CPF');
                $grid->setTableAttribs(array('id' => 'edit'));

                $this->view->grid = $grid;
            }else{
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
            }
        }
    }

    public function situacaodebolsasexcelAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        $modelBolsa = new Fnde_Sice_Model_Bolsa();

        $param = $this->_getAllParams();

        if(!$this->_request->isPost()){
            $this->_redirect('relatorios/situacaodebolsas/situacaodebolsas');
            exit;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="situacao_de_bolsas.xls"');
        set_time_limit(0);
        ini_set("memory_limit", "8000M");

        // CONSULTA
        $rsBolsa = $modelBolsa->retornaQuerySituacaoBolsa($param);
        $this->mergeSGB($rsBolsa);

        // PHPEXCEL
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A1', utf8_encode('Região'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', utf8_encode('Mesorregião'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', utf8_encode('UF'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', utf8_encode('Município'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', utf8_encode('Ano Ref.'));
        $objPHPExcel->getActiveSheet()->setCellValue('F1', utf8_encode('Mês Ref.'));
        $objPHPExcel->getActiveSheet()->setCellValue('G1', utf8_encode('Nome do Curso'));
        $objPHPExcel->getActiveSheet()->setCellValue('H1', utf8_encode('Perfil'));
        $objPHPExcel->getActiveSheet()->setCellValue('I1', utf8_encode('CPF'));
        $objPHPExcel->getActiveSheet()->setCellValue('J1', utf8_encode('Nome'));
        $objPHPExcel->getActiveSheet()->setCellValue('K1', utf8_encode('Qtd. Bolsas'));
        $objPHPExcel->getActiveSheet()->setCellValue('L1', utf8_encode('Valor'));
        $objPHPExcel->getActiveSheet()->setCellValue('M1', utf8_encode('Situação'));

        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CCCCCC');

        if(count($rsBolsa)) {
            foreach ($rsBolsa as $i => $col) {
                foreach ($col as $item => $value) {
                    if (!in_array($item, array(
                        'NO_REGIAO', 'NO_MESO_REGIAO', 'SG_UF',
                        'NO_MUNICIPIO', 'NU_ANO', 'NU_MES',
                        'DS_TIPO_CURSO', 'DS_TIPO_PERFIL', 'NU_CPF',
                        'NO_USUARIO', 'VL_BOLSA', 'QT_BOLSAS', 'DS_SITUACAO_BOLSA',
                    ))
                    ) {
                        unset($rsBolsa[$i][$item]);
                    }else{
                        $rsBolsa[$i][$item] = utf8_encode($rsBolsa[$i][$item]);
                    }
                }
                $rsBolsa[$i]['NU_CPF'] = $this->mascara($rsBolsa[$i]['NU_CPF'],'###.###.###-##');
            }

            $objPHPExcel->getActiveSheet()->fromArray($rsBolsa, null, 'A2');
            $objPHPExcel->getActiveSheet()->getStyle(sprintf(
                'A1:M%s',
                count($rsBolsa) + 1
            ))->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    private function mergeSGB(&$rsBolsa){
        if (count($rsBolsa)) {
            foreach ($rsBolsa as $i => $col) {
                // Busca informações do SGB somente se o status está enviado para o SGB
                if($col['NU_SEQ_SITUACAO_BOLSA'] == 3){
                    $config = Zend_Registry::get('config');

                    $dados = array(
                        'sistema' => 'SICE',
                        'login' => $config['webservices']['sgb']['login'],
                        'senha' => $config['webservices']['sgb']['senha'],
                        'co_programa' => 'CE',
                        'nu_cpf' => $col['NU_CPF'],
                        'nu_mes_referencia' => $col['NU_MES'],
                        'nu_ano_referencia' => $col['NU_ANO'],
                    );
                    // calculando tempo de execução do webservice
                    list($usec, $sec) = explode(" ", microtime());
                    $script_start = (float) $sec + (float) $usec;
                    try {
                        $soap = new Zend_Soap_Client($config['webservices']['sgb']['uri'], array('encoding' => 'ISO-8859-1'));
                        $soap->setSoapVersion(SOAP_1_1);

                        $respXml = $soap->lerDadosDePagamentosPorBolsistaProgramaAnoMesReferencia($dados);

                        $objXml = new Zend_Config_Xml($respXml);

                        $arXml = $objXml->toArray();

                        //em atendimento ao solicitado pelo Igor Francisco de Oliveira Costa no https://sgo.basis.com.br/browse/FNDE-269
                        //Esta sendo alterado o nome do status, quando vir do SGB como "Pagamento solicitado", vai ser renomedo para:
                        if(!$arXml['pagamentos']['pagamento']['SituacaoDaBolsa'] == 'Pagamento solicitado'){
                            $rsBolsa[$i]['DS_SITUACAO_BOLSA'] .= " / " . $arXml['pagamentos']['pagamento']['SituacaoDaBolsa'];
                        } else {
                            $rsBolsa[$i]['DS_SITUACAO_BOLSA'] .= " / " . "pré-aprovação";
                        }
                    }catch ( SoapFault $exp ) {
                        // calculando tempo de conclusão da execução do webservice
                        list($usec, $sec) = explode(" ", microtime());
                        $script_end = (float) $sec + (float) $usec;
                        $elapsed_time = round($script_end - $script_start, 5);

                        $rsBolsa[$i]['DS_SITUACAO_BOLSA'] = "Comunicação ao SGB interrompida após $elapsed_time segundos.";
                        $rsBolsa[$i]['DS_SITUACAO_BOLSA'] .= "<br> Tempo suportado pelo servidor é de " . ini_get('default_socket_timeout') . ' segundos.';
                    }catch (Exception $e){
                        // calculando tempo de conclusão da execução do webservice
                        list($usec, $sec) = explode(" ", microtime());
                        $script_end = (float) $sec + (float) $usec;
                        $elapsed_time = round($script_end - $script_start, 5);

                        $rsBolsa[$i]['DS_SITUACAO_BOLSA'] = "Comunicação ao SGB interrompida após $elapsed_time segundos.";
                        $rsBolsa[$i]['DS_SITUACAO_BOLSA'] .= "Erro: " . $e->getMessage();
                    }
                }
            }
        }
    }

    function mascara($val, $mask){
        $maskared = '';
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

}