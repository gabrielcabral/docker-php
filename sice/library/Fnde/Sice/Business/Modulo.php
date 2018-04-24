<?php

/**
 * Business do Modulo
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Fnde_Sice_Business_Modulo {

	protected $_stModelo = 'Modulo';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('VL_CARGA_DISTANCIA' => 'C.VL_CARGA_DISTANCIA',
				'DS_CONTEUDO_PROGRAMATICO' => 'C.DS_CONTEUDO_PROGRAMATICO',
				'NU_SEQ_TIPO_CURSO' => 'C.NU_SEQ_TIPO_CURSO', 'VL_MAX_CONCLUSAO' => 'C.VL_MAX_CONCLUSAO',
				'ST_MODULO' => 'C.ST_MODULO', 'DS_NOME_MODULO' => 'C.DS_NOME_MODULO',
				'DS_SIGLA_MODULO' => 'C.DS_SIGLA_MODULO',
				'NU_SEQ_MODULO_PREREQUISITO' => 'C.NU_SEQ_MODULO_PREREQUISITO', 'NU_SEQ_MODULO' => 'C.NU_SEQ_MODULO',
				'DS_PREREQUISITO_MODULO' => 'C.DS_PREREQUISITO_MODULO', 'VL_CARGA_HORARIA' => 'C.VL_CARGA_HORARIA',
				'VL_MIN_CONCLUSAO' => 'C.VL_MIN_CONCLUSAO', 'VL_CARGA_PRESENCIAL' => 'C.VL_CARGA_PRESENCIAL',);
	}

	public function getColumnsSearchCustom() {
		return array('NU_SEQ_MODULO' => 'C.NU_SEQ_MODULO', 'DS_NOME_MODULO' => 'C.DS_NOME_MODULO');
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_MODULO']) ) {
			$select->where("C.NU_SEQ_MODULO = {$arParams['id']} ");
		} else {
			$this->setMonFiltroModulo($select, $arParams, 'VL_CARGA_DISTANCIA');
			$this->setMonFiltroModulo($select, $arParams, 'DS_CONTEUDO_PROGRAMATICO');
			$this->setMonFiltroModulo($select, $arParams, 'NU_SEQ_TIPO_CURSO');
			$this->setMonFiltroModulo($select, $arParams, 'VL_MAX_CONCLUSAO');
			$this->setMonFiltroModulo($select, $arParams, 'ST_MODULO');
			$this->setMonFiltroModulo($select, $arParams, 'DS_NOME_MODULO');
			$this->setMonFiltroModulo($select, $arParams, 'DS_SIGLA_MODULO');
			$this->setMonFiltroModulo($select, $arParams, 'NU_SEQ_MODULO_PREREQUISITO');
			$this->setMonFiltroModulo($select, $arParams, 'NU_SEQ_MODULO');
			$this->setMonFiltroModulo($select, $arParams, 'DS_PREREQUISITO_MODULO');
			$this->setMonFiltroModulo($select, $arParams, 'VL_CARGA_HORARIA');
			$this->setMonFiltroModulo($select, $arParams, 'VL_MIN_CONCLUSAO');
			$this->setMonFiltroModulo($select, $arParams, 'VL_CARGA_PRESENCIAL');
		}
	}

	/**
	 * Método que auxilia a montagem dos filtros
	 * @param $select
	 * @param array $arParams
	 * @param string $descricao
	 */
	private function setMonFiltroModulo( $select, $arParams, $descricao ) {
		if ( $arParams[$descricao] ) {
			$select->where("C." . $descricao . " = ?", $arParams[$descricao]);
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_Modulo();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_Modulo();

		try {
			$where = "NU_SEQ_MODULO = " . $id;
			$obModelo->delete($where);
			$this->stMensagem = "Modulo removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Modulo
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
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
	 * Obtem Modulo por Id
	 *
	 * @author diego.matos
	 * @since 11/05/2012
	 */
	public function getModuloById( $id, $boArray = true ) {

		$obModelo = new Fnde_Sice_Model_Modulo();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$aDados = $obModelo->find($id)->current();
		if ( $aDados ) {
			return $boArray ? $aDados->toArray() : $aDados;
		}
		return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
	}

	/**
	 * Obtem Modulo que for diferente do modulo editado
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getListaModuloNaoEditado( $id ) {

		$select = $this->getSelect($this->getColumnsSearchCustom())->where("NU_SEQ_MODULO <> :NU_SEQ_MODULO", $id);

		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_MODULO');
	}

	/**
	 * Efetua a pesquisa dos módulos cadastrado de acordo com o filtro informado.
	 * @param array $arParams
	 */
	public function pesquisarModulo( $arParams ) {
		$query = " SELECT ";
		$query .= " MD.NU_SEQ_MODULO, ";
		$query .= " TC.DS_TIPO_CURSO, ";
		$query .= " MD.DS_SIGLA_MODULO, ";
		$query .= " MD.DS_NOME_MODULO, ";
		$query .= " MD.VL_CARGA_HORARIA, ";
		$query .= " (CASE WHEN MD.DS_PREREQUISITO_MODULO = 'S' THEN 'Sim' WHEN MD.DS_PREREQUISITO_MODULO = 'N' THEN 'Não' END) AS DS_PREREQUISITO_MODULO, ";
		$query .= " (CASE WHEN MD.ST_MODULO = 'A' THEN 'Ativo' WHEN MD.ST_MODULO = 'D' THEN 'Inativo' END) AS ST_MODULO ";
		$query .= " FROM ";
		$query .= " SICE_FNDE.S_MODULO MD INNER JOIN SICE_FNDE.S_TIPO_CURSO TC ON MD.NU_SEQ_TIPO_CURSO = TC.NU_SEQ_TIPO_CURSO ";
		$query .= " WHERE ";
		$query .= " 1=1 ";
		if ( $arParams['NU_SEQ_TIPO_CURSO'] ) {
			$query .= " AND TC.NU_SEQ_TIPO_CURSO = :NU_SEQ_TIPO_CURSO ";
		}
		if ( $arParams['DS_SIGLA_MODULO'] ) {
			$query .= " AND MD.DS_SIGLA_MODULO = :DS_SIGLA_MODULO ";
		}
		if ( $arParams['DS_NOME_MODULO'] ) {
			$query .= " AND MD.DS_NOME_MODULO LIKE :DS_NOME_MODULO ";
			$arParams['DS_NOME_MODULO'] = "%" . $arParams['DS_NOME_MODULO'] . "%";
		}
		if ( $arParams['ST_MODULO'] ) {
			$query .= " AND MD.ST_MODULO = :ST_MODULO ";
		}

		$query .= " ORDER BY MD.DS_SIGLA_MODULO ";

		$obModelo = new Fnde_Sice_Model_Modulo();

		$stm = $obModelo->getAdapter()->query($query, $arParams);

		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Função para calcular o total de horas do módulo.
	 * 
	 * @param array $arModulos
	 */
	public function retornarTotalHoras( $arModulos ) {

		$query = " SELECT SUM(VL_CARGA_HORARIA) AS TOTAL_HORAS FROM SICE_FNDE.S_MODULO WHERE NU_SEQ_MODULO IN ( ";

		foreach ( $arModulos as $val ) {
			$query .= "'" . $val['NU_SEQ_MODULO'] . "',";
		}
		$query = substr($query, 0, -1);
		$query .= ")";

		$obModelo = new Fnde_Sice_Model_Modulo();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result[0]['TOTAL_HORAS'];
	}

	/**
	 * Função para verificar se um módulo está vinculado a algum curso
	 * @param array $arParam
	 */
	public function verificaVinculacao( $arParam ) {
		$query = "SELECT * FROM SICE_FNDE.S_VINC_CURSO_MODULO WHERE NU_SEQ_MODULO = :NU_SEQ_MODULO";
		$obModelo = new Fnde_Sice_Model_Modulo();
		$stm = $obModelo->getAdapter()->query($query, $arParam);
		$result = $stm->fetchAll();
		return $result;
	}
}
