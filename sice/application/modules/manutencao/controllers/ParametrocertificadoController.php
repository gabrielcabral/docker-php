<?php


/**
 * A��o de listagem
 *
 * @author leidison siqueira
 * @since 14/09/2016
 */
class Manutencao_ParametrocertificadoController extends Fnde_Sice_Controller_Action
{

    public function listAction()
    {
        $this->setTitle('Par�metros dos Certificados');
        $this->setSubtitle('Filtrar');

        //monta menu de contexto
        $menu = array($this->getUrl('manutencao', 'parametrocertificado', 'list', ' ') => 'filtrar',
            $this->getUrl('manutencao', 'parametrocertificado', 'cadastro', ' ') => 'cadastrar');
        $this->setActionMenu($menu);

        $form = new ParametroCertificado_FormFilter();
        $form->setMethod('post')
            ->setAction('filtro');

        $form->cancelar->setAttrib('data-url', $this->view->url(array('action' => 'limpar-filtro')));

        $zsn = new Zend_Session_Namespace('parametroCertificado.list.search');
        $form->populate((array)$zsn->filtro);

        try {
            $resultado = array();
            if ($zsn->filtro) {
                $business = new Fnde_Sice_Business_ParametroCertificado();
                $resultado = $business->lista($zsn->filtro);
                if (!$resultado) {
                    $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Nenhum registro localizado para o filtro informado.');
                }
            }
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Erro:' . $e->getMessage());
        }

        $this->view->form = $form;
        $this->view->resultado = $resultado;
    }

    public function filtroAction()
    {
        $zsn = new Zend_Session_Namespace('parametroCertificado.list.search');
        $zsn->filtro = $this->getRequest()->getPost();
        $this->_redirect('/manutencao/parametrocertificado/list');
    }

    public function limparFiltroAction()
    {
        $zsn = new Zend_Session_Namespace('parametroCertificado.list.search');
        $zsn->filtro = array();
        $this->_redirect('/manutencao/parametrocertificado/list');
    }

    public function cadastroAction()
    {
        set_time_limit(60);

        $this->setTitle('Par�metros dos Certificados');
        $this->setSubtitle('Cadastro');

        //monta menu de contexto
        $menu = array($this->getUrl('manutencao', 'parametrocertificado', 'list', ' ') => 'filtrar',
            $this->getUrl('manutencao', 'parametrocertificado', 'cadastro', ' ') => 'cadastrar');
        $this->setActionMenu($menu);

        $form = new ParametroCertificado_Form();
        $form->setMethod('post');

        $form->GerarCertificadoTeste->setAttrib('data-url', $this->view->url(array('action' => 'gerar-certificado-teste')));
        $form->confirmar->setAttrib('data-url', $this->view->url(array('action' => 'cadastro')));
        $form->cancelar->setAttrib('data-url', $this->view->url(array('action' => 'list')));

        $post = array();
        try {

            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
                if ($form->isValid($post)) {
                    $adapter = new Zend_File_Transfer();

                    if ($adapter->receive()) {
                        $files = $adapter->getFileInfo();

                        unset($post['NU_SEQ_PARAM_CERT']);

                        $model = new Fnde_Sice_Business_ParametroCertificado();
                        $model->insert($post, $files['NU_SEQ_LOGOMARCA_CASTOR']);

                        $form->reset();
                        $this->addInstantMessage(Fnde_Message::MSG_SUCCESS, 'Registro inclu�do com sucesso!');
                    } else {
                        $form->populate($post);
                        $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Nao foi poss�vel realizar o upload da logo');
                    }
                } else {
                    $form->populate($post);
                }
            }
        } catch (Exception $e) {
            $form->populate($post);
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
        }

        $this->view->form = $form;
        $this->render('form');
    }

    public function edicaoAction()
    {
        $this->setTitle('Par�metros dos Certificados');
        $this->setSubtitle('Edi��o');

        //monta menu de contexto
        $menu = array($this->getUrl('manutencao', 'parametrocertificado', 'list', ' ') => 'filtrar',
            $this->getUrl('manutencao', 'parametrocertificado', 'cadastro', ' ') => 'cadastrar');
        $this->setActionMenu($menu);

        //monta menu de contexto
        $menu = array($this->getUrl('manutencao', 'parametrocertificado', 'list', ' ') => 'filtrar',
            $this->getUrl('manutencao', 'parametrocertificado', 'cadastro', ' ') => 'cadastrar');
        $this->setActionMenu($menu);

        $form = new ParametroCertificado_Form();
        $form->setMethod('post');
        $form->NU_SEQ_LOGOMARCA_CASTOR->setRequired(false);

        $form->GerarCertificadoTeste->setAttrib('data-url', $this->view->url(array('action' => 'gerar-certificado-teste')));
        $form->confirmar->setAttrib('data-url', $this->_helper->url('edicao', null, null, array('id' => $this->getRequest()->getParam('id', 0))));
        $form->cancelar->setAttrib('data-url', $this->view->url(array('action' => 'list')));

        $model = new Fnde_Sice_Business_ParametroCertificado();

        $post = $this->getRequest()->getPost();

        try {

            try {
                $id = $this->getRequest()->getParam('id', 0);
                $dados = $model->obter($id);
            } catch (Exception $e) {
                $this->addMessage(Fnde_Message::MSG_ALERT, 'N�o foi poss�vel localizar o parametro. Erro: ' . $e->getMessage(), '/manutencao/parametrocertificado/list');
            }

            if ($dados) {
                $form->populate($post ? $post : $dados->toArray());

                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($post)) {

                        $post['NU_SEQ_PARAM_CERT'] = $id;

                        $adapter = new Zend_File_Transfer();

                        $files = $adapter->getFileInfo();

                        $model->update(
                            $post,
                            $files['NU_SEQ_LOGOMARCA_CASTOR']['name'] ? $files['NU_SEQ_LOGOMARCA_CASTOR'] : NULL
                        );

                        $form->populate($post);

                        $this->addInstantMessage(Fnde_Message::MSG_SUCCESS, $files['NU_SEQ_LOGOMARCA_CASTOR']['name'] ? 'Logo e registro atualizados com sucesso!' : 'Registro atualizado com sucesso!');
                    } else {
                        $form->populate($post);
                    }
                }
            } else {
                $this->addMessage(Fnde_Message::MSG_ALERT, 'N�o foi poss�vel localizar o parametro.', '/manutencao/parametrocertificado/list');
            }

        } catch (Exception $e) {
            $form->populate($post);
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
        }

        $this->view->form = $form;
        $this->render('form');
    }

    public function excluirAction()
    {
        try {
            $id = $this->getRequest()->getParam('id', 0);
            $model = new Fnde_Sice_Business_ParametroCertificado();
            $resultado = $model->excluir($id);
            if($resultado){
                $this->addMessage(Fnde_Message::MSG_SUCCESS, 'Registro exclu�do com sucesso!', '/manutencao/parametrocertificado/list');
            }else{
                $this->addMessage(Fnde_Message::MSG_ALERT, 'N�o foi poss�vel localizar o parametro.', '/manutencao/parametrocertificado/list');
            }
        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, 'N�o foi poss�vel excluir o parametro. Erro: ' . $e->getMessage(), '/manutencao/parametrocertificado/list');
        }

    }

    public function gerarCertificadoTesteAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        try {
            $post = $this->getRequest()->getPost();
            $post['validacao_simples'] = true;

            $form = new ParametroCertificado_Form();
            $form->NU_SEQ_LOGOMARCA_CASTOR->setRequired(empty($post['NU_SEQ_PARAM_CERT']));

            if ($form->isValid($post) && $this->getRequest()->isPost()) {
                $adapter = new Zend_File_Transfer();

                if ($adapter->receive()) {
                    // se tiver arquivo via upload
                    $files = $adapter->getFileInfo();
                    $caminhoLogo = $files['NU_SEQ_LOGOMARCA_CASTOR']['tmp_name'];
                } else if (!empty($post['NU_SEQ_PARAM_CERT'])) {
                    // pega o certificado da base de arquivos
                    $model = new Fnde_Sice_Business_ParametroCertificado();
                    $parametro = $model->obter($post['NU_SEQ_PARAM_CERT']);

                    if ($parametro) {

                        $options = Zend_Registry::get('config');
                        $castor = new Fnde_Model_Castor();

                        $file = $castor->view($parametro->NU_SEQ_LOGOMARCA_CASTOR, $options['app']['name']);

                        $caminhoLogo = tempnam('/tmp', 'certificado-teste-');

                        if ($caminhoLogo) {

                            $extensao = explode('/', $file->getHeader('Content-type'));
                            $extensao = isset($extensao[1]) ? ".{$extensao[1]}" : '';
                            $caminhoLogo .= $extensao;

                            $handle = fopen($caminhoLogo, 'w');

                            if ($handle) {
                                fwrite($handle, $file->getBody());
                            } else {
                                $this->addMessage(Fnde_Message::MSG_ALERT, 'N�o foi poss�vel recuperar o arquivo tempor�rio da logo.', '/');
                            }

                        } else {
                            $this->addMessage(Fnde_Message::MSG_ALERT, 'N�o foi poss�vel gerar o nome do arquivo tempor�rio da logo.', '/');
                        }
                    } else {
                        $this->addMessage(Fnde_Message::MSG_ERROR, 'N�o foi poss�vel recuperar o parametro.', '/');
                    }

                } else {
                    $this->addMessage(Fnde_Message::MSG_ALERT, 'Nao foi poss�vel realizar o upload da logo.', '/');
                }

                $post = array_merge($post, array(
                    'LOGO_GOVERNO' => $caminhoLogo,
                    'NO_USUARIO' => 'Nome do cursista',
                    'DS_NOME_MODULO' => 'M�dulo Introdut�rio',
                    'NO_MUNICIPIO' => 'Nome do Munic�pio',
                    'SG_UF' => 'DF',
                    'VL_CARGA_HORARIA' => 80,
                    'COD_IDENTIFICADOR' => 'c�digo da assinatura',
                    'DS_NOME_CURSO' => 'Nome do Curso',
                    'NO_USUARIO_TUTOR' => 'Nome do Tutor',
                    'DS_CONTEUDO_PROGRAMATICO' => 'O Programa Nacional de Fortalecimento dos Conselhos Escolares. Hist�ria dos conselhos no Brasil: forma��o dos espa�os democr�ticos. Organiza��o e funcionamento do Conselho Escolar: o di�logo na diversidade. Conselho Escolar e a dimens�o pol�tico-pedag�gica. As fun��es deliberativa e consultiva do Conselho Escolar. A fun��o fiscal do Conselho Escolar. A fun��o mobilizadora do Conselho Escolar. A fun��o pedag�gica do Conselho Escolar. Conselho Escolar e a Qualidade da Educa��o P�blica.'
                ));

                $this->view->dados = $post;
                $this->view->donwload = true;

                $this->view->setScriptPath(APPLICATION_PATH . '/modules/secretaria/views/scripts/');
                $this->view->render('emitircertificado/gerar-para-cursista.phtml');

            } else {
                $this->addMessage(Fnde_Message::MSG_ALERT, 'Os dados informados n�o s�o v�lidos', '/');
            }
        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, 'N�o foi poss�vel gerar o certificado de teste. Erro: ' . $e->getMessage(), '/');
        }

    }


}