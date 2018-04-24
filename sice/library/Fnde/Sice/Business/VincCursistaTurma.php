<?php

/**
 * Business do VincCursistaTurma
 * 
 * @author rafael.paiva
 * @since 07/05/2012
 */
class Fnde_Sice_Business_VincCursistaTurma {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_TURMA' => 'C.NU_SEQ_TURMA', 'NU_SEQ_USUARIO_CURSISTA' => 'C.NU_SEQ_USUARIO_CURSISTA',
				'NU_MATRICULA' => 'C.NU_MATRICULA', 'NU_NOTA_TUTOR' => 'C.NU_NOTA_TUTOR',
				'NU_NOTA_CURSISTA' => 'C.NU_NOTA_CURSISTA', 'ST_CURSISTA' => 'C.ST_CURSISTA',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_MATRICULA']) ) {
			$select->where("C.NU_MATRICULA = {$arParams['id']} ");
		} else {
			if ( $arParams['NU_SEQ_TURMA'] ) {
				$select->where("C.NU_SEQ_TURMA = ?", $arParams['NU_SEQ_TURMA']);
			}
			if ( $arParams['NU_SEQ_USUARIO_CURSISTA'] ) {
				$select->where("C.NU_SEQ_USUARIO_CURSISTA = ?", $arParams['NU_SEQ_USUARIO_CURSISTA']);
			}
			if ( $arParams['NU_MATRICULA'] ) {
				$select->where("C.NU_MATRICULA = ?", $arParams['NU_MATRICULA']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();

		try {
			$where = "NU_MATRICULA = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "VincCursistaTurma removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Exclui vinculo de cursista e turma passando o id do cursista.
	 * @param string $cursista ID do cursista.
	 */
	public function excluirVinculoPorCursista( $cursista ) {
		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		try {
			$where = "NU_SEQ_USUARIO_CURSISTA= " . $cursista;
			$obModelo->delete($where);

			$this->stMensagem = "VincCursistaTurma removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona VincCursistaTurma
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
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
	 * Recuperar as informações para validação do cursista no cadastro de cursista.
	 * @param String $cpf
	 */
	public function getInfoCursistaByCpf( $cpf ) {
		$query = "SELECT
		  PV.VL_EXERCICIO,
		  U.NU_SEQ_USUARIO,
		  U.ST_USUARIO,
		  T.ST_TURMA,
		  T.NU_SEQ_CURSO,
		  CASE WHEN SUBSTR(UPPER(CA.DS_SITUACAO), 1, 8) = 'APROVADO' THEN 'A'
		  WHEN UPPER(CA.DS_SITUACAO) = 'DESISTENTE' THEN 'D'
		  ELSE 'R' END AS ST_APROVADO
		FROM SICE_FNDE.S_USUARIO U
		JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON U.NU_SEQ_USUARIO = VCT.NU_SEQ_USUARIO_CURSISTA
		JOIN SICE_FNDE.S_TURMA T ON T.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA
		LEFT JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA ON CA.NU_SEQ_CRITERIO_AVAL = VCT.NU_SEQ_CRITERIO_AVAL
		LEFT JOIN SICE_FNDE.S_PERIODO_VINCULACAO PV ON PV.NU_SEQ_PERIODO_VINCULACAO = T.NU_SEQ_PERIODO_VINCULACAO
		WHERE U.NU_CPF = '{$cpf}'
		ORDER BY ST_APROVADO";

		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Recuperar as informações dos cursitas inscritos para a turma.
	 * @author Vinícius Cançado
	 * @param  $NU_SEQ_TURMA
	 */
	public function retornaCursistasTurma( $nuSeqTurma ) {

		$query = "SELECT vinculo.NU_MATRICULA, ";
		$query .= "cursista.NO_USUARIO, ";
		$query .= "cursista.NU_CPF, ";
		$query .= "(CASE WHEN  vinculo.st_notificado = 'S' THEN 'Notificado' ";
		$query .= "WHEN  vinculo.st_notificado = 'N' THEN 'Não notificado' ";
		$query .= "END) as st_notificado, ";
		$query .= "(SELECT (CASE WHEN  COUNT(NU_SEQ_AVALIACAO_CURSO) = 0 THEN 'Não avaliou' ";
		$query .= "WHEN  COUNT(NU_SEQ_AVALIACAO_CURSO) > 0 THEN 'Avaliou' ";
		$query .= "END) as st_avaliado FROM SICE_FNDE.S_AVALIACAO_CURSO ";
		$query .= "WHERE NU_SEQ_TURMA = {$nuSeqTurma} AND NU_SEQ_USUARIO = cursista.nu_seq_usuario) as avalicao ";
		$query .= "FROM ";
		$query .= "SICE_FNDE.S_USUARIO cursista, ";
		$query .= "SICE_FNDE.S_VINC_CURSISTA_TURMA vinculo, ";
		$query .= "SICE_FNDE.S_TURMA turma ";
		$query .= "WHERE ";
		$query .= "cursista.NU_SEQ_USUARIO = vinculo.NU_SEQ_USUARIO_CURSISTA AND ";
		$query .= "vinculo.NU_SEQ_TURMA = turma.NU_SEQ_TURMA AND ";
		$query .= "turma.NU_SEQ_TURMA = {$nuSeqTurma} ";
		$query .= "ORDER BY vinculo.NU_MATRICULA";

		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}

	public function getCursistaAvaliouCursoTurma( $nuSeqTurma ) {
		try {
			$query = "SELECT VCT.NU_MATRICULA
				FROM SICE_FNDE.S_VINC_CURSISTA_TURMA VCT
				INNER JOIN SICE_FNDE.S_AVALIACAO_CURSO AVC ON VCT.NU_SEQ_USUARIO_CURSISTA = AVC.NU_SEQ_USUARIO
       		    AND AVC.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA
				WHERE AVC.NU_SEQ_TURMA = {$nuSeqTurma} ";

			$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
			$stm = $obModelo->getAdapter()->query($query);
			return $stm->fetchAll();
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Recuperar o número de cursistas não notificados para a respectiva turma
	 * @author Vinícius Cançado
	 * @param  $NU_SEQ_TURMA
	 */
	public function retornaCursistasNaoNotificados( $nuSeqTurma ) {

		try {
			$obModelo = new Fnde_Sice_Model_VincCursistaTurma();

			$select = $obModelo->select()->where("ST_NOTIFICADO = 'N' AND NU_SEQ_TURMA = ?", $nuSeqTurma);
			$stmt = $select->query();
			$result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}

		return $result;
	}

	/**
	 * Recuperar o número de cursistas notificados para a respectiva turma
	 * @author Vinícius Cançado
	 * @param  $NU_SEQ_TURMA
	 */
	public function retornaCursistasNotificados( $nuSeqTurma ) {

		try {
			$obModelo = new Fnde_Sice_Model_VincCursistaTurma();

			$select = $obModelo->select()->where("ST_NOTIFICADO = 'S' AND NU_SEQ_TURMA = ?", $nuSeqTurma);
			$stmt = $select->query();
			$result = $stmt->fetchAll();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}

		return $result;
	}

	/**
	 * Recuperar o número de cursistas não notificados para a respectiva turma
	 * @author Vinícius Cançado
	 * @param  $NU_SEQ_TURMA
	 */
	public function atualisarNotificacaoCursista( $arMatriculas, $nuSeqTurma ) {

		$where = " NU_SEQ_TURMA = '{$nuSeqTurma}' AND NU_MATRICULA IN (";
		if ( count($arMatriculas) == 1 )
			$where .= $arMatriculas[0] . ")";
		else {
			for ( $i = 0; $i < count($arMatriculas); $i++ ) {
				if ( $i == ( count($arMatriculas) - 1 ) ) {
					$where .= $arMatriculas[$i] . ")";
				} else {
					$where .= $arMatriculas[$i] . ",";
				}
			}
		}

		$data['ST_NOTIFICADO'] = 'S';
		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$obModelo->getAdapter()->beginTransaction();

		try {
			$obModelo->update($data, $where);
			$obModelo->getAdapter()->commit();
		} catch ( Exception $e ) {
			$obModelo->getAdapter()->rollBack();
			throw $e;
		}
	}

	/**
	 * Obtem VincCursistaTurma por Id
	 *
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function getVincCursistaTurmaById( $id ) {

		$logger = Zend_Registry::get('logger');
		try {
			$obModelo = new Fnde_Sice_Model_VincCursistaTurma();

			$select = $obModelo->select()->where("NU_MATRICULA = ?", $id);

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
	 * @author rafael.paiva
	 * @since 07/05/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_MATRICULA');
	}

	/**
	 * Insere valor da nota do aluno
	 *
	 * @author gustavo.gomes
	 * @since 08/10/2012
	 */
	public function setNotaCursista( $usuario, $turma, $valor, $transacionar = true) {

		$where = "NU_SEQ_TURMA = $turma AND NU_SEQ_USUARIO_CURSISTA = $usuario";

		$data['NU_NOTA_CURSISTA'] = $valor;
		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();

		if($transacionar) {
			$obModelo->getAdapter()->beginTransaction();
		}

		try {
			$obModelo->update($data, $where);
			if($transacionar) {
				$obModelo->getAdapter()->commit();
			}
		} catch ( Exception $e ) {
			if($transacionar) {
				$obModelo->getAdapter()->rollBack();
			}
			throw $e;
		}
	}

	/**
	 * Retorna dados do cursista para envio do email.
	 * @param string $idmatricula Matricula do cursista.
	 * @param string $idturma ID da turma.
	 */
	public function getDadosEmailCursista( $idmatricula, $idturma ) {

		//$logger = Zend_Registry::get('logger');
		try {
			$where = "(";
			if ( count($idmatricula) == 1 )
				$where .= $idmatricula[0] . ")";
			else {
				for ( $i = 0; $i < count($idmatricula); $i++ ) {
					if ( $i == ( count($idmatricula) - 1 ) ) {
						$where .= $idmatricula[$i] . ")";
					} else {
						$where .= $idmatricula[$i] . ",";
					}
				}
			}

			$query = "  SELECT CUR.DS_NOME_CURSO ";
			$query .= "         ,TUT.NO_USUARIO AS TUTOR ";
			$query .= "         ,SMO.VL_CARGA_HORARIA ";
			$query .= "         ,SMO.VL_CARGA_PRESENCIAL ";
			$query .= "         ,SMO.VL_CARGA_DISTANCIA ";
			$query .= "         ,USU.NO_USUARIO AS CURSISTA ";
			$query .= "         ,USU.DS_EMAIL_USUARIO ";
			$query .= "    FROM SICE_FNDE.S_TURMA TUR ";
			$query .= "   INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON VCT.NU_SEQ_TURMA   = TUR.NU_SEQ_TURMA ";
			$query .= "   INNER JOIN SICE_FNDE.S_USUARIO USU             ON USU.NU_SEQ_USUARIO = VCT.NU_SEQ_USUARIO_CURSISTA ";
			$query .= "   INNER JOIN SICE_FNDE.S_USUARIO TUT             ON TUT.NU_SEQ_USUARIO = TUR.NU_SEQ_USUARIO_TUTOR ";
			$query .= "   INNER JOIN SICE_FNDE.S_CURSO CUR               ON CUR.NU_SEQ_CURSO   = TUR.NU_SEQ_CURSO ";
			$query .= "   INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM   ON VCM.NU_SEQ_CURSO   = CUR.NU_SEQ_CURSO ";
			$query .= "   INNER JOIN SICE_FNDE.S_MODULO SMO              ON SMO.NU_SEQ_MODULO  = VCM.NU_SEQ_MODULO ";
			$query .= "   WHERE VCT.NU_MATRICULA IN {$where} ";
			$query .= "     AND TUR.NU_SEQ_TURMA =  {$idturma}";

			$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
			$stm = $obModelo->getAdapter()->query($query);
			$result = $stm->fetchAll();
			return $result;
		} catch ( Exception $e ) {
			//$logger->log($e->getMessage(), Zend_Log::WARN);
		}
	}

	/**
	 * Recuperar as informações dos cursitas inscritos para a turma.
	 * @author Vinícius Cançado
	 * @param  $NU_SEQ_TURMA
	 */
	public function retornaVinculosPorIdTurma( $nuSeqTurma ) {

		$query = "SELECT * ";
		$query .= "FROM SICE_FNDE.S_VINC_CURSISTA_TURMA ";
		$query .= "WHERE NU_SEQ_TURMA = {$nuSeqTurma} ";

		$obModelo = new Fnde_Sice_Model_VincCursistaTurma();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;

	}
}
