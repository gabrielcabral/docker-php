<?php

/**
 * Business do Usuario
 * 
 * @author diego.matos
 * @since 10/04/2012
 */
class Fnde_Sice_Business_Usuario {
	
	const MSG_ERRO_EXECUTIVO = "Não pode existir mais de um Coordenador Executivo Estadual em uma mesma UF.  Para cadastrar um novo, é necessário alterar o perfil do Coordenador Executivo Estadual atual.";
	
	/**
	 * Retorna array das colunas do termo de referencia para grid
	 *
	 * @access protected
	 * @return object - Objeto de Business
	 *
	 */
	public function getColumnsSearch() {
		return array('NO_USUARIO' => 'C.NO_USUARIO', 'CO_OCUPACAO_USUARIO' => 'C.CO_OCUPACAO_USUARIO',
				'CO_SEXO_USUARIO' => 'C.CO_SEXO_USUARIO', 'CO_DISTANCIA_PAGAMENTO' => 'C.CO_DISTANCIA_PAGAMENTO',
				'SG_UF_PAGAMENTO' => 'C.SG_UF_PAGAMENTO', 'SG_UF_EMISSAO_DOC' => 'C.SG_UF_EMISSAO_DOC',
				'SG_UF_NASCIMENTO' => 'C.SG_UF_NASCIMENTO', 'NU_SEQ_TIPO_PERFIL' => 'C.NU_SEQ_TIPO_PERFIL',
				'CO_LOCAL_LOTACAO' => 'C.CO_LOCAL_LOTACAO', 'CO_MUNICIPIO_PERFIL' => 'C.CO_MUNICIPIO_PERFIL',
				'CO_MUNICIPIO_PAGAMENTO' => 'C.CO_MUNICIPIO_PAGAMENTO', 'DS_CELULAR_USUARIO' => 'C.DS_CELULAR_USUARIO',
				'DS_COMPLEMENTO_ENDERECO' => 'C.DS_COMPLEMENTO_ENDERECO', 'DT_NASCIMENTO' => 'C.DT_NASCIMENTO',
				'CO_ESTADO_CIVIL' => 'C.CO_ESTADO_CIVIL', 'DS_ENDERECO' => 'C.DS_ENDERECO',
				'NU_IDENTIDADE' => 'C.NU_IDENTIDADE', 'NO_MAE' => 'C.NO_MAE',
				'DS_BAIRRO_ENDERECO' => 'C.DS_BAIRRO_ENDERECO', 'DS_EMAIL_USUARIO' => 'C.DS_EMAIL_USUARIO',
				'SG_UF_ATUACAO_PERFIL' => 'C.SG_UF_ATUACAO_PERFIL', 'DS_TELEFONE_USUARIO' => 'C.DS_TELEFONE_USUARIO',
				'CO_MESORREGIAO' => 'C.CO_MESORREGIAO', 'CO_ORGAO_EMISSOR' => 'C.CO_ORGAO_EMISSOR',
				'CO_MUNICIPIO_ENDERECO' => 'C.CO_MUNICIPIO_ENDERECO',
				'DT_EMISSAO_DOCUMENTACAO' => 'C.DT_EMISSAO_DOCUMENTACAO', 'TP_ENDERECO' => 'C.TP_ENDERECO',
				'NU_CPF' => 'C.NU_CPF', 'DS_OCUPACAO_ALTERNATIVA' => 'C.DS_OCUPACAO_ALTERNATIVA',
				'CO_SERVIDOR_PUBLICO' => 'C.CO_SERVIDOR_PUBLICO', 'NU_CEP' => 'C.NU_CEP',
				'CO_AGENCIA_PAGAMENTO' => 'C.CO_AGENCIA_PAGAMENTO', 'DS_CARGO_FUNCAO' => 'C.DS_CARGO_FUNCAO',
				'NU_SEQ_USUARIO' => 'C.NU_SEQ_USUARIO', 'CO_MUNICIPIO_NASCIMENTO' => 'C.CO_MUNICIPIO_NASCIMENTO',
				'DT_CADASTRO' => 'C.DT_CADASTRO', 'DT_ALTERACAO' => 'C.DT_ALTERACAO', 'ST_USUARIO' => 'C.ST_USUARIO',
				'DS_LOCAL_LOTACAO_ALTERNATIVA' => 'C.DS_LOCAL_LOTACAO_ALTERNATIVA',
				'NU_SEQ_USUARIO_SEGWEB' => 'C.NU_SEQ_USUARIO_SEGWEB');
	}

	/**
	 * monta filtro para consultar TOR
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setFilter( $select, $arParams ) {
		if ( isset($arParams['NU_SEQ_USUARIO']) && isset($arParams['id']) ) {
			$select->where("C.NU_SEQ_USUARIO = {$arParams['id']} ");
		} else {
			$this->setMonFiltroUsuario($select, $arParams, 'NO_USUARIO');
			$this->setMonFiltroUsuario($select, $arParams, 'NU_SEQ_TIPO_PERFIL');
			$this->setMonFiltroUsuario($select, $arParams, 'SG_UF_ATUACAO_PERFIL', true);
			$this->setMonFiltroUsuario($select, $arParams, 'CO_MUNICIPIO_PERFIL');
			$this->setMonFiltroUsuario($select, $arParams, 'NU_SEQ_USUARIO');
			$this->setMonFiltroUsuario($select, $arParams, 'CO_MESORREGIAO');
			$this->setMonFiltroUsuario($select, $arParams, 'NU_CPF');
			$this->setMonFiltroUsuario($select, $arParams, 'ST_USUARIO', true);
		}
	}

	/**
	 * Método que auxilia a montagem dos filtros
	 * @param  $select
	 * @param array $arParams
	 * @param string $descricao
	 * @param boolean $string
	 */
	private function setMonFiltroUsuario( $select, $arParams, $descricao, $string = false ) {
		if ( $arParams[$descricao] ) {

			if ( $string ) {
				$sql = "C." . $descricao . " = '{$arParams[$descricao]}' ";
			} else {
				$sql = "C." . $descricao . " = {$arParams[$descricao]} ";
			}

			$select->where($sql);
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

		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
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
		$obModelo = new Fnde_Sice_Model_Usuario();

		$where = "NU_SEQ_USUARIO = " . $id;
		$obModelo->delete($where);

		$this->stMensagem = "Usuario removido com sucesso !";
		return $this->stMensagem;
	}

	/**
	 * Seleciona Usuario
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
	 * Obtem Usuario por Id
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getUsuarioById( $id, $boArray = true ) {

		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");

		$aDados = $obModelo->find($id)->current();
		if ( $aDados ) {
			return $boArray ? $aDados->toArray() : $aDados;
		}
		return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
	}

	/**
	 * Obtem Usuario por Id da bolsa
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function getUsuarioByIdBolsa( $id ) {

		$query = "SELECT USU.NU_SEQ_USUARIO, " . "  USU.NU_SEQ_TIPO_PERFIL, " . "  USU.NO_USUARIO "
				. "FROM SICE_FNDE.S_USUARIO USU " . "LEFT JOIN SICE_FNDE.S_BOLSA BOL "
				. "ON USU.NU_SEQ_USUARIO  = BOL.NU_SEQ_USUARIO " . "WHERE BOL.NU_SEQ_BOLSA = $id ";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}

	/**
	 * Define Ordem para pesquisa
	 *
	 * @author diego.matos
	 * @since 10/04/2012
	 */
	public function setOrder( $select ) {
		$select->order('C.NO_USUARIO');
	}

	/**
	 * Efetua a pesquisa de usuários com os filtros informados
	 * 
	 * @param array $arParams
	 */
	public function pesquisarUsuarios( $arParams, $arUsuario = null ) {

		$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
		$perfisUsuarioLogado = $usuarioLogado->credentials;

		$query = " SELECT " . " USU.NU_SEQ_USUARIO, " . " USU.SG_UF_ATUACAO_PERFIL, " . " MR.NO_MUNICIPIO, "
				. " (SUBSTR(USU.NU_CPF,1,3)||'.'||
                                       SUBSTR(USU.NU_CPF,4,3)||'.'||
                                       SUBSTR(USU.NU_CPF,7,3)||'-'||
                                       SUBSTR(USU.NU_CPF,10,2)), " //<-Formatando CPF
 . " USU.NO_USUARIO, " . " (TO_CHAR(USU.DT_CADASTRO, 'DD/MM/YYYY')), " //Formatando Data
 . " (TO_CHAR(USU.DT_ALTERACAO, 'DD/MM/YYYY')), " //Formatando Data
				. " (CASE WHEN USU.ST_USUARIO = 'A' THEN 'Ativo' WHEN USU.ST_USUARIO = 'D' THEN 'Inativo' WHEN USU.ST_USUARIO = 'L' THEN 'Liberação Pendente' END) AS ST_USUARIO " //Formatando Situação do Usuário
 . "  FROM " . " SICE_FNDE.S_USUARIO USU INNER JOIN CORP_FNDE.S_UF UF ON USU.SG_UF_NASCIMENTO = UF.SG_UF "
				. " 			  INNER JOIN CTE_FNDE.T_MESO_REGIAO MR ON USU.CO_MUNICIPIO_PERFIL = MR.CO_MUNICIPIO_IBGE "
				. " WHERE " . " 1=1 ";
		if ( $arParams['SG_UF_ATUACAO_PERFIL'] ) {
			$query .= " AND USU.SG_UF_ATUACAO_PERFIL = '{$arParams['SG_UF_ATUACAO_PERFIL']}' ";
		}
		if ( $arParams['CO_MESORREGIAO'] ) {
			$query .= " AND USU.CO_MESORREGIAO = {$arParams['CO_MESORREGIAO']} ";
		}
		if ( $arParams['CO_MUNICIPIO_PERFIL'] ) {
			$query .= " AND MR.CO_MUNICIPIO_IBGE = {$arParams['CO_MUNICIPIO_PERFIL']} ";
		}
		if ( $arParams['NO_USUARIO'] ) {
			$arParams['NO_USUARIO'] = "%{$arParams['NO_USUARIO']}%";
			$query .= " AND USU.NO_USUARIO LIKE '{$arParams['NO_USUARIO']}' ";
		}
		if ( $arParams['NU_CPF'] ) {
			$arParams['NU_CPF'] = str_replace(".", "", $arParams['NU_CPF']);
			$arParams['NU_CPF'] = str_replace("-", "", $arParams['NU_CPF']);
			$query .= " AND USU.NU_CPF = '{$arParams['NU_CPF']}' ";
		}
		if ( $arParams['NU_SEQ_TIPO_PERFIL'] ) {
			$query .= " AND USU.NU_SEQ_TIPO_PERFIL = {$arParams['NU_SEQ_TIPO_PERFIL']} ";

			if ( in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
				$cpf = $arUsuario['NU_CPF'];
				$query .= " AND USU.NU_CPF = '$cpf'";
			}
		} else {
			$query .= $this->montaConsultaPesquisaUsuarioLogado($perfisUsuarioLogado, $arUsuario);
		}
		if ( $arParams['ST_USUARIO'] ) {
			$query .= " AND USU.ST_USUARIO = '{$arParams['ST_USUARIO']}'";
		}

		$query .= " ORDER BY USU.NO_USUARIO ";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Monta a consulta pesquisarUsuarios() de acondo com o usuário logado
	 * @param array $perfisUsuarioLogado
	 * @param array $arUsuario
	 */
	private function montaConsultaPesquisaUsuarioLogado( $perfisUsuarioLogado, $arUsuario ) {

		if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 1";//Coordenador Nacional Administrador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 2";//Coordenador Nacional Equipe
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 3";//Coordenador Nacional Gestor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 4";//Coordenador Estadual
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 5";//Articulador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";//Tutor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7";//Cursista
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 8)";
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 3";//Coordenador Nacional Gestor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 4";//Coordenador Estadual
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 5";//Articulador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";//Tutor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7";//Cursista
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 8)";
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 2";//Coordenador Nacional Equipe
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 4";//Coordenador Estadual
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 5";//Articulador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";//Tutor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7";//Cursista
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 8)";
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 5";//Articulador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";//Tutor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";//Cursista
		} else if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ) {
			$query .= " AND (USU.NU_SEQ_TIPO_PERFIL = 5";//Articulador
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 6";//Tutor
			$query .= " OR USU.NU_SEQ_TIPO_PERFIL = 7)";//Cursista
                /**
                * SGD 25614 - Articulador não cadastra/edita/exclui tutores
                 * 
                 */
                     
		} else if ( in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado) || in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado) ) {
			$query .= " AND USU.NU_SEQ_TIPO_PERFIL = 7";//Cursista
		} else if ( in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfisUsuarioLogado) ) {
			$query .= " AND USU.NU_SEQ_TIPO_PERFIL = 7";//Cursista
			if ( $arUsuario['NU_CPF'] ) {
				$cpf = $arUsuario['NU_CPF'];
				$query .= " AND USU.NU_CPF = '$cpf'";
			}
		}

		return $query;

	}

	/**
	 * Verifica se o e-mail informado já está cadastrado no banco.
	 * @param unknown_type $email
	 * @return mixed|NULL
	 */
	public function verificaEmailCadastrado( $nuSeqUsuario, $email ) {
		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$email = strtoupper(trim($email));

        if($nuSeqUsuario == null) {
            $select = $obModelo->select()->where("UPPER(TRIM(DS_EMAIL_USUARIO)) = ?", $email);
        } else {
            $select = $obModelo->select()
                ->where("UPPER(TRIM(DS_EMAIL_USUARIO)) = ?", $email)
                ->where("NU_SEQ_USUARIO <> ?", $nuSeqUsuario);
        }

		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * Grava o usuário no banco
	 * @author Marcony.abreu
	 * @param array $arParamsUsuario
	 * @param array $arSessionFormAcad
	 * @param array $arAtividades
	 */
	public function salvar( $arParamsUsuario, $arSessionFormAcad, $arAtividades ) {
		$obModelo = new Fnde_Sice_Model_Usuario();
		try{

			$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
			$obModelo->getAdapter()->beginTransaction();
			$usuarioInserido = null;

			//verifica se o usuário que veio já é cadastrado no sistema
			if ( $arParamsUsuario['NU_SEQ_USUARIO'] ) {
				$this->atualizarDadosSegWeb($arParamsUsuario);

				$where = "NU_SEQ_USUARIO = " . $arParamsUsuario['NU_SEQ_USUARIO'];
				$obModelo->update($arParamsUsuario, $where);
				$usuarioInserido = $arParamsUsuario['NU_SEQ_USUARIO'];
			}
			else {
				$usuarioInserido = $obModelo->insert($arParamsUsuario);
			}

			$obModeloVincFormAcadUsu = new Fnde_Sice_Model_VincFormAcadUsu();
			for ( $i = 0; $i < count($arSessionFormAcad); $i++ ) {
				$arSessionFormAcad[$i]['NU_SEQ_USUARIO'] = $usuarioInserido;

				$arSessionFormAcad[$i]['NU_SEQ_FORMACAO_ACADEMICA'] = $arSessionFormAcad[$i]['TP_ESCOLARIDADE'];
				unset($arSessionFormAcad[$i]['TP_ESCOLARIDADE']);

				$obModeloVincFormAcadUsu->insert($arSessionFormAcad[$i]);
			}

			//vincula as atividades das pessoas cadastradas no sistema
			$obModeloAtividades = new Fnde_Sice_Model_VinculaAtivUsuario();
			for ( $i = 0; $i < count($arAtividades); $i++ ) {
				$arAtividades[$i]['NU_SEQ_USUARIO'] = $usuarioInserido;
				$obModeloAtividades->insert($arAtividades[$i]);
			}

			$obModelo->getAdapter()->commit();
			return $usuarioInserido;
		}catch(Exception $e) {
			$obModelo->getAdapter()->rollback();
			throw $e;
		}
	}

	/**
	 * Grava o usuário no banco em caso de edição
	 * @author Marcony.abreu
	 * @param array $arParamsUsuario
	 * @param array $arSessionFormAcad
	 * @param array $arAtividades
	 */
	public function salvarEdicao( $arParamsUsuario ) {
		try{
			$where1 = "NU_SEQ_USUARIO = " . $_POST['NU_SEQ_USUARIO'];
			$where2 = "NU_SEQ_USUARIO_CURSISTA = " . $_POST['NU_SEQ_USUARIO'];

			$arrVincFormAcad = array();
			$arrVincFormAcad['NU_SEQ_FORMACAO_ACADEMICA'] = $_POST['NU_SEQ_FORMACAO_ACADEMICA'];
			$arrVincFormAcad['TP_INSTITUICAO'] = $_POST['TP_INSTITUICAO'];
			$objVincFormAcad = new Fnde_Sice_Model_VincFormAcadUsu();
			$objVincFormAcad->getAdapter()->beginTransaction();
			$objVincFormAcad->update($arrVincFormAcad,$where1);

			$arrUsuario = array();
			$arrUsuario['DS_TELEFONE_USUARIO'] = preg_replace("/[^0-9]/", "", $_POST['DS_TELEFONE_USUARIO']);
			$arrUsuario['DS_CELULAR_USUARIO'] = preg_replace("/[^0-9]/", "", $_POST['DS_CELULAR_USUARIO']);
			$arrUsuario['DS_EMAIL_USUARIO'] = $_POST['DS_EMAIL_USUARIO'];
			$objUsuario = new Fnde_Sice_Model_Usuario();
			$objUsuario->getAdapter()->beginTransaction();
			$objUsuario->update($arrUsuario,$where1);


            if($_POST['CO_MUNICIPIO_ESCOLA']){
                $arrDadosEscolares = array();
                $arrDadosEscolares['CO_MUNICIPIO_ESCOLA'] = $_POST['CO_MUNICIPIO_ESCOLA'];
                $arrDadosEscolares['CO_UF_ESCOLA'] = $_POST['SG_UF_ESCOLA'];
                $arrDadosEscolares['CO_REDE_ENSINO'] = $_POST['CO_REDE_ENSINO'];
                $arrDadosEscolares['CO_ESCOLA'] = $_POST['CO_ESCOLA'];
                $arrDadosEscolares['CO_SEGMENTO'] = $_POST['CO_SEGMENTO'];
                $objDadosEscolares = new Fnde_Sice_Model_DadosEscolaresCursista();
                $objDadosEscolares->getAdapter()->beginTransaction();
                $objDadosEscolares->update($arrDadosEscolares,$where2);

                $objDadosEscolares->getAdapter()->commit();
            }


			$objVincFormAcad->getAdapter()->commit();
			$objUsuario->getAdapter()->commit();

			return $_POST['NU_SEQ_USUARIO'];
		}catch(Exception $e) {
			$objVincFormAcad->getAdapter()->rollback();
			$objUsuario->getAdapter()->rollback();
			$objDadosEscolares->getAdapter()->rollback();
			throw $e;
		}
	}
        
        /**
         * Método que monta array de dados do usuário para encaminhar ao segweb
         */
        public function atualizarDadosSegWeb($arParamsUsuario) {
            
            
            $arUsuario = $this->getUsuarioById($arParamsUsuario['NU_SEQ_USUARIO']);
            
            //Verifica se o usuário casatrado possui cadastro no segweb -> caso não tenha continua apenas para o update no SICE
            if($arUsuario['NU_SEQ_USUARIO_SEGWEB']){
                $segweb = new Fnde_Model_Segweb();

                // precisa alterar perfil? Então gera verificações de perfil
                if(isset($arParamsUsuario['NU_SEQ_TIPO_PERFIL'])) {

                    $tipoPerfil = new Fnde_Sice_Business_TipoPerfil();
                    $arPerfil = $tipoPerfil->getTipoPerfilById($arParamsUsuario['NU_SEQ_TIPO_PERFIL']);
                    $perfil = $this->getPerfilUsuarioSegweb($arParamsUsuario['NU_SEQ_USUARIO']);
                    //caso possua usuário no segweb ele irá fazer a alterção.

                    //Caso o perfil atual seja diferente do novo realiza o update DO PERFIL no SEGWEB

                    if ($perfil['DS_TIPO_PERFIL_SEGWEB'] != $arPerfil['DS_TIPO_PERFIL_SEGWEB']) {
                        $loginSeg = $this->getUsuarioSegweb($arUsuario['NU_SEQ_USUARIO_SEGWEB']);

                        //Variavel $perfil provê da consultapelo webservice do SEGWEB
                        $desvincula = $segweb->setDesvincularGrupos($loginSeg['DS_LOGIN'], $perfil['DS_TIPO_PERFIL_SEGWEB']);

                        if ($desvincula['result'] == 1) {
                            //variavel $arPefil pega as informações do tipo_perfil
                            $vincula = $segweb->setVincularGrupos($loginSeg['DS_LOGIN'], $arPerfil['DS_TIPO_PERFIL_SEGWEB']);

                            if ($vincula['result'] != 1) {
                                throw new Exception($vincula['message']['text']);
                            }
                        } else {
                            throw new Exception($desvincula['message']['text']);
                        }
                    }
                }
                
                /*
                 * Atualiza as informações referentes aos dados do usuário do SICE pro SEGWEB
                 */
               
                $usuarioSegWeb = $this->getUsuarioSegweb($arUsuario['NU_SEQ_USUARIO_SEGWEB']);
                $dataExpiracao = new Zend_Date($usuarioSegWeb['DT_EXPIRACAO_SENHA']);
                $dataValidade = new Zend_Date($usuarioSegWeb['DT_VALIDADE_SENHA']);
                
                $updateSegWeb = $segweb->setUserUpdate(
                        $arUsuario['NU_SEQ_USUARIO_SEGWEB'], 
                        $usuarioSegWeb['ST_ATIVO'], 
                        $dataExpiracao->toString('Y-MM-dd'), 
                        $dataValidade->toString('Y-MM-dd'), 
                        'N',
                        array(
                            'email' => $arParamsUsuario['DS_EMAIL_USUARIO'],
                            'dddtelefone' => substr($arParamsUsuario['DS_TELEFONE_USUARIO'], 0, 2),
                            'numerotelefone' => substr($arParamsUsuario['DS_TELEFONE_USUARIO'], 2),
                            'nomebairro' => $arParamsUsuario['DS_BAIRRO_ENDERECO'],
                            'cep' => $arParamsUsuario['NU_CEP'],
                            'endereco' => $arParamsUsuario['DS_ENDERECO'],
                            'complementoendereco' => $arParamsUsuario['DS_COMPLEMENTO_ENDERECO']
                        )
                    );
                
                if($updateSegWeb['result'] != 1) {
                    $errorMessage = '';
                    if(is_array($updateSegWeb['message'])) {
                        foreach($updateSegWeb['message'] as $message) {
                            $errorMessage .= "<br />" . $message['text'];
                        }
                    }
                    else {
                        $errorMessage = $updateSegWeb['message']['text'];
                    }
                    throw new Exception($errorMessage);
                }
            }
            
        }

	/**
	 * Função para ativar/inativar um usuário
	 * @param array $usuario
	 */
	public function ativarInativaUsuario( $usuario ) {
		$obModelo = new Fnde_Sice_Model_Usuario();
		$busLogSituUsu = new Fnde_Sice_Business_SituacaoUsuario();
		
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$where = "NU_SEQ_USUARIO = " . $usuario['NU_SEQ_USUARIO'];
		$usuario['DT_ALTERACAO'] = date('d/m/Y');
		
		$busLogSituUsu->setLogSituacao($usuario['ST_USUARIO'], $usuario['NU_SEQ_USUARIO']);
		return $obModelo->update($usuario, $where);
	}

	/**
	 * Função para retornar os dados do usuário a partir do CPF
	 * @param array $usuario
	 */
	public function getUsuarioByCpf( $cpf ) {
		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$select = $obModelo->select()->where("NU_CPF = ?", $cpf);
		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * Função para retornar os dados do usuário com perfil cursista a partir do CPF
	 * @param array $usuario
	 */
	public function getCursistaByCpf( $cpf ) {
		$obModelo = new Fnde_Sice_Model_Usuario();
		$obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

		$select = $obModelo->select()->where("NU_CPF = ?", $cpf)->where("NU_SEQ_TIPO_PERFIL = 7");
		$stmt = $select->query();
		$result = $stmt->fetch();
		return $result;
	}

	/**
	 * Pesquisa rede de ensino pelo município nos dados do EducaCenso
	 *
	 * @author diego.matos
	 * @since 21/05/2012
	 * @param int $municipio
	 */
	public function pesquisarRedeEnsinoPorMunicipio( $municipio ) {

	$arrayParametros = array();
		$query = " SELECT " . " DISTINCT ADM.CO_ESFERA_ADM, " . " ADM.NO_ESFERA_ADM " . " FROM "
				. " CORP_FNDE.S_ENTIDADE ENT "
				. " INNER JOIN CORP_FNDE.S_ESFERA_ADMINISTRATIVA ADM ON ENT.CO_ESFERA_ADM = ADM.CO_ESFERA_ADM "
				. " WHERE " . " ENT.CO_MUNICIPIO_FNDE = :CO_MUNICIPIO_FNDE";

		$arrayParametros["CO_MUNICIPIO_FNDE"] = $municipio;

		$query .= " ORDER BY ADM.CO_ESFERA_ADM ";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query, $arrayParametros);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Pesquisa escolas pela entidade selecionada nos dados do EducaCenso
	 * @param array $arParam
	 */
	public function pesquisarEscola( $arParams ) {
		$sql = "SELECT  SEC.CO_ESCOLA,  SEC.NO_ESCOLA
        FROM  CORP_FNDE.S_ESCOLA_CENSO SEC
        JOIN CORP_FNDE.S_MUNICIPIO SM
        ON SM.CO_MUNICIPIO_FNDE = SEC.CO_MUNICIPIO_FNDE
        WHERE 1=1 ";

        $arBind = array();
        if($arParams['SG_UF_ESCOLA']){
            $arBind['SG_UF_ESCOLA'] = $arParams['SG_UF_ESCOLA'];
            $sql .= " AND SEC.SG_UF = :SG_UF_ESCOLA ";
        }

        if($arParams['CO_MUNICIPIO_ESCOLA']){
            $arBind['CO_MUNICIPIO_ESCOLA'] = $arParams['CO_MUNICIPIO_ESCOLA'];
            $sql .= " AND SEC.CO_MUNICIPIO_FNDE = :CO_MUNICIPIO_ESCOLA ";
        }

        if($arParams['CO_REDE_ENSINO']){
            $arBind['CO_REDE_ENSINO'] = $arParams['CO_REDE_ENSINO'];
            $sql .= " AND SEC.CO_ESFERA_ADM = :CO_REDE_ENSINO ";
        }

        if($arParams['CO_ESCOLA']){
            $arBind['CO_ESCOLA'] = $arParams['CO_ESCOLA'];
            $sql .= " AND SEC.CO_ESCOLA = :CO_ESCOLA ";
        }

        if($arParams['CO_MUNICIPIO_IBGE']){
            $arBind['CO_MUNICIPIO_IBGE'] = $arParams['CO_MUNICIPIO_IBGE'];
            $sql .= " AND SM.CO_MUNICIPIO_IBGE = :CO_MUNICIPIO_IBGE ";
        }

        $sql .= " ORDER BY NO_ESCOLA ";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($sql, $arBind);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Função para buscar os articuladores vinculados ao tutor
	 * @param array $usuario
	 */
	public function getArticuladorPorTurtor( $id ) {

		$query = "SELECT DISTINCT 
				  tur.NU_SEQ_USUARIO_ARTICULADOR as NU_SEQ_USUARIO,
				  usu.NO_USUARIO
				FROM SICE_FNDE.s_usuario usu
				INNER JOIN SICE_FNDE.s_turma tur
				ON usu.NU_SEQ_USUARIO          = tur.NU_SEQ_USUARIO_ARTICULADOR
				WHERE tur.NU_SEQ_USUARIO_TUTOR = $id
    			ORDER BY usu.NO_USUARIO";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Função para buscar os tutores vinculados ao articulador
	 * @param array $usuario
	 */
	public function getTutorPorArticulador( $id ) {

		$query = "SELECT DISTINCT 
				  tur.NU_SEQ_USUARIO_TUTOR as NU_SEQ_USUARIO,
				  usu.NO_USUARIO
				FROM SICE_FNDE.s_usuario usu
				INNER JOIN SICE_FNDE.s_turma tur
				ON usu.NU_SEQ_USUARIO          = tur.NU_SEQ_USUARIO_TUTOR
				WHERE tur.NU_SEQ_USUARIO_ARTICULADOR = $id";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Altera a situação do usuário para a nova situação passada como parâmetro.
	 * @param int $codUsuario Código do usuário que será alterado.
	 * @param char $novaSituacao Nova situação do usuário. A - Ativo; D - Inativo; L - Liberação Pendente.
	 */
	public function alteraSituacaoUsuario( $codUsuario, $novaSituacao ) {
		$obModel = new Fnde_Sice_Model_Usuario();
		$busLogSituUsu = new Fnde_Sice_Business_SituacaoUsuario();
		
		$arUsuario = array('ST_USUARIO' => $novaSituacao);
		$where = 'NU_SEQ_USUARIO = ' . $codUsuario;

		try {
			$busLogSituUsu->setLogSituacao($novaSituacao, $codUsuario);
			$obModel->update($arUsuario, $where);
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Atuliza no SICE a data de expriação e o login e id do usuário SEGWEB por ID SICE 
	 * @param int $id
	 * @param date $login
	 */
	public function atualizaAcessoSegweb($id,$nuSeqUsuarioSegweb){
		$query = "UPDATE SICE_FNDE.S_USUARIO SET
					NU_SEQ_USUARIO_SEGWEB = $nuSeqUsuarioSegweb
				  WHERE NU_SEQ_USUARIO =  $id";
	
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		return $stm;
	}
	
	public function getDataExpiracaoAtualizadaSegweb($id){
		$query = "SELECT NU_SEQ_USUARIO,
					ST_ATIVO,
					to_char(SYSDATE + 90, 'YYYY-MM-DD') DT_EXPIRACAO_SENHA,
					to_char(DT_VALIDADE_SENHA , 'YYYY-MM-DD') DT_VALIDADE_SENHA
					FROM SEGWEB_FNDE.S_USUARIO 
					WHERE NU_SEQ_USUARIO = $id";
		
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	
	/**
	 * Recupera inforção do usuário cadastrado no SEGWEB.
	 * @param integer $nuSeqUsuario
	 * @return mixed
	 */
	public function getUsuarioSegweb($nuSeqUsuario){
		$query = "SELECT 
						DS_E_MAIL,
						DS_LOGIN,
						DS_SENHA,
						DT_ALTERACAO_SENHA,
						DT_CRIACAO_USUARIO,
						DT_EXPIRACAO_SENHA,
						DT_VALIDADE_SENHA,
						NU_SEQ_USUARIO,
						ST_ATIVO,
						TP_USUARIO
						FROM SEGWEB_FNDE.S_USUARIO
						WHERE NU_SEQ_USUARIO = $nuSeqUsuario";
	
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	
	/**
	 * Verifica se o CPF informado é válido e retorna a mensagem
	 */ 
	public static function validaCPF( $cpf, $form, $validaVazio = fasle ) {

		if ( $validaVazio ) {
			if ( $cpf == '' ) {
				return false;
			}
		}

		if ( !Fnde_Sice_Business_Usuario::verificaCpfValido($cpf) ) {
			$form->getElement('NU_CPF')->addError("O CPF $cpf é inválido!");
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Fórmula que verifica se o CPF é válido
	 */
	public static function verificaCpfValido( $cpf ) {

		$cpf = preg_replace('/[^0-9]|0{11}|1{11}|2{11}|3{11}|4{11}|5{11}|6{11}|7{11}|8{11}|9{11}/', '', $cpf);
		$cpf = trim($cpf);

		if ( empty($cpf) || strlen($cpf) != 11 ) {
			return false;
		} else {

			$subCpf = substr($cpf, 0, 9);
			
			$dv = 0;
			list($dv, $resultado1) = self::_processarDv($subCpf, 10, $cpf[9], $dv);
			list($dv, $resultado2) = self::_processarDv($subCpf, 11, $cpf[10], $dv*2);
			
			return $resultado1 && $resultado2;
		}
	}
	/**
	 * Processar o dígito verificador
	 * @param $subCpf
	 * @param $max
	 * @param $digito
	 * @param $dv
	 */
	private static function _processarDv($subCpf, $max, $digito, $dv){
		for ( $i = 0; $i <= 9; $i++ ) {
			$dv += ( $subCpf[$i] * ( $max - $i ) );
		}

		if ( $dv == 0 ) {
			return array($dv, false);
		}
		$dv = 11 - ( $dv % 11 );

		if ( $dv > 9 ) {
			$dv = 0;
		}
		if ( $digito != $dv ) {
			return array($dv, false);
		}
		return array($dv, true);
	}


	/**
	 * Valida se a data de expedicao e maior que a data atual.
	 * @param string $dataExpedicao Data de expedicao.
	 */
	public function validaDataExpedicao( $dataExpedicao ) {

		$dtExpedicao = new Zend_Date($dataExpedicao, 'D/M/Y');
		$dtAtual = new Zend_Date(date('d/m/Y'), 'D/M/Y');

		if ( $dtExpedicao->isLater($dtAtual) ) {
			return false;
		}
		return true;
	}

	/**
	 * Preenche dados do cursista na tela caso seja valido.
	 * @param string $cpf CPF cursista.
	 * @param formulario $form Formulario que sera preenchido.
	 */
	public function validaCursista( $cpf, &$form ) {
		$obUsuario = new Fnde_Sice_Business_Usuario();
		$obTurma = new Fnde_Sice_Business_VincCursistaTurma();
		$cursista = $obUsuario->search(array('NU_CPF' => $cpf));
		$cursista = $cursista[0];
		$infoCursista = $obTurma->getInfoCursistaByCpf($cpf);

		if ( $infoCursista ) {
			//PREENCHE OS DADOS DO CURSISTA NA TELA CASO EXISTA E SEJA VÁLIDO

			//PREENCHENDO FIELDSET DADOS PESSOAIS
			$form->getElement('SG_UF_NASCIMENTO')->setValue($cursista['SG_UF_NASCIMENTO']);
			//Preenchendo o municipio
			$obBusinessUF = new Fnde_Sice_Business_Uf();
			$result = $obBusinessUF->getMunicipioPorUf($cursista['SG_UF_NASCIMENTO']);
			$nucoMunicipioDadosPessoais = $form->getElement('CO_MUNICIPIO_NASCIMENTO');
			for ( $i = 0; $i < count($result); $i++ ) {
				$nucoMunicipioDadosPessoais->addMultiOption($result[$i]['CO_MUNICIPIO_IBGE'],
						$result[$i]['NO_MUNICIPIO']);
			}
			$nucoMunicipioDadosPessoais->setValue($cursista['CO_MUNICIPIO_NASCIMENTO']);

			$form->getElement('DS_EMAIL_USUARIO')->setValue($cursista['DS_EMAIL_USUARIO']);
			$form->getElement('DS_TELEFONE_USUARIO')->setValue($cursista['DS_TELEFONE_USUARIO']);
			$form->getElement('DS_CELULAR_USUARIO')->setValue($cursista['DS_CELULAR_USUARIO']);

			//PREENCHENDO FIELDSET DADOS ESCOLARES
			$obDadosEscolares = new Fnde_Sice_Business_DadosEscolaresCursista();
			$dadosEscolares = $obDadosEscolares->getDadosEscolaresCursistaById($infoCursista[0]['NU_SEQ_USUARIO']);

			$form->getElement('CO_MUNICIPIO_ESCOLA')->setValue($dadosEscolares['CO_MUNICIPIO_ESCOLA']);
			//Preenchedo a mesoregiao
			$obMesoregiao = new Fnde_Sice_Business_MesoRegiao();
			$mesoregiao = $obMesoregiao->getMesoRegiaoById($dadosEscolares['CO_MESORREGIAO']);
			$form->getElement('NO_MESORREGIAO_ESCOLA')->setValue($mesoregiao['NO_MESO_REGIAO']);

			//Preenchendo a rede de ensino
			$businessUsuario = new Fnde_Sice_Business_Usuario();
			$result = $businessUsuario->pesquisarRedeEnsinoPorMunicipio($dadosEscolares['CO_MUNICIPIO_ESCOLA']);
			$nucoRedeEnsino = $form->getElement('CO_REDE_ENSINO');
			for ( $i = 0; $i < count($result); $i++ ) {
				$nucoRedeEnsino->addMultiOption($result[$i]['CO_ESFERA_ADM'], $result[$i]['NO_ESFERA_ADM']);
			}
			$nucoRedeEnsino->setValue($dadosEscolares['CO_REDE_ENSINO']);

			//Preenchendo o nome da escola
			$arParams = array("CO_REDE_ENSINO" => 2, "CO_MUNICIPIO_ESCOLA" => $dadosEscolares['CO_MUNICIPIO_ESCOLA'],
					"SG_UF_ESCOLA" => $dadosEscolares['CO_UF_ESCOLA']);
			$businessUsuario = new Fnde_Sice_Business_Usuario();
			$result = $businessUsuario->pesquisarEscola($arParams);
			$nucoNomeEscola = $form->getElement('CO_ESCOLA');
			for ( $i = 0; $i < count($result); $i++ ) {
				$nucoNomeEscola->addMultiOption($result[$i]['CO_ESCOLA'], $result[$i]['NO_ESCOLA']);
			}
			$nucoNomeEscola->setValue($dadosEscolares['CO_ESCOLA']);

			$form->getElement('CO_SEGMENTO')->setValue($dadosEscolares['CO_SEGMENTO']);
		}

		return true;
	}
	
	/**
	 * Preenche os dados do usuario/cursista de acordo com o CPF digitado.
	 * Utilizado nas telas de usuario e de cadastrar cursista.
	 * @param Usuario_Form $form
	 * @param string $cpf
	 */
	public function preencheDadosPorCpf($form, $cpf) {
		$receita = new Fnde_Model_Receita();
		try {
			$result = $receita->consultarCpf($cpf, $cpf);
			if ( $result['result'] == 1 ) {
				// SUCESSO
				$result = $result['content'];

				$form->getElement("NO_USUARIO")->setValue($result['Nome']);

				if($result['Sexo'] == 'F'){
                    $form->getElement("CO_SEXO_USUARIO")->setValue(2);
                }elseif($result['Sexo'] == 'M'){
                    $form->getElement("CO_SEXO_USUARIO")->setValue(1);
                }

				$dataNascimento = trim($result['DataNascimento']);
				$dataNascimento = substr($dataNascimento, 6, 2) . "/" . substr($dataNascimento, 4, 2) . "/"
				. substr($dataNascimento, 0, 4);
				$form->getElement("NO_MAE")->setValue($result['NomeMae']);
				$form->getElement("DT_NASCIMENTO")->setValue($dataNascimento);
	
				if ( $form->getElement("CO_SEXO_USUARIO")->getValue() == 1 ) {
					$element = $form->getElement("DS_SEXO_USUARIO");
					if ($element) {
						$element->setValue("MASCULINO");
					}
				} else if ( $form->getElement("CO_SEXO_USUARIO")->getValue() == 2 ) {
					$element = $form->getElement("DS_SEXO_USUARIO");
					if ($element) {
						$element->setValue("FEMININO");
					}
				} else {
					$element = $form->getElement("DS_SEXO_USUARIO");
					if ($element) {
						$element->setValue("");
					}
				}
	
			} else {
                throw new Exception("CPF não encontrado na base de dados da Receita Federal!");
			}
		} catch ( Exception $ex ) {
			$form->getElement('NU_CPF')->addError($ex->getMessage());
			$form->getElement("NO_USUARIO")->setValue('');
			$form->getElement("CO_SEXO_USUARIO")->setValue('');
			$form->getElement("NO_MAE")->setValue('');
			$form->getElement("DT_NASCIMENTO")->setValue('');
		}
	}
	
	/**
	 * Seta o valor do combo de UF de acordo com o perfil logado no sistema.
	 * Utilizado nas telas de usuario e de turma.
	 * @param $form Formulario que sera setado.
	 * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
	 * @param $arUsuario Dados do usuario da base de dados do SICE.
	 */
	public function setUfFilter( $form, $perfisUsuarioLogado, $arUsuario ) {
		try {
			$obBusinessUF = new Fnde_Sice_Business_Uf();
	
			$options = array(null => 'Selecione');
	
			if ( in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfisUsuarioLogado)
					|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfisUsuarioLogado)
					|| in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfisUsuarioLogado) ) {
				$result = $obBusinessUF->search(array('SG_UF'));
			} else {
				$result = $obBusinessUF->search(array('SG_UF' => $arUsuario['SG_UF_ATUACAO_PERFIL']));
			}
	
			for ( $i = 0; $i < count($result); $i++ ) {
				$options[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
			}
	
			$form->setUf($options);
		} catch ( Exception $e ) {
			$this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
		}
	}

	/**
	 * Valida as notas lançadas para os cursistas pelo tutor.
	 * @param array $arNotas
	 * @param array $arDesistente
	 */
	public static function validaNota( $arNotas, $arDesistente ) {

		//Verifica se alguma nota foi lançada.
		if ( empty($arNotas) && empty($arDesistente) ) {
			//throw new Exception("Deve ser informado pelo menos uma nota para avaliação da turma.");
		} else {
			foreach ( $arNotas as $nota ) {
				if ( $nota != '0,00' ) {
					return true;
				}
			}

			foreach ( $arDesistente as $check ) {
				if ( $check == 'on' ) {
					return true;
				}
			}
			throw new Exception("Deve ser informado pelo menos uma nota para avaliação da turma.");
		}
	}

	/**
	 * Função criada para verificar se um cursista já está cadastrado em outra turma.
	 */
	public function validaCursistaJaInserido( $cpf, $nu_seq_turma ) {
		$obUsuario = new Fnde_Sice_Business_Usuario();
		$obVincTurma = new Fnde_Sice_Business_VincCursistaTurma();
        $obTurma = new Fnde_Sice_Business_Turma();

		$cursista = $obUsuario->search(array('NU_CPF' => $cpf));
		$cursista = $cursista[0];

        if ( $cursista['ST_USUARIO'] == 'D' ) {
            throw new Exception("O cursista não poderá ser adicionado pois está com a situação de Inativo.", 1);
        }

		$infoCursista = $obVincTurma->getInfoCursistaByCpf($cpf);

		if($infoCursista){
            $turma = $obTurma->getTurmaById($nu_seq_turma);

            foreach($infoCursista as $info){
                if ( in_array($info['ST_TURMA'], array(1, 3, 4, 8)) ) {
                    throw new Exception("O cursista não poderá ser adicionado pois está vinculado a uma turma
                    ativa ou pré-turma ou aguardando autorização ou aguardando cancelamento.", 1);
                }

                if($turma['NU_SEQ_CURSO'] == $info['NU_SEQ_CURSO']) {
                    if ($info['ST_APROVADO'] == 'A') {
                        throw new Exception("O CPF pertence a um cursista já APROVADO nesse Módulo de Curso de
                        Formação para Conselheiros Escolares. Não é permitido cursar novamente o mesmo módulo.", 1);
                    }

                    if ($info['VL_EXERCICIO'] == date('Y') && $info['ST_APROVADO'] == 'R') {
                        throw new Exception("O CPF pertence a um cursista já REPROVADO nesse Módulo de Curso de
                        Formação para Conselheiros Escolares. Somente poderá ser matriculado novamente
                        em um próximo ano", '1');
                    }
                }
            }
        }
	}

	/**
	 * Obtém dados do bolsista para exibição na tela de detalhes de bolsista.
	 * @param $array $arParam
	 * @author diego.matos
	 */
	public function pesquisarDadosBolsistaPorId( $arParam ) {

		$query = " SELECT USU.NO_USUARIO,
					  USU.NU_SEQ_USUARIO,
					  TPF.DS_TIPO_PERFIL,
					  MSR.NO_MESO_REGIAO,
					  MSR.NO_MUNICIPIO,
					  USU.SG_UF_ATUACAO_PERFIL,
					  (
					  CASE
					    WHEN LSU.ST_USUARIO = 'A'
					    THEN 'Ativo'
					    WHEN LSU.ST_USUARIO = 'D'
					    THEN 'Inativo'
					    WHEN LSU.ST_USUARIO = 'L'
					    THEN 'Liberação Pendente'
					  END) AS ST_USUARIO
					FROM SICE_FNDE.H_PERFIL_USUARIO HIP
					INNER JOIN SICE_FNDE.S_BOLSA BOL ON HIP.NU_SEQ_USUARIO = BOL.NU_SEQ_USUARIO
					INNER JOIN SICE_FNDE.S_USUARIO USU ON BOL.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
					INNER JOIN SICE_FNDE.S_TIPO_PERFIL TPF ON HIP.NU_SEQ_TIPO_PERFIL = TPF.NU_SEQ_TIPO_PERFIL
					INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE
					INNER JOIN SICE_FNDE.S_PERIODO_VINCULACAO PVB ON BOL.NU_SEQ_PERIODO_VINCULACAO = PVB.NU_SEQ_PERIODO_VINCULACAO
					INNER JOIN SICE_FNDE.H_SITUACAO_USUARIO LSU
					  ON HIP.NU_SEQ_USUARIO = LSU.NU_SEQ_USUARIO
					  AND (LSU.DT_INICIO <= PVB.DT_FINAL AND NVL(LSU.DT_FIM, PVB.DT_FINAL + 1) > PVB.DT_FINAL)
					WHERE BOL.NU_SEQ_BOLSA = " . $arParam['NU_SEQ_BOLSA'] . "
					AND (HIP.DT_INICIO <= PVB.DT_FINAL AND NVL(HIP.DT_FIM, PVB.DT_FINAL + 1) > PVB.DT_FINAL) " ;
		//die($query);
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}

	/**
	 * Obtém dados do bolsista para exibição na tela de detalhes de bolsista.
	 * @param $array $arParam
	 * @author diego.matos
	 */
	public function pesquisarDadosBolsistaPorIdAntigo( $arParam ) {
	
		$query = " SELECT " . "    USU.NO_USUARIO, " . "    USU.NU_SEQ_USUARIO, " . "    TPF.DS_TIPO_PERFIL, "
		. "    MSR.NO_MESO_REGIAO, " . "    MSR.NO_MUNICIPIO, " . "    USU.SG_UF_ATUACAO_PERFIL, "
		. "    (CASE WHEN USU.ST_USUARIO = 'A' THEN 'Ativo' " . " WHEN USU.ST_USUARIO = 'D' THEN 'Inativo' "
		. " WHEN USU.ST_USUARIO = 'L' THEN 'Liberação Pendente' END) AS ST_USUARIO " . " FROM "
		. "     SICE_FNDE.S_USUARIO USU "
		. "     INNER JOIN SICE_FNDE.S_TIPO_PERFIL TPF ON USU.NU_SEQ_TIPO_PERFIL = TPF.NU_SEQ_TIPO_PERFIL "
		. "     INNER JOIN CTE_FNDE.T_MESO_REGIAO MSR ON USU.CO_MUNICIPIO_PERFIL = MSR.CO_MUNICIPIO_IBGE "
		. "     INNER JOIN SICE_FNDE.S_BOLSA BLS ON USU.NU_SEQ_USUARIO = BLS.NU_SEQ_USUARIO " . " WHERE "
		. "     BLS.NU_SEQ_BOLSA = {$arParam['NU_SEQ_BOLSA']}";
	
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	/**
	 * Função para buscar todos os tutores
	 * @param array $usuario
	 */
	public function getTutores() {
		$query = "SELECT * FROM SICE_FNDE.S_USUARIO WHERE NU_SEQ_TIPO_PERFIL = 6";
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	/**
	 * Retorna dados do usuario para PDF.
	 * @param string $uf
	 */
	public function getCoordExecEstadualPdfByUf( $uf ) {

		$query = " SELECT UF.NO_UF,
					  USU.NO_USUARIO,
					  USU.NU_CPF
					FROM SICE_FNDE.S_USUARIO USU
					LEFT JOIN CORP_FNDE.S_UF UF
					ON USU.SG_UF_ATUACAO_PERFIL = UF.SG_UF
					WHERE USU.NU_SEQ_TIPO_PERFIL  = 8
					AND USU.ST_USUARIO = 'A'
					AND USU.SG_UF_ATUACAO_PERFIL = '$uf'";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	
	/**
	 * Retorna dados do usuario para PDF.
	 * @param string $id ID do usuario.
	 */
	public function getCoordExecEstadualPdfByCpf( $cpf ) {
	
		$query = " SELECT " . "    UF.NO_UF, " . "    USU.NO_USUARIO, " . "    USU.NU_CPF "
		. " FROM SICE_FNDE.S_USUARIO USU "
		. " LEFT JOIN CORP_FNDE.S_UF UF ON USU.SG_UF_ATUACAO_PERFIL = UF.SG_UF "
		. " WHERE USU.NU_CPF = '$cpf'";
	
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	
	/**
	 * Retorna dados do prefil do usuario.
	 * @param string $id ID do usuario.
	 */
	public function getPerfilUsuarioSegweb( $id ) {
	
		$query = " SELECT TP.DS_TIPO_PERFIL_SEGWEB
					  FROM SICE_FNDE.s_tipo_perfil tp,
					       SICE_FNDE.S_USUARIO US
					  WHERE  tp.nu_seq_tipo_perfil = us.nu_seq_tipo_perfil 
					  AND us.nu_seq_usuario = $id";
	
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		return $result;
	}
	
	/**
	 * Função para buscar todos os tutores
	 * @param array $usuario
	 */
	public function validaExecutivoEstadual($uf,$nuSeqUsuario= NULL) {
		$query = "SELECT * 
						FROM SICE_FNDE.S_USUARIO 
						WHERE NU_SEQ_TIPO_PERFIL = 8 
						AND ST_USUARIO = 'A'
						AND SG_UF_ATUACAO_PERFIL = '$uf'
						AND NU_SEQ_USUARIO <> $nuSeqUsuario";
		
		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();
		
		if($result)
			return true;
		else 
			return false;
		
	}

    public function getDadosCursista($param = array()){
        $sql = "SELECT
            --cursista
            su.nu_cpf as cpf,
            su.no_usuario as nome_completo,
            replace(replace(su.co_sexo_usuario,1,'M'),2,'F') as sexo,
            TO_CHAR(su.dt_nascimento, 'DD/MM/YYYY') as data_nascimento,
            su.no_mae as nome_mae,
            su.sg_uf_nascimento as uf_nascimento,
            sm.no_municipio as municipio_nascimento,
            su.ds_email_usuario as email,
            su.ds_telefone_usuario as telefone,
            su.ds_celular_usuario as celular,
            --------------------------------------------
            --escola
            dec2.co_uf_escola as uf_escola,
            (select no_municipio from corp_fnde.s_municipio sm2 where sm2.co_municipio_fnde = dec2.co_municipio_escola) as municipio_escola,
            tmr.NO_MESO_REGIAO as mesorregiao_escola,
            csear.NO_ESFERA_ADM as rede_ensino,
            sec.no_escola as nome_escola,
            sec.co_escola as cod_inep_escola,
            ds_segmento as segmento,
            --------------------------------------------
            --turma
            stc.ds_tipo_curso as tipo_curso,
            vst.ds_nome_curso as curso,
            vst.nu_seq_turma as id_turma,
            (select su2.no_usuario from sice_fnde.s_usuario su2 where su2.nu_seq_usuario = vst.nu_seq_usuario_tutor) as tutor,
            (select su3.no_usuario from sice_fnde.s_usuario su3 where su3.nu_seq_usuario = vst.nu_seq_usuario_articulador) as articulador,
            TO_CHAR(vst.dt_inicio, 'DD/MM/YYYY') as dt_inicio,
            TO_CHAR(vst.dt_fim, 'DD/MM/YYYY') as dt_fim,
            TO_CHAR(vst.dt_finalizacao, 'DD/MM/YYYY') as dt_finalizacao,
            vst.uf_turma,
            vst.no_municipio as municipio_turma,
            vst.no_mesoregiao as mesorregiao_turma,
            ---------------------------------------------
            --avaliação pedagogica
            svct.nu_nota_tutor as nota_tutor,
            svct.nu_nota_cursista as nota_cursista,
            (nvl(svct.nu_nota_tutor,0) + nvl(svct.nu_nota_cursista,0)) as nota_total,
            vst.ds_st_turma as situacao,
            sca.DS_SITUACAO as SITUACAO_CRITERIO_AVALIACAO

        FROM
            sice_fnde.s_dados_escolares_cursista dec2
            inner join sice_fnde.s_usuario su on dec2.nu_seq_usuario_cursista = su.nu_seq_usuario
            inner join corp_fnde.s_municipio sm on sm.co_municipio_fnde = dec2.co_municipio_escola
            left join corp_fnde.s_escola_censo sec on dec2.co_escola = sec.co_escola
            left join CORP_FNDE.S_MUNICIPIO sme on sec.CO_MUNICIPIO_FNDE = sme.CO_MUNICIPIO_FNDE
            left join CTE_FNDE.T_MESO_REGIAO tmr on tmr.CO_MUNICIPIO_IBGE = sme.CO_MUNICIPIO_IBGE
            inner join sice_fnde.s_segmento ss on dec2.co_segmento = ss.nu_seq_segmento
            inner join sice_fnde.s_vinc_cursista_turma svct on dec2.nu_seq_usuario_cursista = svct.nu_seq_usuario_cursista
            left join SICE_FNDE.S_CRITERIO_AVALIACAO sca on sca.NU_SEQ_CRITERIO_AVAL = svct.NU_SEQ_CRITERIO_AVAL
            inner join sice_fnde.v_sisrel_turmas vst on svct.nu_seq_turma = vst.nu_seq_turma
            inner join sice_fnde.s_curso sc on vst.nu_seq_curso = sc.nu_seq_curso
            inner join sice_fnde.s_tipo_curso stc on sc.nu_seq_tipo_curso = stc.nu_seq_tipo_curso
            left join CORP_FNDE.S_ESFERA_ADMINISTRATIVA csear on csear.CO_ESFERA_ADM = dec2.co_rede_ensino

         where 1=1 ";

        $bind = array();
        if($param['NU_SEQ_CURSO']){
            $sql .= " AND sc.nu_seq_curso = :NU_SEQ_CURSO ";
            $bind[':NU_SEQ_CURSO'] = $param['NU_SEQ_CURSO'];
        }

        if($param['UF_TURMA']){
            $sql .= " AND vst.uf_turma = :UF_TURMA ";
            $bind[':UF_TURMA'] = $param['UF_TURMA'];
        }

        if($param['CO_MUNICIPIO']){
            $sql .= " AND vst.co_municipio = :CO_MUNICIPIO ";
            $bind[':CO_MUNICIPIO'] = $param['CO_MUNICIPIO'];
        }

        /*if($param['CO_MESORREGIAO']){
            $sql .= " AND vst.co_messoregiao = :CO_MESORREGIAO ";
            $bind[':CO_MESORREGIAO'] = $param['CO_MESORREGIAO'];
        }*/

        if($param['CO_REDE_ENSINO']){
            $sql .= " AND dec2.co_rede_ensino = :CO_REDE_ENSINO ";
            $bind[':CO_REDE_ENSINO'] = $param['CO_REDE_ENSINO'];
        }

        if($param['CO_ESCOLA']){
            $sql .= " AND sec.co_escola = :CO_ESCOLA ";
            $bind[':CO_ESCOLA'] = $param['CO_ESCOLA'];
        }

        if($param['NU_CPF']){
            $sql .= " AND su.nu_cpf = :NU_CPF ";
            $bind[':NU_CPF'] = preg_replace('(\D+)', "", $param['NU_CPF']);
        }

        if($param['NU_SEQ_TURMA']){
            $sql .= " AND vst.nu_seq_turma = :NU_SEQ_TURMA ";
            $bind[':NU_SEQ_TURMA'] = $param['NU_SEQ_TURMA'];
        }

        if($param['ST_CURSISTA']){
            $sql .= " AND upper(sca.DS_SITUACAO) = :ST_CURSISTA ";
            $bind[':ST_CURSISTA'] = $param['ST_CURSISTA'];
        }

        if($param['NU_ANO']){
            $sql .= " AND TO_CHAR(vst.dt_inicio, 'YYYY') = :NU_ANO ";
            $bind[':NU_ANO'] = $param['NU_ANO'];
        }

        if($param['DT_INICIO']){
            $sql .= " AND vst.dt_finalizacao >= TO_DATE(:DT_INICIO, 'DD/MM/YYYY') ";
            $bind[':DT_INICIO'] = $param['DT_INICIO'];
        }

        if($param['DT_FIM']){
            $sql .= " AND vst.dt_finalizacao <= TO_DATE(:DT_FIM, 'DD/MM/YYYY') ";
            $bind[':DT_FIM'] = $param['DT_FIM'];
        }

        $obModelo = new Fnde_Sice_Model_Usuario();
        $stm = $obModelo->getAdapter()->query($sql, $bind);

        return $stm->fetchAll();
    }

	public function buscaPerfilUsuarioPorCpf($cpf){
		$query = "SELECT h.NU_SEQ_USUARIO,
						  u.NU_CPF ,
						  u.NO_USUARIO,
						  t.DS_TIPO_PERFIL
						FROM SICE_FNDE.H_PERFIL_USUARIO h
						INNER JOIN SICE_FNDE.S_USUARIO u
						ON u.NU_SEQ_USUARIO = h.NU_SEQ_USUARIO
						INNER JOIN SICE_FNDE.S_TIPO_PERFIL t
						ON t.NU_SEQ_TIPO_PERFIL = h.NU_SEQ_TIPO_PERFIL
						WHERE u.NU_CPF          = {$cpf}";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		if($result)
			return $result;
		else
			return false;
	}

	/**
	 * Pesquisa rede de ensino
	 *
	 * @author pedro.correia
	 * @since 11/04/2016
	 */
	public function getRedeEnsino($id) {
		$arrayParametros = array();
		$query = " SELECT " . " DISTINCT ADM.CO_ESFERA_ADM, " . " ADM.NO_ESFERA_ADM " . " FROM "
			. " CORP_FNDE.S_ESFERA_ADMINISTRATIVA ADM";

		$query .= ($id) ? " WHERE ADM.CO_ESFERA_ADM = $id " : "";

		$query .= " ORDER BY ADM.NO_ESFERA_ADM ";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}


	public function getSegmentos() {

		$query = "SELECT * FROM SICE_FNDE.S_SEGMENTO";

		$obModelo = new Fnde_Sice_Model_Segmento();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	public function getMesoregiaoMunicipio($arParams){

		$municipioIbge = $arParams['CO_MUNICIPIO_IBGE'];
		$query = "SELECT tmr.CO_MESO_REGIAO , tmr.NO_MESO_REGIAO
					FROM CTE_FNDE.T_MESO_REGIAO tmr
					JOIN CORP_FNDE.S_MUNICIPIO SM
					ON tmr.CO_MUNICIPIO_IBGE = SM.CO_MUNICIPIO_IBGE
					WHERE SM.CO_MUNICIPIO_FNDE = {$municipioIbge}";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}

	public function getTodasMesorregiaoUF($arParams){
		$uf = $arParams['CO_UF_ESCOLA'];
		$query = "SELECT distinct tmr.CO_MESO_REGIAO , tmr.NO_MESO_REGIAO
					FROM CTE_FNDE.T_MESO_REGIAO tmr
					JOIN CORP_FNDE.S_MUNICIPIO SM
					ON tmr.CO_MUNICIPIO_IBGE = SM.CO_MUNICIPIO_IBGE
          where SM.SG_UF = '{$uf}' ORDER BY tmr.NO_MESO_REGIAO";

		$obModelo = new Fnde_Sice_Model_Usuario();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetchAll();
		return $result;
	}
}
