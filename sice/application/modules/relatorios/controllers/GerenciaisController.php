<?php

/**
 * Created by PhpStorm.
 * User: 05922176633
 * Date: 06/02/2015
 * Time: 09:42
 */
class Relatorios_GerenciaisController extends Fnde_Sice_Controller_Action
{

    public function avaliacaoinstitucionalAction()
    {
        $this->setTitle('Relatório de Avaliação do Curso');
        $this->setSubtitle('Exportar');

        //usuario logado
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        //form
        $param = $this->_getAllParams();
        $form = new Gerenciais_Formavaliacao();

        // curso
        $rsCurso = Fnde_Sice_Business_Componentes::getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"));
        $form->setCurso($rsCurso, $param['NU_SEQ_CURSO']);

        //id relatorio
        $form->setIdRelatorio('avaliacaoinstitucional');

        //anos com turmas
        $objTurma = new Fnde_Sice_Business_Turma();
        $anos = $objTurma->getAnosTurmas();

        $arrAnos = array('' => 'Selecione', '9999' => 'TODOS');
        foreach ($anos as $ano) {
            $arrAnos[$ano['ANO']] = $ano['ANO'];
        }

        $form->setAno($arrAnos);

        /*
         * array de elementos que deve ser feito display
         * todos filtros adicionais devem ser adicionados o seu id ao arrDisplay
         */
        $arrDisplay = array();

        //filtro de ufs
        if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario) ) {
            $rsUf = Fnde_Sice_Business_Componentes::getAllByTable("Uf", array("SG_UF", "SG_UF"));
        } else {
            $rsUf = Fnde_Sice_Business_Componentes::getAllByTable("Uf", array("SG_UF", "SG_UF"), array("stWhere" => "sg_uf = '{$arUsuario['SG_UF_ATUACAO_PERFIL']}'"));
        }

        if ($this->_request->isPost()) {
            $params = $this->_getAllParams();
            if($params['NU_ANO'] == "" && $params['DT_INICIO'] == ""){
                $this->addMessage(Fnde_Message::MSG_ERROR, "É Necessário selecionar o Ano ou a Data de Finalização das Turmas.");
                $this->_redirect("/relatorios/gerenciais/avaliacaoinstitucional");
            }else{

                $arrDisplay[] = $form->addFiltroUF($rsUf, $params['SG_UF']);
                $form->display($arrDisplay);

                $gridExcel = $this->montagridavaliacoesAction($params);
                $this->view->rsRegistros = $gridExcel;

            }
            $form->populate($params);
            if(isset($params["exportar"])){
                $this->baixarexcelAction($gridExcel);
            }
        }else{
            $arrDisplay[] = $form->addFiltroUF($rsUf);
            $form->display($arrDisplay);
        }

        $this->view->form = $form;
    }

    public function excelAction()
    {
        $params = $this->_getAllParams();

        //validar filtro basico
        if ($params['NU_ANO'] == '') {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
            $this->_redirect("/relatorios/gerenciais/" . $params['id_relatorio']);
        }

        //um case para cada id de relatorio
        switch ($params['id_relatorio']) {
            case 'avaliacaoinstitucional':
                //validar filtro especifico do relatorio
                if ($params['SG_UF'] == '') {
                    $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
                    $this->_redirect("/relatorios/gerenciais/" . $params['id_relatorio']);
                }

                //nome do arquivo
                $filename = "Avaliação Institucional " . $params['NU_ANO'] . ".xls";

                //cabeçalho
                $cabecalho = array(array(
                    'SG_UF', 'NO_MESO_REGIAO', 'NO_MUNICIPIO', 'NU_SEQ_TURMA',
                    'Q1_R1', 'Q1_R2', 'Q1_R3', 'Q1_R4',
                    'Q2_R1', 'Q2_R2', 'Q2_R3', 'Q2_R4',
                    'Q3_R1', 'Q3_R2', 'Q3_R3', 'Q3_R4',
                    'Q4_R1', 'Q4_R2', 'Q4_R3', 'Q4_R4',
                    'Q5_R1', 'Q5_R2', 'Q5_R3', 'Q5_R4',
                    'Q6_R1', 'Q6_R2', 'Q6_R3', 'Q6_R4',
                    'Q7_R1', 'Q7_R2', 'Q7_R3', 'Q7_R4',
                    'Q8_R1', 'Q8_R2', 'Q8_R3', 'Q8_R4',
                    'Q9_R1', 'Q9_R2', 'Q9_R3', 'Q9_R4',
                    'Q10_R1', 'Q10_R2', 'Q10_R3', 'Q10_R4'
                ));

                $objGerenciais = new Fnde_Sice_Business_Gerenciais();
                $lista = $objGerenciais->getAvaliacoesInstitucionais($params);
                $resultados = array_merge($cabecalho, $lista);

                //legenda
                $legenda = array(
                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q1),
                    array('Q1_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q1_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q1_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q1_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q2),
                    array('Q2_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q2_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q2_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q2_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q3),
                    array('Q3_R'.Fnde_Sice_Model_AvaliacaoCurso::q3r1),
                    array('Q3_R'.Fnde_Sice_Model_AvaliacaoCurso::q3r2),
                    array('Q3_R'.Fnde_Sice_Model_AvaliacaoCurso::q3r3),
                    array('Q3_R'.Fnde_Sice_Model_AvaliacaoCurso::q3r4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q4),
                    array('Q4_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q4_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q4_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q4_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q5),
                    array('Q5_R'.Fnde_Sice_Model_AvaliacaoCurso::q5r1),
                    array('Q5_R'.Fnde_Sice_Model_AvaliacaoCurso::q5r2),
                    array('Q5_R'.Fnde_Sice_Model_AvaliacaoCurso::q5r3),
                    array('Q5_R'.Fnde_Sice_Model_AvaliacaoCurso::q5r4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q6),
                    array('Q6_R'.Fnde_Sice_Model_AvaliacaoCurso::q6r1),
                    array('Q6_R'.Fnde_Sice_Model_AvaliacaoCurso::q6r2),
                    array('Q6_R'.Fnde_Sice_Model_AvaliacaoCurso::q6r3),
                    array('Q6_R'.Fnde_Sice_Model_AvaliacaoCurso::q6r4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q7),
                    array('Q7_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q7_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q7_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q7_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q8),
                    array('Q8_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q8_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q8_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q8_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q9),
                    array('Q9_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar1),
                    array('Q9_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar2),
                    array('Q9_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar3),
                    array('Q9_R'.Fnde_Sice_Model_AvaliacaoCurso::qBasicar4),
                    array(),

                    array('Q'.Fnde_Sice_Model_AvaliacaoCurso::q10),
                    array('Q10_R'.Fnde_Sice_Model_AvaliacaoCurso::q10r1),
                    array('Q10_R'.Fnde_Sice_Model_AvaliacaoCurso::q10r2),
                    array('Q10_R'.Fnde_Sice_Model_AvaliacaoCurso::q10r3),
                    array('Q10_R'.Fnde_Sice_Model_AvaliacaoCurso::q10r4)
                );

                foreach ($legenda as $i => $itens) {
                    foreach ($itens as $col => $val) {
                        $legenda[$i][$col] = utf8_encode($val);
                    }
                }

                //phpexcel
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->removeSheetByIndex(0);

                $objPHPExcel->addSheet(new PHPExcel_Worksheet(null, $params['NU_ANO']));
                $objPHPExcel->addSheet(new PHPExcel_Worksheet(null, 'legenda'));

                $objPHPExcel->setActiveSheetIndexByName($params['NU_ANO']);
                $objPHPExcel->getActiveSheet()->fromArray($resultados, null, 'A1');

                $objPHPExcel->setActiveSheetIndexByName('legenda');
                $objPHPExcel->getActiveSheet()->fromArray($legenda, null, 'A1');

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '"');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

                break;
        }

        //parar execução para download
        exit;
    }

    public function montagridavaliacoesAction($params){
        set_time_limit(600);

        $objGerenciais = new Fnde_Sice_Business_Gerenciais();
        $lista = $objGerenciais->getAvaliacoesInstitucionaisNova($params);

        if($params['NU_ANO'] == '9999'){
            $objTurma = new Fnde_Sice_Business_Turma();
            $anos = $objTurma->getAnosTurmas();
            foreach ($anos as $y => $ano) {
                $arrAnos[$y] = $ano['ANO'];
            }
            $params['NU_ANO'] = $arrAnos[0] . ' a ' . $arrAnos[$y];

        }

        if($params['DT_INICIO']){
            $dt_fim = ($params['DT_FIM']) ? $params['DT_FIM'] : date('d/m/Y');
            $params['DT_INICIO'] = 'De: ' . $params['DT_INICIO'] . ' Até ' . $dt_fim ;
        }

        if($params['SG_UF']){
            $params['SG_UF'] = (count($params['SG_UF']) == 27) ? 'Todos' : implode(', ',$params['SG_UF']);
        }

//        Curso	Ano
        $dadosXls = "<div class='listagem dataTable'>";
        $dadosXls .= "<table border='1' >";
        $dadosXls .= "<tr>";
        $dadosXls .= "<th>Curso</th>";
        $dadosXls .= ($params['NU_ANO']) ? "<th>Ano</th>" : "";
        $dadosXls .= ($params['DT_INICIO']) ? "<th>Turmas Finalizadas Entre</th>" : "";
        $dadosXls .= ($params['SG_UF']) ? "<th>UF</th>" : "";
        $dadosXls .= ($params['CO_MUNICIPIO']) ? "<th>Município</th>" : "";
        $dadosXls .= ($params['CO_MESORREGIAO']) ? "<th>Mesoregião</th>" : "";
        $dadosXls .= ($params['CO_REDE_ENSINO']) ? "<th>Rede de Ensino</th>" : "";
        $dadosXls .= ($params['NU_SEQ_TURMA']) ? "<th>Número da Turma</th>" : "";
        $dadosXls .= "</tr>";

        $montaXls = array();

        if(count($lista) < 1){
            $lista[0] = null;
        }

        foreach($lista as $dados) {
            $k = (!is_null($dados)) ? $dados["CURSO"] : '';

            //Monta Chave
            $k .= ($params['SG_UF']) ? '-' . $params['SG_UF'] : '';
            $k .= ($params['CO_MESORREGIAO']) ? '-' . $params['CO_MESORREGIAO'] : '';
            $k .= ($params['CO_MUNICIPIO']) ? '-' . $params['CO_MUNICIPIO'] : '';

            $montaXls['cursos'][$k]['nome'] = $dados["CURSO"];
            $montaXls['cursos'][$k]['ano'] = ($params['NU_ANO'])                ? $params['NU_ANO']         : null;
            $montaXls['cursos'][$k]['data'] = ($params['DT_INICIO'])            ? $params['DT_INICIO']      : null;
            $montaXls['cursos'][$k]['uf'] = ($params['SG_UF'])                  ? $params['SG_UF']          : null;
            $montaXls['cursos'][$k]['municipio'] = ($params['CO_MUNICIPIO'])    ? $params['CO_MUNICIPIO']   : null;
            $montaXls['cursos'][$k]['mesoregiao'] = ($params['CO_MESORREGIAO']) ? $params['CO_MESORREGIAO'] : null;
            $montaXls['cursos'][$k]['rede'] = ($params['CO_REDE_ENSINO'])       ? $params['CO_REDE_ENSINO'] : null;
            $montaXls['cursos'][$k]['turma'] = ($params['NU_SEQ_TURMA'])        ? $params['NU_SEQ_TURMA']   : null;

            $montaXls[1]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q1;
            $montaXls[2]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q2;
            $montaXls[3]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q3;
            $montaXls[4]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q4;
            $montaXls[5]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q5;
            $montaXls[6]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q6;
            $montaXls[7]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q7;
            $montaXls[8]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q8;
            $montaXls[9]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q9;
            $montaXls[10]['questao'] = Fnde_Sice_Model_AvaliacaoCurso::q10;

            for ($i = 1; $i <= count($lista); $i++){

                $questao = 'Q' . $i;
                switch($dados[$questao]) {
                    case 1:
                        $montaXls[$i]['pessimo']['qtd']++;
                        break;
                    case 2:
                        $montaXls[$i]['ruim']['qtd']++;
                        break;
                    case 3:
                        $montaXls[$i]['bom']['qtd']++;
                        break;
                    case 4:
                        $montaXls[$i]['excelente']['qtd']++;
                        break;
                }

            }
        }
        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        foreach($montaXls['cursos'] as $cursos){

            if($cursos["mesoregiao"]){
                $mesoregiao = $businessMesoregiao->getMesoRegiaoById($cursos["mesoregiao"]);
                $noMesoregiao = $mesoregiao["NO_MESO_REGIAO"];
            }

            if($cursos["municipio"]){
                $municipio = $businessMesoregiao->getMunicipioById($cursos["municipio"]);
                $noMunicipio = $municipio[0]["NO_MUNICIPIO"];
            }

            if($cursos["rede"]){
                $rede = $businessMesoregiao->getRedeDeEnsino($cursos["rede"]);
                $redeEnsino = $rede[0]["NO_ESFERA_ADM"];
            }

            $dadosXls .= "<tr>";
            $dadosXls .= "<td>".$cursos["nome"]."</td>";
            $dadosXls .= ($cursos["ano"])           ? "<td align='center'>".$cursos["ano"]."</td>" : "";
            $dadosXls .= ($cursos["data"])          ? "<td align='center'>".$cursos["data"]."</td>" : "";
            $dadosXls .= ($cursos["uf"])            ? "<td align='center'>".$cursos["uf"]."</td>" : "";
            $dadosXls .= ($cursos["municipio"])     ? "<td align='center'>".$noMunicipio."</td>" : "";
            $dadosXls .= ($cursos["mesoregiao"])    ? "<td align='center'>".$noMesoregiao."</td>" : "";
            $dadosXls .= ($cursos["rede"])          ? "<td align='center'>".$redeEnsino."</td>" : "";
            $dadosXls .= ($cursos["turma"])         ? "<td align='center'>".$cursos["turma"]."</td>" : "";
            $dadosXls .= "</tr>";

        }

        $dadosXls .= "</table><br><table border='1' >";

        unset($montaXls['cursos']);

        $dadosXls .= "<tr>";
        $dadosXls .= "<th width='800'>Perguntas</th>";
        $dadosXls .= "<th>Péssimo</th>";
        $dadosXls .= "<th>Ruim</th>";
        $dadosXls .= "<th>Bom</th>";
        $dadosXls .= "<th>Excelente</th>";
        $dadosXls .= "<th>Total de Cursistas</th>";
        $dadosXls .= "</tr>";

        foreach($montaXls as $questoes){
            $respostas = 0;

            if(isset($questoes['pessimo'])){
                $respostas += $questoes['pessimo']['qtd'];
            }
            if(isset($questoes['ruim'])){
                $respostas += $questoes['ruim']['qtd'];
            }
            if(isset($questoes['bom'])){
                $respostas += $questoes['bom']['qtd'];
            }
            if(isset($questoes['excelente'])){
                $respostas += $questoes['excelente']['qtd'];
            }

            $dadosXls .= "<tr align='center'>";
            $dadosXls .= "<td align='left'>". $questoes["questao"] ."</td>";
            if($questoes['pessimo']){
                $percent = ($questoes['pessimo']['qtd'] * 100) / $respostas;
                $dadosXls .= "<td> ".$questoes['pessimo']['qtd'] . " (" . number_format($percent, 2, '.', ',') . "% ) </td>";
            }else{
                $dadosXls .= "<td> 0 (0%) </td>";
            }
            if($questoes['ruim']){
                $percent = ($questoes['ruim']['qtd'] * 100) / $respostas;
                $dadosXls .= "<td> ".$questoes['ruim']['qtd'] . " (" . number_format($percent, 2, '.', ',') . "% ) </td>";

            }else{
                $dadosXls .= "<td> 0 (0%) </td>";
            }
            if($questoes['bom']){
                $percent = ($questoes['bom']['qtd'] * 100) / $respostas;
                $dadosXls .= "<td> ".$questoes['bom']['qtd'] . " (" . number_format($percent, 2, '.', ',') . "% ) </td>";
            }else{
                $dadosXls .= "<td> 0 (0%)</td>";
            }
            if($questoes['excelente']){
                $percent = ($questoes['excelente']['qtd'] * 100) / $respostas;
                $dadosXls .= "<td> ".$questoes['excelente']['qtd'] . " (" . number_format($percent, 2, '.', ',') . "% ) </td>";
            }else{
                $dadosXls .= "<td>0 (0%)</td>";
            }
            $dadosXls .= "<td>". $respostas ."</td>";
            $dadosXls .= "</tr>";

        }

        return $dadosXls;

    }

    public function baixarexcelAction($dadosXls){
        $arquivo = "Avaliação Institucional - ".date('d-m-Y H:i').".xls";
        // Configurações header para forçar o download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$arquivo.'"');
        header('Cache-Control: max-age=0');
        // Se for o IE9, isso talvez seja necessário
        header('Cache-Control: max-age=1');

        // Envia o conteúdo do arquivo
        echo $dadosXls;
        exit;
    }

}