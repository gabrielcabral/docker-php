<?php

/**
 * Business do HistoricoTurma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Fnde_Sice_Business_HistoricoTurma {

	protected $_stModelo = 'HistoricoTurma';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('DS_OBSERVACAO' => 'C.DS_OBSERVACAO', 'NU_SEQ_TURMA' => 'C.NU_SEQ_TURMA',
				'CO_MOTIVO_ALTERACAO' => 'C.CO_MOTIVO_ALTERACAO',
				'NU_SEQ_HISTORICO_TURMA' => 'C.NU_SEQ_HISTORICO_TURMA', 'DT_HISTORICO' => 'C.DT_HISTORICO',
				'ST_TURMA' => 'C.ST_TURMA', 'ID_AUTOR' => 'C.ID_AUTOR',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_HISTORICO_TURMA']) ) {
			$select->where("C.NU_SEQ_HISTORICO_TURMA = {$arParams['id']} ");
		} else {
			if ( $arParams['DS_OBSERVACAO'] ) {
				$select->where("C.DS_OBSERVACAO = ?", $arParams['DS_OBSERVACAO']);
			}
			if ( $arParams['NU_SEQ_TURMA'] ) {
				$select->where("C.NU_SEQ_TURMA = ?", $arParams['NU_SEQ_TURMA']);
			}
			if ( $arParams['CO_MOTIVO_ALTERACAO'] ) {
				$select->where("C.CO_MOTIVO_ALTERACAO = ?", $arParams['CO_MOTIVO_ALTERACAO']);
			}
			if ( $arParams['NU_SEQ_HISTORICO_TURMA'] ) {
				$select->where("C.NU_SEQ_HISTORICO_TURMA = ?", $arParams['NU_SEQ_HISTORICO_TURMA']);
			}
			if ( $arParams['DT_HISTORICO'] ) {
				$select->where("C.DT_HISTORICO = ?", $arParams['DT_HISTORICO']);
			}
			if ( $arParams['ST_TURMA'] ) {
				$select->where("C.ST_TURMA = ?", $arParams['ST_TURMA']);
			}
			if ( $arParams['ID_AUTOR'] ) {
				$select->where("C.ID_AUTOR = ?", $arParams['ID_AUTOR']);
			}
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

		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$logger = Zend_Registry::get('logger');

		$logger->log('Tor!', Zend_Log::INFO);
		try {
			$where = "NU_SEQ_HISTORICO_TURMA = " . $id;
			$obModelo->delete($where);

			$logger->log("HistoricoTurma removido com sucesso !", Zend_Log::INFO);

			$this->stMensagem = "HistoricoTurma removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Função para remover o histórico da turma do banco de dados.
	 * 
	 * @param int $codTurma
	 * @throws Exception
	 */
	public function excluirHistoricoByTurma( $codTurma ) {
		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$where = "NU_SEQ_TURMA = " . $codTurma;
		try {
			$obModelo->delete($where);
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Seleciona HistoricoTurma
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function search( $arParams ) {

		$logger = Zend_Registry::get('logger');

		try {
			$select = $this->getSelect($this->getColumnsSearch());
			$this->setFilter($select, $arParams);
			$this->setOrder($select);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
		} catch ( Exception $e ) {

			$logger->log($e->getMessage(), Zend_Log::WARN);
		}

		return $result;
	}

	/**
	 * Obtem HistoricoTurma por Id
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getHistoricoTurmaById( $id ) {

		$logger = Zend_Registry::get('logger');
		try {
			$obModelo = new Fnde_Sice_Model_HistoricoTurma();

			$select = $obModelo->select()->where("NU_SEQ_HISTORICO_TURMA = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			$logger->log($e->getMessage(), Zend_Log::WARN);
		}
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_HISTORICO_TURMA');
	}

	/**
	 * Função para obter os alunos matriculados em uma turma.
	 * 
	 * @param int $codTurma
	 */
	public function getAlunosMatriculadosPorTurma( $codTurma ) {

		$query = " SELECT VINC.NU_MATRICULA, USU.NO_USUARIO, USU.NU_CPF, ";
		$query .= " VINC.NU_NOTA_TUTOR, VINC.NU_NOTA_CURSISTA, CRIT.DS_SITUACAO ";
		$query .= " FROM SICE_FNDE.S_USUARIO USU, SICE_FNDE.S_VINC_CURSISTA_TURMA VINC, ";
		$query .= " SICE_FNDE.S_CRITERIO_AVALIACAO CRIT ";
		$query .= " WHERE USU.NU_SEQ_USUARIO = VINC.NU_SEQ_USUARIO_CURSISTA ";
		$query .= " AND VINC.NU_SEQ_CRITERIO_AVAL = CRIT.NU_SEQ_CRITERIO_AVAL(+) ";
		$query .= " AND VINC.NU_SEQ_TURMA = $codTurma ";
		$query .= " ORDER BY USU.NO_USUARIO";

		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Função para obter o histórico da turma.
	 * 
	 * @param int $codTurma
	 */
	public function getHistoricoPorTurma( $codTurma ) {

		$query = " SELECT ";
		$query .= " 	HIST.NU_SEQ_HISTORICO_TURMA, ";
		$query .= " 	STR.DS_ST_TURMA AS ST_TURMA,";
		$query .= " 	TO_CHAR(HIST.DT_HISTORICO, 'DD/MM/YYYY HH24:MI') AS DT_HISTORICO, ";
		$query .= " 	USU.NO_USUARIO AS NO_USUARIO, ";
		$query .= " 	HIST.DS_OBSERVACAO ";
		$query .= " FROM ";
		$query .= " 	SICE_FNDE.S_HISTORICO_TURMA HIST ";
		$query .= " 	INNER JOIN SICE_FNDE.S_TURMA TUR ON HIST.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA ";
		$query .= " 	INNER JOIN SICE_FNDE.S_SITUACAO_TURMA STR ON HIST.ST_TURMA = STR.NU_SEQ_ST_TURMA ";
		$query .= " 	INNER JOIN SICE_FNDE.S_USUARIO USU ON HIST.ID_AUTOR = USU.NU_SEQ_USUARIO ";
		$query .= "WHERE HIST.NU_SEQ_TURMA = TUR.NU_SEQ_TURMA ";
		$query .= "AND HIST.NU_SEQ_TURMA = $codTurma ";
		$query .= "ORDER BY HIST.NU_SEQ_HISTORICO_TURMA ";

		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Função para gravar um registro de histórico da turma no banco de dados.
	 * 
	 * @param int $codTurma
	 * @param int $situacao
	 * @param int $codMotivo
	 * @param string $observacao
	 * @throws Exception
	 */
	public function preSalvar( $codTurma, $situacao, $codMotivo = NULL, $observacao = NULL ) {
		$obModelo = new Fnde_Sice_Model_HistoricoTurma();

		$arParamHistorico['NU_SEQ_TURMA'] = $codTurma;
		$arParamHistorico['ST_TURMA'] = $situacao;
		$arParamHistorico['DT_HISTORICO'] = date('d/m/Y G:i:s');
		$arParamHistorico['CO_MOTIVO_ALTERACAO'] = $codMotivo;
		$arParamHistorico['DS_OBSERVACAO'] = $observacao;

		//Recupera ID do usuario logado no sistema.
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);

		if ( $cpfUsuarioLogado ) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
			$arParamHistorico['ID_AUTOR'] = $arUsuario['NU_SEQ_USUARIO'];
		}

		try {
			$obModelo->insert($arParamHistorico);
		} catch ( Exception $e ) {
			throw $e;
		}

	}

	/**
	 * Função para recuperar o motivo de observação.
	 * 
	 * @param int $codTurma
	 * @param int $codStatus
	 */
	public function getMotivoObservacao( $codTurma, $codStatus ) {
		//$obModelo = new Fnde_Sice_Model_HistoricoTurma();

		$select = $this->getSelect(array('CO_MOTIVO_ALTERACAO', 'DS_OBSERVACAO'));
		$select->where("ST_TURMA = $codStatus")->where("NU_SEQ_TURMA = $codTurma")->where(
				"DT_HISTORICO =  (SELECT MAX(DT_HISTORICO)
    			 FROM SICE_FNDE.S_HISTORICO_TURMA WHERE NU_SEQ_TURMA = $codTurma AND ST_TURMA = $codStatus)");

		$stm = $select->query();
		$result = $stm->fetch();
		return $result;

	}

	/**
	 * Função para verificar se uma turma está com a situação avaliada.
	 * @param int $codTurma
	 * @return boolean
	 */
	public function isTurmaAvaliada( $codTurma ) {

		$query = " SELECT COUNT(VINC.NU_MATRICULA) QTD ";
		$query .= " FROM SICE_FNDE.S_VINC_CURSISTA_TURMA VINC ";
		$query .= " WHERE VINC.NU_SEQ_TURMA = $codTurma ";
		$query .= " AND VINC.NU_SEQ_CRITERIO_AVAL IS NULL ";

		$obModelo = new Fnde_Sice_Model_HistoricoTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		if ( $result['QTD'] > 0 ) {
			return false;
		} else {
			return true;
		}
	}
}
