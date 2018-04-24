<?php

/**
 * Controller do Turma
 *
 * @author diego.matos
 * @since 25/04/2012
 */
class Secretaria_TurmaController extends Fnde_Sice_Controller_Action
{

    /**
     * Ação de listagem
     *
     * @author diego.matos
     * @since 25/04/2012
     */
    public function listAction()
    {
        $session = new Zend_Session_Namespace('secretaria.moodle.msg');
        if ($session->donwloadArquivo) {
            $this->addInstantMessage($session->donwloadArquivo['tipo'], $session->donwloadArquivo['msg']);
            unset($session->donwloadArquivo);
        }

        if (!Fnde_Sice_Business_Componentes::permitirAcesso()) {
            $this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
            $this->_redirect('index');
        }

        //Retirando os valores de cursistas da sessao, usado na tela matricular cursistas
        $_SESSION['rsCursista'] = null;

        $this->setTitle('Turma');
        $this->setSubtitle('Filtrar');

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        //monta menu de contexto
        $urlFiltrar = $this->getUrl('secretaria', 'turma', 'list', ' ');
        $urlCadastrar = $this->getUrl('secretaria', 'turma', 'form', ' ');

        $menu = Fnde_Sice_Business_Componentes::montaMenuContextoTurma($perfilUsuario, $urlFiltrar, $urlCadastrar);
        $this->setActionMenu($menu);

        //seta novos valores na sessão
        if ($this->_request->isPost()) {
            $this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
            $this->urlFilterNamespace->param = $this->_getAllParams();
        }

        //recupera valores da sessão
        $arFilter = $this->getSearchParamTurma();

        $form = $this->getFormFilter($arFilter, $perfilUsuario, $arUsuario);

        $rsRegistros = array();

        if ($this->isPostValido($this->_request->isPost(), $arFilter)) {
            if ($form->isValid($arFilter)) {

                $obBusiness = new Fnde_Sice_Business_Turma();
                $arParams = array();

                foreach ($form->getElements() as $elemento) {
                    if (!Fnde_Sice_Business_Componentes::isEmpty($elemento->getValue())) {
                        $arParams[$elemento->getName()] = $elemento->getValue();
                    }
                }

                $rsRegistros = $obBusiness->pesquisaTurma($arParams, false, $arUsuario);

                if (!count($rsRegistros)) {
                    $this->addInstantMessage(Fnde_Message::MSG_INFO,
                        'Nenhum registro localizado para o filtro informado.');
                }
            } else {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
                $this->addInstantMessage(Fnde_Message::MSG_ERROR,
                    Fnde_Sice_Business_Componentes::listarCamposComErros($form));
            }
        }

        $obCOnfiguracao = new Fnde_Sice_Business_Configuracao();
        $configAtiva = $obCOnfiguracao->getConfigAtiva();

        //chama filtro form
        $this->view->formFilter = $form;

        foreach ($rsRegistros as $k => $registro) {
            if ($configAtiva == $registro['NU_SEQ_CONFIGURACAO']) {
                $rsRegistros[$k]['STATUS_CONFIGURACAO'] = 'Ativa';
            } else {
                $rsRegistros[$k]['STATUS_CONFIGURACAO'] = 'Inativa(' . $registro['DT_INICIO'] . '~' . $registro['DT_FIM'] . ')';
            }
        }

        //Chamando componente zend.grid dentro do helper
        if ($rsRegistros) {
            $this->view->rowAction = $this->definirAcoesGrid($perfilUsuario);
            $this->view->arrayMaisAcoes = $this->definirOpcoesComboMaisAcoes($perfilUsuario);
            $this->view->rsRegistros = $rsRegistros;
        }
    }

    /**
     * Verifica se o post vindo da tela de filtro possui os parâmetros para prosseguir com a pesquisa
     * @param unknown_type $post
     * @param array $arFilter
     */
    private function isPostValido($post, $arFilter)
    {
        if ($post || isset($arFilter['startlist']) || isset($arFilter['start']) || !empty($arFilter)) {
            return true;
        } else {
            return false;
        }
    }


    public function getConfiguracaoAction(){
        $this->_helper->layout()->disableLayout();
        $obBusinessTurma = new Fnde_Sice_Business_Turma();
        $turma = $obBusinessTurma->getTurmaById($this->getRequest()->getParam("NU_SEQ_TURMA"));

        $dataInicio = date("d/m/Y",strtotime($turma['DT_INICIO']));
        $dataFim = date("d/m/Y",strtotime($turma['DT_FIM']));


        $msg = 'Não é possível Editar a Turma. <br>
         Configuração Inativa com vigência no período ('.$dataInicio.' - '.$dataFim.')';

        echo $msg;
        exit();

    }

    /**
     * Função para especificar o rowAction dos resultados da pesquisa baseado no perfil do usuário.
     * @author diego.matos
     * @since 07/11/2012
     * @param array $perfilUsuario
     */
    public function definirAcoesGrid($perfilUsuario)
    {
        if (
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)
        ) {
            $rowAction = array(
                'visualizar' => array('label' => 'Visualisar',
                    'url' => $this->view->Url(array('action' => 'visualizar-turma', 'NU_SEQ_TURMA' => ''))
                        . '%s', 'params' => array('NU_SEQ_TURMA'), 'title' => 'Visualisar',
                    'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
                'edit' => array('label' => 'editar',
                    'url' => $this->view->Url(array('action' => 'form', 'NU_SEQ_TURMA' => '')) . '%s',
                    'params' => array('NU_SEQ_TURMA'),
                    'attribs' => array('class' => 'icoEditar', 'title' => 'Editar')),
                'delete' => array('label' => 'Excluir',
                    'url' => $this->view->Url(array('action' => 'remover-turma', 'NU_SEQ_TURMA' => '')) . '%s',
                    'params' => array('NU_SEQ_TURMA'),
                    'attribs' => array('class' => 'icoExcluir excluir', 'title' => 'Excluir',
                        'mensagem' => 'Deseja realmente excluir a turma selecionada?')));
        } else {
            $rowAction = array(
                'visualizar' => array('label' => 'Visualisar',
                    'url' => $this->view->Url(array('action' => 'visualizar-turma', 'NU_SEQ_TURMA' => ''))
                        . '%s', 'params' => array('NU_SEQ_TURMA'), 'title' => 'Visualisar',
                    'attribs' => array('class' => 'icoVisualizar', 'title' => 'Visualizar')),
                'edit' => array('label' => 'editar', 'url' => "#e", 'params' => array('NU_SEQ_TURMA'),
                    'attribs' => array('class' => 'icoEditar disabled', 'title' => 'Editar')),
                'delete' => array('label' => 'Excluir', 'url' => '#x', 'params' => array('NU_SEQ_TURMA'),
                    'attribs' => array('class' => 'icoExcluir excluir disabled', 'mensagem' => '',
                        'title' => 'Excluir')));
        }

        return $rowAction;
    }

    /**
     * Função para definir as opções do combo de mais ações baseado no perfil do usuário.
     * @author diego.matos
     * @since 09/11/2012
     */
    public function definirOpcoesComboMaisAcoes($perfilUsuario)
    {
        $arrayMaisAcoes = array();

        if (
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)
        ) {
            $arrayMaisAcoes["Visualizar Histórico"] = $this->getUrl('secretaria', 'historicoturma', 'carregar-historico', true);
        }

        if (
            in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $arrayMaisAcoes["Autorizar Turma"] = $this->getUrl('secretaria', 'autorizarnaoautorizar', 'carregar-autorizar', true);
            $arrayMaisAcoes["Cancelar Turmas"] = $this->getUrl('secretaria', 'cancelarturma', 'carregar-cancelar', true);
        }

        if (
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $arrayMaisAcoes["Gerar arquivo moodle"] = $this->getUrl('secretaria', 'gerararquivomoodle', 'carregar-turma', true);
            $arrayMaisAcoes["Solicitar Autorização"] = $this->getUrl('secretaria', 'solicitarautorizacao', 'carregar-autorizacao', true);
            $arrayMaisAcoes["Solicitar Cancelamento"] = $this->getUrl('secretaria', 'solicitarcancelamento', 'carregar-cancelamento', true);
        }

        if (
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $arrayMaisAcoes["Notificar Cursistas para aval. curso"] = $this->getUrl('secretaria', 'notificarcursista', 'notificarcursista');
        }

        if (
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $arrayMaisAcoes["Matricular Cursistas"] = $this->getUrl('secretaria', 'vinccursistaturma', 'carregar-turma', true);
        }

        return $arrayMaisAcoes;
    }

    /**
     * Monta o formulário e renderiza na view
     *
     * @access public
     *
     * @author diego.matos
     * @since 25/04/2012
     */
    public function formAction()
    {
        $this->setTitle('Turma');
        if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
            $this->setSubtitle('Editar');
        } else {
            $this->setSubtitle('Cadastrar');
        }

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $turma = new Fnde_Sice_Business_Turma();

        if ($this->getRequest()->getParam("NU_SEQ_TURMA")
            && $turma->isTurmaComBolsa($this->getRequest()->getParam("NU_SEQ_TURMA"))
        ) {
            $this->addMessage(Fnde_Message::MSG_ERROR,
                "A turma não pode ser editada, pois existem bolsas lançadas neste período.");
            $this->_redirect("/secretaria/turma/list");
        }

        //monta menu de contexto
        $urlFiltrar = $this->getUrl('secretaria', 'turma', 'list', ' ');
        $urlCadastrar = $this->getUrl('secretaria', 'turma', 'form', ' ');
        $menu = Fnde_Sice_Business_Componentes::montaMenuContextoTurma($perfilUsuario, $urlFiltrar, $urlCadastrar);
        $this->setActionMenu($menu);

        // Recuperando array de dados do banco para setar valores no formulário
        if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
            $arDados = $turma->getTurmaPorId($this->getRequest()->getParam("NU_SEQ_TURMA"));
            if (in_array($arDados['ST_TURMA'], array(
                Fnde_Sice_Business_Turma::CANCELADA,
                Fnde_Sice_Business_Turma::FINALIZACAO_ATRASADA,
                Fnde_Sice_Business_Turma::FINALIZADA
            ))) {
                $this->addMessage(Fnde_Message::MSG_ERROR, "A situação da turma não permite edição");
                $this->_redirect("/secretaria/turma/list");
            }
        } else {
            $businessConfiguracao = new Fnde_Sice_Business_Configuracao();
            $configuracao = $businessConfiguracao->obterUltimaConfiguracaoValida();

            if (!$configuracao) {
                $this->addMessage(Fnde_Message::MSG_ERROR, "Ative uma configuração para cadastrar turma.");
                $this->_redirect("/secretaria/turma/list");
            } else {
                $arDados['NU_SEQ_CONFIGURACAO'] = $configuracao;
            }

        }

        //Recupera o objeto de formulário para validação
        $form = $this->getForm($arDados);
        if ($arDados) {
            $form->populate($this->popularValores($arDados));
        }
        $this->view->form = $form;

        $arParams = $this->_request->getParams();

        if ($this->getRequest()->isPost() && $form->isValid($arParams)) {
            $this->salvarTurmaAction();
        }

        $this->render('form');
    }

    /**
     * Retorna o formulario de cadastro
     *
     * @access public
     *
     * @author diego.matos
     * @since 25/04/2012
     */
    public function getForm($arDados = array(), $arExtra = array())
    {

        $this->_arTitles = array('dtInicio', 'coMunicipio', 'nuSeqTurma', 'dtFinalizacao', 'nuSeqUsuarioArticulador',
            'coMesorregiao', 'dtFim', 'ufTurma', 'nuSeqUsuarioTutor', 'nuSeqCurso', 'stTurma',);
        $this->_arList = array('DT_INICIO', 'CO_MUNICIPIO', 'NU_SEQ_TURMA', 'DT_FINALIZACAO',
            'NU_SEQ_USUARIO_ARTICULADOR', 'CO_MESORREGIAO', 'DT_FIM', 'UF_TURMA', 'NU_SEQ_USUARIO_TUTOR',
            'NU_SEQ_CURSO', 'ST_TURMA',);

        //bloqueando uf
        if ($arDados['NU_SEQ_TURMA']) {
            $arExtra['block']['uf'] = true;
        }
        $form = new Turma_Form($arDados, $arExtra);

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $businessCurso = new Fnde_Sice_Business_Curso();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        $arParam = $this->_getAllParams();

        $tipoCursoSelecionado = !is_null($arParam['NU_SEQ_TIPO_CURSO']) ? $arParam['NU_SEQ_TIPO_CURSO']
            : $arParam['NU_SEQ_TIPO_CURSO_CAD'];
        if ($tipoCursoSelecionado == null && $arDados['NU_SEQ_CURSO'] != null) {
            $vlTipoCurso = $businessCurso->getTipoPorCurso($arDados['NU_SEQ_CURSO']);
            $tipoCursoSelecionado = $vlTipoCurso['NU_SEQ_TIPO_CURSO'];
        }

        $ufSelecionada = !is_null($arParam['UF_TURMA_CAD']) ? $arParam['UF_TURMA_CAD'] : $arDados['UF_TURMA'];

        //Setando valores dos combos
        $this->setTipoCurso($form);
        $this->setCurso($form, $tipoCursoSelecionado);
        $this->setTutor($form, $perfilUsuario, $arDados, $arUsuario);
        $this->setArticulador($form, $arDados);
        $this->setInfoCurso($form, $arDados);
        $this->setUf($form, $perfilUsuario, $arUsuario);
        $this->setMunicipio($form, $ufSelecionada, $perfilUsuario, $arUsuario);
        $this->setDisable($form, $arDados, $perfilUsuario);
        $this->setValueTipoCurso($form, $arDados);

        $form->setDecorators(array('FormElements', 'Form'));

        if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
            $form->setAction($this->view->baseUrl() . '/index.php/secretaria/turma/form/NU_SEQ_TURMA/' . $this->getRequest()->getParam("NU_SEQ_TURMA"))->setMethod('post')->setAttrib(
                'id', 'form');
        } else {
            $form->setAction($this->view->baseUrl() . '/index.php/secretaria/turma/form')->setMethod('post')->setAttrib(
                'id', 'form');
        }

        return $form;
    }

    /**
     * Monta a tela de visualizacao de turmas.
     */
    public function visualizarTurmaAction()
    {
        $this->setTitle('Turma');
        $this->setSubtitle('Visualizar');

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        /*
         *  1	Coordenador Nacional Administrador
         *  2	Coordenador Nacional Equipe
         *  3	Coordenador Nacional Gestor
         *  4	Coordenador Estadual
         *  5	Articulador
         *  6	Tutor
         *  7	Cursista
         *
         */

        //monta menu de contexto
        $urlFiltrar = $this->getUrl('secretaria', 'turma', 'list', ' ');
        $urlCadastrar = $this->getUrl('secretaria', 'turma', 'form', ' ');
        $menu = Fnde_Sice_Business_Componentes::montaMenuContextoTurma($perfilUsuario, $urlFiltrar, $urlCadastrar);
        $this->setActionMenu($menu);

        // Recuperando array de dados do banco para setar valores no formulário
        $turma = new Fnde_Sice_Business_Turma();
        if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
            $arDados = $turma->getTurmaPorId($this->getRequest()->getParam("NU_SEQ_TURMA"));
        }

        //Recupera o objeto de formulário para validação
        $form = $this->getForm($arDados);

        $elementos = $form->getElements();

        foreach ($elementos as $elemento) {
            if ($elemento->getName() != "cancelar") {
                $elemento->setAttrib("disabled", true);
            }
            if ($elemento->getName() == "confirmar") {
                $elemento->setAttrib("style", "display:none");
            }
        }

        //$form->setElements($elementos);

        $this->view->form = $form->populate($this->popularValores($arDados));

        $this->render('form');
    }

    /**
     * Retorna o formulario de pesquisa.
     * @param array $arDados Valores dos campos.
     */
    public function getFormFilter($arDados = array(), $perfilUsuario = null, $arUsuario = null)
    {
        $form = new Turma_FormFilter();
        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $this->setTipoCurso($form);
        $businessUsuario->setUfFilter($form, $perfilUsuario, $arUsuario);
        $this->setMesorregiaoFilter($form, $arDados['UF_TURMA'], $perfilUsuario, $arUsuario);
        $this->setMunicipioFilter($form, $arDados['UF_TURMA'], $arDados['CO_MESORREGIAO'], $perfilUsuario, $arUsuario);
        $this->setTutorFilter($form, $perfilUsuario, $arUsuario);
        $this->setArticuladorFilter($form, $perfilUsuario, $arUsuario);
        $this->setModulo($form);
        $this->setCursoFilter($form);
        $this->setStTurma($form);

        $form->setAction($this->view->baseUrl() . '/index.php/secretaria/turma/list')->setMethod('post');

        return $form;
    }

    /**
     * Renderiza o combo de mesorregiao de acordo com os valores selecionados.
     */
    public function renderizaMesoregiaoAction()
    {
        $this->_helper->layout()->disableLayout();

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = array();
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        $arParam = $this->_getAllParams();

        $form = $this->getFormFilter();

        $this->setMesorregiaoFilter($form, $arParam['UF_TURMA'], $perfilUsuario, $arUsuario);
        $this->setMunicipioFilter($form, $arParam['UF_TURMA'], $arParam['CO_MESORREGIAO'], $perfilUsuario, $arUsuario);
        $this->setArticuladorFilter($form, $perfilUsuario, $arUsuario);

        $this->view->formFilter = $form;
        return $this->render('list');
    }

    /**
     * Renderiza o campo municipio de acordo com os valores selecionados.
     */
    public function renderizaMunicipioAction()
    {
        $this->_helper->layout()->disableLayout();

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        $arParam = $this->_getAllParams();

        $form = $this->getFormFilter();

        $this->setMunicipioFilter($form, $arParam['UF_TURMA'], $arParam['CO_MESORREGIAO'], $perfilUsuario, $arUsuario);

        $this->view->formFilter = $form;
        return $this->render('list');
    }

    /**
     * Renderiza município pof UF para a tela de Cadastro/Edição
     */
    public function renderizaMunicipioCadAction()
    {
        $this->_helper->layout()->disableLayout();
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = array();
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        $arParam = $this->_getAllParams();

        $form = $this->getForm();

        $this->setMunicipio($form, $arParam['UF_TURMA_CAD'], $perfilUsuario, $arUsuario);
        $this->setArticuladorFilter($form, $perfilUsuario, $arUsuario);

        $this->view->form = $form;
        return $this->render('form');
    }

    /**
     * Renderiza articulador por tutor selecionado.
     */
    public function renderizaArticuladorCadAction()
    {
        $this->_helper->layout()->disableLayout();

        $arParam = $this->_getAllParams();

        $form = $this->getForm();

        $this->setArticulador($form, $arParam);

        $this->view->form = $form;
        return $this->render('form');
    }

    /**
     * Renderiza Curso por Tipo de Curso
     *
     * @author diego.matos
     * @since 26/04/2012
     */

    public function renderizaCursoPorTipoCadAction()
    {
        $arParam = $this->_getAllParams();

        $form = $this->getForm();

        $tipoCursoSelecionado = !is_null($arParam['NU_SEQ_TIPO_CURSO']) ? $arParam['NU_SEQ_TIPO_CURSO']
            : $arParam['NU_SEQ_TIPO_CURSO_CAD'];
        $this->setCurso($form, $tipoCursoSelecionado);

        $this->view->form = $form;
        return $this->render('form');
    }

    /**
     * Renderiza informações relacionadas ao curso selecionado
     *
     * @author diego.matos
     * @since 27/04/2012
     */
    public function renderizaInfoCursoCadAction()
    {
        $arParam = $this->_getAllParams();

        $form = $this->getForm();

        $this->setInfoCurso($form, $arParam);

        $this->view->form = $form;
        return $this->render('form');
    }

    /**
     * Salva os dados da turma.
     */
    public function salvarTurmaAction()
    {
        $this->setTitle('Turma');
        if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
            $this->setSubtitle('Editar');
        } else {
            $this->setSubtitle('Cadastrar');
        }

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        /*
         *  1	Coordenador Nacional Administrador
         *  2	Coordenador Nacional Equipe
         *  3	Coordenador Nacional Gestor
         *  4	Coordenador Estadual
         *  5	Articulador
         *  6	Tutor
         *  7	Cursista
         *
         */

        //monta menu de contexto
        $urlFiltrar = $this->getUrl('secretaria', 'turma', 'list', ' ');
        $urlCadastrar = $this->getUrl('secretaria', 'turma', 'form', ' ');
        $menu = Fnde_Sice_Business_Componentes::montaMenuContextoTurma($perfilUsuario, $urlFiltrar, $urlCadastrar);
        $this->setActionMenu($menu);
        // Se os dados não foram enviados por post retorna para a index
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('index');
        }

        //Recupera o objeto de formulário para validação
        $form = $this->getForm($this->_request->getParams());


        //Recupera os parâmetros do request
        $arParams = $this->_request->getParams();

        if (!$arParams["DT_INICIO"]) {
            $form->getElement("DT_FIM")->removeValidator('Fnde_Sice_Validate_DateGreatherThanValidator');
        }
        $obTurma = new Fnde_Sice_Business_Turma();

        $arHistorico = array();

        //Preparando o array de turma para inserção no banco
        $arTurma = $this->preparaDadosTurma($arParams);

        //Preparando o array de HistoricoTurma para inserção no banco
        $arHistorico['ST_TURMA'] = $arTurma['ST_TURMA'];
        $arHistorico['DT_HISTORICO'] = date('d/m/Y G:i:s');

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
            $arHistorico['ID_AUTOR'] = $arUsuario['NU_SEQ_USUARIO'];
        }

        //erro nº 1 relatado na tarefa TAS000000029172
        if (!$_POST['NU_SEQ_CONFIGURACAO']) {
            $this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao cadastrar por falta vinculação vigente no periodo (Data de início/fim) da turma.");
            $this->_redirect("/secretaria/turma/form");
        } else {
            //Erro relatado no SGO nº FNDE-4037
            $obCOnfiguracao = new Fnde_Sice_Business_Configuracao();
            $resultado = $obCOnfiguracao->getConfiguracaoById($_POST['NU_SEQ_CONFIGURACAO']);
            if ($resultado['ST_CONFIGURACAO'] == 'D') {
                $this->addMessage(Fnde_Message::MSG_ERROR, "Não é possível editar esta turma, pois ela está vinculada a 
                        configuração inativa " . $resultado['NU_SEQ_CONFIGURACAO'] . " com vigência no período " .
                    $resultado['DT_INI_VIGENCIA'] . " a " . $resultado['DT_TERMINO_VIGENCIA'] . ".");
                $this->_redirect("/secretaria/turma/list");
            }
        }

        //verifica se existe limite de turma para a mesorregião na edição - INÍCIO
        $businessQdtTurma = new Fnde_Sice_Business_QuantidadeTurma();
        $qtdTurmasCadastradasMesorregiao = $businessQdtTurma->searchQtTurmaCadastradaPorMesorregiaoEConfig($arTurma['CO_MESORREGIAO'], $_POST['NU_SEQ_CONFIGURACAO']);
        $qtdTurmasDaMesorregiao = $businessQdtTurma->searchQtTurmaPorMesorregiaoEConfig($arTurma['CO_MESORREGIAO'], $_POST['NU_SEQ_CONFIGURACAO']);

        //alteração para verificar se houve alteração na mesorregião... coloquei o if para pegar a turma no banco e mudei a validação adicionando o $arParams['CO_MESORREGIAO_CAD'] != $turma['CO_MESORREGIAO']
        if (isset($arTurma['NU_SEQ_TURMA'])) {
            //dados da truma
            $turma = $obTurma->getTurmaById($arTurma['NU_SEQ_TURMA']);
        }

        if (is_null($qtdTurmasDaMesorregiao) && $arParams['CO_MESORREGIAO_CAD'] != $turma['CO_MESORREGIAO']) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'não há disponibilidade de Turmas para essa mesorregião em sua configuração (Configuração nº ' . $_POST['NU_SEQ_CONFIGURACAO'] . ')');
            return;
        }

        if (isset($arTurma['NU_SEQ_TURMA'])) {
            //dados da truma
            $turma = $obTurma->getTurmaById($arTurma['NU_SEQ_TURMA']);

            if ($arParams['CO_MESORREGIAO_CAD'] == $turma['CO_MESORREGIAO']) {
                //OK
                //não houve alteração de MESO região
            } elseif ((int)$qtdTurmasCadastradasMesorregiao >= (int)$qtdTurmasDaMesorregiao['QT_TURMAS']) {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Não há disponibilidade de Turmas para essa mesorregião.');
                return;
            }
            //verifica se existe limite de turma para a mesorregião na edição - FIM
        }

        if ($arParams['CO_MESORREGIAO_CAD'] == $arTurma['CO_MESORREGIAO']) {
            //OK
            //não houve alteração de MESO região
        } elseif ($qtdTurmasCadastradasMesorregiao >= $qtdTurmasDaMesorregiao || is_null($qtdTurmasDaMesorregiao)) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Não há disponibilidade de Turmas para essa mesorregião.');
            return;
        }
        //verifica se existe limite de turma para a mesorregião na edição - FIM

        try {
            $obTurma->salvar($arTurma, $arHistorico);

            if ($arParams['NU_SEQ_TURMA']) {
                $this->addMessage(Fnde_Message::MSG_SUCCESS, 'Cadastro alterado com sucesso');
            } else {
                $this->addMessage(Fnde_Message::MSG_SUCCESS, 'Cadastro realizado com sucesso');
            }
            $this->_redirect("/secretaria/turma/list");
        } catch (Exception $e) {

            $this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());

            if ($this->getRequest()->getParam("NU_SEQ_TURMA")) {
                $this->_redirect("/secretaria/turma/form/NU_SEQ_TURMA/" . $this->getRequest()->getParam("NU_SEQ_TURMA"));
            } else {
                $this->_redirect("/secretaria/turma/form");
            }
            return;
        }

        $this->view->form = $form;
        return;
    }

    /**
     * Remove uma turma.
     */
    public function removerTurmaAction()
    {
        $arParam = $this->_getAllParams();

        $obBusinessTurma = new Fnde_Sice_Business_Turma();

        $nu_seq_turma = (int)$arParam["NU_SEQ_TURMA"];

        try {
            $turma = $obBusinessTurma->getTurmaById($arParam["NU_SEQ_TURMA"]);

            $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
            $perfilUsuario = $usuarioLogado->credentials;

            $permissoes = $obBusinessTurma->statusComPermissaoExclusao($perfilUsuario);

            if (!in_array($turma['ST_TURMA'], $permissoes)) {

                $situacoes = count($permissoes) == 3
                    ? 'pré-turma, não autorizada ou cancelada'
                    : 'pré-turma ou não autorizada';

                $this->addMessage(Fnde_Message::MSG_ERROR,
                    "Erro ao excluir a turma. A turma somente poderá ser excluída se tiver com situação de {$situacoes}.");
                $this->_redirect("/secretaria/turma/list");
            }

            $obBusinessTurma->deletaEmCadeia($nu_seq_turma, $perfilUsuario);

        } catch (Exception $e) {

            $this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao remover a turma: " . $e->getMessage());
            $this->_redirect("/secretaria/turma/list");
        }

        $this->addMessage(Fnde_Message::MSG_SUCCESS, "Turma excluída com sucesso!");
        $this->_redirect("/secretaria/turma/list");
    }

    /**
     * Limpa os dados de pesquisa da sessao.
     */
    public function clearSearchAction()
    {

        //limpa sessão
        Zend_Session::namespaceUnset('searchParam');

        //redireciona para pagina de listagem da ultima sessão
        $this->_redirect($this->_getParam('module') . '/' . $this->_getParam('controller') . '/list');
    }

    /**
     * Recupera os dados de pesquisa da sessao.
     */
    public function getSearchParamTurma()
    {
        $arFilter = array();

        $this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');

        $arSession = $this->urlFilterNamespace->param;

        $stUrlAtual = $this->_getParam('module') . '/' . $this->_getParam('controller') . '/'
            . $this->_getParam('action');
        $stUrlSession = $arSession['module'] . '/' . $arSession['controller'] . '/' . $arSession['action'];

        if ($stUrlAtual == $stUrlSession) {
            $arFilter = $arSession;
        }
        return $arFilter;
    }

    /**
     * Chama-se esta função no lugar do list quando há necessidade de que a tela principal (list)
     * não mantenha a pesquisa anterior
     */
    public function limparAction()
    {

        $this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
        $this->urlFilterNamespace->param = null;

        $this->_redirect("/secretaria/turma/list");
    }

    /**
     * Seta o valor do combo de Tipo de Curso.
     * @param $form Formulario que sera setado.
     */
    private function setTipoCurso($form)
    {
        try {
            $options = array(null => 'Selecione');
            $rsTipoCurso = Fnde_Sice_Business_Componentes::getAllByTable("TipoCurso",
                array("NU_SEQ_TIPO_CURSO", "DS_TIPO_CURSO"));

            foreach ($rsTipoCurso as $key => $tipoCurso) {
                $options[$key] = $tipoCurso;
            }

            $form->setTipoCurso($options);
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o combo de Mesorregiao de acordo com a UF selecionada, ou Municipio selecionado.
     * E tambem de acordo com o usuario logado no sistema.
     * @param $form Formulario.
     * @param $ufSelecionada Uf selecionada.
     * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
     * @param $arUsuario Dados do usuario da base de dados do SICE.
     * @param $municipioSelecionado Municipio selecionado, default null.
     */
    private function setMesorregiaoFilter($form, $ufSelecionada, $perfisUsuarioLogado, $arUsuario,
                                          $municipioSelecionado = null)
    {
        try {
            $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

            $options = array(null => 'Selecione');

            if ($ufSelecionada) {
                $result = $businessMesoregiao->getMesoRegiaoPorUF($ufSelecionada);

                for ($i = 0; $i < count($result); $i++) {
                    if (!in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)) {
                        $options[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
                    } else if ($arUsuario['CO_MESORREGIAO'] == $result[$i]['CO_MESO_REGIAO']) {
                        $options[$result[$i]['CO_MESO_REGIAO']] = $result[$i]['NO_MESO_REGIAO'];
                    }
                }
            }

            if ($municipioSelecionado) {
                //Recuperando a mesorregiao do municipio selecionado.
                $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
                $mesorregiao = $businessMesoregiao->getMesoRegiaoPorMunicipio($municipioSelecionado);
                $value = $mesorregiao[0]['CO_MESO_REGIAO'];
            }

            $form->setMesorregiao($options, $value);
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o valor do combo de Municipio de acordo com a UF selecionada ou Mesorregiao selecionada.
     * E tambem de acordo com o usuario logado no sistema.
     * @param $form Turma_FormFilter Formulario.
     * @param $ufSelecionada Uf selecionada.
     * @param $mesorregiaoSelecionada Mesorregiao selecionada.
     * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
     * @param $arUsuario Dados do usuario da base de dados do SICE.
     */
    private function setMunicipioFilter($form, $ufSelecionada = null, $mesorregiaoSelecionada = null,
                                        $perfisUsuarioLogado, $arUsuario)
    {
        try {
            $obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
            $obBusinessUF = new Fnde_Sice_Business_Uf();

            $options = array(null => 'Selecione');

            if ($mesorregiaoSelecionada) {
                $result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($mesorregiaoSelecionada);
            } elseif ($ufSelecionada) {
                /*
                 *
                 * Conforme solicitado na reunião do dia 30/01/2017 está sendo liberado
                 * para o tutor e articulador selecionar qualquer município dentro da UF
                 * não apenas os da sua Mesorregião.
                 *
                 * O E-mail contendo tal informação será anexado na demanda FNDE_1784
                 * REQ000000014907
                 *
                if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)
                    || in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)
                ) {
                    $result = $obBusinessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
                } else {
                    $result = $obBusinessUF->getMunicipioPorUf($ufSelecionada);
                }
                */
                $result = $obBusinessUF->getMunicipioPorUf($ufSelecionada);
            }

            for ($i = 0; $i < count($result); $i++) {
                $options[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
            }

            $form->setMunicipio($options);
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta os combos de Tutor e Articulador, e o value do combo de Mesorregiao.
     * De acordo com o municipio selecionado e de acordo com o usuario logado no sistema.
     */
    public function municipioChangeAction()
    {
        $this->_helper->layout()->disableLayout();

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        $arParam = $this->_getAllParams();

        $form = $this->getFormFilter();

        $this->setMesorregiaoFilter($form, $arParam['UF_TURMA'], $perfilUsuario, $arUsuario, $arParam['CO_MUNICIPIO']);
        $this->setTutorFilter($form, $perfilUsuario, $arUsuario);
        $this->setArticuladorFilter($form, $perfilUsuario, $arUsuario);

        $this->view->formFilter = $form;
        return $this->render('list');
    }

    /**
     * Seta o combo de Tutor de acordo com o usuario logado no sistema e municipio selecionado.
     * @param $form Formulario.
     * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
     * @param $arUsuario Dados do usuario da base de dados do SICE
     */
    private function setTutorFilter($form, $perfisUsuarioLogado, $arUsuario)
    {
        try {
            $businessUsuario = new Fnde_Sice_Business_Usuario();
            $arDados = $this->_getAllParams();
            $arDados = array_merge($arDados, $this->getSearchParamTurma());

            $options = array(null => 'Selecione');

            if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado)
                || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado)
            ) {
                $result = $businessUsuario->search(
                    array("NU_SEQ_TIPO_PERFIL" => "6", "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL']));
            } else if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)) {
                $result = $businessUsuario->getTutorPorArticulador($arUsuario['NU_SEQ_USUARIO']);
            } else if (in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)) {
                $result = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
            } else if ($arDados["CO_MUNICIPIO"]) {
                $result = $businessUsuario->search(
                    array("NU_SEQ_TIPO_PERFIL" => "6", "CO_MUNICIPIO_PERFIL" => $arDados["CO_MUNICIPIO"]));
            }

            foreach ($result as $res) {
                $options[$res['NU_SEQ_USUARIO']] = $res['NO_USUARIO'];
            }

            $form->setTutor($options);
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o combo de Articulador de acordo com o usuario logado no sistema e uf selecionado.
     * @param $form Turma_FormFilter Formulario.
     * @param $perfisUsuarioLogado Perfil do usuario logado no sistema.
     * @param $arUsuario Array Dados do usuario da base de dados do SICE.
     */
    private function setArticuladorFilter($form, $perfisUsuarioLogado, $arUsuario)
    {
        try {
            $businessUsuario = new Fnde_Sice_Business_Usuario();
            $arDados = $this->_getAllParams();
            $arDados = array_merge($arDados, $this->getSearchParamTurma());

            $options = array(null => 'Selecione');

            if (
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfisUsuarioLogado) ||
                in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfisUsuarioLogado) ||
                in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfisUsuarioLogado)
            ) {
                $result = $businessUsuario->search(array(
                    "NU_SEQ_TIPO_PERFIL" => "5",
                    "SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL'],
                    "ST_USUARIO" => "A"
                ));
            } else if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfisUsuarioLogado)) {
                $result = $businessUsuario->search(array(
                    "NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']
                ));
            } else if ($arDados["CO_MUNICIPIO"]) {
                $result = $businessUsuario->search(array(
                    "NU_SEQ_TIPO_PERFIL" => "5",
                    "CO_MUNICIPIO_PERFIL" => $arDados["CO_MUNICIPIO"],
                    "ST_USUARIO" => "A"
                ));
            } else if ($arDados["UF_TURMA"]) {
                $result = $businessUsuario->search(array(
                    "NU_SEQ_TIPO_PERFIL" => "5",
                    "SG_UF_ATUACAO_PERFIL" => $arDados["UF_TURMA"],
                    "ST_USUARIO" => "A"
                ));
            }

            foreach ($result as $res) {
                $options[$res['NU_SEQ_USUARIO']] = $res['NO_USUARIO'];
            }

            $form->setArticulador($options);
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o combo de Modulo.
     * @param $form Formulario.
     */
    private function setModulo($form)
    {
        try {
            $rsModulo = Fnde_Sice_Business_Componentes::getAllByTable("Modulo",
                array("NU_SEQ_MODULO", "DS_NOME_MODULO"));
            if ($rsModulo) {
                $form->setModulo($rsModulo);
            }
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o combo de Curso da tela de filtro.
     * @param $form Formulario.
     */
    private function setCursoFilter($form)
    {
        try {
            $rsCurso = Fnde_Sice_Business_Componentes::getAllByTable("Curso", array("NU_SEQ_CURSO", "DS_NOME_CURSO"));
            if ($rsCurso) {
                $form->setCurso($rsCurso);
            }
        } catch (Exception $e) {
            $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e);
        }
    }

    /**
     * Seta o valor do combo de Curso de acordo com o tipo de curso selecionado.
     * @param $form
     * @param $tipoCursoSelecionado
     */
    private function setCurso($form, $tipoCursoSelecionado)
    {
        $businessCurso = new Fnde_Sice_Business_Curso();

        $options = array(null => 'Selecione');

        if ($tipoCursoSelecionado != null) {
            $result = $businessCurso->getCursoPorTipo($tipoCursoSelecionado);
            for ($i = 0; $i < count($result); $i++) {
                $options[$result[$i]['NU_SEQ_CURSO']] = $result[$i]['DS_NOME_CURSO'];
            }
        }

        $form->setCurso($options);
    }

    /**
     * Seta o combo de Tutor baseado nas regras de negocio.
     * De acordo com o perfil do usuario logado no sistema
     * @param $form
     * @param $perfilUsuario
     * @param $arDados
     * @param $arUsuario
     */
    private function setTutor($form, $perfilUsuario, $arDados, $arUsuario)
    {
        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $options = array(null => 'Selecione');

        //Verificando se o usuário logado é articulador ou administrador
        //Pois eles não podem cadastrar turmas porém podem visualizar, mudando a forma de preencher os combos de tutor e articulador.
        //Articulador =	Pesquisa e  visualiza as turmas  na qual está vinculado.
        //Coordenador Nacional Gestor e Coordenador Nacional Equipe = Poderá visualizar todas as turmas de todas as UF’s
        if (in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario)
        ) {
            $result = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arDados['NU_SEQ_USUARIO_TUTOR']));
            //Coordenador Nacional Administrador = Poderá visualizar e editar todas as turmas de todas as UF’s
        } else if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)) {
            if ($arDados['UF_TURMA']) { //caso a turma já esteja definida lista apenas os tutores daquela mesorregião
                $result = $businessUsuario->search(array("CO_MESORREGIAO" => $arDados['CO_MESORREGIAO'], "NU_SEQ_TIPO_PERFIL" => "6"));
            } else {
                $result = $businessUsuario->getTutores();
            }
            //Coordenador Estadual = Poderá visualizar e editar apenas as turmas da sua  UF de atuação.
        } else if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $result = $businessUsuario->search(array("SG_UF_ATUACAO_PERFIL" => $arUsuario['SG_UF_ATUACAO_PERFIL'], "NU_SEQ_TIPO_PERFIL" => "6"));
            //Tutor	Cadastra, pesquisa, exclui e visualiza turmas na qual está vinculado.
        } else {
            $result = $businessUsuario->search(array("NU_SEQ_USUARIO" => $arUsuario['NU_SEQ_USUARIO']));
        }

        foreach ($result as $usuario) {
            $options[$usuario['NU_SEQ_USUARIO']] = $usuario['NO_USUARIO'];
        }

        $form->setTutor($options);
    }

    /**
     * Seta o combo de Articulador de acordo com as regras de negocio.
     * De acordo com a mesoregiao selecionada ou de acordo com o tutor selecionado.
     * @param $form
     * @param $arDados
     */
    private function setArticulador($form, $arDados)
    {
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $arParam = $this->_getAllParams();
        $options = array(null => 'Selecione');

        $sgUfAtuacaoPerfil = null;
        if ($arParam['NU_SEQ_USUARIO_TUTOR']) {
            $arUsu = $businessUsuario->getUsuarioById($arParam['NU_SEQ_USUARIO_TUTOR']);
            $sgUfAtuacaoPerfil = $arUsu['SG_UF_ATUACAO_PERFIL'];
        }

        $rsUsuarioArticulador = array();

        if ($arDados['UF_TURMA']) {
            $rsUsuarioArticulador = $businessUsuario->search(array(
                "NU_SEQ_TIPO_PERFIL" => "5",
                "SG_UF_ATUACAO_PERFIL" => $arDados['UF_TURMA'],
                "ST_USUARIO" => "A"
            ));
        }

        if ($sgUfAtuacaoPerfil) {
            $rsUsuarioArticulador = $businessUsuario->search(array(
                "NU_SEQ_TIPO_PERFIL" => "5",
                "SG_UF_ATUACAO_PERFIL" => $sgUfAtuacaoPerfil,
                "ST_USUARIO" => "A"
            ));
        }

        foreach ($rsUsuarioArticulador as $usuario) {
            $options[$usuario['NU_SEQ_USUARIO']] = $usuario['NO_USUARIO'];
        }

        $form->setArticulador($options);
    }

    /**
     * Seta as informacoes do curso de acordo com o curso selecionado.
     * @param $form
     * @param $arDados
     */
    private function setInfoCurso($form, $arDados)
    {
        $businessTurma = new Fnde_Sice_Business_Turma();

        $infoCurso = array();

        if ($arDados['NU_SEQ_CURSO'] != null
            || ($arDados['NU_SEQ_CURSO_CAD'] != null && $arDados['NU_SEQ_CONFIGURACAO'])
        ) {
            $arDados['NU_SEQ_CURSO'] = ($arDados['NU_SEQ_CURSO'] ? $arDados['NU_SEQ_CURSO']
                : $arDados['NU_SEQ_CURSO_CAD']);
            $infoCurso = $businessTurma->pesquisarDadosComplementaresTurma($arDados);
        } else if ($arDados['NU_SEQ_CURSO_CAD'] != null) {
            $infoCurso = $businessTurma->pesquisarDadosTurma($arDados['NU_SEQ_CURSO_CAD']);
        }

        $form->setInfoCurso($infoCurso);
    }

    /**
     * Seta o valor do combo de UF de acordo com o usuario logado no sistema.
     * @param $form
     * @param $perfilUsuario
     * @param $arUsuario
     */
    private function setUf($form, $perfilUsuario, $arUsuario)
    {
        $businessUF = new Fnde_Sice_Business_Uf();

        $result = $businessUF->search(array('SG_UF'));

        $options = array(null => 'Selecione');

        for ($i = 0; $i < count($result); $i++) {
            if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)
                || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario)
                || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario)
            ) {
                $options[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
            } else if ($arUsuario['SG_UF_ATUACAO_PERFIL'] == $result[$i]['SG_UF']) {
                $options[$result[$i]['SG_UF']] = $result[$i]['SG_UF'];
            }
        }

        $form->setUf($options);
    }

    /**
     * Seta o combo de Municipio de acordo com o usuario logado e de acordodo com a UF selecionada.
     * @param $form
     * @param $ufSelecionada
     * @param $perfilUsuario
     * @param $arUsuario
     */
    private function setMunicipio($form, $ufSelecionada, $perfilUsuario, $arUsuario)
    {
        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        $businessUF = new Fnde_Sice_Business_Uf();

        $options = array(null => 'Selecione');

        if ($ufSelecionada != null) {
            if (in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)) {
                $result = $businessMesoregiao->getMunicipioPorMesoRegiao($arUsuario['CO_MESORREGIAO']);
            } else {
                $result = $businessUF->getMunicipioPorUf($ufSelecionada);
            }

            for ($i = 0; $i < count($result); $i++) {
                $options[$result[$i]['CO_MUNICIPIO_IBGE']] = $result[$i]['NO_MUNICIPIO'];
            }
        }

        $form->setMunicipio($options);
    }

    /**
     * Seta a mesorregiao de acordo com o municipio selecionado.
     */
    public function municipioChangeCadAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

        $arParam = $this->_getAllParams();

        if ($arParam['CO_MUNICIPIO_CAD']) {
            $result = $businessMesoregiao->getMesoRegiaoPorMunicipio($arParam['CO_MUNICIPIO_CAD']);
        }

        $retorno['NO_MESORREGIAO'] = utf8_encode($result[0]['NO_MESO_REGIAO']);
        $retorno['CO_MESORREGIAO'] = $result[0]['CO_MESO_REGIAO'];

        $this->_helper->json($retorno);
        return $retorno;
    }

    /**
     * Disabilita os campos da tela se a turma estiver finalizada.
     * @param $form
     * @param $arDados
     * @param $perfilUsuario
     */
    private function setDisable($form, $arDados, $perfilUsuario)
    {
        if ($arDados['ST_TURMA'] == 4
            && (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario)
                || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_ESTADUAL, $perfilUsuario))
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $form->setDisable();
        }
    }

    /**
     * Seta o valor de Tipo de Curso de acordo com o curso.
     * @param $form
     * @param $arDados
     */
    private function setValueTipoCurso($form, $arDados)
    {
        $businessCurso = new Fnde_Sice_Business_Curso();

        if ($arDados['NU_SEQ_CURSO'] != null) {
            $vlTipoCurso = $businessCurso->getTipoPorCurso($arDados['NU_SEQ_CURSO']);
            $value = $vlTipoCurso['NU_SEQ_TIPO_CURSO'];
        }

        $form->setValueTipoCurso($value);
    }

    /**
     * Retorna o array para popular o form em edicao e visualizacao.
     * @param $arDados
     */
    private function popularValores($arDados)
    {
        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();

        if ($arDados['CO_MUNICIPIO']) {
            $result = $businessMesoregiao->getMesoRegiaoPorMunicipio($arDados['CO_MUNICIPIO']);
        }

        $arDados['NU_SEQ_CURSO_CAD'] = $arDados['NU_SEQ_CURSO'];
        $arDados['UF_TURMA_CAD'] = $arDados['UF_TURMA'];
        $arDados['CO_MUNICIPIO_CAD'] = $arDados['CO_MUNICIPIO'];
        $arDados['NO_MESORREGIAO_CAD'] = $result[0]['NO_MESO_REGIAO'];
        $arDados['CO_MESORREGIAO_CAD'] = $arDados['CO_MESORREGIAO'];

        return $arDados;
    }

    /**
     * Retorna o array com os dados preparados de turma para salvar no banco.
     * @param array $arParams
     */
    private function preparaDadosTurma($arParams)
    {
        $businessPeriodoDeVinculacao = new Fnde_Sice_Business_PeriodoVinculacao();

        if ($arParams['NU_SEQ_TURMA'] != null) {
            $arTurma['NU_SEQ_TURMA'] = $arParams['NU_SEQ_TURMA'];
            $arTurma['NU_SEQ_CONFIGURACAO'] = $arParams['NU_SEQ_CONFIGURACAO'];
            $arTurma['ST_TURMA'] = $arParams['ST_TURMA'];
        } else {
            $arTurma['ST_TURMA'] = 1;

            $businessConfiguracao = new Fnde_Sice_Business_Configuracao();
            $businessQuantidade = new Fnde_Sice_Business_QuantidadeTurma();
            $configuracao = $businessConfiguracao->obterUltimaConfiguracaoValida();
            if (!$configuracao) {
                $this->addMessage(Fnde_Message::MSG_ERROR, "Ative uma configuração para cadastrar turma.");
                $this->_redirect("/secretaria/turma/list");
            }

            //Verifica quantidade de turmas cadastradas e quantidade permitida.
            $verificaCadastro = $businessQuantidade->verificaQtdTurmaCadastro($configuracao, $arParams['CO_MESORREGIAO_CAD']);
            if ($verificaCadastro) {
                $this->addMessage(Fnde_Message::MSG_ERROR, "Limite excedido para abrir novas turmas na sua mesorregião.");
                $this->_redirect("/secretaria/turma/form");
            }

            $arTurma['NU_SEQ_CONFIGURACAO'] = $configuracao;
        }

        $vinculacao = $businessPeriodoDeVinculacao->getPeriodoVinculacaoByDtFim($arParams['DT_FIM']);
        if (!$vinculacao) {
            $this->addMessage(Fnde_Message::MSG_ERROR, "Erro ao cadastrar por falta de periodo de vinculação.");
            $this->_redirect("/secretaria/turma/list");
        }

        //pesquisar o periodo de vinculação que tem a ver com a data fim e seja de tutor
        $arTurma['NU_SEQ_PERIODO_VINCULACAO'] = $vinculacao;

        $arTurma['NU_SEQ_CURSO'] = $arParams['NU_SEQ_CURSO_CAD'];
        $arTurma['NU_SEQ_USUARIO_TUTOR'] = $arParams['NU_SEQ_USUARIO_TUTOR'];
        $arTurma['NU_SEQ_USUARIO_ARTICULADOR'] = $arParams['NU_SEQ_USUARIO_ARTICULADOR'];
        $arTurma['DT_INICIO'] = $arParams['DT_INICIO'];
        $arTurma['DT_FIM'] = $arParams['DT_FIM'];
        $arTurma['DT_FINALIZACAO'] = $arParams['DT_FINALIZACAO'];
        $arTurma['UF_TURMA'] = $arParams['UF_TURMA_CAD'];
        $arTurma['CO_MUNICIPIO'] = $arParams['CO_MUNICIPIO_CAD'];
        $arTurma['CO_MESORREGIAO'] = $arParams['CO_MESORREGIAO_CAD'];

        return $arTurma;
    }

    private function setStTurma($form)
    {
        $rsSituacoesTurma = Fnde_Sice_Business_Componentes::getAllByTable("SituacaoTurma",
            array("NU_SEQ_ST_TURMA", "DS_ST_TURMA"));

        $form->setStTurma($rsSituacoesTurma);
    }

    public function buscarturmaAction()
    {
        $arDados = $this->_getAllParams();

        $arDados['id'] = (is_null($arDados['id'])) ? 0 : $arDados['id'];

        $obBusinessTurma = new Fnde_Sice_Business_Turma();
        $turma = $obBusinessTurma->getTurmaById($arDados['id']);

        $retorno = (is_array($turma)) ? true : false;
        $this->_helper->json($retorno);
        return $retorno;
    }

    public function ajaxmesorregiaoAction()
    {
        $arDados = $this->_getAllParams();

        $estados = '\'';
        $estados .= implode('\',\'', $arDados["checkeds"]);
        $estados .= '\'';
        $obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        $estados = $obBusinessMesoregiao->getMesoregioesPorEstados($estados);

        $dados = array();
        foreach ($estados as $estado) {
            $dados[$estado["CO_MESO_REGIAO"]] = utf8_encode($estado["MESO"]);
        }

        $this->_helper->json($dados);
        return $dados;
    }
}
