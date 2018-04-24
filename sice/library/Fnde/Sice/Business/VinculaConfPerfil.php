<?php

/**
 * Business do VinculaConfiguracaoPerfil
 *
 * @author vinicius.cancado
 * @since 05/04/2012
 */
class Fnde_Sice_Business_VinculaConfPerfil {

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */

	public function getColumnsSearch() {
		return array('NU_SEQ_CONFIGURACAO' => 'C.NU_SEQ_CONFIGURACAO');
	}

	/**
	 * Vincula configuracao perfil.
	 * @param array $arVinculaPerfil
	 * @throws Exception
	 */
	public function vinculaConfiguracaoPerfil( $arVinculaPerfil ) {

		$obModelo = new Fnde_Sice_Model_VinculaConfPerfil();
		$obValorBolsaPerfil = new Fnde_Sice_Business_ValorBolsaPerfil();
		try {

			$vinculaPerfilConfiguracao = $arVinculaPerfil["vinculaPerfilConfiguracao"];

			for ( $i = 0; $i < count($vinculaPerfilConfiguracao); $i++ ) {
				$vinculo = array();
				$vinculo["QT_BOLSA_PERIODO"] = $vinculaPerfilConfiguracao[$i]["QT_BOLSA_PERIODO"] . "";
				$vinculo["NU_SEQ_TIPO_PERFIL"] = $vinculaPerfilConfiguracao[$i]["NU_SEQ_TIPO_PERFIL"] . "";
				$vinculo["NU_SEQ_CONFIGURACAO"] = $vinculaPerfilConfiguracao[$i]["NU_SEQ_CONFIGURACAO"] . "";

				$idVinculo = $obModelo->insert($vinculo);
				$valores = $vinculaPerfilConfiguracao[$i]["valores"];

				for ( $j = 0; $j < count($valores); $j++ ) {
					$valorBolsaPerfil = array();

					$valor = ( string ) $valores[$j]['valor'];
					$valorBolsaPerfil["VL_BOLSA"] = str_replace(Fnde_Sice_Business_Componentes::REPLACE_DE,
							Fnde_Sice_Business_Componentes::REPLACE_PARA, $valor);
					$valorBolsaPerfil["QT_TURMA"] = ( string ) $valores[$j]['atTurma'];
					$valorBolsaPerfil["NU_SEQ_VINC_CONF_PERF"] = $idVinculo;
					$obValorBolsaPerfil->criarValorBolsaPerfil($valorBolsaPerfil);
				}
			}
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Obtem Configuracao por Id
	 *
	 * @author diego.matos
	 * @since 30/03/2012
	 */
	public function getVinculoConfiguracaoPerfilPorConfiguracao( $id ) {

		$obModelo = new Fnde_Sice_Model_VinculaConfPerfil();
		$select = $obModelo->select()->where("NU_SEQ_CONFIGURACAO = ?", $id);
		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author vinicius.cancado
	 * @since 05/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_VinculaConfPerfil();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

}
