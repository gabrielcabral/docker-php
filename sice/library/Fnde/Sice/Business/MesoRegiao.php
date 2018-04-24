<?php

/**
 * Business do MesoRegiao
 *
 * @author tiago.ramos
 * @since 03/04/2012
 */
class Fnde_Sice_Business_MesoRegiao {

	protected $_stModelo = 'MesoRegiao';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NO_MESO_REGIAO' => 'C.NO_MESO_REGIAO', 'CO_MUNICIPIO_IBGE' => 'C.CO_MUNICIPIO_IBGE',
				'NO_MUNICIPIO' => 'C.NO_MUNICIPIO', 'CO_MESO_REGIAO' => 'C.CO_MESO_REGIAO', 'NO_UF' => 'C.NO_UF',
				'CO_UF' => 'C.CO_UF',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['CO_MESO_REGIAO']) ) {
			$select->where("C.CO_MESO_REGIAO = {$arParams['CO_MESO_REGIAO']} ");
		} else {
			if ( $arParams['NO_MESO_REGIAO'] ) {
				$select->where("C.NO_MESO_REGIAO = ?", $arParams['NO_MESO_REGIAO']);
			}
			if ( $arParams['CO_MUNICIPIO_IBGE'] ) {
				$select->where("C.CO_MUNICIPIO_IBGE = ?", $arParams['CO_MUNICIPIO_IBGE']);
			}
			if ( $arParams['NO_MUNICIPIO'] ) {
				$select->where("C.NO_MUNICIPIO = ?", $arParams['NO_MUNICIPIO']);
			}
			if ( $arParams['CO_MESO_REGIAO'] ) {
				$select->where("C.CO_MESO_REGIAO = ?", $arParams['CO_MESO_REGIAO']);
			}
			if ( $arParams['NO_UF'] ) {
				$select->where("C.NO_UF = ?", $arParams['NO_UF']);
			}
			if ( $arParams['CO_UF'] ) {
				$select->where("C.CO_UF = ?", $arParams['CO_UF']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		try {
			$where = "CO_MESO_REGIAO = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "MesoRegiao removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona MesoRegiao
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function search( $arParams ) {
		try {
			$select = $this->getSelect($this->getColumnsSearch());
			$this->setFilter($select, $arParams);
			$this->setOrder($select);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
		} catch ( Exception $e ) {

		}

		return $result;
	}

	/**
	 * Obtem MesoRegiao por Id
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getMesoRegiaoById( $id ) {
		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$select = $obModelo->select()->where("CO_MESO_REGIAO = '{$id}'", array());
		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * Obtem MesoRegiao por codigo da UF
	 *
	 * @author vinicius.cancado
	 * @since 10/04/2012
	 */
	public function getMesoRegiaoPorUF( $sgUf ) {

		$query = " SELECT ";
		$query .= " DISTINCT(MESOREGIAO.CO_MESO_REGIAO), ";
		$query .= " MESOREGIAO.NO_MESO_REGIAO ";
		$query .= " FROM ";
		$query .= " CTE_FNDE.T_MESO_REGIAO MESOREGIAO, ";
		$query .= " CORP_FNDE.S_UF UF ";
		$query .= " WHERE ";
		$query .= " UF.CO_UF_IBGE = MESOREGIAO.CO_UF AND ";
		$query .= " UF.SG_UF = '{$sgUf}' ";

		$query .= " ORDER BY MESOREGIAO.NO_MESO_REGIAO";

		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$stm = $obModelo->getAdapter()->query($query, array());
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Função para obter a mesorregião pelo código do município.
	 * 
	 * @param int $codigoMunicipio
	 */
	public function getMesoRegiaoPorMunicipio( $codigoMunicipio ) {

		$select = $this->getSelect(array('NO_MESO_REGIAO', 'CO_MESO_REGIAO', 'NO_MUNICIPIO'));
		$select->where("CO_MUNICIPIO_IBGE = $codigoMunicipio");

		$stm = $select->query();
		$result = $stm->fetchAll();

		return $result;
	}

	/**
	 * Obtem MesoRegiao por Id
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function getMunicipioPorMesoRegiao( $id ) {

		$select = $this->getSelect(array('CO_MUNICIPIO_IBGE', 'NO_MUNICIPIO'));
		$select->where("CO_MESO_REGIAO = '{$id}'")->order('NO_MUNICIPIO');

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;

	}

	/** 
	 * Obtem Municipio por Id
	 *
	 * @author poliane.silva
	 * @since 03/04/2012
	 */
	public function getMunicipioById( $id ) {

		$select = $this->getSelect(array('CO_MUNICIPIO_IBGE', 'NO_MUNICIPIO'));
		$select->where("CO_MUNICIPIO_IBGE = '{$id}'")->order('NO_MUNICIPIO');

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author tiago.ramos
	 * @since 03/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.CO_MESO_REGIAO');
	}

	/**
	 * Obtém a mesorregião pelo município fnde.
	 * @param int $codigoMunicipioFnde
	 */
	public function getMesoRegiaoPorMunicipioFnde( $codigoMunicipioFnde ) {

		$arrayParametros["CO_MUNICIPIO_FNDE"] = $codigoMunicipioFnde;

		$query = " SELECT ";
		$query .= " MESOREGIAO.NO_MESO_REGIAO, ";
		$query .= " MESOREGIAO.CO_MESO_REGIAO ";
		$query .= " FROM ";
		$query .= " CTE_FNDE.T_MESO_REGIAO MESOREGIAO ";
		$query .= " INNER JOIN CORP_FNDE.S_MUNICIPIO MUNICIPIO ";
		$query .= " ON MESOREGIAO.CO_MUNICIPIO_IBGE = MUNICIPIO.CO_MUNICIPIO_IBGE ";
		$query .= " WHERE ";
		$query .= " MUNICIPIO.CO_MUNICIPIO_FNDE = :CO_MUNICIPIO_FNDE ";

		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$stm = $obModelo->getAdapter()->query($query, $arrayParametros);
		$result = $stm->fetchAll();

		return $result;

	}

	/**
	 * Obtem municipio com o código FNDE por mesorregioa
	 *
	 * @author poliane.silva
	 * @since 04/09/2012
	 */
	public function getMunicipioFndePorMesoRegiao( $id ) {

		$query = " SELECT ";
		$query .= " MUN.CO_MUNICIPIO_FNDE, ";
		$query .= " MUN.NO_MUNICIPIO ";
		$query .= " FROM CORP_FNDE.S_MUNICIPIO MUN ";
		$query .= " INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON MUN.CO_MUNICIPIO_IBGE = MSR.CO_MUNICIPIO_IBGE ";
		$query .= " WHERE MSR.CO_MESO_REGIAO = $id ";
		$query .= " ORDER BY MUN.NO_MUNICIPIO ";

		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Obtem mesoregiao por estados selecioandos
	 *
	 * @param array $arrayEstados
	 * @author pedro.correia
	 * @since 04/02/2016
	 */
	public function getMesoregioesPorEstados($arrayEstados){
		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfilUsuario = $usuarioLogado->credentials;

		$businessUsuario = new Fnde_Sice_Business_Usuario();

		$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
		if ($cpfUsuarioLogado) {
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
		}

		$query = " SELECT ";
		$query .= " DISTINCT(MESOREGIAO.CO_MESO_REGIAO), ";
		$query .= " UF.SG_UF || ' - ' || MESOREGIAO.NO_MESO_REGIAO as MESO ";
		$query .= " FROM ";
		$query .= " CTE_FNDE.T_MESO_REGIAO MESOREGIAO, ";
		$query .= " CORP_FNDE.S_UF UF ";
		$query .= " WHERE ";
		$query .= " UF.CO_UF_IBGE = MESOREGIAO.CO_UF AND ";
		$query .= " UF.SG_UF IN ($arrayEstados) ";

		if(in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)){
				$query .= " AND MESOREGIAO.CO_MESO_REGIAO = {$arUsuario['CO_MESORREGIAO']} ";
		}else if(in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)){
				$query .= " AND MESOREGIAO.CO_MUNICIPIO_IBGE = {$arUsuario['CO_MUNICIPIO_PERFIL']} ";
		}

		$query .= " ORDER BY MESO";

		$obModelo = new Fnde_Sice_Model_MesoRegiao();
		$stm = $obModelo->getAdapter()->query($query, array());
		$result = $stm->fetchAll();
		return $result;
	}

	public function getRedeDeEnsino($id = null){
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$result = $businessUsuario->getRedeEnsino($id);
		return $result;
	}
}
