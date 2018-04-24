<?php

/**
 * Business do LiberarSenha
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_LiberarSenha {

	protected $_stModelo = 'LiberarSenha';
	protected $_stSistema = 'sice';

	/**
	 * Efetua a pesquisa de usuários com os filtros informados
	 * 
	 * @param array $arParams
	 */
	public function pesquisarUsuarios( $arParams, $arUsuario = null ) {

                $query = " SELECT DISTINCT";
		$query .= " USU.NU_SEQ_USUARIO, ";
		$query .= " USU.SG_UF_ATUACAO_PERFIL, ";
		$query .= " MR.NO_MUNICIPIO, ";
		$query .= " (SUBSTR(USU.NU_CPF,1,3)	|| '.' || ";
		$query .= " 	SUBSTR(USU.NU_CPF,4,3) || '.' || ";
		$query .= " 	SUBSTR(USU.NU_CPF,7,3) || '-' || ";
		$query .= " 	SUBSTR(USU.NU_CPF,10,2)) AS NU_CPF, ";
		$query .= " USU.NO_USUARIO, ";
		$query .= " PERF.DS_TIPO_PERFIL_SEGWEB, ";
		$query .= " (CASE ";
		$query .= " 	WHEN USU.ST_USUARIO = 'L' THEN 'LIBERAR' ";
		$query .= " 	ELSE 'RENOVAR' ";
		$query .= " END) AS TP_SOLICITACAO  ";
		$query .= " FROM SICE_FNDE.S_USUARIO USU ";
		
                /* SGD 27201
                 * adicionando tabelas para filtro de turma
                 */
                $query .= " LEFT JOIN sice_fnde.s_vinc_cursista_turma vct on vct.nu_seq_usuario_cursista = usu.nu_seq_usuario ";
                $query .= " LEFT JOIN sice_fnde.s_turma tur on tur.nu_seq_turma = vct.nu_seq_turma ";
                
		$query .= " INNER JOIN CORP_FNDE.S_UF UF ON USU.SG_UF_ATUACAO_PERFIL = UF.SG_UF ";
		$query .= " INNER JOIN CTE_FNDE.T_MESO_REGIAO MR ON USU.CO_MUNICIPIO_PERFIL = MR.CO_MUNICIPIO_IBGE ";
		$query .= " INNER JOIN SICE_FNDE.S_TIPO_PERFIL PERF ON USU.NU_SEQ_TIPO_PERFIL = PERF.NU_SEQ_TIPO_PERFIL ";
		$query .= " LEFT JOIN SEGWEB_FNDE.S_USUARIO SEG ON USU.NU_SEQ_USUARIO_SEGWEB = SEG.NU_SEQ_USUARIO ";
		$query .= " WHERE (USU.ST_USUARIO = 'L' OR SEG.DT_EXPIRACAO_SENHA <= SYSDATE) ";
		$query .= $this->getFiltro( $arParams, $arUsuario );
		$query .= " ORDER BY NO_USUARIO ";
                
//		die($query);
		
		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
                
		return $result;
	}

	/**
	 * Método que recupera o filtro do usuário com as restrições previstas na documentação.
	 * Diferencia a pesquisa quando for pesquisar cursista, pois a UF de Atuação do cursista
	 * deve ser a UF de Atuação do Tutor vinculado à ele.
	 * @param array $arParams Parametros da pesquisa.
	 * @param array $arUsuario Parametros do usuário logado no sistema.
	 */
	public function getFiltro( $arParams, $arUsuario ) {
            
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;
                
		$query = "";
                
		if ( $arParams['SG_UF_ATUACAO_PERFIL'] ) {
			$query .= " AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParams['SG_UF_ATUACAO_PERFIL'] . "' ";
		}
		
		if ( $arParams['CO_MESORREGIAO'] ) {
			$query .= " AND USU.CO_MESORREGIAO = " . $arParams['CO_MESORREGIAO'] . " ";
		}
		
		if ( $arParams['CO_MUNICIPIO_PERFIL'] ) {
			$query .= " AND MR.CO_MUNICIPIO_IBGE = " . $arParams['CO_MUNICIPIO_PERFIL'] . " ";
		}
		
		if ( $arParams['NU_CPF'] ) {
			$query .= " AND USU.NU_CPF = '" . $arParams['NU_CPF'] . "' ";
		}
		
		if ( $arParams['NU_SEQ_TIPO_PERFIL'] ) {
			$query .= " AND USU.NU_SEQ_TIPO_PERFIL = " . $arParams['NU_SEQ_TIPO_PERFIL'] . " ";
		} 
		
		if ( $arParams['ST_USUARIO'] ) {
			if($arParams['ST_USUARIO'] == 'R'){
				$query .= " AND (USU.ST_USUARIO = 'A' AND SEG.DT_EXPIRACAO_SENHA  <= SYSDATE) ";
			}else{
				$query .= " AND USU.ST_USUARIO = '" . $arParams['ST_USUARIO'] . "' " ;
			}
		} 
        
        if ( $arParams['NU_SEQ_TURMA'] ) {
			$query .= " AND TUR.NU_SEQ_TURMA = " . $arParams['NU_SEQ_TURMA'] . " ";
		}
                
        if ( $arUsuario != null ) {
			$this->getAtuacaoPerfil($query, $perfisUsuarioLogado, $arUsuario);
		}
        
        $query .= $this->getTipoPerfil($query, $perfisUsuarioLogado);

		return $query;
	}

	private function getTipoPerfil( &$query, $perfisUsuarioLogado ) {
		/*
		 *  1	Coordenador Nacional Administrador
		 *  2	Coordenador Nacional Equipe
		 *  3	Coordenador Nacional Gestor
		 *  4	Coordenador Estadual
		 *  5	Articulador
		 *  6	Tutor
		 *  7	Cursista
		 *  8   Coordenador Executivo Estadual
		 */
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 1";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 2";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 3";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 4";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 8";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 5";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";
		} elseif ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 3";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 4";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 8";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 5";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";
		} elseif ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado)
					||in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 5";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";
		} elseif ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 6";
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";
		} elseif ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {
			$query .= " AND USU.NU_SEQ_TIPO_PERFIL = 7";
		}
	}

	private function getAtuacaoPerfil( &$query, $perfisUsuarioLogado, $arUsuario ) {
		/*
		 *  1	Coordenador Nacional Administrador
		 *  2	Coordenador Nacional Equipe
		 *  3	Coordenador Nacional Gestor
		 *  4	Coordenador Estadual
		 *  5	Articulador
		 *  6	Tutor
		 *  7	Cursista
		 *  8   Coordenador Executivo Estadual
		 */
		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {

			$query .= " AND USU.SG_UF_ATUACAO_PERFIL = '" . $arUsuario['SG_UF_ATUACAO_PERFIL'] . "' ";

		}

		if ( in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
				|| in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) ) {

			$query .= " AND USU.CO_MESORREGIAO = " . $arUsuario['CO_MESORREGIAO'] . " ";
		}
	}
}
