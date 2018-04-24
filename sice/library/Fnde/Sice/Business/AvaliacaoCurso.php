<?php

/**
 * Business do Usuario
 *
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_AvaliacaoCurso {

	CONST NOTA_BONUS_AVALIACAO_INSTITUCIONAL = 1;

	/**
	 * Verifica se o curso foi avaliado pelo cursista
	 * @param int $idUsuario
	 */
	public function isCursoAvaliadoCursista( $idTurma, $idCursista ) {

		$query = " SELECT ";
		$query .= " COUNT(AVC.NU_SEQ_TURMA) QUANT ";
		$query .= " FROM SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ";
		$query .= " INNER JOIN SICE_FNDE.S_AVALIACAO_CURSO AVC ON VCT.NU_SEQ_TURMA = AVC.NU_SEQ_TURMA AND VCT.NU_SEQ_USUARIO_CURSISTA = AVC.NU_SEQ_USUARIO ";
		$query .= " WHERE AVC.NU_SEQ_TURMA = $idTurma ";
		$query .= " AND AVC.NU_SEQ_USUARIO =  $idCursista ";

		$obModelo = new Fnde_Sice_Model_AvaliacaoCurso();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result['QUANT'];

	}

	/**
	 * Verifica se o curso foi avaliado pelo cursista
	 * @param int $idUsuario
	 */
	public function salvarAvaliacaoCurso( $arTurma ) {

		$obModelo = new Fnde_Sice_Model_AvaliacaoCurso();
		return $obModelo->insert($arTurma);

	}

	/**
	 * Verifica se o curso foi avaliado pelo cursista
	 * @param int $idUsuario
	 */
	public function avaliacaoCursoExistente( $arTurma ) {

		$obModelo = new Fnde_Sice_Model_AvaliacaoCurso();
		$avaliacao = $obModelo->find($arTurma);

		if(empty($avaliacao['NU_SEQ_AVALIACAO_CURSO'])){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Verifica se o curso foi avaliado pelo cursista
	 * @param int $idUsuario
	 */
	public function avaliacaoCursoExistentePorTurmaECursista( $idTurma, $idCursista ) {

		$query = " SELECT * ";
		$query .= " FROM SICE_FNDE.S_AVALIACAO_CURSO ";
		$query .= " WHERE NU_SEQ_TURMA = $idTurma ";
		$query .= " AND NU_SEQ_USUARIO =  $idCursista ";

		$obModelo = new Fnde_Sice_Model_AvaliacaoCurso();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		if(empty($result['NU_SEQ_AVALIACAO_CURSO'])){
			return false;
		}else{
			return true;
		}
	}
}
