<?php

/**
 * Business do Configuracao
 * 
 * @author diego.matos
 * @since 30/03/2012
 */
class Fnde_Sice_Business_Configuracao {

  /**
   * Retorna array das colunas do termo de referencia para grid
   *
   * @access protected
   * @return object - Objeto de Business
   *
   */
  public function getColumnsSearch() {
    return array('NU_SEQ_CONFIGURACAO' => 'C.NU_SEQ_CONFIGURACAO', 'QT_TURMA_TUTOR' => 'C.QT_TURMA_TUTOR',
        'QT_ALUNOS_TURMA' => 'C.QT_ALUNOS_TURMA', 'DT_INI_VIGENCIA' => 'C.DT_INI_VIGENCIA',
        'DT_TERMINO_VIGENCIA' => 'C.DT_TERMINO_VIGENCIA', 'DT_INCLUSAO' => 'C.DT_INCLUSAO',
        'DT_ALTERACAO' => 'C.DT_ALTERACAO', 'ST_CONFIGURACAO' => 'C.ST_CONFIGURACAO',);
  }

  /**
   * monta filtro para consultar TOR
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function setFilter($select, $arParams) {
    if (isset($arParams['NU_SEQ_CONFIGURACAO']) && isset($arParams['id'])) {
      $select->where("C.NU_SEQ_CONFIGURACAO = {$arParams['id']} ");
    } else {
      $this->setMonFiltroConfiguracao($select, $arParams, 'NU_SEQ_CONFIGURACAO');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_TERMINO_NOVA_CONFIG');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_INCLUSAO');
      $this->setMonFiltroConfiguracao($select, $arParams, 'QT_TURMA_TUTOR');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_ALTERACAO');
      $this->setMonFiltroConfiguracao($select, $arParams, 'QT_ALUNOS_TURMA');
      $this->setMonFiltroConfiguracao($select, $arParams, 'ST_CONFIGURACAO');
      $this->setMonFiltroConfiguracao($select, $arParams, 'NU_SEQ_TIPO_CURSO');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_INICIO_NOVA_CONFIG');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_TERMINO_VIGENCIA');
      $this->setMonFiltroConfiguracao($select, $arParams, 'DT_INI_VIGENCIA');
    }
  }

  /**
   * M�todo que auxilia a montagem dos filtros
   * @param $select
   * @param array $arParams
   * @param string $descricao
   */
  private function setMonFiltroConfiguracao($select, $arParams, $descricao) {
    if ($arParams[$descricao]) {
      $select->where("C." . $descricao . " = ?", $arParams[$descricao]);
    }
  }

  /**
   * Recupera select para listagem
   *
   * @access public
   * @return object - Objeto Select
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function getSelect($arColumns) {

    $obModelo = new Fnde_Sice_Model_Configuracao();
    $arInfoModelo = $obModelo->info();

    $select = $obModelo->select()->setIntegrityCheck(false)->from(
                    array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
    return $select;
  }

  /**
   * Deleta publica��o vinculada ao Edital
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function del($id) {
    $obModelo = new Fnde_Sice_Model_Configuracao();

    $logger->log('Tor!', Zend_Log::INFO);
    try {
      $where = "NU_SEQ_CONFIGURACAO = " . $id;
      $obModelo->delete($where);

      $logger->log("Configuracao removido com sucesso !", Zend_Log::INFO);

      $this->stMensagem = "Configuracao removido com sucesso !";
      return $this->stMensagem;
    } catch (Exception $e) {
      $logger->log($e->getMessage(), Zend_Log::WARN);
      $this->stMensagem[] = "Erro ao tentar Excluir";
    }
  }

  /**
   * Seleciona Configuracao
   * $desc = true para retornar a listagem em ordem decrescente
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function search($arParams, $desc = false) {
    $parCollumns = $this->getColumnsSearch();
    $parCollumns['DT_INI_VIGENCIA'] = "(TO_CHAR(C.DT_INICIO_NOVA_CONFIG, 'DD/MM/YYYY'))";
    $parCollumns['DT_TERMINO_VIGENCIA'] = "(TO_CHAR(C.DT_TERMINO_NOVA_CONFIG, 'DD/MM/YYYY'))";
    $parCollumns['DT_INCLUSAO'] = "(TO_CHAR(C.DT_INCLUSAO, 'DD/MM/YYYY'))";
    $parCollumns['DT_ALTERACAO'] = "(TO_CHAR(C.DT_ALTERACAO, 'DD/MM/YYYY  HH24:MI'))";
    $parCollumns['ST_CONFIGURACAO'] = "(CASE WHEN C.ST_CONFIGURACAO = 'A' THEN 'Ativo' WHEN ST_CONFIGURACAO = 'D' THEN 'Inativo' END)";
    $select = $this->getSelect($parCollumns);
    $this->setFilter($select, $arParams);
    if($desc){
        $this->setOrder($select->order('C.NU_SEQ_CONFIGURACAO DESC'));
    } else {
        $this->setOrder($select);
    }
    $stmt = $select->query();

    $result = $stmt->fetchAll();
    return $result;
  }

  /**
   * Seleciona Configuracao
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function getConfiguracaoSemDataTerminoVingencia() {
    $obModelo = new Fnde_Sice_Model_Configuracao();
    //$arInfoModelo = $obModelo->info();
    $select = $obModelo->select()->where("DT_TERMINO_VIGENCIA is null");
    $stmt = $select->query();
    $result = $stmt->fetch();
    return $result;
  }

  /**
   * Seleciona Configuracao
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function getUltimaConfiguracao() {
    $obModelo = new Fnde_Sice_Model_Configuracao();
    $select = $obModelo->select("C.NU_SEQ_CONFIGURACAO,
					  (TO_CHAR(C.DT_INI_VIGENCIA, 'DD/MM/YYYY'))     AS DT_INI_VIGENCIA,
					  (TO_CHAR(C.DT_TERMINO_VIGENCIA, 'DD/MM/YYYY')) AS DT_TERMINO_VIGENCIA,
					  (TO_CHAR(C.DT_INICIO_NOVA_CONFIG, 'DD/MM/YYYY')) AS DT_INICIO_NOVA_CONFIG,
					  (TO_CHAR(C.DT_TERMINO_NOVA_CONFIG, 'DD/MM/YYYY')) AS DT_TERMINO_NOVA_CONFIG,
					  C.QT_TURMA_TUTOR,
					  C.QT_ALUNOS_TURMA,
					  C.NU_SEQ_TIPO_CURSO,
					  (TO_CHAR(C.DT_INCLUSAO, 'DD/MM/YYYY'))         AS DT_INCLUSAO,
					  (TO_CHAR(C.DT_ALTERACAO, 'DD/MM/YYYY'))        AS DT_ALTERACAO,
					  C.ST_CONFIGURACAO  AS ST_CONFIGURACAO")->order('NU_SEQ_CONFIGURACAO DESC');
    $stmt = $select->query();
    $result = $stmt->fetch();
    return $result;
  }

  /**
   * Obtem Configuracao por Id
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function getConfiguracaoById($id = null, $notIn = false) {
    $obModelo = new Fnde_Sice_Model_Configuracao();

    $query = "SELECT C.NU_SEQ_CONFIGURACAO,
					  (TO_CHAR(C.DT_INI_VIGENCIA, 'DD/MM/YYYY'))     AS DT_INI_VIGENCIA,
					  (TO_CHAR(C.DT_TERMINO_VIGENCIA, 'DD/MM/YYYY')) AS DT_TERMINO_VIGENCIA,
					  (TO_CHAR(C.DT_INICIO_NOVA_CONFIG, 'DD/MM/YYYY')) AS DT_INICIO_NOVA_CONFIG,
					  (TO_CHAR(C.DT_TERMINO_NOVA_CONFIG, 'DD/MM/YYYY')) AS DT_TERMINO_NOVA_CONFIG,
					  C.QT_TURMA_TUTOR,
					  C.QT_ALUNOS_TURMA,
					  C.NU_SEQ_TIPO_CURSO,
					  (TO_CHAR(C.DT_INCLUSAO, 'DD/MM/YYYY'))         AS DT_INCLUSAO,
					  (TO_CHAR(C.DT_ALTERACAO, 'DD/MM/YYYY'))        AS DT_ALTERACAO,
					  C.ST_CONFIGURACAO  AS ST_CONFIGURACAO
					FROM SICE_FNDE.S_CONFIGURACAO C
					WHERE 1 = 1 ";
      if(!is_null($id) && !$notIn){
        $query .= " AND C.NU_SEQ_CONFIGURACAO = " . $id ;
      }else if(!is_null($id)){
        $query .= " AND C.NU_SEQ_CONFIGURACAO < ($id)";
      }

    $query .= " ORDER BY C.NU_SEQ_CONFIGURACAO DESC";
    $stm = $obModelo->getAdapter()->query($query);
    $result = $stm->fetch();
    return $result;
  }

  /**
   * Define Ordem para pesquisa
   *
   * @author diego.matos
   * @since 30/03/2012
   */
  public function setOrder($select) {

    $select->order('C.NU_SEQ_CONFIGURACAO');
  }

  /**
   * Seleciona o identificador da �ltima configura��o v�lida
   * @author diego.matos
   * @since 05/04/2012
   */
  public function obterUltimaConfiguracaoValida() {
    $obModelo = new Fnde_Sice_Model_Configuracao();
    //$arInfoModelo = $obModelo->info();

    $select = $obModelo->select()->where("ST_CONFIGURACAO = 'A'");

    $stmt = $select->query();
    $result = $stmt->fetch();

    return $result['NU_SEQ_CONFIGURACAO'];
  }

  /**
   * Obt�m a configura��o pelo tipo de curso a que ela pertence.
   * 
   * @param int $idTipoCurso
   */
  public function getConfiguracaoPorTipoCurso($idTipoCurso) {
    $obModelo = new Fnde_Sice_Model_Configuracao();

    $select = $obModelo->select()->where("NU_SEQ_TIPO_CURSO = $idTipoCurso")->where("ST_CONFIGURACAO = 'A'");

    $stm = $select->query();
    $result = $stm->fetch();
    return $result;
  }

  /**
   * Fun��o para validar se a data da nova configura��o entra em conflito com datas de configura��es j� cadastradas.
   * 
   * @param array $arConfiguracao
   */
    public function verificaDatasConflitantes($arConfiguracao) {
        echo '<pre>';
        $d = explode('/', $arConfiguracao['DT_INICIO_NOVA_CONFIG']);
        $inicioVigencia = $d[2] . '-' . $d[1] . '-' . $d[0];

        if($arConfiguracao['DT_TERMINO_NOVA_CONFIG'] != ""){
            $d = explode('/', $arConfiguracao['DT_TERMINO_NOVA_CONFIG']);
            $terminoVigencia = $d[2] . '-' . $d[1] . '-' . $d[0];
        }

        if (strtotime(date('Y-m-d')) > strtotime($inicioVigencia) && !isset($arConfiguracao['NU_SEQ_CONFIGURACAO'])) {
            $retorno['mensagem'] = "O in�cio da vig�ncia � menor que a data atual.";
            $retorno['conflito'] = true;
            return $retorno;
        }
        if(!is_null($terminoVigencia) && strtotime($inicioVigencia) >= strtotime($terminoVigencia)){
            $retorno['mensagem'] = "Termino da vig�ncia � menor que a data do in�cio da vig�ncia.";
            $retorno['conflito'] = true;
            return $retorno;
        }

        $query = " SELECT * ";
        $query .= " FROM SICE_FNDE.S_CONFIGURACAO ";
        $query .= " WHERE ST_CONFIGURACAO    = 'A' ";
        $query .= " AND TO_DATE ('" . $arConfiguracao['DT_INICIO_NOVA_CONFIG'] . "', 'DD/MM/YYYY') BETWEEN DT_INICIO_NOVA_CONFIG AND DT_TERMINO_NOVA_CONFIG";
        if(isset($arConfiguracao['NU_SEQ_CONFIGURACAO'])){
            $query .= " AND NU_SEQ_CONFIGURACAO  NOT IN (" . $arConfiguracao['NU_SEQ_CONFIGURACAO'] . ") ";
        }
        $obModelo = new Fnde_Sice_Model_Configuracao();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();

        if (count($result) >= 1) {
            $retorno['mensagem'] = "As datas da nova configura��o est�o conflitando com as datas de configura��es j� cadastradas.";
            $retorno['conflito'] = true;
            return $retorno;
        }

        $retorno['conflito'] = false;
        return $retorno;
    }

  /**
   * Fun��o para gravar uma configura��o no banco de dados.
   * 
   * @param array $arConfiguracao
   * @param array $configuracao
   */
  public function salvarConfiguracao($arConfiguracao, $configuracao = array()) {
    $obBusinessConfiguracao = new Fnde_Sice_Business_Configuracao();
    $obModelConfiguracao = new Fnde_Sice_Model_Configuracao();

    if(isset($arConfiguracao["configuracao"]["NU_SEQ_CONFIGURACAO"])){
      $configuracaoSemDataVingencia = $obBusinessConfiguracao->getConfiguracaoById($arConfiguracao["configuracao"]["NU_SEQ_CONFIGURACAO"], true);
    }else{
      $configuracaoSemDataVingencia = $obBusinessConfiguracao->getConfiguracaoById();
    }
    $obModelConfiguracao->getAdapter()->beginTransaction();
    $obModelConfiguracao->fixDateToBr();

    $retorno = array();

    try{
      //Salva data termino vigencia para ultima configura��o cadastrada.
        if (isset($configuracaoSemDataVingencia['NU_SEQ_CONFIGURACAO'])) {
            //desativa colocando a data da config vigente com data fim anterior a 1 dia da data inicial da config a ser cadastrada
            $data = explode('/', $arConfiguracao['configuracao']['DT_INICIO_NOVA_CONFIG']);
            $timestamp = strtotime($data[2].'-'.$data[1].'-'.$data[0] . '-1 days');

            $conf['DT_TERMINO_NOVA_CONFIG'] = date('d/m/Y', $timestamp);
            $conf['ST_CONFIGURACAO'] = 'D';
            $conf['DT_ALTERACAO'] = date("d/m/Y G:i:s");
            $where = "NU_SEQ_CONFIGURACAO = " . $configuracaoSemDataVingencia['NU_SEQ_CONFIGURACAO'];
            $obModelConfiguracao->update($conf, $where);
            $arConfiguracao['configuracao']["DT_INI_VIGENCIA"] = $configuracaoSemDataVingencia["DT_INICIO_NOVA_CONFIG"];
            $arConfiguracao['configuracao']["DT_TERMINO_VIGENCIA"] = $conf['DT_TERMINO_NOVA_CONFIG'];
        }
        //Verificar se � altera��o ou nova inclus�o de configura��o
        if(isset($arConfiguracao['configuracao']["NU_SEQ_CONFIGURACAO"])){
            $arConfiguracao['configuracao']['DT_ALTERACAO'] = date("d/m/Y G:i:s");
            $where = "NU_SEQ_CONFIGURACAO = " . $arConfiguracao['configuracao']["NU_SEQ_CONFIGURACAO"];
            $obModelConfiguracao->update($arConfiguracao['configuracao'], $where);
            $idConfiguracao = $arConfiguracao['configuracao']["NU_SEQ_CONFIGURACAO"];
        }else{
            $idConfiguracao = $obModelConfiguracao->insert($arConfiguracao['configuracao']);

            $arConfiguracao['avaliacao'][] = array(
                'DS_SITUACAO' => 'Desistente',
                'NU_MINIMO' => '0',
                'NU_MAXIMO' => '0'
            );
        }

        //Insere ou altera criterios de avalia��o.
        $obCriterioAvaliacao = new Fnde_Sice_Model_CriterioAvaliacao();
        $criterioAvaliacao = array();

        foreach ($arConfiguracao['avaliacao'] as $criterio) {
            $criterioAvaliacao["DS_SITUACAO"] = $criterio['DS_SITUACAO'] . "";
            $criterioAvaliacao["NU_MINIMO"] = $criterio['NU_MINIMO'] . "";
            $criterioAvaliacao["NU_MAXIMO"] = $criterio['NU_MAXIMO'] . "";
            $criterioAvaliacao["NU_SEQ_CONFIGURACAO"] = $idConfiguracao;
            if(!isset($criterio["NU_SEQ_CRITERIO_AVAL"])){
                $idAvaliacao = $obCriterioAvaliacao->insert($criterioAvaliacao);
            }else{
                $where = "NU_SEQ_CRITERIO_AVAL = ". $criterio["NU_SEQ_CRITERIO_AVAL"];
                $obCriterioAvaliacao->update($criterioAvaliacao, $where);
                $idAvaliacao = $criterio["NU_SEQ_CRITERIO_AVAL"];
            }
        }

        $obModeloVinc = new Fnde_Sice_Model_VinculaConfPerfil();
        $obModeloVl = new Fnde_Sice_Model_ValorBolsaPerfil();
        foreach($arConfiguracao['valorbolsa'] as $dadosBolsa){
            $vinculo = array();
            $vinculo["QT_BOLSA_PERIODO"] = $dadosBolsa["QT_BOLSA_PERIODO"] . "";
            $vinculo["NU_SEQ_TIPO_PERFIL"] = $dadosBolsa["NU_SEQ_TIPO_PERFIL"] . "";
            $vinculo["NU_SEQ_CONFIGURACAO"] = $idConfiguracao;
            if(!isset($dadosBolsa["NU_SEQ_VINC_CONF_PERF"])){
                $idVinculo = $obModeloVinc->insert($vinculo);
            }else{
                $where = "NU_SEQ_VINC_CONF_PERF = " . $dadosBolsa["NU_SEQ_VINC_CONF_PERF"];
                $idVinculo = $dadosBolsa["NU_SEQ_VINC_CONF_PERF"];
                $obModeloVinc->update($vinculo, $where);
            }

            $valorBolsaPerfil["VL_BOLSA"] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
                                            Fnde_Sice_Business_Componentes::REPLACE_PARA, $dadosBolsa["VL_BOLSA"]);
            $valorBolsaPerfil["QT_TURMA"] = ( string ) $dadosBolsa['QT_TURMA'];
            $valorBolsaPerfil["NU_SEQ_VINC_CONF_PERF"] = $idVinculo;
            if(!isset($dadosBolsa["NU_SEQ_VAL_BOLSA_PERF"])){
                $obModeloVl->insert($valorBolsaPerfil);
            }else{
                $where = "NU_SEQ_VAL_BOLSA_PERF = " . $dadosBolsa["NU_SEQ_VAL_BOLSA_PERF"];
                $obModeloVl->update($valorBolsaPerfil, $where);
            }
        }

        $obModelConfiguracao->getAdapter()->commit();
        $retorno['retorno'] = true;
        $retorno['idconfiguracao'] = $idConfiguracao;
        $retorno['mensagem'] = utf8_decode('Configuração Cadastrada com Sucesso.');
    }catch(Exception $e){
        $obModelConfiguracao->getAdapter()->rollback();
        $retorno['retorno'] = false;
        $retorno['mensagem'] = utf8_decode('Erro ao Cadastrar Configuração.');
    }

    return $retorno;
  }

  private function getPerfilConfiguracao($descricaoPerfil, &$vinculaPerfilConfiguracao, $i) {
    if (strcasecmp($descricaoPerfil, 'Coordenador Nacional Equipe') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 2;
    } else if (strcasecmp($descricaoPerfil, 'Coordenador Nacional Gestor') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 3;
    } else if (strcasecmp($descricaoPerfil, 'Coordenador Estadual') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 4;
    } else if (strcasecmp($descricaoPerfil, 'Coordenador Executivo Estadual') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 8;
    } else if (strcasecmp($descricaoPerfil, 'Articulador') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 5;
    } else if (strpos($descricaoPerfil, 'Tutor', 0) >= 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 6;
    } else if (strcasecmp($descricaoPerfil, 'Coordenador Nacional Administrador') == 0) {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 1;
    } else {
      $vinculaPerfilConfiguracao['vinculaPerfilConfiguracao'][$i]['NU_SEQ_TIPO_PERFIL'] = 7;
    }
  }

  /**
   * M�todo acess�rio get de mensagem.
   */
  public function getMensagem() {
    return $this->stMensagem;
  }

  /**
   * Obt�m a quantidade de alunos por turma de uma configura��o
   * @param unknown_type $idTurma
   */
  public function getQtAlunosPorTurma($idTurma) {
    $query = " SELECT CONF.QT_ALUNOS_TURMA AS QT_ALUNOS_TURMA FROM SICE_FNDE.S_CONFIGURACAO CONF ";
    $query .= "INNER JOIN SICE_FNDE.S_TURMA TUR ON CONF.NU_SEQ_CONFIGURACAO = TUR.NU_SEQ_CONFIGURACAO WHERE TUR.NU_SEQ_TURMA = "
            . $idTurma;

    $obModelo = new Fnde_Sice_Model_Configuracao();
    $stm = $obModelo->getAdapter()->query($query);
    $result = $stm->fetch();
    return $result['QT_ALUNOS_TURMA'];
  }
  /**
   * retorna dados da ultima configuração
   *
   * @return mixed
   */
  public function getPreviousConfig() {
    $query = "SELECT max(NU_SEQ_CONFIGURACAO) as nu_seq_configuracao_anterior FROM SICE_FNDE.S_CONFIGURACAO WHERE ST_CONFIGURACAO = 'D'";
    $obModelo = new Fnde_Sice_Model_Configuracao();
    $stm = $obModelo->getAdapter()->query($query);
    $result = $stm->fetch();

    return $this->getConfig($result['NU_SEQ_CONFIGURACAO_ANTERIOR']);
  }


    /**
     * retorna dados da configuração anterior à selecionada
     *
     * @return mixed
     */
    public function getPreviousSelectedConfig($nu_seq_configuracao) {
        $query = "SELECT max(NU_SEQ_CONFIGURACAO) as nu_seq_configuracao_anterior FROM SICE_FNDE.S_CONFIGURACAO WHERE ST_CONFIGURACAO = 'D' AND NU_SEQ_CONFIGURACAO < ".$nu_seq_configuracao;
        $obModelo = new Fnde_Sice_Model_Configuracao();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();

        if($result['NU_SEQ_CONFIGURACAO_ANTERIOR']){
            return $this->getConfig($result['NU_SEQ_CONFIGURACAO_ANTERIOR']);
        }

        return null;

    }

  /**
   * retorna dados da configuração pelo nu_seq, sem relacionamentos
   *
   * @param $nu_seq_config
   * @return mixed
   */
  public function getConfig($nu_seq_config) {
      $query = "SELECT * FROM SICE_FNDE.S_CONFIGURACAO WHERE NU_SEQ_CONFIGURACAO = " . $nu_seq_config;
      $obModelo = new Fnde_Sice_Model_Configuracao();
      $stm = $obModelo->getAdapter()->query($query);
      $result = $stm->fetch();
      return $result;
  }

  /**
   * retorna dados da configuração ativa, sem relacionamentos
   *
   * @param $nu_seq_config
   * @return mixed
   */
  public function getConfigAtiva() {
    $query = "SELECT NU_SEQ_CONFIGURACAO FROM SICE_FNDE.S_CONFIGURACAO WHERE ST_CONFIGURACAO = 'A'";
    $obModelo = new Fnde_Sice_Model_Configuracao();
    $stm = $obModelo->getAdapter()->query($query);
    $result = $stm->fetch();
    return $result['NU_SEQ_CONFIGURACAO'];
  }

  public function getDadosConfiguracaoById($idConfiguracao){
    $obModelo = new Fnde_Sice_Model_Configuracao();

    $dadosConfiguracao['configuracao'] = $this->getConfiguracaoById($idConfiguracao);

    $queryBolsa = "SELECT * FROM SICE_FNDE.S_VINCULA_CONF_PERFIL VC
                      INNER JOIN SICE_FNDE.S_VALOR_BOLSA_PERFIL VB
                  ON VB.NU_SEQ_VINC_CONF_PERF = VC.NU_SEQ_VINC_CONF_PERF
                  INNER JOIN SICE_FNDE.S_TIPO_PERFIL TP
                  ON TP.NU_SEQ_TIPO_PERFIL = VC.NU_SEQ_TIPO_PERFIL
                  where VC.NU_SEQ_CONFIGURACAO = {$idConfiguracao}";
    $stm = $obModelo->getAdapter()->query($queryBolsa);
    $dadosConfiguracao['valorbolsa'] = $stm->fetchAll();


    $queryBolsa = "SELECT * FROM SICE_FNDE.S_CRITERIO_AVALIACAO CA
                    where CA.NU_SEQ_CONFIGURACAO = {$idConfiguracao}";
    $stm = $obModelo->getAdapter()->query($queryBolsa);
    $dadosConfiguracao['avaliacao'] = $stm->fetchAll();

    return $dadosConfiguracao;

  }

}
