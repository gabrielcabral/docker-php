<?php

/**
 * Business do VinculaConfiguracaoPerfil
 *
 * @author vinicius.cancado
 * @since 05/04/2011
 */
class Fnde_Sice_Business_ValorBolsaPerfil {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function criarValorBolsaPerfil( $valorBolsaPerfil ) {

		$obModelo = new Fnde_Sice_Model_ValorBolsaPerfil();
		try {
			$obModelo->insert($valorBolsaPerfil);
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Obtem Configuracao por Id
	 *
	 * @author vinicius.cancado
	 * @since 09/04/2012
	 */
	public function getValorBolsaPerfilPorVinculo( $id ) {

		try {
			$obModelo = new Fnde_Sice_Model_ValorBolsaPerfil();
			//$arInfoModelo = $obModelo->info();

			$select = $obModelo->select()->where("NU_SEQ_VINC_CONF_PERF = ?", $id);

			$stmt = $select->query();
			$result = $stmt->fetchAll();

			return $result;
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author vinicius.cancado
	 * @since 09/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_ValorBolsaPerfil();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}
}
