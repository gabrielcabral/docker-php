<?php

/**
 * Business do EmitirCertificado
 *
 * @author rafael.paiva
 * @since 12/09/2012
 */
class Fnde_Sice_Business_EmitirCertificado
{
    /**
     * Cusista pode ver apenas os seus certificados
     * Tutor pode ver apenas os seus proprios certificados e dos cursistas vinculados a turmas de sua tutoria
     * O articulador pode emitir apenas suas próprias declarações, emitir certificados dos tutores e cursistas de sua UF
     * O coordenador executivo estadual pode emitir declarações dos articuladores, emitir certificados dos tutores e cursistas da sua UF
     * O coordenador nacional administrador e equipe podem emitir qualquer certificado e declaração
     */
    public function permissoes()
    {
        $usuario = Zend_Auth::getInstance()->getIdentity();

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR,
                $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE,
                $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR,
                $usuario->credentials)
        ) {
            return array(
                'estado' => 'todos',
                'mesorregiao' => 'todas',
                'municipio' => 'todos',
                'declaracao_articulador' => 'todos',
                'certificado_tutor' => 'todos',
                'certificado_cursista' => 'todos',
                // necessário evoluir a aplicação de avaliação de curso pela tela de Emitir Certificado
                // hoje o sistema aceita que apenas o cursista avalie o curso.
                // o coordenador nacional e equipe e adm podem avaliar pela pesquisa de usuário  (manutenção/usuário)
                // nas ações do RODAPÉ
//                'avaliar_curso' => 'todos',
                'notificar_cursista' => 'todos',
            );
        } else if (
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $usuario->credentials)
        ) {
            $arUsuario = $businessUsuario->getUsuarioByCpf(preg_replace("/[^0-9]/", "", $usuario->cpf));
            return array(
                'estado' => $arUsuario['SG_UF_ATUACAO_PERFIL'],
                'mesorregiao' => 'do_estado',
                'municipio' => 'do_estado',
                'declaracao_articulador' => 'do_estado',
                'certificado_tutor' => 'do_estado',
                'certificado_cursista' => 'do_estado',
                'notificar_cursista' => 'do_estado'
            );
        } else if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $usuario->credentials)) {
            $arUsuario = $businessUsuario->getUsuarioByCpf(preg_replace("/[^0-9]/", "", $usuario->cpf));
            return array(
                'estado' => $arUsuario['SG_UF_ATUACAO_PERFIL'],
                'mesorregiao' => $arUsuario['CO_MESORREGIAO'],
                'municipio' => 'da_mesorregiao',
                'declaracao_articulador' => 'proprio',
                'certificado_tutor' => 'do_estado',
                'certificado_cursista' => 'do_estado',
                'notificar_cursista' => 'do_estado'
            );
        } else if (in_array(Fnde_Sice_Business_Componentes::TUTOR, $usuario->credentials)) {
            $arUsuario = $businessUsuario->getUsuarioByCpf(preg_replace("/[^0-9]/", "", $usuario->cpf));
            return array(
                'estado' => $arUsuario['SG_UF_ATUACAO_PERFIL'],
                'mesorregiao' => $arUsuario['CO_MESORREGIAO'],
                'municipio' => 'da_mesorregiao',
                'certificado_tutor' => 'proprio',
                'certificado_cursista' => 'turmas_vinculadas',
                'notificar_cursista' => 'turmas_vinculadas'
            );
        } else if (in_array(Fnde_Sice_Business_Componentes::CURSISTA, $usuario->credentials)) {
            $arUsuario = $businessUsuario->getUsuarioByCpf(preg_replace("/[^0-9]/", "", $usuario->cpf));
            return array(
                'estado' => $arUsuario['SG_UF_ATUACAO_PERFIL'],
                'mesorregiao' => $arUsuario['CO_MESORREGIAO'],
                'municipio' => $arUsuario['CO_MUNICIPIO_PERFIL'],
                'certificado_cursista' => 'proprio',
                'avaliar_curso' => 'proprio'
            );
        } else {
            throw new Exception('Perfil do usuário não foi localizado');
        }

    }

    /**
     * Lista os usuarios agrupados por curso e de acordo com as permissões do usuário logado
     *
     * @param $filtro
     * @return array|void
     * @throws Exception
     */
    public function lista($filtro)
    {
        $filtro = (array)$filtro;

        $filtro['ST_TURMA'] = Fnde_Sice_Business_Turma::FINALIZADA;

        $model = new Fnde_Sice_Model_EmitirCertificado();

        $permissoes = $this->permissoes();

        if ($permissoes['estado'] != 'todos') {
            $filtro['SG_UF'] = $permissoes['estado'];
        }
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $objUsuario = new Fnde_Sice_Business_Usuario();

        $arUsuario = $objUsuario->getUsuarioByCpf($usuarioLogado->cpf);
        // verifica o tipo de perfil escolhido na tela

        $tipoPerfil = is_numeric($filtro['NU_SEQ_TIPO_PERFIL']) ? $filtro['NU_SEQ_TIPO_PERFIL'] : null;
        $resultado = array();
        if ($tipoPerfil == Fnde_Sice_Business_PerfilUsuario::ARTICULADOR && isset($permissoes['declaracao_articulador'])) {

            if ($permissoes['declaracao_articulador'] == 'proprio') {
                $filtro['articulador'] = $arUsuario['NU_SEQ_USUARIO'];
            }
            $resultado = $model->listaArticuladores($filtro);

        } else if ($tipoPerfil == Fnde_Sice_Business_PerfilUsuario::TUTOR && isset($permissoes['certificado_tutor'])) {

            if ($permissoes['certificado_tutor'] == 'proprio') {
                $filtro['tutor'] = $arUsuario['NU_SEQ_USUARIO'];
            }

            $resultado = $model->listaTutores($filtro);

        } else if ($tipoPerfil == Fnde_Sice_Business_PerfilUsuario::CURSISTA && isset($permissoes['certificado_cursista'])) {

//            $filtro['aprovado_ou_aprovado_destaque'] = true;

            if ($permissoes['certificado_cursista'] == 'proprio') {
                $filtro['cursista'] = $arUsuario['NU_SEQ_USUARIO'];
            } else if ($permissoes['certificado_cursista'] == 'turmas_vinculadas') {
                $filtro['tutor'] = $arUsuario['NU_SEQ_USUARIO'];
            }
            $resultado = $model->listaCursistas($filtro);
        }

        return $resultado;
    }

    public function getDadosResumidosTutor($idTutor)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        return $model->getDadosResumidosTutor($idTutor);
    }

    public function getDadosResumidosArticulador($idArticulador)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        return $model->getDadosResumidosArticulador($idArticulador);
    }

    public function getTurmasTutorCurso($idTutor, $idCurso)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        return $model->getTurmasTutorCurso($idTutor, $idCurso);
    }

    public function getPeriodosArticulador($idTutor, $idCurso)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        return $model->getPeriodosArticulador($idTutor, $idCurso);
    }

    public function dadosParaCursista($usuario, $turma)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        $dados = $model->dadosParaCursista($usuario, $turma);

        if ($dados) {


            if (in_array($dados['DS_SITUACAO'], array('Aprovado com destaque', 'Aprovado'))) {

                if (!empty($dados['ST_CURSO_AVAL'])) {
                    $dados['DS_NOME_MODULO'] = implode(', ', $this->getNomeModulo($dados['NU_SEQ_CURSO']));
                    $dados['DS_CONTEUDO_PROGRAMATICO'] = implode(', ', $this->getConteudoProgramatico($dados['NU_SEQ_CURSO']));
                    $dados['DS_CONTEUDO_PROGRAMATICO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $dados['DS_CONTEUDO_PROGRAMATICO']);

                    $modelParametro = new Fnde_Sice_Business_ParametroCertificado();
                    $responsavel = $modelParametro->getPorData($dados['DT_FINALIZACAO_REAL']);

                    if ($responsavel) {
                        unset($responsavel['DT_INICIO'], $responsavel['DT_FIM']);
                        $dados = array_merge($responsavel, $dados);

                        return $dados;
                    } else {
                        throw new Exception("{$dados['NO_USUARIO']}: Não foi encontrado um dirigente para a data de finalização da turma ({$dados['DT_FINALIZACAO_REAL']})");
                    }
                } else {
                    throw new Exception("{$dados['NO_USUARIO']}: Cursista aprovado. Antes de emitir o Certificado é necessário realizar a Avaliação Institucional do Curso.");
                }
            } else {

                if (in_array($dados['ST_TURMA'], array(Fnde_Sice_Business_Turma::FINALIZACAO_ATRASADA, Fnde_Sice_Business_Turma::FINALIZADA))) {
                    throw new Exception('Cursista não foi aprovado em nenhuma turma.');
                } else {
                    $msgSemAva = '';
                    if (empty($dados['ST_CURSO_AVAL'])) {
                        $msgSemAva = 'É importante realizar a Avaliação Institucional do Curso.';
                    }
                    throw new Exception(($dados['DS_SITUACAO'] == 'Reprovado' ? 'Cursista reprovado. ' : 'Cursista desistente. ') . $msgSemAva);
                }
            }
        } else {
            throw new Exception("{$dados['NO_USUARIO']}: Não foi possível localizar os dados do cursista");
        }
    }

    public function dadosParaTutor($usuario, $turma)
    {

        $model = new Fnde_Sice_Model_EmitirCertificado();
        $dados = $model->dadosParaTutor($usuario, $turma);

        if ($dados) {

            $dados['DS_NOME_MODULO'] = implode(', ', $this->getNomeModulo($dados['NU_SEQ_CURSO']));
            $dados['DS_CONTEUDO_PROGRAMATICO'] = implode(', ', $this->getConteudoProgramatico($dados['NU_SEQ_CURSO']));
            $dados['DS_CONTEUDO_PROGRAMATICO'] = str_replace(array('\r\n', '\r', '\n'), "\n", $dados['DS_CONTEUDO_PROGRAMATICO']);

            $modelParametro = new Fnde_Sice_Business_ParametroCertificado();
            $responsavel = $modelParametro->getPorData($dados['DT_FINALIZACAO_REAL']);

            if ($responsavel) {
                unset($responsavel['DT_INICIO'], $responsavel['DT_FIM']);
                $dados = array_merge($responsavel, $dados);

                return $dados;
            } else {
                throw new Exception("{$dados['NO_USUARIO']}: Não foi encontrado um dirigente para a data de finalização da turma ({$dados['DT_FINALIZACAO_REAL']})");
            }
        } else {
            throw new Exception("{$dados['NO_USUARIO']}: Não foi possível localizar os dados do tutor");
        }
    }

    public function dadosParaDeclaracaoArticulador($usuario, $periodoVinculacao)
    {
        $model = new Fnde_Sice_Model_EmitirCertificado();
        $dados = $model->dadosParaDeclaracaoArticulador($usuario, $periodoVinculacao);

        if ($dados) {

            $dados['DS_NOME_MODULO'] = implode(', ', $this->getNomeModulo($dados['NU_SEQ_CURSO']));

            $modelParametro = new Fnde_Sice_Business_ParametroCertificado();
            $responsavel = $modelParametro->getPorData($dados['DT_FINAL']);

            if ($responsavel) {
                unset($responsavel['DT_INICIO'], $responsavel['DT_FIM']);
                $dados = array_merge($responsavel, $dados);


                return $dados;
            } else {
                throw new Exception("{$dados['NO_USUARIO']}: Não foi encontrado um dirigente para a data de final do período ({$dados['DT_FINAL']})");
            }
        } else {
            throw new Exception("{$dados['NO_USUARIO']}: Não foi possível localizar os dados do articulador");
        }
    }

    public function assinarCertificado($path)
    {
        try {
            $assinatura = new Fnde_Model_Assinatura();
            $usuarioLogado = Zend_Auth::getInstance()->getIdentity();

            //Parametros WebService Assinatura
            $dsLogin = $usuarioLogado->username;
            $qtAssinatura = 1;
            $file = $path;

            $filename = explode('/', $path);
            $filename = $filename[count($filename) - 1];

            $mimeType = mime_content_type($path);

            $tpDocumento = 'CRT';

            $resultCreate = $assinatura->create($dsLogin, $qtAssinatura, $file, $filename, $mimeType, $tpDocumento);

            if (is_numeric($resultCreate)) {
                $result = $assinatura->sign($resultCreate, $dsLogin);

                if ($result['ds_assinatura']) {
                    return $result['ds_assinatura'];
                } else {
                    throw new Exception('Problema com o WS da assinatura (leitura): ' . $result['message']['text']);
                }
            } else {
                throw new Exception('Problema com o WS da assinatura (criação): ' . $resultCreate['message']['text']);
            }
        } catch (Exception $e) {
            throw new Fnde_Exception($e->getMessage(), $e->getCode());
        }
    }

    public function salvarCertificadoGerado($tipo, $id, $caminho)
    {
        $filename = explode('/', $caminho);
        $filename = $filename[count($filename) - 1];

        $mimeType = mime_content_type($caminho);

        $usuarioLogado = (array)Zend_Auth::getInstance()->getIdentity();

        $castor = new Fnde_Model_Castor();
        $codCertificado = $castor->write($usuarioLogado['nu_seq_usuario'], $caminho, $filename, $mimeType);

        if ($tipo == 'cursista') {
            $model = new Fnde_Sice_Model_VincCursistaTurma();
            $dados = $model->find($id)->current();

            $dados->NU_CERTIFICADO = $codCertificado;

            $dados->save();
        }

        return $codCertificado;

    }

    //////////////////////////////////////


    /**
     * Retorna a pesquisa da tela de filtro do UC Emitir Certificado.
     * @param array $arParam Parametros da pesquisa realizada pelo usuario.
     * @throws Fnde_Business_Exception
     */
    public function pesquisarDados($arParam)
    {
        try {
            $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
            $businessUsuario = new Fnde_Sice_Business_Usuario();
            $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
            if ($cpfUsuarioLogado) {
                $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
            }

            $sql = "SELECT (USU.NU_SEQ_USUARIO || '/' || T.NU_SEQ_TURMA) AS NU_SEQ_USUARIO_NU_SEQ_TURMA,
					  USU.NU_SEQ_USUARIO,
					  T.NU_SEQ_TURMA,
					  USU.SG_UF_ATUACAO_PERFIL,
					  MR.NO_MUNICIPIO,
					  USU.NO_USUARIO,
					  (SUBSTR(USU.NU_CPF,1,3)||'.'||
					    SUBSTR(USU.NU_CPF,4,3)||'.'||
					    SUBSTR(USU.NU_CPF,7,3)||'-'||
					    SUBSTR(USU.NU_CPF,10,2)
					  ) AS NU_CPF,
					  C.DS_NOME_CURSO,
					  (TO_CHAR(T.DT_FINALIZACAO, 'DD/MM/YYYY')) AS DT_FINALIZACAO,
					  (SELECT 1 FROM SICE_FNDE.S_AVALIACAO_CURSO
					    WHERE NU_SEQ_TURMA = T.NU_SEQ_TURMA
					    AND NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
					  ) AS ST_CURSO_AVAL
					FROM SICE_FNDE.S_USUARIO USU
					INNER JOIN CTE_FNDE.T_MESO_REGIAO MR ON USU.CO_MUNICIPIO_PERFIL = MR.CO_MUNICIPIO_IBGE
					INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON USU.NU_SEQ_USUARIO = VCT.NU_SEQ_USUARIO_CURSISTA
					INNER JOIN SICE_FNDE.S_TURMA T ON VCT.NU_SEQ_TURMA = T.NU_SEQ_TURMA
					LEFT  JOIN SICE_FNDE.S_CERTIFICADO CERT ON CERT.NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
					INNER JOIN SICE_FNDE.S_CURSO C ON T.NU_SEQ_CURSO = C.NU_SEQ_CURSO
					INNER JOIN SICE_FNDE.S_CRITERIO_AVALIACAO CA ON VCT.NU_SEQ_CRITERIO_AVAL = CA.NU_SEQ_CRITERIO_AVAL
					WHERE (CA.DS_SITUACAO = 'Aprovado' OR CA.DS_SITUACAO = 'Aprovado com destaque')
					AND T.ST_TURMA = 11 ";
            //filtro da EPE FNDE_EPE020_Certificado_Declaração/FNDE_EPE020_01_Emitir_Certificado
            if ($arParam['NU_SEQ_TIPO_PERFIL']) {
                switch ($arParam['NU_SEQ_TIPO_PERFIL']) {
                    case 5:
                        $sql .= " AND USU.NU_SEQ_USUARIO IN (SELECT DISTINCT(NU_SEQ_USUARIO_ARTICULADOR) FROM SICE_FNDE.S_TURMA) ";
                        break;
                    case 6:
                        $sql .= " AND USU.NU_SEQ_USUARIO IN (SELECT DISTINCT(NU_SEQ_USUARIO_TUTOR) FROM SICE_FNDE.S_TURMA) ";
                        break;
                    case 7:
                        //		$sql .= " AND CERT.NU_SEQ_TIPO_PERFIL = " . $arParam['NU_SEQ_TIPO_PERFIL'];
                        break;
                }
            }

            if ($arParam['SG_UF']) {
                $sql .= " AND USU.SG_UF_ATUACAO_PERFIL = '" . $arParam['SG_UF'] . "'";
            }
            if ($arParam['CO_MESORREGIAO']) {
                $sql .= " AND USU.CO_MESORREGIAO = " . $arParam['CO_MESORREGIAO'];
            }
            if ($arParam['CO_MUNICIPIO']) {
                $sql .= " AND MR.CO_MUNICIPIO_IBGE = " . $arParam['CO_MUNICIPIO'];
            }
            if ($arParam['NO_USUARIO']) {
                $sql .= " AND USU.NO_USUARIO LIKE '%" . $arParam['NO_USUARIO'] . "%'";
            }
            if ($arParam['NU_CPF']) {
                $sql .= " AND USU.NU_CPF = " . $arParam['NU_CPF'];
            }
            if ($arParam['NU_SEQ_CURSO']) {
                $sql .= " AND C.NU_SEQ_CURSO = " . $arParam['NU_SEQ_CURSO'];
            }

            //Retorna a parte da query que restringe o resultado pelo proprio usuário logado.
            //Dependendo do tipo do perfil.
            $sql .= $this->verificaUsuarioLogado($usuarioLogado, $arUsuario);

            $sql .= " ORDER BY USU.NO_USUARIO";
            /*echo '<pre>';
            var_dump();
            die();
            */
            $obModelo = new Fnde_Sice_Model_Usuario();
            $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm->fetchAll();
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Verifica o perfil do usuario e faz a restricao de resultados aprenas do proprio usuario
     * para os perfis de articulador, tutor e cursista.
     * @param array $usuarioLogado dados do usuario logado na sessao.
     * @param array $arUsuario dados do usuario do banco.
     */
    public function verificaUsuarioLogado($usuarioLogado, $arUsuario)
    {
        if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $usuarioLogado->credentials)) {
            $sql .= " AND T.NU_SEQ_USUARIO_ARTICULADOR = " . $arUsuario['NU_SEQ_USUARIO'];
        } elseif (in_array(Fnde_Sice_Business_Componentes::TUTOR, $usuarioLogado->credentials)) {
            $sql .= " AND T.NU_SEQ_USUARIO_TUTOR = " . $arUsuario['NU_SEQ_USUARIO'];
        } elseif (in_array(Fnde_Sice_Business_Componentes::CURSISTA, $usuarioLogado->credentials)) {
            $sql .= " AND VCT.NU_SEQ_USUARIO_CURSISTA = " . $arUsuario['NU_SEQ_USUARIO'];
        }

        return $sql;
    }

    /**
     * Retorna os dados do Cursista para gerar o PDF do Certificado.
     * @param $codCursista Codigo do cursista.
     * @param $codTurma Codigo da turma.
     * @throws Fnde_Business_Exception
     */
    public function getDadosCertificado($codCursista, $codTurma)
    {

        try {
            $sql = "SELECT C.NU_SEQ_CURSO,
					  USU.NO_USUARIO,
					  MU.NO_MUNICIPIO,
					  MU.SG_UF,
					  USU.NU_SEQ_USUARIO,
					  (TO_CHAR(T.DT_INICIO, 'DD/MM/YYYY')) AS DT_INICIO,
					  (TO_CHAR(T.DT_FIM, 'DD/MM/YYYY'))    AS DT_FIM,
					  C.VL_CARGA_HORARIA,
					  C.DS_NOME_CURSO,
					  USU_TUTOR.NO_USUARIO AS NO_USUARIO_TUTOR,
                                          (SELECT 1 FROM SICE_FNDE.S_AVALIACAO_CURSO
					    WHERE NU_SEQ_TURMA = T.NU_SEQ_TURMA
					    AND NU_SEQ_USUARIO = USU.NU_SEQ_USUARIO
					  ) AS ST_CURSO_AVAL
					FROM SICE_FNDE.S_USUARIO USU 
					INNER JOIN SICE_FNDE.S_VINC_CURSISTA_TURMA VCT ON USU.NU_SEQ_USUARIO = VCT.NU_SEQ_USUARIO_CURSISTA
					INNER JOIN SICE_FNDE.S_TURMA T ON VCT.NU_SEQ_TURMA = T.NU_SEQ_TURMA
					INNER JOIN SICE_FNDE.S_CURSO C ON T.NU_SEQ_CURSO = C.NU_SEQ_CURSO
					INNER JOIN CORP_FNDE.S_MUNICIPIO MU ON CAST(USU.CO_MUNICIPIO_PERFIL AS VARCHAR2(12)) = MU.CO_MUNICIPIO_IBGE
					INNER JOIN SICE_FNDE.S_USUARIO USU_TUTOR ON T.NU_SEQ_USUARIO_TUTOR = USU_TUTOR.NU_SEQ_USUARIO
					WHERE USU.NU_SEQ_USUARIO  = {$codCursista}
					AND T.NU_SEQ_TURMA        = {$codTurma} ";

            $obModelo = new Fnde_Sice_Model_Usuario();
            $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm->fetch();
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Atualiza o campo NU_CERTIFICADO com o número do certificado quando o PDF é salvo no Castor.
     * @param $codCursista Codigo do cursista.
     * @param $codTurma Codigo da turma.
     * @param $codCertificado Codigo do Certificado
     * @throws Fnde_Business_Exception
     */
    public function setNumeroCertificado($codCursista, $codTurma, $codCertificado)
    {

        try {
            $sql = "UPDATE SICE_FNDE.S_VINC_CURSISTA_TURMA
 					SET NU_CERTIFICADO            = $codCertificado
 					WHERE NU_SEQ_USUARIO_CURSISTA = $codCursista
 					AND NU_SEQ_TURMA              = $codTurma";

            $obModelo = new Fnde_Sice_Model_Usuario();
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm;
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retorna o número do certificado para consultar se ele já existe no castor.
     * @param $codCursista Codigo do cursista.
     * @param $codTurma Codigo da turma.
     * @throws Fnde_Business_Exception
     */

    public function getNumeroCertificado($codCursista, $codTurma)
    {
        try {
            $sql = "SELECT NU_CERTIFICADO
					FROM SICE_FNDE.S_VINC_CURSISTA_TURMA
					WHERE NU_SEQ_USUARIO_CURSISTA = $codCursista
					AND NU_SEQ_TURMA              = $codTurma";

            $obModelo = new Fnde_Sice_Model_Usuario();
            $stm = $obModelo->getAdapter()->query($sql);
            $arResultado = $stm->fetch();
            return $arResultado['NU_CERTIFICADO'];
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Recupera o nome de todos os modulos de um determinado curso.
     * @param $codCurso Codigo do curso.
     * @throws Fnde_Business_Exception
     */
    public function getNomeModulo($codCurso)
    {
        try {
            $sql = "SELECT M.DS_NOME_MODULO
					FROM SICE_FNDE.S_VINC_CURSO_MODULO VCM
					INNER JOIN SICE_FNDE.S_MODULO M ON VCM.NU_SEQ_MODULO = M.NU_SEQ_MODULO
					WHERE VCM.NU_SEQ_CURSO = {$codCurso} ";

            $obModelo = new Fnde_Sice_Model_Usuario();
            $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Recupera o conteudo programatico de todos os modulos de um determinado curso.
     * @param $codCurso Codigo do curso.
     * @throws Fnde_Business_Exception
     */
    public function getConteudoProgramatico($codCurso)
    {
        try {
            $sql = "SELECT M.DS_CONTEUDO_PROGRAMATICO
			FROM SICE_FNDE.S_VINC_CURSO_MODULO VCM
			INNER JOIN SICE_FNDE.S_MODULO M ON VCM.NU_SEQ_MODULO = M.NU_SEQ_MODULO
			WHERE VCM.NU_SEQ_CURSO = {$codCurso} ";

            $obModelo = new Fnde_Sice_Model_Usuario();
            $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

}
