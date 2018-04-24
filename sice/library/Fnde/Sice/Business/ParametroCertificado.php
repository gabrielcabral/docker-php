<?php

class Fnde_Sice_Business_ParametroCertificado
{

    public function getPorData($data){
        $model = new Fnde_Sice_Model_ParametroCertificado();
        return $model->getPorData($data);
    }

    /**
     * Seleciona o secretário responsável pelo certificado
     * @throws Fnde_Business_Exception
     * @return mixed
     * @return 12/04/2013
     */
    public function getResponsavelCertificado()
    {
        try {
            $sql = "SELECT NO_SECRETARIO, NO_CARGO, NO_LOCAL_ATUACAO, 'col removida' as DS_LOGIN
			FROM SICE_FNDE.S_PARAMETRO_CERTIFICADO";

            $obModelo = new Fnde_Sice_Model_ParametroCertificado();
            $stm = $obModelo->getAdapter()->query($sql);
            return $stm->fetch();
        } catch (Exception $e) {
            throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Atualiza os dados do secretário responsável pelo certificado
     * @param unknown_type $arDados
     */
    public function setAtualizaResponsavel($arDados)
    {
        $obModelo = new Fnde_Sice_Model_ParametroCertificado();
        $obModelo->update($arDados, "1=1");
    }

    /**
     * Verifica se o ds_login informado é realmente do segweb
     * @param unknown_type $ds_login
     */
    public function isUserSegWeb($ds_login)
    {
//            try {
        $sql = "select u.DS_LOGIN, ud.NO_USUARIO from segweb_fnde.s_usuario u
                            inner join sice_fnde.s_usuario ud on ud.nu_seq_usuario_segweb = u.nu_seq_usuario
                            WHERE upper(u.DS_LOGIN) = '" . strtoupper($ds_login) . "'";

        $obModelo = new Fnde_Sice_Model_ParametroCertificado();
        $stm = $obModelo->getAdapter()->query($sql);
        $result = $stm->fetch();
        return $result;
//		} catch ( Exception $e ) {
//			throw new Fnde_Business_Exception($e->getMessage(), $e->getCode());
//		}
    }

    /**
     * Efetua a pesquisa dos cursos cadastrado de acordo com o filtro informado.
     * @param array $arParams
     */
    public function lista($filtro)
    {
        $model = new Fnde_Sice_Model_ParametroCertificado();
        return $model->lista((array)$filtro);
    }

    public function update($data, $file = null)
    {

        try {

            $model = new Fnde_Sice_Model_ParametroCertificado();

            $parametroMaisAtual = $model->getMaisAtual($data['NU_SEQ_PARAM_CERT']);

            if ($parametroMaisAtual && empty($parametroMaisAtual->DT_FIM)) {

                list($dayI, $monthI, $yearI) = sscanf($data['DT_INICIO'], '%02d/%02d/%04d');
                $dayI = str_pad($dayI, 2, '0', STR_PAD_LEFT);
                $monthI = str_pad($monthI, 2, '0', STR_PAD_LEFT);
                $dataFim = new DateTime("$yearI-$monthI-$dayI");

                list($dayI, $monthI, $yearI) = sscanf($parametroMaisAtual->DT_INICIO, '%02d/%02d/%04d');
                $dayI = str_pad($dayI, 2, '0', STR_PAD_LEFT);
                $monthI = str_pad($monthI, 2, '0', STR_PAD_LEFT);
                $dataInicioParamAtual = new DateTime("$yearI-$monthI-$dayI");

                if($dataFim->format('Y-m-d') > $dataInicioParamAtual->format('Y-m-d')) {

                    $dataFim->modify('-1 day');

                    $dataFim = $dataFim->format('d/m/Y');

                    $parametroMaisAtual->DT_FIM = new Zend_Db_Expr("to_date('{$dataFim}','DD/MM/YYYY')");

                    $parametroMaisAtual->setReadOnly(false);
                    $parametroMaisAtual->save();
                }
            }

            $data['DT_INICIO'] = new Zend_Db_Expr("to_date('{$data['DT_INICIO']}','DD/MM/YYYY')");
            $data['DT_FIM'] = new Zend_Db_Expr("to_date('{$data['DT_FIM']}','DD/MM/YYYY')");

            $usuarioLogado = (array)Zend_Auth::getInstance()->getIdentity();

            if ($file) {
                $type = explode('/', $file['type']);

                try {
                    $castor = new Fnde_Model_Castor();
                    $coCastor = $castor->write($usuarioLogado['nu_seq_usuario'], $file['tmp_name'], $file['name'], $type[1]);

                    $data['DS_FRASE_EFEITO'] = $file['name'];
                    $data['NU_SEQ_LOGOMARCA_CASTOR'] = $coCastor;

                } catch (Exception $e) {
                    throw new Exception('Castor: ' . $e->getMessage());
                }
            }

            $modelUser = new Fnde_Sice_Model_Usuario();
            $dadosUser = $modelUser->getUserByIdSEGWEB($usuarioLogado['nu_seq_usuario']);
            // por enquanto o ds_slogan armazena o nome do arquivo. o certo era um texto de efeito do governo
            $data['NU_SEQ_USUARIO_ATUALIZADOR'] = $dadosUser['NU_SEQ_USUARIO'];

            $where = $model->getAdapter()->quoteInto('NU_SEQ_PARAM_CERT = ?', $data['NU_SEQ_PARAM_CERT']);

            $return = $model->update($data, $where);

            return $return;

        } catch (Exception $e) {

            throw $e;
        }
    }

    public function insert(array $data, $file)
    {
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
            $model = new Fnde_Sice_Model_ParametroCertificado();

            $parametroMaisAtual = $model->getMaisAtual();


            if ($parametroMaisAtual && empty($parametroMaisAtual->DT_FIM)) {

                list($dayI, $monthI, $yearI) = sscanf($data['DT_INICIO'], '%02d/%02d/%04d');
                $dayI = str_pad($dayI, 2, '0', STR_PAD_LEFT);
                $monthI = str_pad($monthI, 2, '0', STR_PAD_LEFT);
                $dataFim = new DateTime("$yearI-$monthI-$dayI");

                list($dayI, $monthI, $yearI) = sscanf($parametroMaisAtual->DT_INICIO, '%02d/%02d/%04d');
                $dayI = str_pad($dayI, 2, '0', STR_PAD_LEFT);
                $monthI = str_pad($monthI, 2, '0', STR_PAD_LEFT);
                $dataInicioParamAtual = new DateTime("$yearI-$monthI-$dayI");

                if($dataFim->format('Y-m-d') > $dataInicioParamAtual->format('Y-m-d')) {

                    $dataFim->modify('-1 day');

                    $dataFim = $dataFim->format('d/m/Y');

                    $parametroMaisAtual->DT_FIM = new Zend_Db_Expr("to_date('{$dataFim}','DD/MM/YYYY')");

                    $parametroMaisAtual->setReadOnly(false);
                    $parametroMaisAtual->save();
                }
            }

            $data['DT_INICIO'] = new Zend_Db_Expr("to_date('{$data['DT_INICIO']}','DD/MM/YYYY')");
            $data['DT_FIM'] = new Zend_Db_Expr("to_date('{$data['DT_FIM']}','DD/MM/YYYY')");

            $usuarioLogado = (array)Zend_Auth::getInstance()->getIdentity();

            $type = explode('/', $file['type']);

            try {
                $castor = new Fnde_Model_Castor();
                $coCastor = $castor->write($usuarioLogado['nu_seq_usuario'], $file['tmp_name'], $file['name'], $type[1]);
            } catch (Exception $e) {
                throw new Exception('Castor: ' . $e->getMessage());
            }

            $modelUser = new Fnde_Sice_Model_Usuario();
            $dadosUser = $modelUser->getUserByIdSEGWEB($usuarioLogado['nu_seq_usuario']);
            // por enquanto o ds_slogan armazena o nome do arquivo. o certo era um texto de efeito do governo
            $data['DS_FRASE_EFEITO'] = $file['name'];
            $data['NU_SEQ_LOGOMARCA_CASTOR'] = $coCastor;
            $data['NU_SEQ_USUARIO_ATUALIZADOR'] = $dadosUser['NU_SEQ_USUARIO'];
            $data = array_intersect_key($data, array_flip($model->_cols));

            $return = $model->insert($data);

            Zend_Db_Table::getDefaultAdapter()->commit();

            return $return;

        } catch (Exception $e) {

            Zend_Db_Table::getDefaultAdapter()->rollBack();

            throw $e;
        }
    }

    public function excluir($id)
    {
        $model = new Fnde_Sice_Model_ParametroCertificado();
        return $model->delete(array('NU_SEQ_PARAM_CERT = ?' => $id));
    }

    public function conflitoData($dataInicio, $dataFim = null, $idExcessao = null)
    {
        $model = new Fnde_Sice_Model_ParametroCertificado();
        return $model->conflitoData($dataInicio, $dataFim, $idExcessao);
    }

    public function obter($id)
    {
        $model = new Fnde_Sice_Model_ParametroCertificado();
        return $model->find($id);
    }
}