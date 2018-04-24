<?php

/**
 * Business do Turma
 *
 * @author diego.matos
 * @since 25/04/2012
 */
class Fnde_Sice_Business_Turma {


    const PRE_TURMA = 1;
    const SOLICITADO_AUTORIZACAO = 2;
    const AGUARDANDO_AUTORIZACAO = 3;
    const ATIVA = 4;
    const NAO_AUTORIZADA = 5;
    const SOLICITADO_CANCELAMENTO = 6;
    const REJEITAR_CANCELAMENTO = 7;
    const AGUARDANDO_CANCELAMENTO = 8;
    const CANCELADA = 9;
    const FINALIZACAO_ATRASADA = 10;
    const FINALIZADA = 11;
    const EM_AVALIACAO = 12;

	public function statusComPermissaoExclusao($perfilUsuario){

        if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario)
        ) {
            return array(
                self::CANCELADA,
                self::PRE_TURMA,
                self::NAO_AUTORIZADA
            );
        } else {
            return array(
                self::PRE_TURMA,
                self::NAO_AUTORIZADA
            );
        }

	}

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DT_INICIO' => 'C.DT_INICIO', 'CO_MUNICIPIO' => 'C.CO_MUNICIPIO',
				'NU_SEQ_TURMA' => 'C.NU_SEQ_TURMA', 'DT_FINALIZACAO' => 'C.DT_FINALIZACAO',
				'NU_SEQ_USUARIO_ARTICULADOR' => 'C.NU_SEQ_USUARIO_ARTICULADOR', 'CO_MESORREGIAO' => 'C.CO_MESORREGIAO',
				'DT_FIM' => 'C.DT_FIM', 'UF_TURMA' => 'C.UF_TURMA', 'NU_SEQ_USUARIO_TUTOR' => 'C.NU_SEQ_USUARIO_TUTOR',
				'NU_SEQ_CURSO' => 'C.NU_SEQ_CURSO', 'ST_TURMA' => 'C.ST_TURMA',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_TURMA']) ) {
			$select->where("C.NU_SEQ_TURMA = {$arParams['id']} ");
		} else {
			$this->setMonFiltroTurma($select, $arParams, 'DT_INICIO');
			$this->setMonFiltroTurma($select, $arParams, 'CO_MUNICIPIO');
			$this->setMonFiltroTurma($select, $arParams, 'NU_SEQ_TURMA');
			$this->setMonFiltroTurma($select, $arParams, 'DT_FINALIZACAO');
			$this->setMonFiltroTurma($select, $arParams, 'NU_SEQ_USUARIO_ARTICULADOR');
			$this->setMonFiltroTurma($select, $arParams, 'CO_MESORREGIAO');
			$this->setMonFiltroTurma($select, $arParams, 'DT_FIM');
			$this->setMonFiltroTurma($select, $arParams, 'UF_TURMA');
			$this->setMonFiltroTurma($select, $arParams, 'NU_SEQ_USUARIO_TUTOR');
			$this->setMonFiltroTurma($select, $arParams, 'NU_SEQ_CURSO');
			$this->setMonFiltroTurma($select, $arParams, 'ST_TURMA');
		}
	}

	/**
	 * Método que auxilia a montagem dos filtros
	 * @param $select
	 * @param array $arParams
	 * @param string $descricao
	 */
	private function setMonFiltroTurma( $select, $arParams, $descricao ) {
		if ( $arParams[$descricao] ) {
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
	 * @since 25/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_Turma();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	public function deletaEmCadeia($idTurma, $perfilUsuario)
	{

		$db = Zend_Db_Table::getDefaultAdapter();

		$db->beginTransaction();

		try {

			$statusComPermissaoExclusao = $this->statusComPermissaoExclusao($perfilUsuario);
			$statusComPermissaoExclusao = implode(',', $statusComPermissaoExclusao);

			$sql = <<<EOT

                SELECT DISTINCT TEMP.NU_SEQ_USUARIO
				  FROM (
						SELECT DISTINCT USU.NU_SEQ_USUARIO
						 FROM SICE_FNDE.S_USUARIO USU
						   INNER JOIN SICE_FNDE.S_TIPO_PERFIL PERF
							 ON USU.NU_SEQ_TIPO_PERFIL = PERF.NU_SEQ_TIPO_PERFIL
								AND PERF.DS_TIPO_PERFIL_SEGWEB = 'sice_cursista'

						   -- RELACIONA COM TURMA (INNER JOIN)
						   INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA CUR_TURM
							 ON CUR_TURM.NU_SEQ_USUARIO_CURSISTA = USU.NU_SEQ_USUARIO
						   INNER JOIN SICE_FNDE.S_TURMA TURM
							 ON CUR_TURM.NU_SEQ_TURMA = TURM.NU_SEQ_TURMA
								AND TURM.NU_SEQ_TURMA = {$idTurma}

						   -- RELACIONA COM OUTRA TURMA
						   LEFT JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA CUR_TURM_NAO_CANCE
							 ON CUR_TURM_NAO_CANCE.NU_SEQ_USUARIO_CURSISTA = USU.NU_SEQ_USUARIO
						   LEFT JOIN SICE_FNDE.S_TURMA TURM_NAO_CANCE
							 ON CUR_TURM_NAO_CANCE.NU_SEQ_TURMA = TURM_NAO_CANCE.NU_SEQ_TURMA
								AND TURM_NAO_CANCE.NU_SEQ_TURMA <> {$idTurma}

						 GROUP BY USU.NU_SEQ_USUARIO

						 -- BUSCO APENAS OS CURSISTAS QUE TEM TURMA CANCELADA, MAS NÃO TEM TURMAS COM OUTRO STATUS
						 HAVING COUNT(TURM_NAO_CANCE.NU_SEQ_TURMA) = 0
				  ) TEMP

					-- PARA VERIFICAR SE O USUÁRIO NÃO TEM OUTROS PERFIS
					LEFT JOIN SICE_FNDE.H_PERFIL_USUARIO HIST
					  ON TEMP.NU_SEQ_USUARIO = HIST.NU_SEQ_USUARIO
					LEFT JOIN SICE_FNDE.S_TIPO_PERFIL TPERF
					  ON HIST.NU_SEQ_TIPO_PERFIL = TPERF.NU_SEQ_TIPO_PERFIL
						 AND TPERF.DS_TIPO_PERFIL_SEGWEB <> 'sice_cursista'

					-- PARA VERIFICAR SE O USUÁRIO NÃO TEM BOLSA (NÃO É TUTOR OU ARTICULADOR)
					LEFT JOIN SICE_FNDE.S_BOLSA BOL
					  ON BOL.NU_SEQ_USUARIO = TEMP.NU_SEQ_USUARIO

					-- PARA VERIFICAR SE O USUÁRIO NÃO TEM BOLSA (NÃO É TUTOR OU ARTICULADOR) EM ALGUM MOMENTO
					LEFT JOIN SICE_FNDE.S_HISTORICO_BOLSA HBOL
					  ON HBOL.NU_SEQ_USUARIO = TEMP.NU_SEQ_USUARIO

				  WHERE
					-- NÃO PODE TER OUTROS PERFIS
					TPERF.NU_SEQ_TIPO_PERFIL IS NULL
					-- NÃO PODE TER BOLSA
					AND BOL.NU_SEQ_USUARIO IS NULL
					-- NÃO PODE TER TIDO BOLSA
					AND HBOL.NU_SEQ_USUARIO IS NULL

EOT;

            $usuarios = $db->fetchCol($sql);

            $sqlAgenda = <<<EOT
                SELECT CHAMADA.NU_SEQ_AGENDA_ENCONTRO, CHAMADA.NU_SEQ_CHAMADA_ELETRONICA
                FROM SICE_FNDE.S_AGENDA_ENCONTRO AGENDA
                    INNER JOIN SICE_FNDE.S_CHAMADA_ELETRONICA CHAMADA
                        ON AGENDA.NU_SEQ_AGENDA_ENCONTRO = CHAMADA.NU_SEQ_AGENDA_ENCONTRO
                    INNER JOIN SICE_FNDE.S_AVALIACAO_ENCONTRO AVALIACAO
                        ON CHAMADA.NU_SEQ_CHAMADA_ELETRONICA = AVALIACAO.NU_SEQ_CHAMADA_ELETRONICA
                WHERE AGENDA.NU_SEQ_TURMA = {$idTurma}
EOT;

            $agendas = $db->fetchAll($sqlAgenda);

            $idAgenda = array();
            $idChamada = array();
            foreach ($agendas as $agenda) {
                $idAgenda[$agenda['NU_SEQ_AGENDA_ENCONTRO']] = $agenda['NU_SEQ_AGENDA_ENCONTRO'];
                $idChamada[$agenda['NU_SEQ_CHAMADA_ELETRONICA']] = $agenda['NU_SEQ_CHAMADA_ELETRONICA'];
            }

            if (count($idAgenda)) {
                if (count($idChamada)) {
                    $db->delete('SICE_FNDE.S_AVALIACAO_ENCONTRO', array('NU_SEQ_CHAMADA_ELETRONICA IN (?)' => $idChamada));
                }

                $db->delete('SICE_FNDE.S_CHAMADA_ELETRONICA', array('NU_SEQ_AGENDA_ENCONTRO IN (?)' => $idAgenda));
            }
            $db->delete('SICE_FNDE.S_AGENDA_ENCONTRO', array('NU_SEQ_TURMA = ?' => $idTurma));

            $db->delete('SICE_FNDE.S_AVALIACAO_TURMA', array('NU_SEQ_TURMA = ?' => $idTurma));

            $db->delete('SICE_FNDE.S_AVALIACAO_CURSO', array('NU_SEQ_TURMA = ?' => $idTurma));

            $db->delete('SICE_FNDE.S_VINC_CURSISTA_TURMA', array('NU_SEQ_TURMA = ?' => $idTurma));

			if(!empty($usuarios)) {
				$db->delete('SICE_FNDE.H_PERFIL_USUARIO', array('NU_SEQ_USUARIO IN (?)' => $usuarios));

				$db->delete('SICE_FNDE.H_SITUACAO_USUARIO', array('NU_SEQ_USUARIO IN (?)' => $usuarios));

				$db->delete('SICE_FNDE.S_VINC_FORM_ACAD_USU', array('NU_SEQ_USUARIO IN (?)' => $usuarios));

				$db->delete('SICE_FNDE.S_USUARIO', array('NU_SEQ_USUARIO IN (?)' => $usuarios));
			}

            $db->delete('SICE_FNDE.S_HISTORICO_TURMA', array('NU_SEQ_TURMA = ?' => $idTurma));

            $db->delete('SICE_FNDE.S_TURMA', array('NU_SEQ_TURMA = ?' => $idTurma));

            $db->commit();

            $this->stMensagem = "Turma removida com sucesso!";

			return $this->stMensagem;

		} catch (Exception $e) {
			$db->rollback();
			throw $e;
		}
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_Turma();

		try {
			$where = "NU_SEQ_TURMA = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "Turma removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Seleciona Turma
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function search( $arParams ) {

		$select = $this->getSelect($this->getColumnsSearch());
		$this->setFilter($select, $arParams);
		$this->setOrder($select);
		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 * Obtem Turma por Id
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getTurmaById( $id ) {

		$obModelo = new Fnde_Sice_Model_Turma();
		//$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->where("NU_SEQ_TURMA = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Obtem turma por id retornando as datas formatadas dd/mm/yyyy.
	 * O método acima estava retornando as datas no formato dd/mm/yy.
	 * Também retorna a descrição da situação da turma através do campo DS_ST_TURMA.
	 * @param int $id
	 */
	public function getTurmaPorId( $id ) {
		$query = " SELECT TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY') AS DT_INICIO, ";
		$query .= " TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY') AS DT_FIM, ";
		$query .= " TUR.CO_MUNICIPIO, TUR.NU_SEQ_TURMA, TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') AS DT_FINALIZACAO, TUR.NU_SEQ_USUARIO_ARTICULADOR, ";
		$query .= " TUR.CO_MESORREGIAO, TUR.UF_TURMA, TUR.NU_SEQ_USUARIO_TUTOR, TUR.NU_SEQ_CURSO, TUR.ST_TURMA, TUR.NU_SEQ_CONFIGURACAO, ";
		$query .= " SIT.DS_ST_TURMA ";
		$query .= " FROM SICE_FNDE.S_TURMA TUR ";
		$query .= " INNER JOIN SICE_FNDE.S_SITUACAO_TURMA SIT ON TUR.ST_TURMA = SIT.NU_SEQ_ST_TURMA ";
		$query .= " WHERE TUR.NU_SEQ_TURMA = $id ";

		$obModelo = new Fnde_Sice_Model_Turma();

		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_TURMA');
	}

	/**
	 * Efetua a pesquisa das turmas cadastradas de acordo com o filtro informado
	 * @param array $arParams
	 * @param boolean $indicadorAcoes
	 * @param array $arUsuario
	 */
	public function pesquisaTurma( $arParams, $indicadorAcoes, $arUsuario = null ) {
		$query = " SELECT "
				 . " DISTINCT TUR.NU_SEQ_TURMA AS NU_SEQ_TURMA, "
				 . " TUR.UF_TURMA, "
				 . " MES.NO_MUNICIPIO, "
				 . " MES.NO_MESO_REGIAO, "
				 . " CUR.DS_NOME_CURSO, ";
		if ( $indicadorAcoes ) { //Se indicadorAcoes for true, então a chamada ao método veio de uma das ações do combo de "+ Ações" então busca mais estas duas informações
			$query .= " CUR.NU_SEQ_CURSO, "
				   . " CUR.DS_PREREQUISITO_CURSO, ";
		}
		$query .= " TUT.NO_USUARIO AS NO_TUTOR, "
				. " ART.NO_USUARIO AS NO_ARTICULADOR, "
				. " TO_CHAR(TUR.DT_INICIO,'DD/MM/YYYY') AS DT_INICIO, "
				. " TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY') AS DT_FIM, "
				. " TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') AS DT_FINALIZACAO, "
				. " SIT.DS_ST_TURMA, "
				. " TUR.NU_SEQ_CONFIGURACAO "
				. " FROM SICE_FNDE.S_TURMA TUR "
				. " INNER JOIN CTE_FNDE.T_MESO_REGIAO MES ON TUR.CO_MUNICIPIO = MES.CO_MUNICIPIO_IBGE "
				. " INNER JOIN SICE_FNDE.S_CURSO CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO "
				. " INNER JOIN SICE_FNDE.S_USUARIO TUT ON TUR.NU_SEQ_USUARIO_TUTOR = TUT.NU_SEQ_USUARIO "
				. " INNER JOIN SICE_FNDE.S_USUARIO ART ON TUR.NU_SEQ_USUARIO_ARTICULADOR = ART.NU_SEQ_USUARIO "
				. " INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM ON CUR.NU_SEQ_CURSO = VCM.NU_SEQ_CURSO "
				. " INNER JOIN SICE_FNDE.S_MODULO MDL ON VCM.NU_SEQ_MODULO = MDL.NU_SEQ_MODULO "
				. " INNER JOIN SICE_FNDE.S_SITUACAO_TURMA SIT ON TUR.ST_TURMA = SIT.NU_SEQ_ST_TURMA "

				. " WHERE 1=1 ";

		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'CUR', 'NU_SEQ_TIPO_CURSO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'UF_TURMA');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'CO_MESORREGIAO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'CO_MUNICIPIO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_USUARIO_TUTOR');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_USUARIO_ARTICULADOR');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_TURMA');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'VCM', 'NU_SEQ_MODULO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_CURSO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'DT_INICIO');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'DT_FIM');
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'ST_TURMA');
		if ( $arParams['DT_FINAL'] ) {
			$arParams['DT_FINALIZACAO'] = $arParams['DT_FINAL'];
		}
		$this->setFiltroPesquisaTurma($arParams, $query, $arFiltro, 'TUR', 'DT_FINALIZACAO');

		if ( $arUsuario != null ) {
			/*
			 *  1	Coordenador Nacional Administrador
			 *  2	Coordenador Nacional Equipe
			 *  3	Coordenador Nacional Gestor
			 *  4	Coordenador Estadual
			 *  5	Articulador
			 *  6	Tutor
			 *  7	Cursista
			 *
			 */

			//Articulador ou Tutor somente turmas na qual está vinculado
			if ( $arUsuario['NU_SEQ_TIPO_PERFIL'] == 5 ) {
				$query .= " AND TUR.NU_SEQ_USUARIO_ARTICULADOR = " . $arUsuario['NU_SEQ_USUARIO'];
			} elseif ( $arUsuario['NU_SEQ_TIPO_PERFIL'] == 6 ) {
				$query .= " AND TUR.NU_SEQ_USUARIO_TUTOR = " . $arUsuario['NU_SEQ_USUARIO'];
			}

			//Coordenador Estadual apenas as turmas da sua  UF de atuação
			if ( $arUsuario['NU_SEQ_TIPO_PERFIL'] == 4 ) {
				$query .= " AND TUR.UF_TURMA = '" . $arUsuario['SG_UF_ATUACAO_PERFIL'] . "'";
			}

		}

		$query .= " ORDER BY TUR.NU_SEQ_TURMA ";

		$obModelo = new Fnde_Sice_Model_Turma();

		$stm = $obModelo->getAdapter()->query($query, $arFiltro);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Metodo auxiliar que configura o filtro da pesquisa de acordo com os parametros passados.
	 * @param $arParams
	 * @param $query
	 * @param $arFiltro
	 * @param $prefixo
	 * @param $descricao
	 */
	private function setFiltroPesquisaTurma( $arParams, &$query, &$arFiltro, $prefixo, $descricao ) {
		if ( $arParams[$descricao] ) {
			$query .= " AND " . $prefixo . "." . $descricao . " = :" . $descricao;
			$arFiltro[$descricao] = $arParams[$descricao];
		}
	}

	/**
	 * Função para gravar uma turma no banco
	 * @param array $arParamTurma parametros da turma
	 * @param array $arParamHistorico parametros do historico
	 */
	public function salvar( $arParamTurma, $arParamHistorico ) {
		$obModelo = new Fnde_Sice_Model_Turma();
		$obModelo->getAdapter()->beginTransaction();
		try {
			if ( $arParamTurma['NU_SEQ_TURMA'] ) {
				$infoCurso = $this->pesquisarDadosComplementaresTurma($arParamTurma);
				$this->validaDataInicioFim($arParamTurma, $infoCurso);
				$obModelo->update($arParamTurma, "NU_SEQ_TURMA = " . $arParamTurma['NU_SEQ_TURMA']);
			} else {
				$infoCurso = $this->pesquisarDadosTurma($arParamTurma['NU_SEQ_CURSO']);
				$this->validaDataInicioFim($arParamTurma, $infoCurso);
				$this->validaTurmasPorTutor($arParamTurma);
				//$this->validaTurmasPorMesoregiao($arParamTurma);
				$turmaInserida = $obModelo->insert($arParamTurma);
			}

			//quando estiver editando não salva no historico
			if ( !$arParamTurma['NU_SEQ_TURMA'] ) {
				$obModeloHistorico = new Fnde_Sice_Model_HistoricoTurma();
				$arParamHistorico['NU_SEQ_TURMA'] = $turmaInserida;
				$obModeloHistorico->insert($arParamHistorico);
			}

			$obModelo->getAdapter()->commit();
		} catch ( Exception $e ) {
			$obModelo->getAdapter()->rollBack();
			throw $e;
		}
	}

	/**
	 * Função para pesquisar dados relacionados a turma (carga presencial e a distância, quantidade mínima de alunos, etc...)
	 * para cadastro
	 * @param int $arParam
	 */
	public function pesquisarDadosTurma( $arParam ) {

		$query = " SELECT "
			. " CUR.VL_CARGA_HORARIA AS NU_CARGA_CURSO, "
			. " SUM(MDL.VL_CARGA_DISTANCIA) AS NU_CARGA_DISTANCIA, "
			. " SUM(MDL.VL_CARGA_PRESENCIAL) AS NU_CARGA_PRESENCIAL, "
			. " SUM(MDL.VL_MIN_CONCLUSAO) AS NU_MIN_CONCLUSAO, "
			. " SUM(MDL.VL_MAX_CONCLUSAO) AS NU_MAX_CONCLUSAO, "
			. " CONF.QT_ALUNOS_TURMA AS NU_MIN_ALUNOS "
			. " FROM "
			. " SICE_FNDE.S_CURSO CUR "
			. "       INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM ON CUR.NU_SEQ_CURSO = VCM.NU_SEQ_CURSO "
			. "       INNER JOIN SICE_FNDE.S_MODULO MDL ON VCM.NU_SEQ_MODULO = MDL.NU_SEQ_MODULO "
			. "       INNER JOIN SICE_FNDE.S_CONFIGURACAO CONF ON CUR.NU_SEQ_TIPO_CURSO = CONF.NU_SEQ_TIPO_CURSO "
			. " WHERE CUR.NU_SEQ_CURSO = $arParam AND MDL.ST_MODULO = 'A' AND CONF.ST_CONFIGURACAO = 'A' "
			. " GROUP BY( "
			. "          CUR.VL_CARGA_HORARIA, "
			. "          CONF.QT_ALUNOS_TURMA) ";
		//die($query);
		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result[0];

	}

	/**
	 * Função para pesquisar dados relacionados a turma (carga presencial e a distância, quantidade mínima de alunos, etc...)
	 * para visualização e edição.
	 * @param int $arParam
	 */
	public function pesquisarDadosComplementaresTurma( $arParam ) {

		$query = " SELECT "
				. " CUR.VL_CARGA_HORARIA AS NU_CARGA_CURSO, "
				. " CUR.DS_PREREQUISITO_CURSO, "
				. " SUM(MDL.VL_CARGA_DISTANCIA) AS NU_CARGA_DISTANCIA, "
				. " SUM(MDL.VL_CARGA_PRESENCIAL) AS NU_CARGA_PRESENCIAL, "
				. " SUM(MDL.VL_MIN_CONCLUSAO) AS NU_MIN_CONCLUSAO, "
				. " SUM(MDL.VL_MAX_CONCLUSAO) AS NU_MAX_CONCLUSAO, "
				. " CONF.QT_ALUNOS_TURMA AS NU_MIN_ALUNOS "
				. " FROM "
				. " SICE_FNDE.S_CURSO CUR "
				. "       INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM ON CUR.NU_SEQ_CURSO = VCM.NU_SEQ_CURSO "
				. "       INNER JOIN SICE_FNDE.S_MODULO MDL ON VCM.NU_SEQ_MODULO = MDL.NU_SEQ_MODULO "
				. "       INNER JOIN SICE_FNDE.S_CONFIGURACAO CONF ON CUR.NU_SEQ_TIPO_CURSO = CONF.NU_SEQ_TIPO_CURSO "
				. " WHERE CUR.NU_SEQ_CURSO = " . $arParam['NU_SEQ_CURSO']
				. " AND MDL.ST_MODULO = 'A' AND CONF.NU_SEQ_CONFIGURACAO = " . $arParam['NU_SEQ_CONFIGURACAO']
				. " GROUP BY( "
				. "          CUR.VL_CARGA_HORARIA, "
				. "          CUR.DS_PREREQUISITO_CURSO, "
				. "          CONF.QT_ALUNOS_TURMA) ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result[0];

	}

	/**
	 * Pesquisa se determinada turma possui cursistas matriculados
	 * @param int $arParam
	 */
	public function pesquisarVinculosPorTurma( $idTurma ) {
		$query = " SELECT COUNT(*) AS QUANT_CURSISTAS FROM SICE_FNDE.S_VINC_CURSISTA_TURMA WHERE NU_SEQ_TURMA = $idTurma ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		return $result;
	}

	/**
	 * Retorna turmas de um determinado tutor e configuracao.
	 * @param string $idTutor ID tutor.
	 * @param string $idConf ID configuracao.
	 */
	public function pesquisarTurmaPorTutor( $idTutor, $idConf ) {
		$query = "SELECT COUNT(*) AS QT_TURMA_TUTOR
				FROM SICE_FNDE.S_TURMA
				WHERE NU_SEQ_USUARIO_TUTOR = $idTutor
				AND ST_TURMA IN (1, 4, 3)
				AND NU_SEQ_CONFIGURACAO = $idConf ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		//retorna array onde a posição 0 é a qtde de turma pre-turma e a posição 1 é a qtde de turma ativa
		return $result;
	}

	/**
	 * Altera situacao da turma para uma determinada situacao.
	 * @param array $arParam Parametros da turma.
	 * @param string $situacao Situacao para alteracao
	 * @param string $dataFinalizacao Data de finalizacao.
	 * @throws Exception
	 */
	public function alteraSituacaoTurma( $arParam, $situacao, $dataFinalizacao = null ) {
		$obModelo = new Fnde_Sice_Model_Turma();

        $obModelo->fixDateToBr();
		$data['ST_TURMA'] = $situacao;
		if ( $dataFinalizacao ) {
			$data['DT_FINALIZACAO'] = $dataFinalizacao;
		}
		$where = "NU_SEQ_TURMA = {$arParam['NU_SEQ_TURMA']}";

		$obModelo->getAdapter()->beginTransaction();
		$result = 0;
		try {
			$result = $obModelo->update($data, $where);
			if ( $result ) {
				$obHistorico = new Fnde_Sice_Business_HistoricoTurma();
				$obHistorico->preSalvar($arParam['NU_SEQ_TURMA'], $data['ST_TURMA'], $arParam['CO_MOTIVO_ALTERACAO'],
						$arParam['DS_OBSERVACAO']);
			}
		} catch ( Exception $e ) {
			$obModelo->getAdapter()->rollBack();
			throw $e;
		}
		$obModelo->getAdapter()->commit();
		return $result;
	}

	/**
	 * Obtém os dados necessários para compor o arquivo CSV que será gerado para o moodle.
	 * @param array $arParam
	 */
	public function obterDadosArquivoMoodle( $arParam ) {
		$query = "SELECT DISTINCT
          TUR.NU_SEQ_TURMA,
          USU.NU_CPF,
          INITCAP(USU.NO_USUARIO) AS NO_USUARIO,
          LOWER(USU.DS_EMAIL_USUARIO) AS DS_EMAIL_USUARIO,
          INITCAP(MES.NO_MUNICIPIO) AS NO_MUNICIPIO,
          TUR.UF_TURMA,
          STP.DS_TIPO_PERFIL,
          STP.DS_TIPO_PERFIL_SEGWEB
        FROM SICE_FNDE.S_TURMA TUR
          INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON VCT.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA
          INNER JOIN SICE_FNDE.S_USUARIO USU ON
            VCT.NU_SEQ_USUARIO_CURSISTA = USU.NU_SEQ_USUARIO OR
            USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR OR
            USU.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR
          INNER JOIN SICE_FNDE.S_CURSO CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO
          INNER JOIN SICE_FNDE.S_TIPO_CURSO STC ON CUR.NU_SEQ_TIPO_CURSO = STC.NU_SEQ_TIPO_CURSO
          INNER JOIN CTE_FNDE.T_MESO_REGIAO MES ON TUR.CO_MUNICIPIO = MES.CO_MUNICIPIO_IBGE
          INNER JOIN SICE_FNDE.S_TIPO_PERFIL STP ON STP.NU_SEQ_TIPO_PERFIL = USU.NU_SEQ_TIPO_PERFIL
        WHERE TUR.NU_SEQ_TURMA = :NU_SEQ_TURMA";

		$obModelo = new Fnde_Sice_Model_Turma();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
		$params = array('NU_SEQ_TURMA' => $arParam);
		$stm = $obModelo->getAdapter()->query($query, $params);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Retorna dados da turma e curso de acordo com o perfil.
	 * @param string $codMunicipio Codigo do municipio.
	 */
	public function obterDadosTurmaCurso( $codMunicipio ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}
		$query = " SELECT DISTINCT TUR.NU_SEQ_TURMA, "
				. " CUR.DS_SIGLA_CURSO "
				. " FROM SICE_FNDE.S_TURMA TUR "
				. " INNER JOIN SICE_FNDE.S_CURSO CUR "
				. " ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO "
				. " WHERE "
				. " 1=1 ";

		//in_array(Fnde_Sice_Business_Componentes::TUTOR,$perfisUsuarioLogado)

		if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
			$query .= " AND TUR.NU_SEQ_USUARIO_ARTICULADOR = {$arUsuario['NU_SEQ_USUARIO']} ";
		} elseif ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
			$query .= " AND TUR.NU_SEQ_USUARIO_TUTOR = {$arUsuario['NU_SEQ_USUARIO']} ";
		} elseif ( $codMunicipio ) {
			$query .= " AND TUR.CO_MUNICIPIO = $codMunicipio ";
		} else {
			return null;
		}

		$query .= " ORDER BY CUR.DS_SIGLA_CURSO ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();

		return $result;
	}

	/**
	 * Valida da final para conclusão do curso
	 * @param $arParams
	 * @param $infoCurso
	 */
	public function validaDataInicioFim( $arParams, $infoCurso ) {

		$dataInicio = new Zend_date($arParams['DT_INICIO'], 'D/M/Y');
		$dataFim = new Zend_date($arParams['DT_FIM'], 'D/M/Y');

		// calcula a diferença de dias entre as datas de inicio e fim.
		$diferenca = ( int ) floor( ( $dataFim->getTimestamp() - $dataInicio->getTimestamp() ) / ( 3600 * 24 ) + 1);

		// verifica se a diferenca acima é maior que o maximo, ou menor que o mínimo, permitido.
		if ( $diferenca < $infoCurso['NU_MIN_CONCLUSAO'] ) {
			throw new Exception(
					"O intervalo de dias entre a data início e a data previsão término não pode ser inferior a quantidade mínima de dias para conclusão do curso!");
		} elseif ( $diferenca > $infoCurso['NU_MAX_CONCLUSAO'] ) {
			throw new Exception(
					"O intervalo de dias entre a data início e a data previsão término da turma não pode ser superior a quantidade máxima de dias para conclusão do curso!");
		}
		return true;
	}

	/**
	 * Verifica se o tutor ja possui a quantidade de turma maxima permitida na configuracao.
	 * @param array $arParams Parametros de validacao.
	 * @throws Exception
	 * @return boolean
	 */
	public function validaTurmasPorTutor( $arParams ) {
		//Recupera quantidade de cursos por tutor na configuração.
		$obCurso = new Fnde_Sice_Business_Curso();
		$obConfiguracao = new Fnde_Sice_Business_Configuracao();
		$tipoCurso = $obCurso->getTipoPorCurso($arParams['NU_SEQ_CURSO']);
		$qtTurma = $obConfiguracao->getConfiguracaoPorTipoCurso($tipoCurso['NU_SEQ_TIPO_CURSO']);

		//Recupera a quantidade de turmas que o tutor já possui.
		$obTurma = new Fnde_Sice_Business_Turma();
		$arTurmaTutor = $obTurma->pesquisarTurmaPorTutor($arParams['NU_SEQ_USUARIO_TUTOR'],
				$qtTurma['NU_SEQ_CONFIGURACAO']);

		//Verifica se o tutor já possui a quantidade de turma máxima permitida.
		if ( $arTurmaTutor['QT_TURMA_TUTOR'] >= $qtTurma['QT_TURMA_TUTOR'] ) {
			throw new Exception(
					"O tutor poderá ter no máximo " . $qtTurma['QT_TURMA_TUTOR']
							. " turmas ativa ou pré-turma ou aguardando autorização");
		}
		return true;
	}

	/**
	 * Recupera os dados para montar a listagem de turmas no UC Avaliar Bolsista
	 * Atendendo os requisitos previstos nas regras de negócio para Tutor ou Articulador.
	 * @param int $idUsuario ID do usuário tutor ou articulador.
	 */
	public function getDadosAvaliarTurmas( $arParams, $idBolsa ) {

		$query = "SELECT DISTINCT BOL.NU_SEQ_BOLSA,
				  TUR.NU_SEQ_TURMA,
				  CUR.DS_NOME_CURSO,
				  CUR.QT_MODULOS AS QTD_MODULO,
				  TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY') DT_INICIO,
				  TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY') DT_FIM,
				  TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') DT_FINALIZACAO,
				  ART.NO_USUARIO AS NO_USUARIO_ARTICULADOR,
				  TUT.NO_USUARIO AS NO_USUARIO_TUTOR,
				  AVAL.ST_APROVACAO,
				  (SELECT COUNT(*)
				  FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
				  INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
				  ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
				  WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
				  AND CA.DS_SITUACAO        = 'Aprovado com destaque'
				  ) AS AD,
				  (SELECT COUNT(*)
				  FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
				  INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
				  ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
				  WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
				  AND CA.DS_SITUACAO        = 'Aprovado'
				  ) AS A,
				  (SELECT COUNT(*)
				  FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
				  INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
				  ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
				  WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
				  AND CA.DS_SITUACAO        = 'Reprovado'
				  ) AS R,
				  (SELECT COUNT(*)
				  FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
				  INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
				  ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
				  WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
				  AND CA.DS_SITUACAO        = 'Desistente'
				  ) AS D,
				  (SELECT COUNT(*)
				  FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
				  WHERE V.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA
				  ) AS CM 
				FROM SICE_FNDE.S_BOLSA BOL
				INNER JOIN SICE_FNDE.S_TURMA TUR ON BOL.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO AND " .
				($arParams['NU_SEQ_TIPO_PERFIL']==5?"BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR":($arParams['NU_SEQ_TIPO_PERFIL']==8?"TUR.UF_TURMA = '".$arParams['SG_UF']."' ":"BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR")) . "
				INNER JOIN SICE_FNDE.S_USUARIO TUT ON TUR.NU_SEQ_USUARIO_TUTOR = TUT.NU_SEQ_USUARIO
				INNER JOIN SICE_FNDE.S_USUARIO ART ON TUR.NU_SEQ_USUARIO_ARTICULADOR = ART.NU_SEQ_USUARIO
				LEFT JOIN SICE_FNDE.S_AVALIACAO_TURMA AVAL ON TUR.NU_SEQ_TURMA = AVAL.NU_SEQ_TURMA
				INNER JOIN SICE_FNDE.S_CURSO CUR ON CUR.NU_SEQ_CURSO    = TUR.NU_SEQ_CURSO
				WHERE BOL.NU_SEQ_BOLSA = $idBolsa ";

		//echo $query;die();
		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();

		return $result;
	}

	/**
	 * Recupera os dados para montar a listagem de turmas no UC Avaliar Bolsista
	 * Atendendo os requisitos previstos nas regras de negócio para Tutor ou Articulador.
	 * @param int $idUsuario ID do usuário tutor ou articulador.
	 */
	public function getDadosAvaliarTurmasAntigo( $arParams, $idBolsa ) {

		$query = "SELECT
		BLS.NU_SEQ_BOLSA,
		AVAL.NU_SEQ_AVALIACAO_TURMA,
		TUR.NU_SEQ_TURMA,
		CUR.DS_NOME_CURSO,
		CUR.QT_MODULOS AS QTD_MODULO,
		TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY') DT_INICIO,
		TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY') DT_FIM,
		TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') DT_FINALIZACAO,
		ART.NO_USUARIO AS NO_USUARIO_ARTICULADOR,
		TUT.NO_USUARIO      AS NO_USUARIO_TUTOR,
		AVAL.ST_APROVACAO,
		(SELECT COUNT(*)
		FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
		INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
		ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
		WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
		AND CA.DS_SITUACAO        = 'Aprovado com destaque'
		) AS AD,
		(SELECT COUNT(*)
		FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
		INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
		ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
		WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
		AND CA.DS_SITUACAO        = 'Aprovado'
		) AS A,
		(SELECT COUNT(*)
		FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
		INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
		ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
		WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
		AND CA.DS_SITUACAO        = 'Reprovado'
		) AS R,
		(SELECT COUNT(*)
		FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
		INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA
		ON V.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
		WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
		AND CA.DS_SITUACAO        = 'Desistente'
		) AS D,
		(SELECT COUNT(*)
		FROM SICE_FNDE.S_VINC_CURSISTA_TURMA V
		WHERE V.NU_SEQ_TURMA      = TUR.NU_SEQ_TURMA
		) AS CM
		FROM SICE_FNDE.S_TURMA TUR
		INNER JOIN SICE_FNDE.S_USUARIO 					TUT 	ON TUR.NU_SEQ_USUARIO_TUTOR 		= TUT.NU_SEQ_USUARIO
		INNER JOIN SICE_FNDE.S_USUARIO 					ART 	ON TUR.NU_SEQ_USUARIO_ARTICULADOR 	= ART.NU_SEQ_USUARIO
		INNER JOIN SICE_FNDE.S_AVALIACAO_TURMA 			AVAL 	ON TUR.NU_SEQ_TURMA 				= AVAL.NU_SEQ_TURMA
		INNER JOIN SICE_FNDE.S_BOLSA 					BLS 	ON BLS.NU_SEQ_BOLSA 				= AVAL.NU_SEQ_BOLSA
		INNER JOIN SICE_FNDE.S_CURSO 					CUR 	ON CUR.NU_SEQ_CURSO    				= TUR.NU_SEQ_CURSO
	
		WHERE BLS.NU_SEQ_BOLSA = $idBolsa ";
		//die($query);
		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();

		return $result;
	}

	/**
	 * Obtém dados das turmas avaliadas de um bolsista.
	 * @param array $arParam
	 */
	public function obterDadosTurmasAvaliadasAntigo( $arParam ) {

		$query =  " SELECT "
		. " TUR.NU_SEQ_TURMA, "
		. " CUR.DS_NOME_CURSO, "
		. " COUNT(VCT.NU_SEQ_USUARIO_CURSISTA) AS QTD_CURSISTA, "
		. " USU.NO_USUARIO, "
		. " TPF.DS_TIPO_PERFIL, "
		. " TO_CHAR(AVT.DT_AVALIACAO, 'DD/MM/YYYY') AS DT_AVALIACAO "
		. " FROM "
		. " SICE_FNDE.S_BOLSA BLS "
		. " INNER JOIN SICE_FNDE.S_AVALIACAO_TURMA        AVT ON BLS.NU_SEQ_BOLSA = AVT.NU_SEQ_BOLSA "
		. " INNER JOIN SICE_FNDE.S_TURMA                  TUR ON avt.nu_seq_turma = TUR.NU_SEQ_TURMA "
		. " INNER JOIN SICE_FNDE.S_CURSO                  CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO "
		. " INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA    VCT ON TUR.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA "
		. " INNER JOIN SICE_FNDE.S_USUARIO                USU ON AVT.NU_SEQ_USUARIO_AVALIADOR = USU.NU_SEQ_USUARIO "
		. " INNER JOIN SICE_FNDE.S_TIPO_PERFIL            TPF ON USU.NU_SEQ_TIPO_PERFIL = TPF.NU_SEQ_TIPO_PERFIL "
		. " WHERE "
		. " BLS.NU_SEQ_BOLSA = {$arParam['NU_SEQ_BOLSA']} "
		. " GROUP BY "
		. " TUR.NU_SEQ_TURMA, "
		. " CUR.DS_NOME_CURSO, "
		. " USU.NO_USUARIO, "
		. " TPF.DS_TIPO_PERFIL, "
		. " AVT.DT_AVALIACAO ";

		$obModelo = new Fnde_Sice_Model_Bolsa();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Obtém dados das turmas avaliadas de um bolsista.
	 * @param array $arParam
	 */
	public function obterDadosTurmasAvaliadas( $arParam ) {

		$query =  "SELECT
					  TUR.NU_SEQ_TURMA,
					  CUR.DS_NOME_CURSO,
					  COUNT(VCT.NU_SEQ_USUARIO_CURSISTA) AS QTD_CURSISTA,
					  USU.NO_USUARIO,
					  TPF.DS_TIPO_PERFIL,
					  TO_CHAR(AVT.DT_AVALIACAO, 'DD/MM/YYYY') AS DT_AVALIACAO
					FROM SICE_FNDE.S_BOLSA BOL
					INNER JOIN SICE_FNDE.S_TURMA TUR ON BOL.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO AND ".
					($arParam['NU_SEQ_TIPO_PERFIL']==5?"BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_ARTICULADOR":($arParam['NU_SEQ_TIPO_PERFIL']==8?"TUR.UF_TURMA = '".$arParam['SG_UF']."' ":"BOL.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR")) . "
					INNER JOIN SICE_FNDE.S_CURSO CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO
					INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON TUR.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA
					LEFT JOIN SICE_FNDE.S_AVALIACAO_TURMA AVT ON TUR.NU_SEQ_TURMA = AVT.NU_SEQ_TURMA
					LEFT JOIN SICE_FNDE.S_USUARIO USU ON AVT.NU_SEQ_USUARIO_AVALIADOR = USU.NU_SEQ_USUARIO
					LEFT JOIN SICE_FNDE.S_TIPO_PERFIL TPF ON USU.NU_SEQ_TIPO_PERFIL = TPF.NU_SEQ_TIPO_PERFIL
					WHERE BOL.NU_SEQ_BOLSA = " . $arParam['NU_SEQ_BOLSA'] . "
					GROUP BY TUR.NU_SEQ_TURMA,
					  CUR.DS_NOME_CURSO,
					  USU.NO_USUARIO,
					  TPF.DS_TIPO_PERFIL,
					  AVT.DT_AVALIACAO";

		//die($query);
		$obModelo = new Fnde_Sice_Model_Bolsa();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Retorna a quantidade de turmas ativia de um determinado usuario.
	 * @param string $idUsuario ID usuario.
	 */
	public function identificarVinculosUsuarioTurma( $idUsuario ) {
		$query = "SELECT COUNT(*) QTD FROM SICE_FNDE.S_TURMA WHERE ST_TURMA = 4 AND (NU_SEQ_USUARIO_TUTOR = {$idUsuario} OR NU_SEQ_USUARIO_ARTICULADOR = {$idUsuario})";

		$obModelo = new Fnde_Sice_Model_Bolsa();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result['QTD'];
	}

	/**
	 * Função para verificar se um curso está vinculado a alguma turma
	 * @param int $idCurso
	 */
	public function verificarVinculoCursoTurma( $idCurso ) {
		$obModelo = new Fnde_Sice_Model_Turma();
		$select = $obModelo->select()->where("NU_SEQ_CURSO = ?", $idCurso);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
	}

	/**
	 * Retorna se a turma ja foi avaliada.
	 * @param string $idTurma Codigo da turma.
	 */
	public function isTurmaComBolsa( $idTurma ) {

		$query = " SELECT COUNT(TUR.NU_SEQ_TURMA) QUANT "
				. " FROM SICE_FNDE.S_TURMA TUR "
				. " INNER JOIN SICE_FNDE.S_AVALIACAO_TURMA AVA ON TUR.NU_SEQ_TURMA = AVA.NU_SEQ_TURMA "
				. " WHERE TUR.NU_SEQ_TURMA = $idTurma "
				. " AND TUR.ST_TURMA = 11 ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result['QUANT'];

	}

	/**
	 * Obtem a turma ativa do cursista informado
	 * @param int $idUsuario
	 */
	public function getTurmaAtivaByIdCursista( $idCursista ) {

		$query = " SELECT "
				. " 	TUR.NU_SEQ_TURMA, "
				. " 	TUR.NU_SEQ_CURSO, "
				. " 	CUR.DS_NOME_CURSO, "
				. " 	CUR.DS_PREREQUISITO_CURSO, "
				. " 	TUR.NU_SEQ_USUARIO_TUTOR, "
				. " 	TUT.NO_USUARIO NO_TUTOR, "
				. " 	TUR.NU_SEQ_USUARIO_ARTICULADOR, "
				. " 	ART.NO_USUARIO NO_ARTICULADOR, "
				. " 	TO_CHAR(TUR.DT_INICIO, 'DD/MM/YYYY') DT_INICIO, "
				. " 	TO_CHAR(TUR.DT_FIM, 'DD/MM/YYYY') DT_FIM, "
				. " 	TO_CHAR(TUR.DT_FINALIZACAO, 'DD/MM/YYYY') DT_FINALIZACAO, "
				. " 	TUR.UF_TURMA, "
				. " 	TUR.CO_MUNICIPIO, "
				. " 	MSO.NO_MUNICIPIO, "
				. " 	TUR.CO_MESORREGIAO, "
				. " 	MSO.NO_MESO_REGIAO, "
				. " 	SIT.DS_ST_TURMA, "
				. " 	TUR.NU_SEQ_CONFIGURACAO, "
				. " 	TO_CHAR(PVC.DT_FINAL, 'MM/YYYY') MES_REFERENCIA "
				. " FROM SICE_FNDE.S_TURMA TUR "
				. " INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON TUR.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA "
				. " INNER JOIN SICE_FNDE.S_USUARIO TUT ON TUR.NU_SEQ_USUARIO_TUTOR = TUT.NU_SEQ_USUARIO "
				. " INNER JOIN SICE_FNDE.S_USUARIO ART ON TUR.NU_SEQ_USUARIO_ARTICULADOR = ART.NU_SEQ_USUARIO "
				. " INNER JOIN CTE_FNDE.T_MESO_REGIAO MSO ON TUR.CO_MUNICIPIO = MSO.CO_MUNICIPIO_IBGE "
				. " INNER JOIN SICE_FNDE.S_CURSO CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO "
				. " INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVC ON PVC.NU_SEQ_PERIODO_VINCULACAO = TUR.NU_SEQ_PERIODO_VINCULACAO "
				. " INNER JOIN SICE_FNDE.S_SITUACAO_TURMA SIT ON TUR.ST_TURMA = SIT.NU_SEQ_ST_TURMA "
				. " WHERE VCT.NU_SEQ_USUARIO_CURSISTA = $idCursista "
				. " AND TUR.ST_TURMA in(4, 10, 11, 12) ";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;

	}

	/**
	 * Retorna a Turma de determinado usuario cursista.
	 * @param $idUsuario
	 */
	public function getTurmaPorUsuario( $idUsuario ) {
		try {
			$sql = " SELECT T.*
					 FROM SICE_FNDE.S_TURMA T
					 INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA V ON T.NU_SEQ_TURMA = V.NU_SEQ_TURMA
					 WHERE V.NU_SEQ_USUARIO_CURSISTA = {$idUsuario} ";

			$obModelo = new Fnde_Sice_Model_Turma();
			$stm = $obModelo->getAdapter()->query($sql);
			return $stm->fetch();
		} catch ( Exception $e ) {
			throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Verifica de acordo com o id da turma se a data de finalização (hoje) é menor que a data mínima
	 * prevista para finalização de acondo com a soma dos dias mínimo dos módulos do curso
	 * @param $idTurma
	 */
	public function isDataMinimaConclusao( $idTurma ) {

		$query = "SELECT " . "SUM(MDL.VL_MIN_CONCLUSAO) DIAS, " . "TO_CHAR(TUR.DT_INICIO, 'YYYY-MM-DD') DT_INICIO "
				. "FROM SICE_FNDE.S_TURMA TUR "
				. "INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM ON TUR.NU_SEQ_CURSO = VCM.NU_SEQ_CURSO "
				. "INNER JOIN SICE_FNDE.S_MODULO MDL ON VCM.NU_SEQ_MODULO = MDL.NU_SEQ_MODULO "
				. "WHERE TUR.NU_SEQ_TURMA = {$idTurma} " . "GROUP BY (DT_INICIO)";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		//Recupara a soma da quantidade de dias necessário pelos módulos cadastrados no curso
		$dias = $result['DIAS'];
		$dataInicial = $result['DT_INICIO'];

		//adiciona a quntidade de dias recuperado à data de início do curos
		$menorDataFimTT = strtotime("+{$dias} day", strtotime($dataInicial));
		$hojeDataFinalizacaoTT = strtotime(date("Y-m-d"));

		/*
		$menorDataFim = date("d/m/Y", $menorDataFimTT);
		$hojeDataFinalizacao =  date("d/m/Y", $hojeDataFinalizacaoTT);
		 */

		//Se a data de hoje (finalização) maior ou igual a menor data para conclusão é premitido finalizar o curso
		if ( $hojeDataFinalizacaoTT >= $menorDataFimTT ) {
			return true;
		} else {
			return false;
		}

	}

    public function getAnosTurmas()
    {
        $query = "select distinct to_char(dt_fim, 'yyyy') as ano from sice_fnde.s_turma order by 1";

        $obModelo = new Fnde_Sice_Model_Turma();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetchAll();

        return $result;
    }

	public function getTurmaPorAluno($idAluno){
		$query = "SELECT * FROM SICE_FNDE.S_TURMA tur
					INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA v
					ON tur.NU_SEQ_TURMA = v.NU_SEQ_TURMA
					INNER JOIN  SICE_FNDE.S_SITUACAO_TURMA st
					ON st.NU_SEQ_ST_TURMA = tur.ST_TURMA
					WHERE v.NU_SEQ_USUARIO_CURSISTA = {$idAluno}";

		$obModelo = new Fnde_Sice_Model_Turma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		return $result;
	}
}
