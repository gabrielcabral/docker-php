<?php

/**
 * Created by PhpStorm.
 * User: 05922176633
 * Date: 20/01/2015
 * Time: 10:11
 */
class Fnde_Sice_Business_TermoCompromisso
{

	const DT_INICIO = '2015';

    public function getTermo($nu_seq_termo_compromisso)
    {
        $obModelo = new Fnde_Sice_Model_TermoCompromisso();
        $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
        return $obModelo->find($nu_seq_termo_compromisso)->current();
    }

    public function getAssinaturas($nu_seq_usuario, $nu_ano = null, $dt_fim = false)
    {
        $where = ($nu_ano) ? " and nu_ano = $nu_ano" : "";
        $where_dt_fim = ($dt_fim) ? " and dt_fim is null " : "";

        $obModelo = new Fnde_Sice_Model_TermoCompromisso();
        $result = $obModelo->select()
                            ->where("nu_seq_usuario = $nu_seq_usuario $where $where_dt_fim")
                            ->order(array("DT_INICIO DESC"))
                            ;

        return $result->query()->fetchAll();
    }

    public function getTermoAtivoUsuario($nu_seq_usuario, $nu_seq_tipo_perfil)
    {
        $obModelo = new Fnde_Sice_Model_TermoCompromisso();
        $result = $obModelo->select()
            ->where(" dt_fim is null and nu_seq_usuario = $nu_seq_usuario and nu_seq_tipo_perfil = $nu_seq_tipo_perfil ")
        ;

        return $result->query()->fetchAll();
    }

    public function innerJoinTermo($dt_inicio, $dt_fim, $nu_ano)
    {
        if ($nu_ano < 2015){ //aplica exigência de termo de compromisso somente para o ano de 2015 em diante
            return '';
        }

        //apenas bolsistas com termo assinado para receber bolsas terão direito a novas bolsas
        return $termo = " /*SGD 26371*/
			inner join sice_fnde.s_termo_compromisso ter on
                            ter.nu_seq_usuario = usu.nu_seq_usuario
                            -- RNS082
                            -- and ter.nu_seq_tipo_perfil = usu.nu_seq_tipo_perfil
                            and ter.nu_ano = '" . $nu_ano . "'
                            and ter.co_acao = '" . Fnde_Sice_Model_TermoCompromisso::CO_ACAO_COMBOLSA . "'

                            /*ou o termo esta aberto ou no periodo do vinculo existia um termo*/
                            and (
                                ter.dt_fim is null
                                or (
                                    " . $dt_inicio . " > ter.dt_inicio and " . $dt_inicio . " < ter.dt_fim
                                    and " . $dt_fim . " > ter.dt_inicio and " . $dt_fim . " < ter.dt_fim
                                )
                            )
            ";
    }

    public function salvar($arParams)
    {
        $obModelo = new Fnde_Sice_Model_TermoCompromisso();

        try {
            $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
            $obModelo->getAdapter()->beginTransaction();
            $usuarioInserido = null;

            if ($arParams['NU_SEQ_TERMO_COMPROMISSO']) {
                $where = "NU_SEQ_TERMO_COMPROMISSO = " . $arParams['NU_SEQ_TERMO_COMPROMISSO'];
                $obModelo->update($arParams, $where);
                $termoInserido = $arParams['NU_SEQ_TERMO_COMPROMISSO'];
            } else {
                $termoInserido = $obModelo->insert($arParams);
            }

            $obModelo->getAdapter()->commit();
            return $termoInserido;
        } catch (Exception $e) {
            $obModelo->getAdapter()->rollback();
            throw $e;
        }
    }

    public static function possuiTermo()
    {
        //pega o usuario logado
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        //verifica se o usuario tem perfil que precisa de termo
        $precisaTermo = false;

        foreach($usuarioLogado->credentials as $credential) {
            $precisaTermo = in_array($credential, Fnde_Sice_Model_TermoCompromisso::$arrPerfis);

            //armazena perfis atuais
            $perfis[] = $credential;
        }

        if ($precisaTermo) {
            //verifica se ja assinou o termo
            $objTermo = new Fnde_Sice_Business_TermoCompromisso();

            $result = $objTermo->getAssinaturas($arUsuario['NU_SEQ_USUARIO'], date('Y'), true);

            if (empty($result)) {
                return false;
            } else {
                //se já assinou
                //e não concorda com o termo, seta variaveis de permissao de acesso apenas para view
                if ($result['0']['CO_ACAO'] == Fnde_Sice_Model_TermoCompromisso::CO_ACAO_VIEW) {
                    foreach ($perfis as $perfil) {
                        $perfilVisualizador[] = $perfil . '_view';
                    }

                    $usuarioLogado->credentials = $perfilVisualizador;
                }

            }
        }

        return true;
    }
}
