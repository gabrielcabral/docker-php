<?php

/**
 * Business do LogSituacaoUsuario
 * 
 * @author poliane.silva
 * @since 01/04/2014
 */
class Fnde_Sice_Business_SituacaoUsuario {

	/**
	 * Função para gravar um registro de log da situação do usuário no banco de dados.
	 * 
	 * @param int $codTurma
	 * @param int $situacao
	 * @param int $codMotivo
	 * @param string $observacao
	 * @throws Exception
	 */
	public function setLogSituacao( $stuacao, $idUsuario ) {

		$obModelo = new Fnde_Sice_Model_SituacaoUsuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		try {

			$this->fimLogSituacaoAnterior($idUsuario);

			$arParamHistorico['NU_SEQ_USUARIO'] = $idUsuario;
			$arParamHistorico['ST_USUARIO'] = $stuacao;
			$arParamHistorico['DT_INICIO'] = date('d/m/Y G:i:s');
			
			$obModelo->insert($arParamHistorico);
		} catch ( Exception $e ) {
			throw $e;
		}

	}

	public function fimLogSituacaoAnterior($idUsuario) {

		$obModelo = new Fnde_Sice_Model_SituacaoUsuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		try {

			//Recupera ID do usuario logado no sistema.
			$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
			$businessUsuario = new Fnde_Sice_Business_Usuario();
			$cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
			$arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
			
			$select = $obModelo->select()->where("NU_SEQ_USUARIO = $idUsuario AND DT_FIM IS NULL");
			$stmt = $select->query();
			$rsUsuario = $stmt->fetch();
			
			$arParamHistorico['NU_SEQ_USUARIO_RESPONSAVEL'] = $arUsuario['NU_SEQ_USUARIO'];
			$arParamHistorico['DT_FIM'] = date('d/m/Y G:i:s');
			
			if ( $rsUsuario ) {
				$obModelo->update($arParamHistorico, "NU_SEQ_HIST_SITUACAO = " . $rsUsuario['NU_SEQ_HIST_SITUACAO']);
			}

		} catch ( Exception $e ) {
			throw $e;
		}
	}
	
	public function excluirLogSituacao($idUsuario){
		$obModelo = new Fnde_Sice_Model_SituacaoUsuario();
		$where = "NU_SEQ_USUARIO = " . $idUsuario;
		$obModelo->delete($where);
	}

}
