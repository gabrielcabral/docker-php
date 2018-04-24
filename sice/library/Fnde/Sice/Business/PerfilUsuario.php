<?php

/**
 * Business do HistoricoPerfil
 * 
 * @author poliane.silva
 * @since 27/03/2014
 */
class Fnde_Sice_Business_PerfilUsuario {

	const ARTICULADOR = 5;
	const TUTOR = 6;
	const CURSISTA = 7;

	/**
	 * Obtem histórico pelo id do usuário
	 *
	 * @author poliane.silva
	 * @since 27/03/2014
	 */
	public function getHistoricoByUsu( $idUsuario ) {

		try {
			$sql = "
                            select 
                            to_char(su.dt_inicio, 'DD/MM/YYYY') dt_inicio,
                            nvl(to_char(su.dt_fim, 'DD/MM/YYYY'),'-') dt_fim,
                            nvl(u.no_usuario,'-') no_responsavel,
                            case su.st_usuario
                            when 'A' then 'ATIVO'
                            when 'D' then 'INATIVO'
                            when 'L' then 'LIBERAÇÃO PENDENTE'
                            end situacao
                            from sice_fnde.h_situacao_usuario su
                            left join sice_fnde.s_usuario u on u.nu_seq_usuario = su.nu_seq_usuario_responsavel
                            where su.nu_seq_usuario = $idUsuario
                            order by su.dt_inicio";
						
			$obModelo = new Fnde_Sice_Model_PerfilUsuario();
			$stm = $obModelo->getAdapter()->query($sql);
			$result = $stm->fetchAll();
			
			return $result;
		} catch ( Exception $e ) {
			echo $e->getMessage();
		}
	}
        
	/**
	 * Obtem histórico pelo id do usuário
	 *
	 * @author poliane.silva
	 * @since 27/03/2014
	 */
	public function getHistoricoPerfilByUsu( $idUsuario ) {

		try {
			
			$sql = "SELECT PER.DS_TIPO_PERFIL,
					TO_CHAR(HPE.DT_INICIO, 'DD/MM/YYYY') DT_INICIO,
					TO_CHAR(HPE.DT_FIM, 'DD/MM/YYYY') DT_FIM,
					TO_CHAR(HPE.DT_FIM, 'DD/MM/YYYY HH24:MI') DT_ALTERACAO,
					RES.NO_USUARIO NO_RESPONSAVEL,
					HPE.NU_SEQ_HIST_PERFIL
					FROM SICE_FNDE.H_PERFIL_USUARIO HPE
					LEFT JOIN SICE_FNDE.S_TIPO_PERFIL PER ON HPE.NU_SEQ_TIPO_PERFIL = PER.NU_SEQ_TIPO_PERFIL
					LEFT JOIN SICE_FNDE.S_USUARIO RES ON HPE.NU_SEQ_USUARIO_RESPONSAVEL = RES.NU_SEQ_USUARIO
					WHERE HPE.NU_SEQ_USUARIO = $idUsuario
					ORDER BY HPE.DT_INICIO";
			
			$obModelo = new Fnde_Sice_Model_PerfilUsuario();
			$stm = $obModelo->getAdapter()->query($sql);
			$result = $stm->fetchAll();
			
			return $result;
		} catch ( Exception $e ) {
            echo $e->getMessage();
		}
	}

	/**
	 * Função para gravar um registro de histórico da turma no banco de dados.
	 * 
	 * @param int $codTurma
	 * @param int $situacao
	 * @param int $codMotivo
	 * @param string $observacao
	 * @throws Exception
	 */
	public function setHistoricoPerfil( $idPerfil, $idUsuario ) {

		$obModelo = new Fnde_Sice_Model_PerfilUsuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		try {

			$this->fimHistoricoAnterior($idUsuario);

			$arParamHistorico['NU_SEQ_USUARIO'] = $idUsuario;
			$arParamHistorico['NU_SEQ_TIPO_PERFIL'] = $idPerfil;
			$arParamHistorico['DT_INICIO'] = date('d/m/Y G:i:s');

			$obModelo->insert($arParamHistorico);
		} catch ( Exception $e ) {
            echo $e;
		}

	}

	public function fimHistoricoAnterior($idUsuario) {

		$obModelo = new Fnde_Sice_Model_PerfilUsuario();
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
				$obModelo->update($arParamHistorico, "NU_SEQ_HIST_PERFIL = " . $rsUsuario['NU_SEQ_HIST_PERFIL']);
			}

		} catch ( Exception $e ) {
            echo $e;
		}
	}
	
	public function excluirHistoricoPerfil($idUsuario){
		$obModelo = new Fnde_Sice_Model_PerfilUsuario();
		$where = "NU_SEQ_USUARIO = " . $idUsuario;
		$obModelo->delete($where);
	}

}
