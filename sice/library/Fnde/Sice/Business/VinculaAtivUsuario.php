<?php

/**
 * Business do VinculaConfiguracaoPerfil
 *
 * @author vinicius.cancado
 * @since 05/04/2012
 */
class Fnde_Sice_Business_VinculaAtivUsuario {

	protected $_stModelo = 'VinculaAtivUsuario';
	protected $_stSistema = 'sice';

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_VinculaAtivUsuario();
		$where = "NU_SEQ_USUARIO = " . $id;
		$obModelo->delete($where);

		$this->stMensagem = "Vinculo removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Retorna atividade por usuario.
	 * @param string $id ID do usuario.
	 */
	public function buscarVinculoPorUsuario( $id ) {
		//$obModelo = new Fnde_Sice_Model_VinculaAtivUsuario();

		$select = $this->getSelect(array('NU_SEQ_ATIVIDADE', 'NU_SEQ_USUARIO', 'DS_ATIVIDADE_ALTERNATIVA'));
		$select->where('NU_SEQ_USUARIO = ?', $id);

		$stm = $select->query();
		$result = $stm->fetchAll();

		return $result;
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

		$obModelo = new Fnde_Sice_Model_VinculaAtivUsuario();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}
}
