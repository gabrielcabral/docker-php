<?php

/**
 * Business do FormacaoAcademica
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_FormacaoAcademica {

	protected $_stModelo = 'FormacaoAcademica';
	protected $_stSistema = 'sice';

	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NO_INSTITUICAO' => 'C.NO_INSTITUICAO', 'DT_CONCLUSAO' => 'C.DT_CONCLUSAO',
				'TP_ESCOLARIDADE' => 'C.TP_ESCOLARIDADE', 'NO_CURSO' => 'C.NO_CURSO',
				'NU_SEQ_USUARIO' => 'C.NU_SEQ_USUARIO', 'TP_INSTITUICAO' => 'C.TP_INSTITUICAO',
				'NU_SEQ_FORMACAO_ACADEMICA' => 'C.NU_SEQ_FORMACAO_ACADEMICA',);
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_FORMACAO_ACADEMICA']) ) {
			$select->where("C.NU_SEQ_FORMACAO_ACADEMICA = {$arParams['id']} ");
		} else {
			if ( $arParams['NO_INSTITUICAO'] ) {
				$select->where("C.NO_INSTITUICAO = ?", $arParams['NO_INSTITUICAO']);
			}
			if ( $arParams['DT_CONCLUSAO'] ) {
				$select->where("C.DT_CONCLUSAO = ?", $arParams['DT_CONCLUSAO']);
			}
			if ( $arParams['TP_ESCOLARIDADE'] ) {
				$select->where("C.TP_ESCOLARIDADE = ?", $arParams['TP_ESCOLARIDADE']);
			}
			if ( $arParams['NO_CURSO'] ) {
				$select->where("C.NO_CURSO = ?", $arParams['NO_CURSO']);
			}
			if ( $arParams['NU_SEQ_USUARIO'] ) {
				$select->where("C.NU_SEQ_USUARIO = ?", $arParams['NU_SEQ_USUARIO']);
			}
			if ( $arParams['TP_INSTITUICAO'] ) {
				$select->where("C.TP_INSTITUICAO = ?", $arParams['TP_INSTITUICAO']);
			}
			if ( $arParams['NU_SEQ_FORMACAO_ACADEMICA'] ) {
				$select->where("C.NU_SEQ_FORMACAO_ACADEMICA = ?", $arParams['NU_SEQ_FORMACAO_ACADEMICA']);
			}
		}
	}

	/**
	 * Recupera select para listagem
	 *
	 * @access public
	 * @return object - Objeto Select
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getSelect( $arColumns ) {

		$obModelo = new Fnde_Sice_Model_FormacaoAcademica();
		$arInfoModelo = $obModelo->info();

		$select = $obModelo->select()->setIntegrityCheck(false)->from(
				array('C' => $arInfoModelo['schema'] . "." . $arInfoModelo['name']), '')->columns($arColumns);
		return $select;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function del( $id ) {
		$obModelo = new Fnde_Sice_Model_FormacaoAcademica();
		$where = "NU_SEQ_FORMACAO_ACADEMICA = " . $id;
		$obModelo->delete($where);

		//$logger->log("FormacaoAcademica removido com sucesso !", Zend_Log::INFO);

		$this->stMensagem = "FormacaoAcademica removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Deleta publicação vinculada ao Edital
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function removerPorUsuario( $id ) {
		$obModelo = new Fnde_Sice_Model_FormacaoAcademica();
		$where = "NU_SEQ_USUARIO = " . $id;
		$obModelo->delete($where);

		$this->stMensagem = "FormacaoAcademica removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Seleciona FormacaoAcademica
	 *
	 * @author diego.matos
	 * @since 10/04/2012
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
	 * Obtem FormacaoAcademica por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getFormacaoAcademicaById( $id ) {

		$obModelo = new Fnde_Sice_Model_FormacaoAcademica();

		$select = $obModelo->select()->where("NU_SEQ_FORMACAO_ACADEMICA = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetch();

		return $result;
	}

	/**
	 * Obtem FormacaoAcademica por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getFormacaoAcademicaByUsuario( $id ) {

		$obModelo = new Fnde_Sice_Model_FormacaoAcademica();

		$select = $obModelo->select()->where("NU_SEQ_USUARIO = ?", $id);

		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NU_SEQ_FORMACAO_ACADEMICA');
	}

}
