<?php

/**
 * Created by PhpStorm.
 * User: 05922176633
 * Date: 06/02/2015
 * Time: 09:42
 */
class Relatorios_DadosdoscursistasController extends Fnde_Sice_Controller_Action
{
    public function dadosdoscursistasAction(){
        $this->setTitle('Dados dos Cursistas');
        $this->setSubtitle('Exportar');

        $form = new DadosDosCursistas_FormFilter();

        // AUTH
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        // FORM COMBOS
        $param = $this->_getAllParams();

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

        $rsCurso = Fnde_Sice_Business_Componentes::getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"));

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

        if($param['CO_MUNICIPIO'] && isset($param['CO_REDE_ENSINO'])) {
            $rsEscola = $businessUsuario->pesquisarEscola(array(
                'CO_REDE_ENSINO' => $param['CO_REDE_ENSINO'],
                'CO_MUNICIPIO_IBGE' => $param['CO_MUNICIPIO']
            ));
        }else{
            $rsEscola = array();
        }

        $businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
        $rsAnosPeriodoVinc = $businessPeriodoVinc->obterAnosPeriodoVinculacao();
        $arAno = array();
        foreach ( $rsAnosPeriodoVinc as $row ) {
            $arAno[$row['VL_EXERCICIO']] = $row['VL_EXERCICIO'];
        }

        // FORM
        $form->setUf($rsUf, $param['UF_TURMA']);
        $form->setCurso($rsCurso, $param['NU_SEQ_CURSO']);
        $form->setMunicipio($rsMunicipio, $param['CO_MUNICIPIO']);
        $form->setMesorregiao($rsMessoregiao, $param['CO_MESORREGIAO']);
        $form->setRedeEnsino($param['CO_REDE_ENSINO']);
        $form->setNomeEscola($rsEscola, $param['CO_ESCOLA']);
        $form->setCpf($param['NU_CPF']);
        $form->setNumeroTurma($param['NU_SEQ_TURMA']);
        $form->setSituacaoTurma($param['ST_CURSISTA']);
        $form->setAno($arAno, ($param['NU_ANO'] ? $param['NU_ANO'] : date('Y')));
        $form->setDtInicio($param['DT_INICIO']);
        $form->setDtFim($param['DT_FIM']);

        $this->view->form = $form;

        // GRID
        if($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {

                // se for uma solicitação de excel, faz o encaminhamento para a outra action
                if($param['excel']) {
                    return $this->_forward("dadosdoscursistasexcel", "dadosdoscursistas", "relatorios");
                }

                $rsUsuario = $businessUsuario->getDadosCursista($param);

                if (count($rsUsuario)) {
                    foreach ($rsUsuario as $i => $col) {
                        foreach ($col as $item => $value) {
                            if (!in_array($item, array(
                                'CPF', 'NOME_COMPLETO', 'UF_NASCIMENTO',
                                'EMAIL', 'TELEFONE', 'CELULAR',
                                'UF_ESCOLA', 'MUNICIPIO_ESCOLA'
                            ))
                            ) {
                                unset($rsUsuario[$i][$item]);
                            }
                        }
                    }
                }

                $grid = new Fnde_View_Helper_DataTables();
                $grid->setAutoCallJs(true);
                $grid->setData($rsUsuario);
                $grid->setHeader(array(
                    'CPF',
                    'Nome Completo',
                    'UF',
                    'Email do Cursista',
                    'Telefone',
                    'Celular',
                    'UF Escola',
                    'Município Escola'
                ));
                $grid->setHeaderActive(false);
                $grid->setTitle("Listagem de usuários");
                $grid->setId('CPF');
                $grid->setRowInput(Fnde_View_Helper_DataTables::INPUT_TYPE_RADIO);
                $grid->setTableAttribs(array('id' => 'edit'));

                $this->view->grid = $grid;
            }else{
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
            }
        }
    }

    public function dadosdoscursistasexcelAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        $businessUsuario = new Fnde_Sice_Business_Usuario();

        // CABEÇALHO
        $param = $this->_getAllParams();
        $arCabecalho = array();

        if($this->_request->isPost()){

        }else{
            $this->_redirect('relatorios/dadosdoscursistas/dadosdoscursistas');
            exit;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="dados_do_cursista.xls"');
        set_time_limit(0);
        ini_set("memory_limit", "8000M");

        if($param['NU_SEQ_CURSO']) {
            $rsComponente = Fnde_Sice_Business_Componentes::getAllByTable(
                "Curso",
                array("NU_SEQ_CURSO", "DS_NOME_CURSO"),
                array("stWhere" => sprintf("NU_SEQ_CURSO = '%s'", $param['NU_SEQ_CURSO']))
            );

            $arCabecalho['Curso'] = current($rsComponente);
        }

        if($param['UF_TURMA']) {
            $arCabecalho['UF'] = $param['UF_TURMA'];
        }else{
            $arCabecalho['UF'] = "TODOS";
        }

        if($param['CO_MUNICIPIO']) {
            $rsComponente = Fnde_Sice_Business_Componentes::getAllByTable(
                "Municipio",
                array("CO_MUNICIPIO_FNDE", "NO_MUNICIPIO"),
                array("stWhere" => sprintf("CO_MUNICIPIO_IBGE = '%s'", $param['CO_MUNICIPIO']))
            );

            $arCabecalho['Município'] = current($rsComponente);
        }

        if($param['CO_MESORREGIAO']) {
            $rsComponente = Fnde_Sice_Business_Componentes::getAllByTable(
                "MesoRegiao",
                array("CO_MESO_REGIAO", "NO_MESO_REGIAO"),
                array("stWhere" => sprintf("CO_MESO_REGIAO = '%s'", $param['CO_MESORREGIAO']))
            );

            $arCabecalho['Mesorregião'] = current($rsComponente);
        }

        if($param['CO_REDE_ENSINO']) {
            $rsComponente = array(
                '' => 'Selecione',
                '0' => 'FEDERAL',
                '1' => 'ESTADUAL',
                '2' => 'MUNICIPAL',
                '3' => 'PARTICULAR',
                '4' => 'INTERNACIONAL',
                '5' => 'NAO APLICADA',
                '6' => 'Migração SME',
                '8' => 'ESTADUAL E MUNICIPAL',
                '7' => 'DISTRITAL',
            );

            $arCabecalho['Rede de Ensino'] = $rsComponente[$param['CO_REDE_ENSINO']];
        }

        if($param['CO_ESCOLA']) {
            $rsEscola = current($businessUsuario->pesquisarEscola(array(
                'CO_ESCOLA' => $param['CO_ESCOLA']
            )));

            $arCabecalho['Nome da Escola'] = $rsEscola['NO_ESCOLA'];
        }

        if($param['NU_CPF']) {
            $arCabecalho['CPF'] = $param['NU_CPF'];
        }

        if($param['NU_SEQ_TURMA']) {
            $arCabecalho['Número turma'] = $param['NU_SEQ_TURMA'];
        }

        if($param['ST_CURSISTA']) {
            $arCabecalho['Situação do Cursista'] = $param['ST_CURSISTA'];
        }

        if($param['NU_ANO']) {
            $arCabecalho['Ano'] = $param['NU_ANO'];
        }

        if($param['DT_INICIO'] || $param['DT_FIM']) {
            $str = "";

            if($param['DT_INICIO']){
                $str .= sprintf("De: %s ", $param['DT_INICIO']);
            }

            if($param['DT_FIM']){
                $str .= sprintf(" Até: %s ", $param['DT_FIM']);
            }

            $arCabecalho['Turmas Finalizadas Entre'] = $str;
        }

        // CONSULTA
        $rsUsuario = $businessUsuario->getDadosCursista($this->_getAllParams());

        // PHPEXCEL
        $objPHPExcel = new PHPExcel();

        $arExcelHead = array();
        foreach($arCabecalho as $head => $value){
            $arExcelHead[0][] = utf8_encode($head);
            $arExcelHead[1][] = utf8_encode($value);
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Dados dos Cursistas');
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->mergeCells(sprintf(
            'A1:%s1',
            PHPExcel_Cell::stringFromColumnIndex(count($arExcelHead[0]) - 1)
        ));

        $objPHPExcel->getActiveSheet()->fromArray($arExcelHead[0], null, 'A2');
        $objPHPExcel->getActiveSheet()->fromArray($arExcelHead[1], null, 'A3');
        $objPHPExcel->getActiveSheet()->getStyle(sprintf(
            'A1:%s3',
            PHPExcel_Cell::stringFromColumnIndex(count($arExcelHead[0]) - 1)
        ))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle(sprintf(
            'A1:%s2',
            PHPExcel_Cell::stringFromColumnIndex(count($arExcelHead[0]) - 1)
        ))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CCCCCC');

        if(count($rsUsuario)) {
            foreach ($rsUsuario as $i => $col) {
                foreach($col as $head => $value){
                    $rsUsuario[$i][$head] = utf8_encode($value);
                }
            }

            $arExcelDataHead = array();
            foreach ($rsUsuario[0] as $colHead => $col) {
                $arExcelDataHead[] = $colHead;
            }

            $objPHPExcel->getActiveSheet()->fromArray($arExcelDataHead, null, 'A5');
            $objPHPExcel->getActiveSheet()->fromArray($rsUsuario, null, 'A6');
            $objPHPExcel->getActiveSheet()->getStyle(sprintf(
                'A5:%s%s',
                PHPExcel_Cell::stringFromColumnIndex(count($arExcelDataHead) - 1),
                count($rsUsuario) + 5
            ))->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle(sprintf(
                'A5:%s5',
                PHPExcel_Cell::stringFromColumnIndex(count($arExcelDataHead) - 1)
            ))
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCCCCC');
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function nomeescolachangeAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $param = $this->_getAllParams();

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $rsEscola = $businessUsuario->pesquisarEscola(array(
            'SG_UF_ESCOLA' => $param['SG_UF_ESCOLA'],
            'CO_MUNICIPIO_IBGE' => $param['CO_MUNICIPIO_ESCOLA'],
            'CO_REDE_ENSINO' => $param['CO_REDE_ENSINO']
        ));

        $form = new DadosDosCursistas_FormFilter();
        $form->setNomeEscola($rsEscola, null);

        $this->getResponse()->setBody($form);

        return $this;
    }

    public function nomeescolachangefndeAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $param = $this->_getAllParams();

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $rsEscola = $businessUsuario->pesquisarEscola(array(
            'CO_MUNICIPIO_ESCOLA' => $param['CO_MUNICIPIO_ESCOLA'],
            'CO_REDE_ENSINO' => $param['CO_REDE_ENSINO']
        ));

        $form = new DadosDosCursistas_FormFilter();
        $form->setNomeEscola($rsEscola, null);

        $this->getResponse()->setBody($form);

        return $this;
    }

    public function dadosmesoregiaoAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $param = $this->_getAllParams();

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $arDados = $businessUsuario->getUsuarioById($param['NU_SEQ_USUARIO']);

        $rsMesoregiao = $businessUsuario->getMesoregiaoMunicipio(array(
            'CO_MUNICIPIO_IBGE' => $param['CO_MUNICIPIO_ESCOLA']
        ));

        if($param['NU_SEQ_USUARIO']){
            $form = new Usuario_Form($arDados);
        }else{
            $form = new Usuario_Form();
        }

        $form->setMesoregiaoAbaEscola($rsMesoregiao);

        $this->getResponse()->setBody($form);

        return $this;
    }


}