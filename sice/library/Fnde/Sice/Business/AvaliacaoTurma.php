<?php

/**
 * Business do AvaliacaoTurma
 * 
 * @author rafael.paiva
 * @since 10/07/2012
 */
class Fnde_Sice_Business_AvaliacaoTurma {

	/**
	 * Obtem AvaliacaoTurma por Id
	 *
	 * @author rafael.paiva
	 * @since 10/07/2012
	 */
	public function getAvaliacaoTurmaById( $id ) {

		$obModelo = new Fnde_Sice_Model_AvaliacaoTurma();

		$select = $obModelo->select()->where("NU_SEQ_TURMA = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}
}
