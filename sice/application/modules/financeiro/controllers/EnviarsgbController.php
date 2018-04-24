<?php

/**
 * Controller do Avaliar Bolsas
 * 
 * @author poliane.silva	
 * @since 04/07/2012
 */
class Financeiro_EnviarSgbController extends Fnde_Sice_Controller_Action {

    /**
     * Monta o formulário e renderiza na view
     *
     * @access public
     *
     * @author poliane.silva	
     * @since 04/07/2012
     */
    public function formAction() {
        try {
            $arSessao = $_SESSION['searchParam']['param'];

            //2 - Homologada;
            if ($arSessao['ST_BOLSA'] != "2") {
                $this->addMessage(Fnde_Message::MSG_ERROR, "A ação Enviar SGB não pode ser executada, pois a (s) bolsa (s) selecionada (s) está com a situação "
                        . Fnde_Sice_Business_Componentes::nomeSituacaoBolsa($arSessao['ST_BOLSA']) . ".");
                $this->_redirect("/financeiro/bolsa/list");
            }

            $this->setTitle('Bolsas');
            $this->setSubtitle('Enviar para SGB');

            //monta menu de contexto
            $menu = array($this->getUrl('financeiro', 'bolsa', 'list', ' ') => 'filtrar');
            $this->setActionMenu($menu);

            //Montando a mensagem de orientação
            $msgOrientacao = "Para enviar as bolsas, selecione a opção ao lado do perfil";

            $this->addInstantMessage(Fnde_Message::MSG_INFO, $msgOrientacao);

            $arParam = $this->_getAllParams($arParam);

            //Recupera o objeto de formulário para validação
            $form = $this->getForm($arParam);

            if ($this->getRequest()->isPost() && !isset($arParam['table_main_action'])) {
                $this->enviarSgb();
            }

            $this->view->form = $form;
            return $this->render('form');
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
            $this->view->form = $form;
            return $this->render('form');
        }
    }

    /**
     * Retorna o formulario de cadastro
     *
     * @access public
     *
     * @author poliane.silva	
     * @since 04/07/2012
     */
    public function getForm($arDados = array()) {
        try {
            $businessRegiao = new Fnde_Sice_Business_Regiao();
            $businessBolsa = new Fnde_Sice_Business_Bolsa();
            $businessSituacaoBolsa = new Fnde_Sice_Business_SituacaoBolsa();

            unset($_SESSION['BOLSAS_COORD_EST']);

            if (isset($arDados['IDENTIFICADOR_LINHA'])) {
                $arDados['identificador_linha'] = $arDados['IDENTIFICADOR_LINHA'];
            }
            $identificadorLinha = "";
            if (!is_array($arDados['identificador_linha'])) {
                $identificador = explode("-", $arDados['identificador_linha']);
                $arDados['UF_BOLSISTAS'] = $identificador[0];
                $arDados['PERFIL_BOLSISTAS'] = $identificador[1];
                $arDados['PERIODO_BOLSISTAS'] = $identificador[2];
                $identificadorLinha = "/identificador_linha/" . $arDados['identificador_linha'];
            } else {
                for ($i = 0; $i < count($arDados['identificador_linha']); $i++) {
                    $identificador = explode("-", $arDados['identificador_linha'][$i]);
                    $arDados['UF_BOLSISTAS'] = $identificador[0];
                    $arDados['PERFIL_BOLSISTAS'][$i] = $identificador[1];
                    $arDados['PERIODO_BOLSISTAS'][$i] = $identificador[2];
                    $identificador = null;
                    $identificadorLinha .= "/identificador_linha/" . $arDados['identificador_linha'][$i];
                }
            }

            $form = new EnviarSgb_Form($arDados);
            $form->setDecorators(array('FormElements', 'Form'));
            $form->setAction(
                    $this->view->baseUrl() . '/index.php/financeiro/enviarsgb/form'
                    . $identificadorLinha)->setMethod('post')->setAttrib('id', 'form');

            //Recupera os dados de pesquisa da tela de filtrar bolsas para montar o HTML de Período.
            $arPeriodo = $_SESSION['searchParam']['param'];

            $businessPeriodoVinc = new Fnde_Sice_Business_PeriodoVinculacao();
            $periodo = $businessPeriodoVinc->getDatasPeriodoById(
                    array("NU_SEQ_PERIODO_VINCULACAO" => $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']));
            $arPeriodo['DT_INICIAL'] = $periodo['DT_INICIAL'];
            $arPeriodo['DT_FINAL'] = $periodo['DT_FINAL'];

            $situacao = $businessSituacaoBolsa->getSituacaoBolsaById($arPeriodo['ST_BOLSA']);
            $arPeriodo['DS_SITUACAO_BOLSA'] = $situacao['DS_SITUACAO_BOLSA'];
            $arPeriodo['MES_REFERENCIA'] = substr($arPeriodo['DT_FINAL'], -7);
            $arDados['SITUACAO_BOLSISTAS'] = $arPeriodo['ST_BOLSA'];

            //Preparando UF dos Usuários.
            $arPeriodo['SG_UF'] = $arDados['UF_BOLSISTAS'];
            //Preparando a Regiao
            $result = $businessRegiao->obterRegiaoPorUF(array('SG_UF' => $arPeriodo['SG_UF']));
            $arPeriodo['NO_REGIAO'] = $result['NO_REGIAO'];

            if ($businessBolsa->isBolsaAntiga($arPeriodo['NU_SEQ_PERIODO_VINCULACAO'])) {
                $arBolsistas = $businessBolsa->pesquisarBolsasEnviarSgb($arDados['UF_BOLSISTAS'], $arDados['PERFIL_BOLSISTAS'], $arPeriodo['NU_SEQ_PERIODO_VINCULACAO']);
            } else {
                $arBolsistas = $businessBolsa->pesquisarBolsasAvaliacao($arDados);
            }

            //Cria HTML com os dados do Período de Vinculação.
            $htmlPeriodo = $form->getElement("htmlPeriodo");
            $strDadosPeriodo = $this->view->retornaHtmlPeriodo($arPeriodo);
            $htmlPeriodo->setValue($strDadosPeriodo);

            $htmlBolsistas = $form->getElement("htmlBolsistas");
            $strDadosBolsistas = $this->retornaHtmlBolsistas($arBolsistas, $arDados['NU_SEQ_BOLSA']);
            $htmlBolsistas->setValue($strDadosBolsistas);

            return $form;
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
            $this->view->form = $form;
            return $this->render('form');
        }
    }

    /**
     * Retorna o HTML da tabela com os dados dos bolsista.
     * @param array $arBolsistas Dados dos bolsistas.
     * @return string HTML.
     */
    public function retornaHtmlBolsistas($arBolsistas, $bolsas) {
        $html = "<div class='listagem datatable'>";
        $html .= "<table id='tbBolsistas'>";
        $html .= "	<caption><i>Listagem dos bolsistas para envio ao SGB</i></caption>";
        $html .= "	<thead><tr>";
        $html .= "		<th style='text-align: center'></th>";
        $html .= "		<th>Perfil</th>";
        $html .= "		<th>UF</th>";
        $html .= "		<th>Nome</th>";
        $html .= "		<th width='110px'>CPF</th>";
        $html .= "		<th>Montante por bolsista</th>";
        $html .= "		<th>Quantidade de bolsas</th>";
        $html .= "		<th style='text-align: center' >Situação</th>";
        $html .= "		<th style='text-align: center' >Avaliador</th>";
        $html .= "		<th style='text-align: center' >Perfil Avaliador</th>";
        $html .= "	</tr></thead>";
        $html .= "	<tbody>";
        foreach ($arBolsistas as $bolsista) {
            $html .= "<tr>";
            $html .= "	<td><center><input type='checkbox' " . (in_array($bolsista['NU_SEQ_BOLSA'], $bolsas) ? "checked" : "") . " name='NU_SEQ_BOLSA[]' value='" . $bolsista['NU_SEQ_BOLSA']
                    . "' /></center></td>";
            $html .= "	<td>" . $bolsista['DS_TIPO_PERFIL'] . "</td>";
            $html .= "	<td>" . $bolsista['SG_UF_ATUACAO_PERFIL'] . "</td>";
            $html .= "	<td>" . $bolsista['NO_USUARIO'] . "</td>";
            $html .= "	<td>" . Fnde_Sice_Business_Componentes::formataCpf($bolsista['NU_CPF']) . "</td>";
            $html .= "	<td style='text-align: right'>" . 'R$ '
                    . number_format((float) $bolsista['VL_BOLSA'], 2, ',', '.') . "</td>";
            $html .= "	<td style='text-align: center' >" . $bolsista['QTD_BOLSA'] . "</td>";
            $html .= "	<td>" . $bolsista['DS_SITUACAO_BOLSA'] . "</td>";
            $html .= "	<td>" . $bolsista['NO_USUARIO_AVALIADOR'] . "</td>";
            $html .= "	<td style='text-align: center'>" . $bolsista['DS_TIPO_PERFIL_AVALIADOR'] . "</td>";
            $html .= "</tr>";
        }
        $html .= "	</tbody>";
        $html .= "</table>";
        $html .= "</div>";

        return $html;
    }

    /**
     * Método de teste para conexão com o serviço de produção.
     * Para DEV, alterar os valores de LOGIN, SENHA e URI do serviço no application.ini, para os mesmos de produção
     */
    public function sgbAction() {
        $config = Zend_Registry::get('config');
        $wsdl = $config['webservices']['sgb']['uri'];

        $dados = array('sistema' => 'SICE', 'login' => $config['webservices']['sgb']['login'],
            'senha' => $config['webservices']['sgb']['senha'], 'nu_cpf' => $this->getRequest()->getParam('cpf'));

        //realiza a chamada remota ao método...
        $soap = new Zend_Soap_Client($wsdl);
        $soap->setSoapVersion(SOAP_1_1);
        $respXml = $soap->lerDadosBolsista($dados);

        $objXml = new Zend_Config_Xml($respXml);
        $arr = $objXml->toArray();

        echo "<pre>";
        echo "<h3>SERVIÇO ACESSADO COM SUCESSO!</h3><br />";
        print_r($arr);
        die;
    }

    /**
     * Action de enviar ao SGB buncando do pop-up de coordenador estadual
     */
    public function enviarSgb() {
        $arParams = $this->_getAllParams();

        if (!$arParams['NU_SEQ_BOLSA']) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Selecione pelo menos um bolsista para enviar ao SGB.');
            return $this->render('form');
        }

        $bolsas = $arParams['NU_SEQ_BOLSA'];

        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $arSessao = $_SESSION['searchParam']['param'];
        //Recupera os dados do Coordenador estadual selecionado
        $rsCoordExecEstadual = $businessUsuario->getCoordExecEstadualPdfByUf($arSessao['SG_UF']);

        if (!$rsCoordExecEstadual) {
            $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
            $cpf = $usuarioLogado->cpf;
            $rsCoordExecEstadual = $businessUsuario->getCoordExecEstadualPdfByCpf($cpf);
        }

        $coordEstadual['NO_UF'] = $rsCoordExecEstadual['NO_UF'];
        $coordEstadual['NO_USUARIO'] = $rsCoordExecEstadual['NO_USUARIO'];
        $coordEstadual['NU_CPF'] = Fnde_Sice_Business_Componentes::formataCpf($rsCoordExecEstadual['NU_CPF']);

        try {
            $sgb = $this->sgb($bolsas);

            $_SESSION['COORD_ESTADUAL_PDF_SGB'] = $coordEstadual;
            $_SESSION['BOLSAS_PDF_SGB'] = $sgb;

            $this->addMessage(Fnde_Message::MSG_SUCCESS, "Bolsa (s) enviada (s) ao SGB com sucesso.<script	stype='text/javascript'>window.open('" . $this->view->baseUrl() . "/index.php/financeiro/enviarsgb/pdf');</script>");
        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
        }

        return $this->_redirect("financeiro/bolsa/list");
    }

    /**
     * Mecanismo principal para enviar ao SGB, que retorna um array com os dados necessários para gerar o PDF
     *
     * @param array $bolsas
     * @return array $sgb somente os dados das bolsas enviados com sucesso ao SGB para gerar o PDF
     */
    public function sgb($bolsas) {

        $businessBolsa = new Fnde_Sice_Business_Bolsa();
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $businessPeriodoVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();
        $businessEnviarSgb = new Fnde_Sice_Business_EnviarSgb();

        $sgb = array();
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $resultUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        $arSessao = $_SESSION['searchParam']['param'];

        //Envia as bolsas para o SGB
        for ($i = 0; $i < count($bolsas); $i++) {

          
            $rsPeriodoVinculacao = $businessPeriodoVinculacao->getPeriodoVinculacaoByIdBolsa($bolsas[$i]);
            $data = explode("/", $rsPeriodoVinculacao['DT_FINAL']);
            $rsBolsa = $businessBolsa->getBolsaPdfById($bolsas[$i]);
            //Monta o array de dados para enviar ao SGB

            /*
             * CO_FUNCAO
             */

            $qt_turma = 0;
            $config = Zend_Registry::get('config');
            if ($rsBolsa['NU_SEQ_TIPO_PERFIL'] == 4 || $rsBolsa['NU_SEQ_TIPO_PERFIL'] == 8) {
                $arDadosBolsa['CO_FUNCAO'] = $config['webservices']['sgb']['co_funcao']['coordenador_estadual'];
                $qt_turma = 1;
            } else if ($rsBolsa['NU_SEQ_TIPO_PERFIL'] == 5) {
                $arDadosBolsa['CO_FUNCAO'] = $config['webservices']['sgb']['co_funcao']['articulador'];
                $qt_turma = 1;
            } else {
                if ($rsBolsa['TOTAL_TURMA_TUTOR_FINALIZADA'] == 0 || $rsBolsa['TOTAL_TURMA_TUTOR_FINALIZADA'] == null) {
                    throw new Exception('Não é possível enviar o SGB pois o tutor não possui turmas finalizadas');
                } else if ($rsBolsa['TOTAL_TURMA_TUTOR_FINALIZADA'] == 1) {
                    $arDadosBolsa['CO_FUNCAO'] = $config['webservices']['sgb']['co_funcao']['tutor_uma_turma'];
                    $qt_turma = 1;
                } else if ($rsBolsa['TOTAL_TURMA_TUTOR_FINALIZADA'] >= 2) {
                    $arDadosBolsa['CO_FUNCAO'] = $config['webservices']['sgb']['co_funcao']['tutor_duas_turmas'];
                    $qt_turma = 2;
                }
            }

            $arDadosBolsa['NU_CPF'] = $rsBolsa['NU_CPF'];
            $arDadosBolsa['MES_REFERENCIA'] = $data[1];
            $arDadosBolsa['ANO_REFERENCIA'] = $data[2];
            $arDadosBolsa['NU_CNPJ_ENTIDADE'] = $businessBolsa->getCnpjEntidade($rsBolsa['SG_UF_ATUACAO_PERFIL']);
            $arDadosBolsa['VL_BOLSA'] = $businessBolsa->getValorBolsaById($rsBolsa['NU_SEQ_TIPO_PERFIL'], $qt_turma, $bolsas[$i]);

            $arDadosBolsa['SG_UF_ATUACAO_PERFIL'] = $rsBolsa['SG_UF_ATUACAO_PERFIL'];
            $arDadosBolsa['CO_MUNICIPIO_PERFIL'] = $rsBolsa['CO_MUNICIPIO_PERFIL'];

            //Verificar se o bolsista já está cadastrado
            $res = $businessEnviarSgb->lerBolsistaSgbWs($arDadosBolsa['NU_CPF']);

            if (!$res['nu_cpf']) {
                //Gravar dados do bolsista
                $retornoGravarBolsista = $businessEnviarSgb->salvarBolsistaWs($rsBolsa['NU_CPF'], $arDadosBolsa['NU_CNPJ_ENTIDADE']);
                $arrayGravarBolsista = explode(':', $retornoGravarBolsista);
                if ($arrayGravarBolsista[0] != "OK") {
                    $arHistorico = array('NU_SEQ_BOLSA' => $bolsas[$i], 'NU_SEQ_USUARIO' => $resultUsuario['NU_SEQ_USUARIO'],
                        'DT_HISTORICO' => date('d/m/Y G:i:s'), 'ST_BOLSA' => 9,
                        'DS_OBSERVACAO' => trim($arrayGravarBolsista[2]));

                    //Altera a situação da bolsa para Enviada para SGB
                    $businessBolsa->alterarStatusBolsa($arHistorico);
                    //Salvar mensagem de retorno no histórico
                    $businessBolsa->salvarHistoricoBolsa($arHistorico);
                    continue;
                }
            }

            //Enviar ao SGB
            $retorno = $businessEnviarSgb->enviarSgbWs($arDadosBolsa);
            //Exemplo de retorno:
            //Erro: 00029: Acesso Negado.
            $resp = explode(":", $retorno);

            $codResp = preg_replace("/[^0-9]/", "", $resp[1]);

            //Recupera os dados para gerar PDF das bolsas que foram enviadas ao SGB
            $arAuxSgb['NU_SEQ_BOLSA'] = $bolsas[$i];
            $arAuxSgb['DS_TIPO_PERFIL'] = $rsBolsa['DS_TIPO_PERFIL'];
            $arAuxSgb['NO_USUARIO'] = $rsBolsa['NO_USUARIO'];
            $arAuxSgb['NU_CPF'] = Fnde_Sice_Business_Componentes::formataCpf($rsBolsa['NU_CPF']);
            $arAuxSgb['DS_TIPO_PERFIL_AVALIADOR'] = $rsBolsa['DS_TIPO_PERFIL_AVALIADOR'];
            $arAuxSgb['NO_USUARIO_AVALIADOR'] = $rsBolsa['NO_USUARIO_AVALIADOR'];
            $arAuxSgb['NU_CPF_AVALIADOR'] = Fnde_Sice_Business_Componentes::formataCpf($rsBolsa['NU_CPF_AVALIADOR']);
            $arAuxSgb['MES_REFERENCIA'] = $arDadosBolsa['MES_REFERENCIA'];
            $arAuxSgb['ANO_REFERENCIA'] = $arDadosBolsa['ANO_REFERENCIA'];

            if ($codResp == 10001) {
                $arAuxSgb['ST_BOLSA'] = 3;
            } else {
                $arAuxSgb['ST_BOLSA'] = 9;
            }

            $sgb[$i] = $arAuxSgb;

            $msg = $resp[2] == "" ? "Falha no envio" : trim($resp[2]);

            $arHistorico = array('NU_SEQ_BOLSA' => $bolsas[$i], 'NU_SEQ_USUARIO' => $resultUsuario['NU_SEQ_USUARIO'],
                'DT_HISTORICO' => date('d/m/Y G:i:s'), 'ST_BOLSA' => $arAuxSgb['ST_BOLSA'],
                'DS_OBSERVACAO' => $msg);
            //Altera a situação da bolsa para Enviada para SGB
            $businessBolsa->alterarStatusBolsa($arHistorico);

            //Salvar mensagem de retorno no histórico
            $businessBolsa->salvarHistoricoBolsa($arHistorico);
        }
        return $sgb;
    }

    /**
     * Action para gerar o PDF
     * Depende de ter sido executada a função enviarSgbAction() para preencher os valores na sessão
     *
     */
    public function pdfAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $coordEstadual = $_SESSION['COORD_ESTADUAL_PDF_SGB'];
        $arBolsas = $_SESSION['BOLSAS_PDF_SGB'];

        Fnde_Sice_Business_Componentes::gerarPdfSgb($arBolsas, $coordEstadual);
    }

    /**
     * Reenvia para o SGB sem a necessidade de apresentar o PDF. Solicitado pela tela de verificar pendências
     */
    public function enviarSgbVerifPendAction() {

        $arParams = $this->_getAllParams();

        if (!is_array($arParams['NU_SEQ_BOLSA'])) {
            $arParams['NU_SEQ_BOLSA'] = array($arParams['NU_SEQ_BOLSA']);
        }

        $bolsas = $arParams['NU_SEQ_BOLSA'];

        try {

            $this->sgb($bolsas);

            $this->addMessage(Fnde_Message::MSG_SUCCESS, "Bolsa (s) reenviada (s) ao SGB com sucesso.");
        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
        }

        $this->_redirect("/financeiro/verificarpendencias/form");
    }

}
