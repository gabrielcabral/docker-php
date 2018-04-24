<?php
/**
 * Business do Situação Bolsa
 *
 * @author rafael.paiva
 * @since 03/04/2012
 */
class Fnde_Sice_Business_SituacaoBolsa {

	/**
	 * Obtem SituacaoBolsa por Id
	 *
	 * @author rafael.paiva
	 * @since 03/07/2012
	 */
	public function getSituacaoBolsaById( $id ) {

		try {
			$obModelo = new Fnde_Sice_Model_SituacaoBolsa();

			$select = $obModelo->select()->where("NU_SEQ_SITUACAO_BOLSA = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetch();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}
}
