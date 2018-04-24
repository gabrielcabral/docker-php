<?php

/**
 * Business do Curso
 * 
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class Fnde_Sice_Business_Curso {

	protected $_stModelo = 'Curso';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NU_SEQ_TIPO_CURSO' => 'C.NU_SEQ_TIPO_CURSO', 'QT_MODULOS' => 'C.QT_MODULOS',
				'ST_CURSO' => 'C.ST_CURSO', 'NU_SEQ_CURSO_PREREQUISITO' => 'C.NU_SEQ_CURSO_PREREQUISITO',
				'DS_PREREQUISITO_CURSO' => 'C.DS_PREREQUISITO_CURSO', 'NU_SEQ_CURSO' => 'C.NU_SEQ_CURSO',
				'VL_CARGA_HORARIA' => 'C.VL_CARGA_HORARIA', 'DS_NOME_CURSO' => 'C.DS_NOME_CURSO',
				'DS_SIGLA_CURSO' => 'C.DS_SIGLA_CURSO',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_CURSO']) ) {
			$select->where("C.NU_SEQ_CURSO = {$arParams['id']} ");
		} else {
			$this->setMonFiltroCurso($select, $arParams, 'NU_SEQ_TIPO_CURSO');
			$this->setMonFiltroCurso($select, $arParams, 'QT_MODULOS');
			$this->setMonFiltroCurso($select, $arParams, 'ST_CURSO');
			$this->setMonFiltroCurso($select, $arParams, 'NU_SEQ_CURSO_PREREQUISITO');
			$this->setMonFiltroCurso($select, $arParams, 'DS_PREREQUISITO_CURSO');
			$this->setMonFiltroCurso($select, $arParams, 'NU_SEQ_CURSO');
			$this->setMonFiltroCurso($select, $arParams, 'VL_CARGA_HORARIA');
			$this->setMonFiltroCurso($select, $arParams, 'DS_NOME_CURSO');
			$this->setMonFiltroCurso($select, $arParams, 'DS_SIGLA_CURSO');
		}
	}

	/**
	 * Método que auxilia a montagem dos filtros
	 * @param $select
	 * @param array $arParams
	 * @param string $descricao
	 */
	private function setMonFiltroCurso( $select, $arParams, $descricao ) {
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

		$obModelo = new Fnde_Sice_Model_Curso();
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
		$obModelo = new Fnde_Sice_Model_Curso();

		try {
			$where = "NU_SEQ_CURSO = " . $id;
			$obModelo->delete($where);

			$this->stMensagem = "Curso removido com sucesso !";
			return $this->stMensagem;
		} catch ( Exception $e ) {
			$this->stMensagem[] = "Erro ao tentar Excluir";
		}
	}

	/**
	 * Seleciona Curso
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
	 * Obtem Curso por Id
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function getCursoById( $id ) {
		$obModelo = new Fnde_Sice_Model_Curso();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$aDados = $obModelo->find($id)->current();
		if ( $aDados ) {
			return $boArray ? $aDados->toArray() : $aDados;
		}
		return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author vinicius.cancado
	 * @since 18/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_CURSO');
	}

	/**
	 * Efetua a pesquisa dos cursos cadastrado de acordo com o filtro informado.
	 * @param array $arParams
	 */
	public function pesquisarCurso( $arParams ) {
		$query = " SELECT ";
		$query .= " CR.NU_SEQ_CURSO, ";
		$query .= " TC.DS_TIPO_CURSO, ";
		$query .= " CR.DS_SIGLA_CURSO, ";
		$query .= " CR.DS_NOME_CURSO, ";
		$query .= " CR.VL_CARGA_HORARIA, ";
		$query .= " (CASE WHEN CR.DS_PREREQUISITO_CURSO = 'S' THEN 'Sim' WHEN CR.DS_PREREQUISITO_CURSO = 'N' THEN 'Não' END) AS DS_PREREQUISITO_CURSO, ";
		$query .= " (CASE WHEN CR.ST_CURSO = 'A' THEN 'Ativo' WHEN CR.ST_CURSO = 'D' THEN 'Inativo' END) AS ST_CURSO ";
		$query .= " FROM ";
		$query .= " SICE_FNDE.S_CURSO CR INNER JOIN SICE_FNDE.S_TIPO_CURSO TC ON CR.NU_SEQ_TIPO_CURSO = TC.NU_SEQ_TIPO_CURSO ";
		$query .= " WHERE ";
		$query .= " 1=1 ";
		if ( $arParams['NU_SEQ_TIPO_CURSO'] ) {
			$query .= " AND TC.NU_SEQ_TIPO_CURSO = :NU_SEQ_TIPO_CURSO ";
		}
		if ( $arParams['DS_SIGLA_CURSO'] ) {
			$query .= " AND CR.DS_SIGLA_CURSO = :DS_SIGLA_CURSO ";
		}
		if ( $arParams['DS_NOME_CURSO'] ) {
			$query .= " AND CR.DS_NOME_CURSO LIKE :DS_NOME_CURSO ";
			$arParams["DS_NOME_CURSO"] = "%" . $arParams['DS_NOME_CURSO'] . "%";
		}
		if ( $arParams['ST_CURSO'] ) {
			$query .= " AND CR.ST_CURSO = :ST_CURSO ";
		}

		$query .= " ORDER BY CR.DS_SIGLA_CURSO ";

		$obModelo = new Fnde_Sice_Model_Curso();
		$stm = $obModelo->getAdapter()->query($query, $arParams);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Método para gravar um curso e os vínculos de módulos a ele associados
	 * 
	 * @param array $curso
	 * @param array $modulosCurso
	 * @throws Exception
	 */
	public function salvarCurso( $curso, $modulosCurso ) {
		try {
			$obModelo = new Fnde_Sice_Model_Curso();
			$obModelo->getAdapter()->beginTransaction();

			$cursoInserido = null;
			if ( $curso['NU_SEQ_CURSO'] == null ) {
				$cursoInserido = $obModelo->insert($curso);
			} else {
				$where = "NU_SEQ_CURSO = " . $curso['NU_SEQ_CURSO'];
				$cursoInserido = $obModelo->update($curso, $where);
				$cursoInserido = $curso['NU_SEQ_CURSO'];
			}
			$obModeloVincCursoModulo = new Fnde_Sice_Model_VincCursoModulo();
			for ( $i = 0; $i < count($modulosCurso); $i++ ) {
				$arrayParametros = array("NU_SEQ_CURSO" => $cursoInserido,
						"NU_SEQ_MODULO" => $modulosCurso[$i]['NU_SEQ_MODULO']);
				$obModeloVincCursoModulo->insert($arrayParametros);
			}
			$obModelo->getAdapter()->commit();
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Obtem Curso por Tipo de Curso
	 *
	 * @author diego.matos
	 * @since 27/04/2012
	 */
	public function getCursoPorTipo( $tipoCurso ) {

		$select = $this->getSelect(array('NU_SEQ_CURSO', 'DS_NOME_CURSO',));
		$select->where("NU_SEQ_TIPO_CURSO = $tipoCurso")->order('DS_NOME_CURSO');

		$stm = $select->query();
		$result = $stm->fetchAll();
		return $result;

	}

	/**
	 * Obtém o tipo pelo curso.
	 * @param int $idCurso
	 */
	public function getTipoPorCurso( $idCurso ) {

		$select = $this->getSelect(array('NU_SEQ_TIPO_CURSO'));
		$select->where("NU_SEQ_CURSO = $idCurso");

		$stm = $select->query();
		$result = $stm->fetch();
		return $result;

	}

	/**
	 * Função para excluir um curso do banco de dados.
	 * 
	 * @param int $idCurso
	 * @throws Exception
	 */
	public function removerCurso( $idCurso ) {
		$businessVincCursoModulo = new Fnde_Sice_Model_VincCursoModulo();
		$businessCurso = new Fnde_Sice_Model_Curso();

		try {
			$businessCurso->getAdapter()->beginTransaction();
			$where = "NU_SEQ_CURSO = " . $idCurso;
			$businessVincCursoModulo->delete($where);
			$businessCurso->delete($where);
			$businessCurso->getAdapter()->commit();
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Retorna os cursos de determinada UF. (Pela UF da Turma).
	 * Seleciona o nome e o codigo do curso.
	 * @param string $sgUf UF para pesquisa.
	 */
	public function getCursoPorUf( $sgUf ) {
		try {

			$cursoModel = new Fnde_Sice_Model_Curso();

			$sql = "SELECT DISTINCT C.NU_SEQ_CURSO, C.DS_NOME_CURSO
	    			FROM SICE_FNDE.S_CURSO C
					INNER JOIN SICE_FNDE.S_TURMA T ON T.NU_SEQ_CURSO = C.NU_SEQ_CURSO
					WHERE T.UF_TURMA = '{$sgUf}' 
					ORDER BY DS_NOME_CURSO ";

			$stm = $cursoModel->getAdapter()->query($sql);
			return $stm->fetchAll();
		} catch ( Exception $e ) {
			throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
		}
	}
}
