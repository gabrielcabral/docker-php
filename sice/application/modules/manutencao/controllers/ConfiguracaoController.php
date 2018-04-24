<?php

/**
 * Controller do Configuracao
 *
 * @author diego.matos
 * @since 30/03/2012
 */
class Manutencao_ConfiguracaoController extends Fnde_Sice_Controller_Action {

  /**
   * Ação de listagem
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function listAction() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    if (!Fnde_Sice_Business_Componentes::permitirAcesso()) {
      $this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
      $this->_redirect('index');
    }

    $this->setTitle('Configuração');
    $this->setSubtitle('Filtrar');

    //monta menu de contexto
    if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
      $menu[$this->getUrl('manutencao', 'configuracao', 'form', ' ')] =  'cadastrar';
    }
    $menu[$this->getUrl('manutencao', 'configuracao', 'list', ' ')] = 'filtrar';
    $this->setActionMenu($menu);

    //seta novos valores na sessão
    if ($this->_request->isPost()) {
      $this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
      $this->urlFilterNamespace->param = $this->_getAllParams();
    }

    //recupera valores da sessão
    $arFilter = $this->getSearchParamConfiguracao();

    $form = $this->getFormFilter();
    $form->populate($arFilter);

    $rsRegistros = array();

    if ($this->_request->isPost() || !empty($arFilter)) {
      if ($form->isValid($arFilter)) {
        $obBusiness = new Fnde_Sice_Business_Configuracao();
        $rsRegistros = $obBusiness->search($form->getValues(), true);
        if (!count($rsRegistros)) {
          $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Nenhum registro localizado para o filtro informado.');
        }
      } else {
        $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
        $this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
      }
    }

    //chama filtro form
    $this->view->formFilter = $form;

    //Chamando componente zend.grid dentro do helper
    if ($rsRegistros) {
//      if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){


      // funcionalidade desabilitada SGO FNDE-1892

//        $rowAction['desativar'] = array('label' => 'Desativar',
//            'url' => $this->view->Url(array('action' => 'desativar', 'id' => ''))
//                . '%s/ST_CONFIGURACAO/%s',
//            'params' => array('NU_SEQ_CONFIGURACAO', 'ST_CONFIGURACAO'), 'title' => 'Desativar',
//            'attribs' => array('class' => 'icoAceitar', 'title' => 'Desativar',
//                'mensagem' => 'Deseja realmente desativar a configuração? A operação é irreversível!'));
////      }

      $rowAction['visualizar'] = array('label' => 'Visualizar',
          'url' => $this->view->Url(
                  array('action' => 'visualizar-configuracao', 'v' => '1',
                      'NU_SEQ_CONFIGURACAO' => '')) . '%s',
          'params' => array('NU_SEQ_CONFIGURACAO'), 'title' => 'Visualizar',
          'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar'));

      $arrHeader = array('<center>ID</center>', '<center>Qtd Turmas por tutor</center>',
          '<center>Qtd Alunos por turma</center>', '<center>Data inicial da configuração</center>',
          '<center>Data final da configuração</center>', '<center>Data Inclusão</center>',
          '<center>Data alteração</center>', '<center>Situação</center>',);

      $grid = new Fnde_View_Helper_DataTables();
      $grid->setActionColumn("<center>Ação</center>");
      $grid->setHeaderActive(false);
      $grid->setAutoCallJs(true);
      $grid->setTitle("Listagem de configurações");
      $this->view->grid = $grid->setData($rsRegistros)->setHeader($arrHeader)->setRowAction($rowAction)->setId(
                      'NU_SEQ_CONFIGURACAO')->setTableAttribs(array('id' => 'edit', 'style' => 'text-align:center'));
    }
  }

  /**
   * Remove um registro de Configuracao
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function delConfiguracaoAction() {
    $arParam = $this->_request->getParams();

    $obConfiguracao = new Fnde_Sice_Business_Configuracao();
    $resposta = $obConfiguracao->del($arParam['NU_SEQ_CONFIGURACAO']);

    $resposta = (string) $resposta;

    if ($resposta) {
      $this->addMessage(Fnde_Message::MSG_SUCCESS, "Operação realizada com sucesso!");
    } elseif ($resposta == '0') {
      $this->addMessage(Fnde_Message::MSG_ERROR, "Exclusão do registro já realizada.");
    } else {
      $this->addMessage(Fnde_Message::MSG_ERROR, "Configuracao não pode ser excluído, pois o mesmo está associado." . $resposta);
    }

    $this->_redirect("/manutencao/configuracao/list");
  }

  /**
   * Remove um registro de Configuracao
   *
   * @author diego.matos
   * @since 30/03/2012
   */
    public function desativarAction()
    {
        // funcionalidade desabilitada SGO FNDE-1892
        $this->addMessage(Fnde_Message::MSG_INFO, "Não é mais possível desativar configurações.");
        $this->_redirect("/manutencao/configuracao/list");

        /*
        $arParam = $this->_getAllParams();

        $obBusiness = new Fnde_Sice_Business_Configuracao();
        $obModel = new Fnde_Sice_Model_Configuracao();

        $rsRegistro = $obBusiness->getConfiguracaoById($arParam["id"]);

        $rsRegistro['ST_CONFIGURACAO'] = "D";
        $rsRegistro['DT_ALTERACAO'] = date("d/m/Y G:i:s");

        $where = " NU_SEQ_CONFIGURACAO = " . $rsRegistro['NU_SEQ_CONFIGURACAO'];

        //alter session para trabalhar com datas de bd
        $obModel->fixDateToBr();
        $resposta = $obModel->update($rsRegistro, $where);

        $resposta = (string) $resposta;

        if ($resposta) {
        $this->addMessage(Fnde_Message::MSG_SUCCESS, "Configuração desativada com sucesso.");
        } elseif ($resposta == '0') {
        $this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao tentar ativar/desativar configuração.");
        }

        $this->_redirect("/manutencao/configuracao/list");
        */
    }

  /**
   * Monta o formulário e renderiza na view
   *
   * @access public
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function formAction() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    $this->setTitle('Configuração');
    $this->setSubtitle('Cadastrar');

    //monta menu de contexto
    if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
      $menu[$this->getUrl('manutencao', 'configuracao', 'form', ' ')] =  'cadastrar';
    }
    $menu[$this->getUrl('manutencao', 'configuracao', 'list', ' ')] = 'filtrar';
    $this->setActionMenu($menu);

    // Recuperando array de dados do banco para setar valores no formulário
    $configuracao = new Fnde_Sice_Business_Configuracao();
    if ($this->getRequest()->getParam("NU_SEQ_CONFIGURACAO")) {
      $this->view->nu_seq_conf = $this->getRequest()->getParam("NU_SEQ_CONFIGURACAO");
      $arDados = $configuracao->getConfiguracaoById($this->getRequest()->getParam("NU_SEQ_CONFIGURACAO"));
    }else{
      $arDados = $configuracao->getConfiguracaoById();
    }
    $arDados["DT_INI_VIGENCIA"] = $arDados["DT_INICIO_NOVA_CONFIG"];
    $arDados["DT_TERMINO_VIGENCIA"] = $arDados["DT_TERMINO_NOVA_CONFIG"];

    //Recupera o objeto de formulário para validação
    $form = $this->getForm($arDados);

    if ($arDados['NU_SEQ_CONFIGURACAO']) {
      $this->view->form = $form->populate($arDados);
    } else {
      $this->view->form = $this->getForm();
    }

    if ($this->getRequest()->isPost()) {
      return $this->salvarConfiguracaoAction();
    }

    $this->render('form');
  }

  /**
   * Retorna o formulario de cadastro
   *
   * @access public
   *
   * @author diego.matos
   * @since 30/03/2012
   */
    public function getForm($arDados = array(), $arExtra = array(), $conf_aterior = false) {

        $obBusiness = new Fnde_Sice_Business_Configuracao();

        $params = $this->_getAllParams();

        $form = new Configuracao_Form($this->view->registros);
        if (isset($params['NU_SEQ_CONFIGURACAO']) && $params['NU_SEQ_CONFIGURACAO'] != "") {
            $nuSeqConfig = $params['NU_SEQ_CONFIGURACAO'];
            $this->view->registros = $obBusiness->getDadosConfiguracaoById($nuSeqConfig);
            $form->removeElement('confirmar');
        } else {
          $nuSeqConfig = null;
        }

        $form->setDecorators(array('FormElements', 'Form'));

        $form->setAction($this->view->baseUrl() . '/index.php/manutencao/configuracao/form')->setMethod('post')->setAttrib(
                'id', 'form');


        $form->configuracaoVingente->setLegend('Configuração Anterior Nº:'.$arDados['NU_SEQ_CONFIGURACAO']);

        $combo = $form->getElement("NU_SEQ_TIPO_CURSO");
        $combo->setLabel('Tipo de Curso:');
        $buss = new Fnde_Sice_Business_TipoCurso();
          $combo->addMultiOption(null, 'Selecione');
          $result = $buss->search(array('NU_SEQ_TIPO_CURSO', 'DS_TIPO_CURSO'));
          for ($i = 0; $i < count($result); $i++) {
            $combo->addMultiOption($result[$i]['NU_SEQ_TIPO_CURSO'], $result[$i]['DS_TIPO_CURSO']);
          }
        $combo->setValue($this->view->registros['configuracao']['NU_SEQ_TIPO_CURSO']);

        if(!$arDados['NU_SEQ_CONFIGURACAO']){
            $form->configuracaoVingente->setLegend('Não Existe Configuração Anterior');
            $form->removeElement('NU_SEQ_TIPO_CURSO');
            $form->removeElement('DT_INI_VIGENCIA');
            $form->removeElement('DT_TERMINO_VIGENCIA');
        }elseif($conf_aterior){
            $dt_inicio_conf_antwerior = $form->getElement('DT_INI_VIGENCIA');
            $dt_fim_conf_antwerior = $form->getElement('DT_TERMINO_VIGENCIA');

            $dt_inicio_conf_antwerior->setValue( date("d/m/Y",strtotime($arDados['DT_INICIO_NOVA_CONFIG'])));
            if($arDados['DT_TERMINO_NOVA_CONFIG']){
                $dt_fim_conf_antwerior->setValue(date("d/m/Y",strtotime($arDados['DT_TERMINO_NOVA_CONFIG'])));
            }

        }

        $html = $form->getElement("htmlNovaConf");

        $str = '<h4><div class="" align="left">Configuração Nº'.$params['NU_SEQ_CONFIGURACAO'].' </div></h4><br/>' . $this->retornaHtmlNovaConf()
                . "<br/>" . $this->retornaHtmlTurma() . "<br/>" . $this->retornaHtmlGridBolsa() . "<br/>"
                . $this->retornaHtmlGridCriterioAvaliacao() . "</fieldset></fieldset>";

        $html->setValue($str);

        return $form;
    }

  /**
   * Verifica a necessidade de leitura do arquivo ou exibição de dados existentes
   * @param $nuSeqConfig
   * @param $arExtra
   */
  private function leituraDeArquivo($nuSeqConfig, &$arExtra) {
    if (Fnde_Sice_Business_Componentes::isEmpty($nuSeqConfig)) {
      $this->lerArquivoConfiguracao();
    } else {
      $arExtra['NU_SEQ_CONFIGURACAO'] = $nuSeqConfig;

      $this->view->record = $this->getDadosConfiguracao($nuSeqConfig);
    }
  }

  /**
   * Retorna os dados para montar a tela de configuracao.
   * @param $nuSeqConfig ID da configuracao
   */
  private function getDadosConfiguracao($nuSeqConfig) {
    $obBusiness = new Fnde_Sice_Business_VinculaConfPerfil();
    $vinculos = $obBusiness->getVinculoConfiguracaoPerfilPorConfiguracao($nuSeqConfig);

    $bolsa = array();
    $bolsa['perfis'] = array();
    $obBusinessTipoPerfil = new Fnde_Sice_Business_TipoPerfil();
    $obBusinessValorBolsaPerfil = new Fnde_Sice_Business_ValorBolsaPerfil();
    $obBusinessCriterioAvaliacao = new Fnde_Sice_Business_CriterioAvaliacao();
    for ($i = 0; $i < count($vinculos); $i++) {

      $perfil = $obBusinessTipoPerfil->getTipoPerfilById($vinculos[$i]['NU_SEQ_TIPO_PERFIL']);
      $bolsa["perfis"]['perfil'][$i]['nome'] = $perfil['DS_TIPO_PERFIL'];
      $bolsa["perfis"]['perfil'][$i]['qtBolsa'] = $vinculos[$i]['QT_BOLSA_PERIODO'];

      $bolsa["perfis"]['perfil'][$i]['valores'] = array();

      $valores = $obBusinessValorBolsaPerfil->getValorBolsaPerfilPorVinculo(
              $vinculos[$i]['NU_SEQ_VINC_CONF_PERF']);
      for ($j = 0; $j < count($valores); $j++) {

        $bolsa["perfis"]['perfil'][$i]['valores'][$j]['atTurma'] = $valores[$j]['QT_TURMA'];
        $bolsa["perfis"]['perfil'][$i]['valores'][$j]['valor'] = $valores[$j]['VL_BOLSA'];
      }
    }

    $bolsa['avaliacaoPedagogica'] = array();

    $criterioAvaliacao = $obBusinessCriterioAvaliacao->getCriterioAvaliacaoByIdConfiguracao($nuSeqConfig);

    for ($k = 0; $k < count($criterioAvaliacao); $k++) {
      $bolsa['avaliacaoPedagogica']['criterioAvaliacao'][$k]['situacao'] = $criterioAvaliacao[$k]['DS_SITUACAO'];
      $bolsa['avaliacaoPedagogica']['criterioAvaliacao'][$k]['min'] = $criterioAvaliacao[$k]['NU_MINIMO'];
      $bolsa['avaliacaoPedagogica']['criterioAvaliacao'][$k]['max'] = $criterioAvaliacao[$k]['NU_MAXIMO'];
    }

    $obBusiness = new Fnde_Sice_Business_Configuracao();
    $rsConfig = $obBusiness->getConfiguracaoById($nuSeqConfig);
    $bolsa['novaConfiguracao']['dataInicioConfiguracao'] = $rsConfig['DT_INICIO_NOVA_CONFIG'];
    $bolsa['novaConfiguracao']['dataExpiracaoConfiguracao'] = $rsConfig['DT_TERMINO_NOVA_CONFIG'];
    $bolsa['novaConfiguracao']['qtdTurmaTutor'] = $rsConfig['QT_TURMA_TUTOR'];
    $bolsa['novaConfiguracao']['qtdAlunoTurma'] = $rsConfig['QT_ALUNOS_TURMA'];

    return $bolsa;
  }

  /**
   * Método acessório get de nameList.
   */
  public function getNameList() {
    return $this->_arList;
  }

  /**
   * Método acessório set de nameList.
   * @param array $arList
   */
  public function setNameList($arList) {
    $this->_arList = $arList;
  }

  /**
   * Método acessório get de title.
   */
  public function getTitles() {
    return $this->_arTitles;
  }

  /**
   * Método acessório set de title.
   * @param array $arTitles
   */
  public function setTitles($arTitles) {
    $this->_arTitles = $arTitles;
  }

  /**
   * Método acessório get do formulário de pesquisa de configuração.
   * @param array $arDados
   * @param array $obGrid
   */
  public function getFormFilter($arDados = array(), $obGrid = null) {
    $form = new Configuracao_FormFilter($arDados);
    $form->setAction($this->view->baseUrl() . '/index.php/manutencao/configuracao/list')->setMethod('post');

    return $form;
  }

  /**
   * Método responsável por gravar uma configuração no banco de dados.
   *
   * @author vinicius.cancado
   * @since 30/03/2012
   */
  public function salvarConfiguracaoAction() {
    $this->setTitle('Configuracao');
    $this->setSubtitle('Cadastro');

    $obBusinessConfiguracao = new Fnde_Sice_Business_Configuracao();

    //monta menu de contexto
    $menu = array($this->getUrl('manutencao', 'configuracao', 'list', ' ') => 'filtrar',
        $this->getUrl('manutencao', 'configuracao', 'form', ' ') => 'cadastrar');
    $this->setActionMenu($menu);

    // Se os dados não foram enviados por post retorna para a index
    if (!$this->getRequest()->isPost()) {
      return $this->_forward('index');
    }

    $dadosConfig = array();


    $arParams = $this->_request->getParams();
    $form = $this->getForm($arParams);

    if (!$form->isValid($arParams)) {

      $html = $form->getElement("htmlNovaConf");
      $str = $this->retornaHtmlNovaConf() . "<br/>" . $this->retornaHtmlTurma() . "<br/>"
              . $this->retornaHtmlGridBolsa() . "<br/>" . $this->retornaHtmlGridCriterioAvaliacao()
              . "</fieldset>";
      $html->setValue($str);
      $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
      $this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
      $this->view->form = $form;
      return $this->render("form");
    }
      //Dados Configuração
      if($arParams["NU_SEQ_CONFIGURACAO"] != ''){
          $dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"] = $arParams["NU_SEQ_CONFIGURACAO"];

        $d = explode('/', $arParams["DT_TERMINO_NOVA_CONFIG"]);
        $inicioTermino = $d[2] . '-' . $d[1] . '-' . $d[0];

        if (strtotime(date('Y-m-d')) < strtotime($inicioTermino)) {
          $dadosConfig['configuracao']["ST_CONFIGURACAO"] = "A";
        }else{
          $dadosConfig['configuracao']["ST_CONFIGURACAO"] = "D";
        }
      }else{
          $dadosConfig['configuracao']["ST_CONFIGURACAO"] = "A";
      }
      $dadosConfig['configuracao']["DT_INICIO_NOVA_CONFIG"] = $arParams["DT_INICIO_NOVA_CONFIG"];
      $dadosConfig['configuracao']["DT_TERMINO_NOVA_CONFIG"] = $arParams["DT_TERMINO_NOVA_CONFIG"];
      $dadosConfig['configuracao']["QT_TURMA_TUTOR"] = $arParams["QT_TURMA_TUTOR"];
      $dadosConfig['configuracao']["QT_ALUNOS_TURMA"] = $arParams["QT_ALUNOS_TURMA"];
      $dadosConfig['configuracao']["NU_SEQ_TIPO_CURSO"] = 1;
      $dadosConfig['configuracao']["DT_INCLUSAO"] = date("d/m/Y");

      //Dados Bolsa
      $i = 0;
      foreach($arParams['BOLSA'] as $bolsas){
          if($bolsas["NU_SEQ_VINC_CONF_PERF"] != ''){
            $dadosConfig['valorbolsa'][$i]["NU_SEQ_VINC_CONF_PERF"] = $bolsas["NU_SEQ_VINC_CONF_PERF"];
          }
          $dadosConfig['valorbolsa'][$i]["QT_BOLSA_PERIODO"] = $bolsas["QT_BOLSA"];
          $dadosConfig['valorbolsa'][$i]["NU_SEQ_TIPO_PERFIL"] = $bolsas["NU_SEQ_TIPO_PERFIL"];

          if($bolsas["NU_SEQ_VAL_BOLSA_PERF"] != ''){
              $dadosConfig['valorbolsa'][$i]["NU_SEQ_VAL_BOLSA_PERF"] = $bolsas["NU_SEQ_VAL_BOLSA_PERF"];
          }
          $dadosConfig['valorbolsa'][$i]['QT_TURMA'] = $bolsas["QT_TURMA"];
          $dadosConfig['valorbolsa'][$i]['VL_BOLSA'] = $bolsas["VL_BOLSA"];
          $i++;
      }

      //Dados Avaliação
      $i = 0;
      foreach($arParams['AVALIACAO'] as $situacao => $avaliacao){
          if($avaliacao["NU_SEQ_CRITERIO_AVAL"] != ''){
              $dadosConfig['avaliacao'][$i]["NU_SEQ_CRITERIO_AVAL"] = $avaliacao["NU_SEQ_CRITERIO_AVAL"];
          }
          $dadosConfig['avaliacao'][$i]['DS_SITUACAO'] = $situacao;
          $dadosConfig['avaliacao'][$i]['NU_MINIMO'] = $avaliacao["NU_MINIMO"];
          $dadosConfig['avaliacao'][$i]['NU_MAXIMO'] = $avaliacao["NU_MAXIMO"];
          $i++;
      }
    try {
      $datasConflitantes = $obBusinessConfiguracao->verificaDatasConflitantes($dadosConfig['configuracao']);
      if ($datasConflitantes['conflito'] == true) {
        $this->addMessage(Fnde_Message::MSG_ERROR, $datasConflitantes['mensagem']);
        if($dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"]){
          $this->_redirect("/manutencao/configuracao/visualizar-configuracao/v/1/NU_SEQ_CONFIGURACAO/" . $dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"]);
        }else{
          $this->_redirect("/manutencao/configuracao/form");
        }
      } else {
          $salvarDados = $obBusinessConfiguracao->salvarConfiguracao($dadosConfig);

        if($salvarDados['retorno']){
          $this->addMessage(Fnde_Message::MSG_SUCCESS, $salvarDados['mensagem']);
          $this->_redirect("/manutencao/configuracao/visualizar-configuracao/v/1/NU_SEQ_CONFIGURACAO/" . $salvarDados['idconfiguracao']);
        }else if(!$salvarDados['retorno'] && $dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"]){
          $this->addMessage(Fnde_Message::MSG_ERROR, $salvarDados['mensagem']);
          $this->_redirect("/manutencao/configuracao/visualizar-configuracao/v/1/NU_SEQ_CONFIGURACAO/" . $dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"]);
        }else if(!$salvarDados['retorno'] && $dadosConfig['configuracao']["NU_SEQ_CONFIGURACAO"]){
          $html = $form->getElement("htmlNovaConf");
          $str = $this->retornaHtmlNovaConf() . "<br/>" . $this->retornaHtmlTurma() . "<br/>"
              . $this->retornaHtmlGridBolsa() . "<br/>" . $this->retornaHtmlGridCriterioAvaliacao()
              . "</fieldset>";
          $html->setValue($str);
          $this->addInstantMessage(Fnde_Message::MSG_ERROR, $salvarDados['mensagem']);
          $this->addInstantMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::listarCamposComErros($form));
          $this->view->form = $form;
          return $this->render("form");
        }
      }
    } catch (Exception $e) {
      $html = $form->getElement("htmlNovaConf");
      $str = $this->retornaHtmlNovaConf() . "<br/>" . $this->retornaHtmlTurma() . "<br/>"
              . $this->retornaHtmlGridBolsa() . "<br/>" . $this->retornaHtmlGridCriterioAvaliacao()
              . "</fieldset>";
      $html->setValue($str);
      $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
      $this->view->form = $form;
      return $this->render('form');
    }

    $html = $form->getElement("htmlNovaConf");
    $str = $this->retornaHtmlNovaConf() . "<br/>" . $this->retornaHtmlTurma() . "<br/>"
            . $this->retornaHtmlGridBolsa() . "<br/>" . $this->retornaHtmlGridCriterioAvaliacao() . "</fieldset>";
    $html->setValue($str);
    $this->view->form = $form;
    return $this->render('form');
  }

  /**
   * Método acessório get de arTitleList
   */
  public function getArTitlesList() {
    return array('nuSeqConfiguracao', 'dtTerminoNovaConfig', 'dtInclusao', 'qtTurmaTutor', 'dtAlteracao',
        'qtAlunosTurma', 'vlBolsa', 'stConfiguracao', 'qtTurmasPerfil', 'STipoCurso', 'dtInicioNovaConfig',
        'dtTerminoVigencia', 'dtIniVigencia',);
  }

  /**
   * Método para abrir e ler o arquivo XML com as informações de configuração.
   *
   * @author vinicius.cancado
   * @since 30/03/2012
   */
  public function lerArquivoConfiguracao() {
    $entrou = false;
    $record = array();

    if (!$entrou) {
      $nomeArquivo = "ArquivoConfiguracao.xml";
      $caminhoArquivo = dirname(__FILE__);
      $arquivo = $caminhoArquivo . "/" . $nomeArquivo;

      if (file_exists($arquivo)) {
        try {
          $entrou = true;
          $xml = simplexml_load_file($arquivo);
          Fnde_Sice_Business_Componentes::validaXmlConfiguracao($caminhoArquivo, $nomeArquivo);

          $record['configuracaoVingente'] = $this->getConfiguracaoVigente($xml);

          $record['novaConfiguracao'] = $this->getNovaConfiguracao($xml);

          $record['perfis'] = $this->getPerfisConfiguracao($xml);

          $record['avaliacaoPedagogica'] = $this->getAvaliacaoPedagogicaConfiguracao($xml);
        } catch (Exception $e) {
          $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
          $this->_redirect("/manutencao/configuracao/list");
        }
      } else {
        $this->addMessage(Fnde_Message::MSG_ERROR, "Arquivo não encontrado");
        $this->_redirect("/manutencao/configuracao/list");
      }
    }
    $this->view->record = $record;
  }

  /**
   * Recupera o valor de configuração vigente do arquivo XML
   * @param $xml
   */
  private function getConfiguracaoVigente($xml) {
    $record['configuracaoVingente'] = array();

    foreach ($xml->configuracaoVigente as $configuracaoVingente) {
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($configuracaoVingente->dataInicioVigencia, "Início da Vingência");

      $record['configuracaoVingente']['identificadorTipoCurso'] = $configuracaoVingente->identificadorTipoCurso;
      $record['configuracaoVingente']['dataInicioVingencia'] = $configuracaoVingente->dataInicioVigencia;
    }

    return $record['configuracaoVingente'];
  }

  /**
   * Recupera o valor da nova configruação do arquivo XML
   * @param $xml
   */
  private function getNovaConfiguracao($xml) {
    $record['novaConfiguracao'] = array();

    foreach ($xml->novaConfiguracao as $novaConfiguracao) {
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($novaConfiguracao->dataInicioConfiguracao, "Dt. Início");
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($novaConfiguracao->dataExpiracao, "Dt. Expiração");

      $record['novaConfiguracao']['dataInicioConfiguracao'] = $novaConfiguracao->dataInicioConfiguracao;
      $record['novaConfiguracao']['dataExpiracaoConfiguracao'] = $novaConfiguracao->dataExpiracao;
    }

    foreach ($xml->novaConfiguracao->turma as $turma) {
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($turma->qtdTurmaTutor, "Qtd. de turmas por tutor");
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($turma->qtdAlunoTurma, "Qtd. de alunos por turma");

      $record['novaConfiguracao']['qtdTurmaTutor'] = $turma->qtdTurmaTutor;
      $record['novaConfiguracao']['qtdAlunoTurma'] = $turma->qtdAlunoTurma;
    }

    return $record['novaConfiguracao'];
  }

  /**
   * Recupera o valor dos perfis do arquivo XML
   * @param $xml
   */
  private function getPerfisConfiguracao($xml) {
    $i = 0;
    $j = 0;
    $record['perfis'] = array();

    foreach ($xml->novaConfiguracao->bolsa->perfis->perfil as $perfil) {
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($perfil->nomePerfil, "Perfil");
      Fnde_Sice_Business_Componentes::validaXmlCampoVazio($perfil->qtdBolsaPeriodo, "Qtd. de Bolsas por período");
      $record["perfis"]['perfil'][$j]['nome'] = $perfil->nomePerfil;
      $record["perfis"]['perfil'][$j]['qtBolsa'] = $perfil->qtdBolsaPeriodo;
      $record["perfis"]['perfil'][$j]['valores'] = array();

      foreach ($perfil->valores as $valores) {
        $record['perfis']['perfil'][$j]['valores'][$i] = array();
        foreach ($valores->valor as $valor) {
          Fnde_Sice_Business_Componentes::validaXmlCampoVazio($valor->qtdTurma, "Qtd. Turma");
          Fnde_Sice_Business_Componentes::validaXmlCampoVazio($valor->valorBolsa, "Valor da Bolsa");
          $record["perfis"]['perfil'][$j]['valores'][$i]['atTurma'] = $valor->qtdTurma;
          $record["perfis"]['perfil'][$j]['valores'][$i]['valor'] = $valor->valorBolsa;
          $i++;
        }
      }
      $j++;
      $i = 0;
    }

    return $record['perfis'];
  }

  /**
   * Recupera o valor da avaliação pedagógica do arqivo XML
   * @param $xml
   */
  private function getAvaliacaoPedagogicaConfiguracao($xml) {
    $i = 0;
    $record['avaliacaoPedagogica'] = array();

    foreach ($xml->novaConfiguracao->avaliacaoPedagogica as $criterioAvaliacao) {
      foreach ($criterioAvaliacao->avaliacao as $avaliacao) {
        Fnde_Sice_Business_Componentes::validaXmlCampoVazio($avaliacao->situacao, "Situação");
        Fnde_Sice_Business_Componentes::validaXmlCampoVazio($avaliacao->criterioAvalicao->min, "Critério de Avaliação");
        Fnde_Sice_Business_Componentes::validaXmlCampoVazio($avaliacao->criterioAvalicao->max, "Critério de Avaliação");
        $record['avaliacaoPedagogica']['criterioAvaliacao'][$i] = array('situacao' => $avaliacao->situacao,
            'min' => $avaliacao->criterioAvalicao->min, 'max' => $avaliacao->criterioAvalicao->max,);
        $i++;
      }
    }

    return $record['avaliacaoPedagogica'];
  }

  /**
   * Método para escrever o HTML da tela de configuração na parte "Nova Configuração".
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function retornaHtmlNovaConf() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    $admin = '';
    if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
        strripos($_SERVER ['REQUEST_URI'], 'visualizar-configuracao')){
      $admin = "disabled = 'disabled'";
    }
    $dados = $this->view->registros['configuracao'];
    $html = '<fieldset>';
    $html .= "<input type='hidden'name='NU_SEQ_CONFIGURACAO' value='". $dados['NU_SEQ_CONFIGURACAO'] ."' size='10'/>";
    $html .= '<legend>Dados de Vigência</legend>';
    $html .= "<table>";
    $html .= "<tr>";
    $html .= "<td>";
    $html .= "<span class='campoRequerido'>Dt. Início: </span>";
    $html .= "<input type='text' name='DT_INICIO_NOVA_CONFIG' class='date dp-applied' disabled='disabled' value=\"";
    $html .= date('d/m/Y');
    $html .= "\" required/>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>";
    $html .= "<span>Dt. Expiração: </span>";
    $html .= "<input disabled='disabled' type='text' name='DT_TERMINO_NOVA_CONFIG' class='date dp-applied' $admin value=\"";
    $html .= $dados["DT_TERMINO_NOVA_CONFIG"];
    $html .= "\"/>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</table>";

    return $html;
  }

  /**
   * Método para escrever o HTML da tela de configuração na parte "Turma".
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function retornaHtmlTurma() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    $admin = '';
    if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
        strripos($_SERVER ['REQUEST_URI'], 'visualizar-configuracao')){
      $admin = "disabled = 'disabled'";
    }

    $dados = $this->view->registros['configuracao'];
    $html = '<fieldset>';
    $html .= '<legend>Turma</legend>';
    $html .= "<table>";
    $html .= "<tr>";
    $html .= "<td>";
    $html .= "<span class='campoRequerido'>Qtd. de turmas por tutor: </span>";
    $html .= "<input type='text' name='QT_TURMA_TUTOR' $admin value=\"";
    $html .= $dados["QT_TURMA_TUTOR"];
    $html .= "\" required/>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>";
    $html .= "<span class='campoRequerido'>Qtd. de alunos por turma: </span>";
    $html .= "<input type='text' name='QT_ALUNOS_TURMA' $admin value=\"";
    $html .= $dados["QT_ALUNOS_TURMA"];
    $html .= "\" required/>";
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</table>";
    $html .= "</fieldset>";

    return $html;
  }

  /**
   * Método para escrever o HTML da tela de configuração na parte "Grid de Bolsa".
   *
   * @author diego.matos
   * @since 30/03/2012
   */
    public function retornaHtmlGridBolsa() {
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $admin = '';
      if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
          strripos($_SERVER ['REQUEST_URI'], 'visualizar-configuracao')){
            $admin = "disabled = 'disabled'";
        }

        $dados = $this->view->registros['valorbolsa'];
        if(!isset($dados)){
            $dados[0]["NU_SEQ_TIPO_PERFIL"] = '6';
            $dados[1]["NU_SEQ_TIPO_PERFIL"] = '6';
            $dados[2]["NU_SEQ_TIPO_PERFIL"] = '5';
            $dados[3]["NU_SEQ_TIPO_PERFIL"] = '8';
        }
        $html = '<fieldset>';
        $html .= '<legend>Bolsa</legend>';
        $html .= '<div class="listagem">';
        $html .= "<table><tr><th rowspan='2'>Perfil</th><th rowspan='2'>Qtd. de Bolsas por período</th><th colspan='2'>Valores</th></tr><tr><th width='20%'>Qtd. Turma</th><th width='20%'>Valor da Bolsa</th></tr>";

        $x = 1;
        foreach($dados as $i => $perfis){
            $objTipoPerfil = new Fnde_Sice_Business_TipoPerfil();
            $tipo_perfil = $objTipoPerfil->getTipoPerfilById($perfis["NU_SEQ_TIPO_PERFIL"]);
            if($tipo_perfil['DS_TIPO_PERFIL'] == 'Tutor'){
              $tipo_perfil['DS_TIPO_PERFIL'] .= " $x";
              $x++;
            }
            if(isset($perfis["VL_BOLSA"])){
                $valor = number_format($perfis["VL_BOLSA"], 2, ',', '.');
            }
            $html .= '<tr>';
            $html .= "<td>".$tipo_perfil['DS_TIPO_PERFIL']."</td>";
            $html .= "<input type='hidden' name='BOLSA[$i][NU_SEQ_VINC_CONF_PERF]' value='". $perfis['NU_SEQ_VINC_CONF_PERF'] ."' size='10'/>";
            $html .= "<input type='hidden' name='BOLSA[$i][NU_SEQ_VAL_BOLSA_PERF]' value='". $perfis['NU_SEQ_VAL_BOLSA_PERF'] ."' size='10'/>";
            $html .= "<td align='center'><input type='text' name='BOLSA[$i][QT_BOLSA]' $admin value='".$perfis["QT_BOLSA_PERIODO"]."' size='3' required/></td>";
            $html .= "<td align='center'><input type='text' name='BOLSA[$i][QT_TURMA]' $admin value='".$perfis["QT_TURMA"]."' size='3' required/></td>";
            $html .= "<td align='center'>R$ <input type='text' class ='valor' name='BOLSA[$i][VL_BOLSA]' $admin value='".$valor."' size='10'/></td>";
            $html .= "<input type='hidden'name='BOLSA[$i][NU_SEQ_TIPO_PERFIL]' $admin value='". $tipo_perfil['NU_SEQ_TIPO_PERFIL'] ."' size='10'/>";
            $html .= '</tr>';
        }
        $html .= "</table>";
        $html .= "</div>";
        $html .= "</fieldset>";
        return $html;
    }

  /**
   * Método para escrever o HTML da tela de configuração na parte "Grid de Critério de Avaliação".
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function retornaHtmlGridCriterioAvaliacao() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    $admin = '';
    if ( !in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
        strripos($_SERVER ['REQUEST_URI'], 'visualizar-configuracao')){
      $admin = "disabled = 'disabled'";
    }
      $dados = $this->view->registros['avaliacao'];
      if(!isset($dados)){
          $dados[0]["DS_SITUACAO"] = 'Aprovado com destaque';
          $dados[1]["DS_SITUACAO"] = 'Aprovado';
          $dados[2]["DS_SITUACAO"] = 'Reprovado';
      }

    $html = '<fieldset>';
    $html .= '<legend>Avaliação Pedagógica</legend>';
    $html .= '<div class="listagem">';
    $html .= "<table><tr><th>Situação</th><th>Critério de Avaliação</th></tr>";

      if(!function_exists('filtra_desistente_func')) {
          function filtra_desistente_func($value)
          {
              return $value['DS_SITUACAO'] != 'Desistente';
          }
      }

      $dados = array_filter($dados, 'filtra_desistente_func');

    foreach ($dados as $criterio) {
        $html .= "<tr>";
        $html .= "<input type='hidden' name='AVALIACAO[". $criterio['DS_SITUACAO'] . "][NU_SEQ_CRITERIO_AVAL]' value='". $criterio['NU_SEQ_CRITERIO_AVAL'] ."' size='10'/>";
        $html .= "<td>" . $criterio ["DS_SITUACAO"] . "</td>";
        $html .= "<td><center>
            <input type='text' name='AVALIACAO[". $criterio['DS_SITUACAO'] . "][NU_MINIMO]' $admin value='".$criterio['NU_MINIMO']."' size='3' style='text-align:right'/>% a
            <input type='text' name='AVALIACAO[". $criterio['DS_SITUACAO'] . "][NU_MAXIMO]' $admin value='".$criterio['NU_MAXIMO']."' size='3' style='text-align:right'/>%
            </center></td>";

        $html .= "</tr>";
    }

    $html .= "<tr><td>Desistente</td><td><center>.....................</center></td></tr>";
    $html .= "</table>";
    $html .= "</div>";
    $html .= "</fieldset>";

    return $html;
  }

  /**
   * Método para limpar a pesquisa feita anteriormente.
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function clearSearchAction() {

    //limpa sessão
    Zend_Session::namespaceUnset('searchParam');

    //redireciona para pagina de listagem da ultima sessão
    $this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
  }

  /**
   * Método para recuperar os parâmetros usados na pesquisa.
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function getSearchParamConfiguracao() {
    $arFilter = array();

    $this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');

    $arSession = $this->urlFilterNamespace->param;

    $stUrlAtual = $this->_getParam('module') . '/' . $this->_getParam('controller') . '/'
            . $this->_getParam('action');
    $stUrlSession = $arSession['module'] . '/' . $arSession['controller'] . '/' . $arSession['action'];

    if ($stUrlAtual == $stUrlSession) {
      $arFilter = $arSession;
    }
    return $arFilter;
  }

  /**
   * Método para visualizar uma configuração gravada no sistema.
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function visualizarConfiguracaoAction() {
    $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
    $perfilUsuario = $usuarioLogado->credentials;

    $this->setTitle('Configuração');
    $this->setSubtitle('Cadastrar');

    //monta menu de contexto
    if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)){
      $menu[$this->getUrl('manutencao', 'configuracao', 'form', ' ')] =  'cadastrar';
    }
    $menu[$this->getUrl('manutencao', 'configuracao', 'list', ' ')] = 'filtrar';
    $this->setActionMenu($menu);

    // Recuperando array de dados do banco para setar valores no formulário
    $configuracao = new Fnde_Sice_Business_Configuracao();

    if ($this->getRequest()->getParam("NU_SEQ_CONFIGURACAO")) {
      $arDados = $configuracao->getDadosConfiguracaoById($this->getRequest()->getParam("NU_SEQ_CONFIGURACAO"));
      $this->view->nu_seq_conf = $this->getRequest()->getParam("NU_SEQ_CONFIGURACAO");
    }
    $arDados['v'] = $this->getRequest()->getParam("v");
    //Recupera o objeto de formulário para validação
    $form = $this->getForm($arDados);


    // recupera dados da configuração anterior

    $dadosConfiguracaoAnterior = $configuracao->getPreviousSelectedConfig($arDados['configuracao']['NU_SEQ_CONFIGURACAO']);

    //echo "<pre>";print_r($dadosConfiguracaoAnterior);die;

    if ($arDados['NU_SEQ_CONFIGURACAO']) {
        $this->view->form = $form->populate($arDados);
    } else {
        $this->view->form = $this->getForm($dadosConfiguracaoAnterior, null, true);
    }
    $this->render('form');
  }
}
