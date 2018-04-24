<?php

/**
 * Controller do Emitir Certificado
 *
 * @author leidison siqueira
 * @since 05/10/2016
 */
class Secretaria_EmitirCertificadoController extends Fnde_Sice_Controller_Action
{

    public function init()
    {

    }

    public function listAction()
    {
        $business = new Fnde_Sice_Business_EmitirCertificado();

        $this->setTitle('Emitir Certificado');
        $this->setSubtitle('Filtrar');

        //monta menu de contexto
        $menu = array($this->getUrl('secretaria', 'emitircertificado', 'list', ' ') => 'filtrar');
        $this->setActionMenu($menu);

        //Mensagem de orientação
        $msg = "O certificado somente será emitido para o cursista aprovado e que tenha realizado a avaliação institucional do curso.";
        $this->addInstantMessage(Fnde_Message::MSG_INFO, $msg);

        $permissoes = $business->permissoes();

        $zsn = new Zend_Session_Namespace('emitircertificado.list.search');
        $zsn->filtro = (array)$zsn->filtro;

        $form = new EmitirCertificado_FormFilter();
        $form->setMethod('post')
            ->setAction('filtro');

        $form->cancelar->setAttrib('data-url', $this->view->url(array('action' => 'limpar-filtro')));

        $form->encadeiaCombos($zsn->filtro);
        $form->carregaCursos($zsn->filtro);
        $form->populate($zsn->filtro);

        if ($zsn->filtro && $form->isValid($zsn->filtro)) {
            try {

                $resultado = $business->lista($zsn->filtro);
                if (!$resultado) {
                    $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Nenhum registro localizado para o filtro informado.');
                }

            } catch (Exception $e) {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Erro:' . $e->getMessage());
            }

            // especifica os botões que vao aparecer na view

            if ($zsn->filtro['NU_SEQ_TIPO_PERFIL'] == Fnde_Sice_Business_PerfilUsuario::ARTICULADOR) {
                $botoes = array(
                    'declaracao_articulador'
                );
                $this->view->tituloGrid = 'Listagem de Articuladores';
            } else if ($zsn->filtro['NU_SEQ_TIPO_PERFIL'] == Fnde_Sice_Business_PerfilUsuario::CURSISTA) {
                $botoes = array(
                    'certificado_cursista'
                );
                if (isset($permissoes['notificar_cursista'])) {
                    $botoes[] = 'notificar_cursista';
                }
                if (isset($permissoes['avaliar_curso'])) {
                    $botoes[] = 'avaliar_curso';
                }
                $this->view->tituloGrid = 'Listagem de Cursistas';
            } else if ($zsn->filtro['NU_SEQ_TIPO_PERFIL'] == Fnde_Sice_Business_PerfilUsuario::TUTOR) {
                $botoes = array(
                    'certificado_tutor'
                );
                $this->view->tituloGrid = 'Listagem de Tutores';
            }

            $this->view->botoes = $botoes;
        }

        $zsn = new Zend_Session_Namespace('emitircertificado.list.download');
        $this->view->file = $zsn->file;
        $zsn->file = '';

        $this->view->form = $form;
        $this->view->resultado = $resultado;
    }

    public function downloadZipAction()
    {
        try {
            if ($this->getRequest()->getParam('file')) {
                $file = APPLICATION_ROOT . '/public/' . $this->getRequest()->getParam('file');
                Fnde_Sice_Business_Componentes::downloadZip($file, $this->getRequest()->getParam('file'));

            } else {
                throw new Exception('Não foi informado um arquivo para download.');
            }

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
            $this->_redirect("/secretaria/emitircertificado/list");
        }
    }

    public function filtroAction()
    {
        $zsn = new Zend_Session_Namespace('emitircertificado.list.search');
        $zsn->filtro = $this->getRequest()->getPost();
        $this->_redirect('/secretaria/emitircertificado/list');
    }

    public function limparFiltroAction()
    {
        $zsn = new Zend_Session_Namespace('emitircertificado.list.search');
        $zsn->filtro = array();
        $this->_redirect('/secretaria/emitircertificado/list');
    }

    /**
     * Renderiza o combo de mesorregiao e municipio de acordo com a uf selecionada.
     */
    public function ufChangeAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        $arParam = $this->_getAllParams();

        $form = new EmitirCertificado_FormFilter();

        $business = new Fnde_Sice_Business_EmitirCertificado();
        $permissoes = $business->permissoes();

        $form->encadeiaMesorregiao($arParam, $permissoes);
        $form->encadeiaMunicipio($arParam, $permissoes);
        $form->carregaCursos($arParam);

        function moon(&$item, $key)
        {
            $item = utf8_encode($item);
        }

        $meso = $form->CO_MESORREGIAO->getMultiOptions();
        $municipio = $form->CO_MUNICIPIO->getMultiOptions();
        $curso = $form->NU_SEQ_CURSO->getMultiOptions();

        array_walk($meso, 'moon');
        array_walk($municipio, 'moon');
        array_walk($curso, 'moon');

        return $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(json_encode(array(
                'mesorregiao' => $meso,
                'municipio' => $municipio,
                'curso' => $curso,
            )));

    }

    /**
     * Renderiza o combo de municipio de acordo com a mesorregiao selecionada.
     */
    public function mesorregiaoChangeAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        $arParam = $this->_getAllParams();

        $form = new EmitirCertificado_FormFilter();

        $business = new Fnde_Sice_Business_EmitirCertificado();
        $permissoes = $business->permissoes();

        $form->encadeiaMunicipio($arParam, $permissoes);

        function moon(&$item, $key)
        {
            $item = utf8_encode($item);
        }

        $municipio = $form->CO_MUNICIPIO->getMultiOptions();

        array_walk($municipio, 'moon');

        return $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(json_encode($municipio));
    }

    public function gerarParaCursistaAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        $arParam = $this->getRequest()->getParams();

        $business = new Fnde_Sice_Business_EmitirCertificado();

        try {
            $arDados = $business->dadosParaCursista($arParam['NU_SEQ_USUARIO'], $arParam['NU_SEQ_TURMA']);

            $codigoCertificado = $this->gerarCertificado($arDados, 'cursista');

            $options = Zend_Registry::get('config');
            $this->_redirect($options['webservices']['castor']['uri'] . "view/nu_seq_arquivo/" . $codigoCertificado . "/sg_aplicacao/" . $options['app']['name']);

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/');
        }
    }

    public function gerarParaTutorAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        $arParam = $this->getRequest()->getParams();

        $business = new Fnde_Sice_Business_EmitirCertificado();

        try {
            $arDados = $business->dadosParaTutor($arParam['NU_SEQ_USUARIO'], $arParam['NU_SEQ_TURMA']);

            $codigoCertificado = $this->gerarCertificado($arDados, 'tutor');

            $options = Zend_Registry::get('config');
            $this->_redirect($options['webservices']['castor']['uri'] . "view/nu_seq_arquivo/" . $codigoCertificado . "/sg_aplicacao/" . $options['app']['name']);

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/');
        }
    }

    public function gerarParaArticuladorAction()
    {
        $this->getHelper('Layout')
            ->disableLayout();

        $this->_helper->viewRenderer->setNoRender(true);

        $arParam = $this->getRequest()->getParams();

        $business = new Fnde_Sice_Business_EmitirCertificado();
        try {
            $arDados = $business->dadosParaDeclaracaoArticulador($arParam['NU_SEQ_USUARIO'], $arParam['NU_SEQ_PERIODO_VINCULACAO']);

            $codigoCertificado = $this->gerarCertificado($arDados, 'articulador');

            $options = Zend_Registry::get('config');
            $this->_redirect($options['webservices']['castor']['uri'] . "view/nu_seq_arquivo/" . $codigoCertificado . "/sg_aplicacao/" . $options['app']['name']);

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/');
        }
    }


    public function gerarCertificado($arDados, $tipo)
    {

        $business = new Fnde_Sice_Business_EmitirCertificado();

        $options = Zend_Registry::get('config');
        $castor = new Fnde_Model_Castor();

        try {
            $file = $castor->view($arDados['NU_SEQ_LOGOMARCA_CASTOR'], $options['app']['name']);
        }catch(Exception $e){
            throw new Exception('Problema com o WS do CASTOR: ' . $e->getMessage());
        }
        $caminhoLogo = tempnam('/tmp', 'logo-certificado-');

        if ($caminhoLogo) {

            $extensao = explode('/', $file->getHeader('Content-type'));
            $extensao = isset($extensao[1]) ? ".{$extensao[1]}" : '';
            $caminhoLogo .= $extensao;

            $handle = fopen($caminhoLogo, 'w');

            if ($handle) {
                fwrite($handle, $file->getBody());
            } else {
                throw new Exception($arDados['NO_USUARIO'] . ': Não foi possível recuperar o arquivo temporário da logo.');
            }

        } else {
            throw new Exception($arDados['NO_USUARIO'] . ': Não foi possível gerar o nome do arquivo temporário da logo.');
        }


        $arDados['LOGO_GOVERNO'] = $caminhoLogo;

        $this->view->dados = $arDados;
        $this->view->nomeArquivo = tempnam('/tmp', 'certificado-') . '.pdf';

        $this->view->download = false;

        if ($tipo == 'cursista') {
            $this->view->render('emitircertificado/gerar-para-cursista.phtml');
        } else if ($tipo == 'tutor') {
            $this->view->render('emitircertificado/gerar-para-tutores.phtml');
        } else if ($tipo == 'articulador') {
            $this->view->render('emitircertificado/gerar-declaracao-articulador.phtml');
        }

        $result = $business->assinarCertificado($this->view->nomeArquivo);

        $arDados['COD_IDENTIFICADOR'] = $result;

        $this->view->dados = $arDados;

        if ($tipo == 'cursista') {

            $id = $arDados['NU_MATRICULA'];

            $this->view->render('emitircertificado/gerar-para-cursista.phtml');
        } else if ($tipo == 'tutor') {

            $id = $arDados['NU_SEQ_USUARIO'];

            $this->view->render('emitircertificado/gerar-para-tutores.phtml');
        } else if ($tipo == 'articulador') {
            $id = $arDados['NU_SEQ_USUARIO'];

            $this->view->render('emitircertificado/gerar-declaracao-articulador.phtml');
        }
        $codCertificado = $business->salvarCertificadoGerado($tipo, $id, $this->view->nomeArquivo);

        return $codCertificado;
    }

    public function gerarVariosParaCursistaAction()
    {

        try {
            $business = new Fnde_Sice_Business_EmitirCertificado();
            $arParam = $this->getRequest()->getParams();

            $mensagem_multiplos = '';
            $mensagem_multiplosNaoEmitidos = '';
            $arCertificados = array();
            foreach ($arParam['usuario'] as $index => $value) {
                try {

                    $arDados = $business->dadosParaCursista($arParam['usuario'][$index], $arParam['turma'][$index]);
                    $codigoCertificado = $this->gerarCertificado($arDados, 'cursista');
                    $arCertificados[$index]['nu_seq_arquivo'] = $codigoCertificado;
                    $arCertificados[$index]['no_arquivo'] = 'certificado_' . $arDados['NO_USUARIO'] . '.pdf';

                    $mensagem_multiplos .= '- ' . $arDados['NO_USUARIO'] . '<br />';

                } catch (Exception $e) {
                    $mensagem_multiplosNaoEmitidos .= "- {$e->getMessage()}<br />";
                }
            }

            if ($mensagem_multiplos) {
                $mensagem_multiplos = 'Os certificados solicitados foram emitidos com sucesso, exceto aqueles dos cursistas que ainda não fizeram a Avaliação Institucional do Curso, conforme lista abaixo:<br />'
                    . $mensagem_multiplos;
                $this->addMessage(Fnde_Message::MSG_SUCCESS, $mensagem_multiplos);
            }
            if ($mensagem_multiplosNaoEmitidos) {
                $mensagem_multiplosNaoEmitidos = 'Os certificados abaixo não foram emitidos:<br />'
                    . $mensagem_multiplosNaoEmitidos;
                $this->addMessage(Fnde_Message::MSG_ALERT, $mensagem_multiplosNaoEmitidos);
            }

            if (!empty($arCertificados)) {
                $file = Fnde_Sice_Business_Componentes::generateZip($arCertificados);
                $zsn = new Zend_Session_Namespace('emitircertificado.list.download');
                $zsn->file = $file;

            }
            $this->_redirect("/secretaria/emitircertificado/list");

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/secretaria/emitircertificado/list');
        }
    }

    public function tutorAction()
    {
        //monta menu de contexto
        $menu = array($this->getUrl('secretaria', 'emitircertificado', 'list', ' ') => 'filtrar');
        $this->setActionMenu($menu);

        $params = $this->getRequest()->getParams();
        try {
            $model = new Fnde_Sice_Business_EmitirCertificado();
            $tutor = $model->getDadosResumidosTutor($params['NU_SEQ_USUARIO']);
            $turmas = $model->getTurmasTutorCurso($params['NU_SEQ_USUARIO'], $params['NU_SEQ_CURSO']);

            $this->setTitle('Emitir Certificado');
            $this->setSubtitle("Tutor");

            $this->view->turmas = $turmas;
            $this->view->tutor = $tutor;

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/secretaria/emitircertificado/list');
        }
    }

    public function articuladorAction()
    {
        //monta menu de contexto
        $menu = array($this->getUrl('secretaria', 'emitircertificado', 'list', ' ') => 'filtrar');
        $this->setActionMenu($menu);

        $params = $this->getRequest()->getParams();
        try {
            $model = new Fnde_Sice_Business_EmitirCertificado();
            $articulador = $model->getDadosResumidosArticulador($params['NU_SEQ_USUARIO']);
            $periodos = $model->getPeriodosArticulador($params['NU_SEQ_USUARIO'], $params['NU_SEQ_CURSO']);

            $this->setTitle('Emitir Declaração');
            $this->setSubtitle("Articulador");
            $this->view->periodos = $periodos;
            $this->view->articulador = $articulador;

        } catch (Exception $e) {
            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage(), '/secretaria/emitircertificado/list');
        }
    }

}
