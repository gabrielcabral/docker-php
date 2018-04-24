<?php

/**
 * Business do Avaliação Pedagógica
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Fnde_Sice_Business_AvaliacaoPedagogica {
	
	/**
	 * Efetua a pesquisa das turmas cadastradas de acordo com o filtro informado
	 * @param array $arParams
	 * @param boolean $indicadorAcoes
	 * @param array $arUsuario
	 */
	public function pesquisaAvaliacaoPedagogica( $arParams, $arUsuario = null, $perfilUsuario = null ) {
		$query = " SELECT "
				." DISTINCT TUR.NU_SEQ_TURMA AS NU_SEQ_TURMA, "
				." TUR.UF_TURMA, "
				." MES.NO_MUNICIPIO, "
				." CUR.DS_NOME_CURSO, "
				." TUT.NO_USUARIO AS NO_TUTOR, "
				." ART.NO_USUARIO AS NO_ARTICULADOR, "
				." TO_CHAR(DT_INICIO,'DD/MM/YYYY') AS DT_INICIO, "
				." TO_CHAR(DT_FIM, 'DD/MM/YYYY') AS DT_FIM, "
				." TO_CHAR(DT_FINALIZACAO, 'DD/MM/YYYY') AS DT_FINALIZACAO, "
				." SIT.DS_ST_TURMA "
				." FROM SICE_FNDE.S_TURMA TUR "
				." INNER JOIN CTE_FNDE.T_MESO_REGIAO MES ON TUR.CO_MUNICIPIO = MES.CO_MUNICIPIO_IBGE "
				." INNER JOIN SICE_FNDE.S_CURSO CUR ON TUR.NU_SEQ_CURSO = CUR.NU_SEQ_CURSO "
				." INNER JOIN SICE_FNDE.S_USUARIO TUT ON TUR.NU_SEQ_USUARIO_TUTOR = TUT.NU_SEQ_USUARIO "
				." INNER JOIN SICE_FNDE.S_USUARIO ART ON TUR.NU_SEQ_USUARIO_ARTICULADOR = ART.NU_SEQ_USUARIO "
				." LEFT JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON TUR.NU_SEQ_TURMA = VCT.NU_SEQ_TURMA "
				." INNER JOIN SICE_FNDE.S_VINC_CURSO_MODULO VCM ON CUR.NU_SEQ_CURSO = VCM.NU_SEQ_CURSO "
				." INNER JOIN SICE_FNDE.S_SITUACAO_TURMA SIT ON TUR.ST_TURMA = SIT.NU_SEQ_ST_TURMA "
				 
				." WHERE 1=1 ";

		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'CUR', 'NU_SEQ_TIPO_CURSO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'UF_TURMA');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'CO_MESORREGIAO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'CO_MUNICIPIO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_USUARIO_TUTOR');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_USUARIO_ARTICULADOR');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_TURMA');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'VCM', 'NU_SEQ_MODULO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'NU_SEQ_CURSO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'DT_INICIO');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'DT_FIM');
		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'ST_TURMA');

		if ( $arParams['ST_TURMA'] ) {
			$query .= " AND TUR.ST_TURMA = :ST_TURMA ";
			$arFiltro['ST_TURMA'] = $arParams['ST_TURMA'];
		} else {
			//$query .= " AND TUR.ST_TURMA IN (4,12) ";
		}

		$this->setFiltroPesquisaAvalPedagogica($arParams, $query, $arFiltro, 'TUR', 'DT_FINALIZACAO');

		if ( $arUsuario != null ) {
			/*
			 * sice_coordenador_nacional_administrador 
			 * sice_coordenador_nacional_equipe 
			 * sice_coordenador_nacional_gestor 
			 * sice_coordenador_estadual 
			 * sice_articulador 
			 * sice_tutor 
			 * sice_cursista 
			 * 
			 */ 

			//Articulador ou Tutor somente turmas na qual está vinculado
			if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ) {
				$query .= " AND TUR.NU_SEQ_USUARIO_ARTICULADOR = " . $arUsuario['NU_SEQ_USUARIO'];
			} elseif ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ) {
				$query .= " AND TUR.NU_SEQ_USUARIO_TUTOR = " . $arUsuario['NU_SEQ_USUARIO'];
			}

			//Coordenador Estadual apenas as turmas da sua  UF de atuação
			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario) ) {
				$query .= " AND TUR.UF_TURMA = '" . $arUsuario['SG_UF_ATUACAO_PERFIL'] . "'";
			}

		}

		$query .= " ORDER BY TUR.NU_SEQ_TURMA ";

		$obModelo = new Fnde_Sice_Model_Turma();

		$stm = $obModelo->getAdapter()->query($query, $arFiltro);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Configura o filtro da pesquisa de acordo com os parametros do usuario.
	 * @param $arParams
	 * @param $query
	 * @param $arFiltro
	 * @param $prefixo
	 * @param $descricao
	 */
	private function setFiltroPesquisaAvalPedagogica( $arParams, &$query, &$arFiltro, $prefixo, $descricao ) {
		if ( $arParams[$descricao] ) {
			$query .= " AND " . $prefixo . "." . $descricao . " = :" . $descricao;
			$arFiltro[$descricao] = $arParams[$descricao];
		}
	}

}
