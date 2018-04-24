<?php

/**
 * Business do Matricular Cursista
 * 
 * @author diego.matos
 * @since 25/04/2012
 */
class Fnde_Sice_Business_MatricularCursista {

	/**
	 * Salva o usuario cursista sem matricula.
	 * @param array $arParamsUsuario
	 * @param array $arParamsDadosEscolares
	 * @param array $cursistaExistente
	 */
	public function preSalvarCursista( $arParamsUsuario, $arParamsDadosEscolares, $cursistaExistente , $arParamsFormacaoAcademica = null) {
		$obModeloUsuario = new Fnde_Sice_Model_Usuario();
		$obModeloDadosEscolares = new Fnde_Sice_Model_DadosEscolaresCursista();
        $obHistorico = new Fnde_Sice_Business_PerfilUsuario();
        $obFormacaoAcademica = new Fnde_Sice_Model_VincFormAcadUsu();

		$obModeloUsuario->getAdapter()->beginTransaction();
		// Verifica se o cursista já existe, caso exista atualiza os dados do mesmo.
		// Caso não exista, cadastra um novo cursista.
		if ( isset($cursistaExistente) && $cursistaExistente['NU_SEQ_TIPO_PERFIL'] == 7 ) {
			$obModeloUsuario->update($arParamsUsuario, "NU_SEQ_USUARIO = {$cursistaExistente['NU_SEQ_USUARIO']}");
			$usuarioInserido = $cursistaExistente['NU_SEQ_USUARIO'];
			$obModeloDadosEscolares->update($arParamsDadosEscolares, "NU_SEQ_USUARIO_CURSISTA = $usuarioInserido");
			if(isset($arParamsFormacaoAcademica))
				$obFormacaoAcademica->update($arParamsFormacaoAcademica, "NU_SEQ_USUARIO = $usuarioInserido");
		} else {
			$usuarioInserido = $obModeloUsuario->insert($arParamsUsuario);
			//Insere os dados escolares
			$arParamsDadosEscolares['NU_SEQ_USUARIO_CURSISTA'] = $usuarioInserido;
			$obModeloDadosEscolares->insert($arParamsDadosEscolares);
			//Insere os dados de formação
			if(isset($arParamsFormacaoAcademica)){
				$arParamsFormacaoAcademica['NU_SEQ_USUARIO'] = $usuarioInserido;
				$obFormacaoAcademica->insert($arParamsFormacaoAcademica);
			}
		}

        if (is_null($cursistaExistente) || $cursistaExistente['NU_SEQ_TIPO_PERFIL'] != $arParamsUsuario['NU_SEQ_TIPO_PERFIL']) {
            $obHistorico->setHistoricoPerfil($arParamsUsuario['NU_SEQ_TIPO_PERFIL'], $usuarioInserido);
        }

        $obModeloUsuario->getAdapter()->commit();

		return $usuarioInserido;
	}

}
