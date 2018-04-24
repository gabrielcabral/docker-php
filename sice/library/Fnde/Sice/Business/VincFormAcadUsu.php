<?php

/**
 * Business do VincFormAcadUsu
 *
 * @author rafael.paiva
 * @since 25/06/2012
 */
class Fnde_Sice_Business_VincFormAcadUsu {

	/**
	 * Obtem VincFormAcadUsu por Id
	 *
	 * @author rafael.paiva
	 * @since 25/06/2012
	 */
	public function getVincFormAcadUsuById( $id ) {

		$obModelo = new Fnde_Sice_Model_VincFormAcadUsu();

		$select = $obModelo->select()->where("NU_SEQ_VINC_FORM_ACAD_USU = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Obtem o vinculo de formação academica com o usuário pelo ID do usuário.
	 * @param int $id Id do usuário.
	 */
	public function getVinculoByUsuario( $id ) {

		$obModelo = new Fnde_Sice_Model_VincFormAcadUsu();

		$select = $obModelo->select()->where("NU_SEQ_USUARIO = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 * Deleta vinculo formação acadêmica com usuário pelo ID.
	 *
	 * @author rafael.paiva
	 * @since 25/06/2012
	 */
	public function removerPorUsuario( $id ) {
		$obModelo = new Fnde_Sice_Model_VincFormAcadUsu();
		$where = "NU_SEQ_USUARIO = " . $id;
		$obModelo->delete($where);

		$this->stMensagem = "VincFormAcadUsu removido com sucesso !";
		return $this->stMensagem;
	}
}
