<?php

/**
 * Created by PhpStorm.
 * User: 05922176633
 * Date: 22/01/2015
 * Time: 16:20
 */
class Manutencao_TermocompromissoController extends Fnde_Sice_Controller_Action
{

    public function listAction()
    {
        if (!Fnde_Sice_Business_Componentes::permitirAcesso()) {
            $this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
            $this->_redirect('index');
        }

        $this->setTitle('Termo de Compromisso');
        $this->setSubtitle('Listar');

        $possuiTermo = Fnde_Sice_Business_TermoCompromisso::possuiTermo();

        if (!$possuiTermo) {
            //se não assinou redireciona para assinar termo
            $this->_redirect('/manutencao/termocompromisso/assinar');
        }

        $businessUsuario = new Fnde_Sice_Business_Usuario();

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

        //buscar termos assinados
        $obBusiness = new Fnde_Sice_Business_TermoCompromisso();
        $rsRegistros = $obBusiness->getAssinaturas($arUsuario['NU_SEQ_USUARIO']);

        $businessTipoPerfil = new Fnde_Sice_Business_TipoPerfil();

        $result = array();

        foreach ($rsRegistros as $i=>$registro) {
            $tipoPerfil = $businessTipoPerfil->getTipoPerfilById($registro['NU_SEQ_TIPO_PERFIL']);

            $result[$i]['NU_SEQ_TERMO_COMPROMISSO'] = $registro['NU_SEQ_TERMO_COMPROMISSO'];
            $result[$i]['NU_ANO'] = $registro['NU_ANO'];
            $result[$i]['NU_SEQ_TIPO_PERFIL'] = $tipoPerfil['DS_TIPO_PERFIL'];
            $result[$i]['DT_INICIO'] = $registro['DT_INICIO'];
            $result[$i]['DT_FIM'] = $registro['DT_FIM'];
            $result[$i]['CO_ACORDO'] = Fnde_Sice_Model_TermoCompromisso::$arrAcordo[$registro['CO_ACORDO']];
            $result[$i]['CO_ACAO'] = Fnde_Sice_Model_TermoCompromisso::$arrAcao[$registro['CO_ACAO']];

            $imprimir = "<a href='{$this->getUrl('manutencao', 'termocompromisso', 'imprimir')}/NU_SEQ_TERMO_COMPROMISSO/{$registro['NU_SEQ_TERMO_COMPROMISSO']}' class='icoImprimir imprimir' title='Imprimir' target='blank'><span>Imprimir</span></a>";
            $deletar = ($registro['DT_FIM'] == "") ?
                "<a href='{$this->getUrl('manutencao', 'termocompromisso', 'delete')}/NU_SEQ_TERMO_COMPROMISSO/{$registro['NU_SEQ_TERMO_COMPROMISSO']}' class='icoExcluir excluir' title='Excluir' mensagem='Deseja realmente excluir o registro?'><span>Excluir</span></a>" :
                "";
            $result[$i]['ACAO'] = $imprimir . " " . $deletar;
        }

        //monta grid
        $arrHeader = array('<center>Ano</center>', '<center>Perfil</center>', '<center>Data Início</center>', '<center>Data Término</center>', '<center>Acordo</center>', '<center>Permissão</center>', '<center>Ações</center>');
//        $rowAction = $this->getArRowAction();

        $grid = new Fnde_View_Helper_DataTables();
//        $grid->setMainAction(array("Visualizar histórico bolsista" => $this->getUrl('manutencao', 'visualizahistbolsa', 'form', true),));
        $grid->setAutoCallJs(true);
//        $grid->setActionColumn("<center>Ações</center>");

        $this->view->grid = $grid->setData($result)
            ->setHeader($arrHeader)
            ->setHeaderActive(false)
            ->setTitle("Listagem de Termos de Compromissos")
//            ->setRowAction($rowAction)
            ->setId('NU_SEQ_TERMO_COMPROMISSO')
//            ->setRowInput(Fnde_View_Helper_DataTables::INPUT_TYPE_RADIO)
            ->setTableAttribs(array('id' => 'edit'))
            ->setColumnsHidden(array("NU_SEQ_TERMO_COMPROMISSO"))
        ;
    }

    public function assinarAction()
    {
        if (!Fnde_Sice_Business_Componentes::permitirAcesso()) {
            $this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
            $this->_redirect('index');
        }

        $this->setTitle('Termo de Compromisso');
        $this->setSubtitle('Assinar');

        //pega o usuario logado
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $dadosUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        $dadosUsuario['DS_TIPO_PERFIL_SEGWEB'] = $usuarioLogado->credentials[0];

        //verifica se é post
        if ($this->_request->isPost()) {
            //buscar valor do acordo
            $params = $this->_getAllParams();
            //seta valores para salvar termo
            $params['nu_seq_usuario'] = $dadosUsuario['NU_SEQ_USUARIO'];
            $params['nu_seq_tipo_perfil'] = $dadosUsuario['NU_SEQ_TIPO_PERFIL'];

            //separa valores para inserir termo
            $paramsTermo = $this->getParamsTermo($params);

            //inserir termo
            $businessTermo = new Fnde_Sice_Business_TermoCompromisso();
            $businessTermo->salvar($paramsTermo);

            //redirecionar para tela de listar termos
            $this->_redirect("/manutencao/termocompromisso/list");
        }

        //cria o novo formulario
        $form = new TermoCompromisso_Form();
        $this->view->termo = $form->termo($dadosUsuario);
        $this->view->assinar = $form->assinar();
    }

    public function deleteAction()
    {
        $params = $this->_getAllParams();

        $paramsTermo = array(
            'NU_SEQ_TERMO_COMPROMISSO'  => $params['NU_SEQ_TERMO_COMPROMISSO'],
            'DT_FIM'                    => date('d/m/Y G:i:s')
        );

        $businessTermo = new Fnde_Sice_Business_TermoCompromisso();
        $businessTermo->salvar($paramsTermo);

        //redirecionar para tela de listar termos
        $this->addMessage(Fnde_Message::MSG_SUCCESS, "O termo de compromisso foi finalizado com sucesso.");
        $this->_redirect("/manutencao/termocompromisso/list");
    }

    public function imprimirAction()
    {
        $params = $this->_getAllParams();

        //busca termo a ser impresso
        $obBusiness = new Fnde_Sice_Business_TermoCompromisso();
        $termo = $obBusiness->getTermo($params['NU_SEQ_TERMO_COMPROMISSO']);

        //pega o usuario logado
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
        $businessUsuario = new Fnde_Sice_Business_Usuario();

        if($params['NU_SEQ_USUARIO']){
            $dadosUsuario = $businessUsuario->getUsuarioById($params['NU_SEQ_USUARIO']);
        }else{
            $dadosUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
        }

        //adicionando dados do termo
        $dadosUsuario['DT_ASSINATURA'] = Fnde_Sice_Business_Componentes::dataPorExtensoCertificado(substr($termo->DT_INICIO,0,10));
        $dadosUsuario['CO_ACAO'] = $termo->CO_ACAO;

        $obBusinessTipoPerfil = new Fnde_Sice_Business_TipoPerfil();
        $perfil = $obBusinessTipoPerfil->getTipoPerfilById($termo->NU_SEQ_TIPO_PERFIL);
        $dadosUsuario['DS_TIPO_PERFIL_SEGWEB'] = $perfil['DS_TIPO_PERFIL_SEGWEB'];

        $form = new TermoCompromisso_Form();

        $termo = $form->termo($dadosUsuario);
        $assinatura = $form->assinatura($dadosUsuario);

        $content = $termo . $assinatura;

        Fnde_Sice_Business_Componentes::gerarPdf($content);
    }

    public function getParamsTermo($params) {
        //seta valor do acordo baseado na ação
        switch ($params['co_acao']) {
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_COMBOLSA:
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_SEMBOLSA:
                $co_acordo = Fnde_Sice_Model_TermoCompromisso::CO_ACORDO_CONCORDO;
                break;
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_VIEW:
                $co_acordo = Fnde_Sice_Model_TermoCompromisso::CO_ACORDO_NAOCONCORDO;
                break;
        }

        $arParamsTermos = array(
            'CO_ACAO'                   => $params['co_acao'],
            'CO_ACORDO'                 => $co_acordo,
            'DT_INICIO'                 => date('d/m/Y G:i:s'),
            'DT_FIM'                    => null,
            'NU_ANO'                    => date('Y'),
            'NU_SEQ_TIPO_PERFIL'        => $params['nu_seq_tipo_perfil'],
            'NU_SEQ_USUARIO'            => $params['nu_seq_usuario'],
            'NU_SEQ_TERMO_COMPROMISSO'  => $params['nu_seq_termo_compromisso']
        );

        return $arParamsTermos;
    }

    public function getArRowAction() {
        $rowAction = array(
            'imprimir' => array('label' => 'Imprimir',
                'url' => $this->view->Url(array('action' => 'imprimir', 'NU_SEQ_TERMO_COMPROMISSO' => '')) . '%s', 'params' => array('NU_SEQ_TERMO_COMPROMISSO'),
                'attribs' => array(
                    'class' => 'icoImprimir imprimir', 'title' => 'Imprimir',
                    'target' => 'blank'
                )
            ),
            'delete' => array('label' => 'Excluir',
                'url' => $this->view->Url(array('action' => 'delete', 'NU_SEQ_TERMO_COMPROMISSO' => '')) . '%s', 'params' => array('NU_SEQ_TERMO_COMPROMISSO'),
                'attribs' => array(
                    'class' => 'icoExcluir excluir', 'title' => 'Excluir',
                    'mensagem' => 'Deseja realmente excluir o registro?'
                )
            ),
        );

        return $rowAction;
    }
}